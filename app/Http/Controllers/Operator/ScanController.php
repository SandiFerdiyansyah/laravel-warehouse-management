<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\ProductMovement;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        $storageLocations = StorageLocation::orderBy('location_code')->get();
        return view('operator.scan', compact('storageLocations'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'type' => 'required|in:in,out',
            'quantity' => 'nullable|integer|min:1',
            'storage_location_id' => 'nullable|exists:storage_locations,id',
        ]);

        $product = Product::where('qr_code', 'like', '%' . $request->qr_code . '%')
            ->orWhere('sku', $request->qr_code)
            ->first();

        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        $qty = $request->filled('quantity') ? (int) $request->quantity : 1;
        $type = $request->type;

        $storageLocation = null;
        if ($type === 'in') {
            if (!$request->filled('storage_location_id')) {
                return back()->with('error', 'Storage location is required for incoming items.');
            }

            $storageLocation = StorageLocation::find($request->storage_location_id);
            if (!$storageLocation) {
                return back()->with('error', 'Storage location not found.');
            }
            if ($storageLocation->is_filled) {
                return back()->with('error', 'Storage location is already filled.');
            }

            // Enforce capacity: quantity must not exceed storage capacity
            if ($qty > $storageLocation->capacity) {
                return back()->with('error', "Quantity ({$qty}) exceeds storage location capacity ({$storageLocation->capacity}).");
            }
        }

        if ($type === 'out') {
            // Ensure product has enough approved stock to remove
            if ($product->stock_quantity < $qty) {
                return back()->with('error', 'Cannot remove more items than available in stock. Current stock: ' . $product->stock_quantity);
            }
        }

        // Record movement but DO NOT update product stock yet — admin will verify/approve
        $movementData = [
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity' => $qty,
            'timestamp' => now(),
            'approved' => false,
        ];

        if ($type === 'in' && $storageLocation) {
            $movementData['storage_location_id'] = $storageLocation->id;
        }

        $movement = ProductMovement::create($movementData);

        // If incoming, mark storage location as filled and optionally record link (simple behavior)
        if ($type === 'in' && $storageLocation) {
            $storageLocation->update(['is_filled' => true]);
        }

        return back()->with('success', 'Product scanned and recorded. Admin verification required before stock update.');
    }

    public function verifyProduct(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $product = Product::where('qr_code', 'like', '%' . $request->qr_code . '%')
            ->orWhere('sku', $request->qr_code)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name,
                'supplier' => $product->supplier->name,
                'stock' => $product->stock_quantity,
            ]
        ]);
    }

    public function recentScans()
    {
        // Get recent product movements by this operator (both in/out)
        $recentMovements = ProductMovement::with(['product','storageLocation'])
            ->where('user_id', auth()->id())
            ->orderBy('timestamp', 'desc')
            ->take(15)
            ->get();

        $scans = $recentMovements->map(function($movement) {
            return [
                'id' => $movement->id,
                'product_name' => $movement->product?->name ?? '-',
                'type' => $movement->type,
                'quantity' => $movement->quantity,
                'approved' => (bool) $movement->approved,
                'timestamp' => $movement->timestamp->diffForHumans(),
                'location_code' => $movement->storageLocation?->location_code ?? null,
            ];
        });

        return response()->json($scans);
    }
}