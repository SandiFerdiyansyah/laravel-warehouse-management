<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StorageLocation;
use App\Models\ProductMovement;
use App\Models\POReceiveLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Display list of POs ready to receive (status = shipped)
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'admin', 'purchaseOrderItems.product'])
            ->where('status', 'shipped')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('operator.purchase_orders.index', compact('purchaseOrders'));
    }

    /**
     * Resolve the main warehouse based on environment configuration or name fallback
     * Priority: MAIN_WAREHOUSE_ID -> MAIN_WAREHOUSE_CODE -> name 'gudang utama kota serang' -> first warehouse
     *
     * @return \App\Models\Warehouse|null
     */
    private function resolveMainWarehouse()
    {
        // 1) Check explicit ID from env
        $envId = env('MAIN_WAREHOUSE_ID');
        if ($envId) {
            $w = \App\Models\Warehouse::find($envId);
            if ($w) return $w;
        }

        // 2) Check warehouse_code from env
        $envCode = env('MAIN_WAREHOUSE_CODE');
        if ($envCode) {
            $w = \App\Models\Warehouse::where('warehouse_code', $envCode)->first();
            if ($w) return $w;
        }

        // 3) Fallback to name match (case-insensitive)
        $w = \App\Models\Warehouse::whereRaw('lower(name) = ?', [trim(strtolower('gudang utama kota serang'))])->first();
        if ($w) return $w;

        // 4) Last resort - first warehouse
        return \App\Models\Warehouse::with('storageLocations')->first();
    }

    /**
     * Show PO receive/scan form for a specific PO
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'shipped') {
            return redirect()->route('operator.po.index')
                ->with('error', 'Only shipped POs can be received.');
        }

        $purchaseOrder->load(['supplier', 'admin', 'purchaseOrderItems.product']);
        
        // Resolve the main warehouse (env override by id or code, else name fallback)
        $mainWarehouse = $this->resolveMainWarehouse();

        return view('operator.purchase_orders.receive', compact('purchaseOrder', 'mainWarehouse'));
    }

    /**
     * Get storage locations for a specific product in a warehouse
     */
    public function getStorageLocations(Request $request)
    {
        try {
            $productId = $request->query('product_id');
            $warehouseId = $request->query('warehouse_id');

            // If warehouse id not provided, use main warehouse
            if (!$warehouseId) {
                $main = $this->resolveMainWarehouse();
                if ($main) $warehouseId = $main->id;
            }

            // Basic validation: ensure numeric IDs
            if (!is_numeric($productId) || !is_numeric($warehouseId)) {
                return response()->json(['error' => 'Invalid parameters: product_id and warehouse_id must be numeric'], 400);
            }

            $productId = (int)$productId;
            $warehouseId = (int)$warehouseId;

            $locations = StorageLocation::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->orderBy('location_code')
                ->get(['id', 'location_code', 'capacity', 'quantity', 'product_id']);

            return response()->json($locations);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getStorageLocations error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'input' => $request->all()]);
            return response()->json(['error' => 'Server error while fetching locations'], 500);
        }
    }

    /**
     * Scan and receive item into a storage location
     */
    public function scanItem(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
            'po_item_id' => 'required|exists:purchase_order_items,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'scanned_quantity' => 'required|integer|min:1',
        ]);
        // Use database transaction + row-level locking to avoid race conditions when multiple operators scan simultaneously
        try {
            $result = DB::transaction(function () use ($validated) {
                $po = PurchaseOrder::lockForUpdate()->findOrFail($validated['po_id']);

                // Lock the PO item row
                $poItem = PurchaseOrderItem::where('id', $validated['po_item_id'])->lockForUpdate()->firstOrFail();

                // Lock the storage location row
                $storageLocation = StorageLocation::where('id', $validated['storage_location_id'])->lockForUpdate()->firstOrFail();

                // Verify PO item belongs to this PO
                if ($poItem->po_id !== $po->id) {
                    throw new \Exception('PO Item does not belong to this PO');
                }

                // Verify storage location belongs to the main warehouse
                $mainWarehouse = $this->resolveMainWarehouse();
                if ($mainWarehouse && $storageLocation->warehouse_id !== $mainWarehouse->id) {
                    throw new \Exception('Storage location is not in the main warehouse');
                }

                // Verify storage location has the correct product
                if ($storageLocation->product_id !== $poItem->product_id) {
                    throw new \Exception('Storage location is assigned to a different product');
                }

                // Verify scanned quantity doesn't exceed location capacity
                $availableCapacity = $storageLocation->capacity - $storageLocation->quantity;
                if ($validated['scanned_quantity'] > $availableCapacity) {
                    throw new \Exception("Scanned quantity ({$validated['scanned_quantity']}) exceeds available capacity ({$availableCapacity}).");
                }

                // Verify scanned quantity doesn't exceed remaining PO quantity
                $remainingQuantity = $poItem->quantity - ($poItem->received_quantity ?? 0);
                if ($validated['scanned_quantity'] > $remainingQuantity) {
                    throw new \Exception("Scanned quantity ({$validated['scanned_quantity']}) exceeds remaining PO quantity ({$remainingQuantity}).");
                }

                // Update storage location quantity
                $storageLocation->quantity = $storageLocation->quantity + $validated['scanned_quantity'];
                $storageLocation->save();

                // Update PO item received quantity
                $poItem->received_quantity = ($poItem->received_quantity ?? 0) + $validated['scanned_quantity'];
                $poItem->save();

                // Record audit log
                POReceiveLog::create([
                    'po_id' => $po->id,
                    'po_item_id' => $poItem->id,
                    'operator_id' => Auth::id(),
                    'storage_location_id' => $storageLocation->id,
                    'quantity_received' => $validated['scanned_quantity'],
                    'received_at' => now(),
                    'notes' => "Scanned via operator interface",
                ]);

                // Record product movement
                ProductMovement::create([
                    'product_id' => $poItem->product_id,
                    'storage_location_id' => $storageLocation->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $validated['scanned_quantity'],
                    'timestamp' => now(),
                    'notes' => "Received from PO {$po->po_number}",
                ]);

                return [
                    'location_quantity' => $storageLocation->quantity,
                    'po_item_received' => $poItem->received_quantity,
                    'po_item_remaining' => $poItem->quantity - $poItem->received_quantity,
                ];
            }, 5);

            return response()->json(array_merge(['success' => true, 'message' => "Scanned {$validated['scanned_quantity']} items successfully"], $result));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing scan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark PO as fully received (all items scanned)
     */
    public function complete(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'shipped') {
            return back()->with('error', 'Only shipped POs can be completed.');
        }

        $purchaseOrder->load('purchaseOrderItems');

        // Verify all PO items have been received
        foreach ($purchaseOrder->purchaseOrderItems as $item) {
            $received = $item->received_quantity ?? 0;
            if ($received < $item->quantity) {
                return back()->with('error', "Not all items have been received. {$item->product->name}: {$received}/{$item->quantity}");
            }
        }

        // Update PO status to received
        $purchaseOrder->update([
            'status' => 'received',
            'received_by' => Auth::id(),
            'received_at' => now(),
        ]);

        // Notify supplier that PO has been received
        \Illuminate\Support\Facades\Mail::send('emails.po-received', [
            'purchaseOrder' => $purchaseOrder,
            'operator' => Auth::user(),
        ], function ($message) use ($purchaseOrder) {
            $message->to($purchaseOrder->supplier->email)
                ->subject("Purchase Order {$purchaseOrder->po_number} - Received Notification");
        });

        return back()->with('success', 'Purchase Order marked as received and inventory updated. Supplier has been notified.');
    }

    /**
     * Undo a scanned item (reduce quantity from storage location and PO item)
     */
    public function undoScan(Request $request)
    {
        $validated = $request->validate([
            'po_item_id' => 'required|exists:purchase_order_items,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'quantity_to_undo' => 'required|integer|min:1',
        ]);

        try {
            $poItem = PurchaseOrderItem::findOrFail($validated['po_item_id']);
            $storageLocation = StorageLocation::findOrFail($validated['storage_location_id']);

            // Verify quantity to undo doesn't exceed what was received
            if ($validated['quantity_to_undo'] > ($poItem->received_quantity ?? 0)) {
                return response()->json(['error' => 'Cannot undo more than received quantity'], 400);
            }

            if ($validated['quantity_to_undo'] > $storageLocation->quantity) {
                return response()->json(['error' => 'Storage location quantity mismatch'], 400);
            }

            // Reduce storage location quantity
            $storageLocation->decrement('quantity', $validated['quantity_to_undo']);

            // Reduce PO item received quantity
            $poItem->received_quantity = max(0, ($poItem->received_quantity ?? 0) - $validated['quantity_to_undo']);
            $poItem->save();

            // Record product movement (reversal)
            ProductMovement::create([
                'product_id' => $poItem->product_id,
                'storage_location_id' => $storageLocation->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'quantity' => $validated['quantity_to_undo'],
                'timestamp' => now(),
                'notes' => "Undo scan - PO {$poItem->purchaseOrder->po_number}",
            ]);

            return response()->json([
                'success' => true,
                'message' => "Undid {$validated['quantity_to_undo']} items successfully",
                'location_quantity' => $storageLocation->quantity,
                'po_item_received' => $poItem->received_quantity,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error undoing scan: ' . $e->getMessage()], 500);
        }
    }
}
