<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $supplier = auth()->user()->supplier;
        
        if (!$supplier) {
            return back()->with('error', 'Supplier profile not found.');
        }

        $purchaseOrders = PurchaseOrder::with(['admin', 'purchaseOrderItems.product'])
            ->where('supplier_id', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('supplier.orders.index', compact('purchaseOrders'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        // Check if this order belongs to the authenticated supplier
        $supplier = auth()->user()->supplier;
        if (!$supplier || $purchaseOrder->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }

        $purchaseOrder->load(['admin', 'purchaseOrderItems.product']);

        return view('supplier.orders.show', compact('purchaseOrder'));
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier || $purchaseOrder->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Order cannot be approved.');
        }

        $purchaseOrder->update(['status' => 'approved_supplier']);

        return back()->with('success', 'Order approved successfully.');
    }

    public function ship(Request $request, PurchaseOrder $purchaseOrder)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier || $purchaseOrder->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'approved_supplier') {
            return $request->wantsJson()
                ? response()->json(['error' => 'Order must be approved before shipping.'], 400)
                : back()->with('error', 'Order must be approved before shipping.');
        }

        $request->validate([
            'courier_type' => 'required|in:truck,express',
            'shipping_notes' => 'nullable|string',
        ]);

        // Generate tracking number
        $trackingNumber = PurchaseOrder::generateTrackingNumber();
        
        // Calculate estimated delivery date
        $estimatedDelivery = $purchaseOrder->calculateEstimatedDelivery($request->courier_type);

        // Before marking shipped, check and decrement supplier inventory for each item
        foreach ($purchaseOrder->purchaseOrderItems as $item) {
            $inv = \App\Models\SupplierInventory::where('supplier_id', $supplier->id)
                ->where('product_id', $item->product_id)
                ->first();

            if (!$inv || $inv->quantity < $item->quantity) {
                $msg = 'Insufficient supplier stock for product: ' . optional($item->product)->name . ' (required: ' . $item->quantity . ', available: ' . ($inv?->quantity ?? 0) . ')';
                return $request->wantsJson()
                    ? response()->json(['error' => $msg], 400)
                    : back()->with('error', $msg);
            }

            // decrement supplier inventory
            $inv->decrement('quantity', $item->quantity);
        }

        // Update purchase order with shipping information
        $purchaseOrder->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'courier_type' => $request->courier_type,
            'estimated_delivery' => $estimatedDelivery,
            'shipping_notes' => $request->shipping_notes,
            'shipped_at' => now(),
        ]);

        $message = 'Order marked as shipped. Tracking number: ' . $trackingNumber;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'tracking_number' => $trackingNumber,
                    'courier_type' => $request->courier_type,
                    'estimated_delivery' => $estimatedDelivery,
                ]
            ]);
        }

        return back()->with('success', $message);
    }

    public function updateShipping(Request $request, PurchaseOrder $purchaseOrder)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier || $purchaseOrder->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow updating shipping if order is being prepared to ship
        if (!$purchaseOrder->isApproved() && !$purchaseOrder->isShipped()) {
            return back()->with('error', 'Shipping information can only be updated for approved orders.');
        }

        $request->validate([
            'courier_type' => 'required|in:truck,express',
            'shipping_notes' => 'nullable|string',
        ]);

        // Recalculate estimated delivery if courier type changed
        $estimatedDelivery = $purchaseOrder->calculateEstimatedDelivery($request->courier_type);

        $purchaseOrder->update([
            'courier_type' => $request->courier_type,
            'estimated_delivery' => $estimatedDelivery,
            'shipping_notes' => $request->shipping_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipping information updated',
            'data' => [
                'courier_type' => $purchaseOrder->courier_type,
                'estimated_delivery' => $purchaseOrder->estimated_delivery?->format('Y-m-d'),
                'estimated_days' => PurchaseOrder::getEstimatedDays($request->courier_type),
            ]
        ]);
    }

    public function deleteShipping(PurchaseOrder $purchaseOrder)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier || $purchaseOrder->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deleting shipping if order hasn't been shipped yet
        if ($purchaseOrder->isShipped()) {
            return response()->json(['error' => 'Cannot delete shipping information for shipped orders'], 403);
        }

        $purchaseOrder->update([
            'tracking_number' => null,
            'courier_type' => null,
            'estimated_delivery' => null,
            'shipping_notes' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Shipping information deleted']);
    }
}