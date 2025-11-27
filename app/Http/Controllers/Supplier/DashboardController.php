<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\ProductMovement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $supplier = $user->supplier;
        
        if (!$supplier) {
            // Create supplier profile if user doesn't have one
            return redirect()->route('supplier.orders.index')
                ->with('warning', 'Please complete your supplier profile to access all features.');
        }

        $purchaseOrders = PurchaseOrder::with(['admin', 'purchaseOrderItems.product'])
            ->where('supplier_id', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'pending_orders' => PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'pending')->count(),
            'approved_orders' => PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'approved_supplier')->count(),
            'shipped_orders' => PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'shipped')->count(),
            'received_orders' => PurchaseOrder::where('supplier_id', $supplier->id)
                ->where('status', 'received')->count(),
        ];

        return view('supplier.dashboard', compact('purchaseOrders', 'stats'));
    }
}