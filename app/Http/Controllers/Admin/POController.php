<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProductMovement; // <-- PERBAIKAN 1: Import model
use Illuminate\Support\Facades\Auth;

class POController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.po.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with(['category', 'supplier'])->orderBy('name')->get();

        return view('admin.po.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Generate PO number
        $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $purchaseOrder = PurchaseOrder::create([
            'po_number' => $poNumber,
            'admin_id' => auth()->id(),
            'supplier_id' => $request->supplier_id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Add items
        foreach ($request->items as $item) {
            PurchaseOrderItem::create([
                'po_id' => $purchaseOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return redirect()->route('admin.po.show', $purchaseOrder)
            ->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'admin', 'purchaseOrderItems.product']);
        return view('admin.po.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'admin', 'purchaseOrderItems.product']);
        return view('admin.po.edit', compact('purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,approved_supplier,shipped,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder->update($request->only(['status', 'notes']));

        return redirect()->route('admin.po.show', $purchaseOrder)
            ->with('success', 'Purchase Order updated successfully.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'shipped') {
            return back()->with('error', 'PO must be shipped before receiving.');
        }

        $purchaseOrder->load('purchaseOrderItems.product');

        // Update stock quantities
        foreach ($purchaseOrder->purchaseOrderItems as $item) {
            $item->product->increment('stock_quantity', $item->quantity);

            // Record movement
            ProductMovement::create([
                'product_id' => $item->product_id,
                'user_id' => auth()->id(),
                'type' => 'in',
                'quantity' => $item->quantity,
                'timestamp' => now(),
            ]);
        }

        $purchaseOrder->update(['status' => 'received']);

        return back()->with('success', 'Purchase Order received and stock updated.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Cannot cancel received PO.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return back()->with('success', 'Purchase Order cancelled.');
    }
}