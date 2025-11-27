<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductMovementController extends Controller
{
    public function index()
    {
        $movements = ProductMovement::with(['product', 'user'])
            ->orderBy('timestamp', 'desc')
            ->paginate(30);

        return view('admin.movements.index', compact('movements'));
    }

    public function show(ProductMovement $movement)
    {
        $movement->load(['product', 'user', 'storageLocation']);
        return view('admin.movements.show', compact('movement'));
    }

    public function approve(ProductMovement $movement)
    {
        if ($movement->approved) {
            return back()->with('error', 'Already approved.');
        }

        // Apply to product stock
        $product = Product::find($movement->product_id);
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        if ($movement->type === 'in') {
            $product->stock_quantity = $product->stock_quantity + $movement->quantity;
        } else {
            $product->stock_quantity = max(0, $product->stock_quantity - $movement->quantity);
            // if outgoing movement had a storage_location_id, free that location
            if ($movement->storage_location_id) {
                $loc = \App\Models\StorageLocation::find($movement->storage_location_id);
                if ($loc) {
                    $loc->is_filled = false;
                    $loc->save();
                }
            }
        }
        $product->save();

        $movement->approved = true;
        $movement->approved_by = auth()->id();
        $movement->approved_at = now();
        $movement->save();

        return back()->with('success', 'Movement approved and stock updated.');
    }

    public function cancel(ProductMovement $movement)
    {
        // If already approved, revert stock change
        $product = Product::find($movement->product_id);
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        if ($movement->approved) {
            if ($movement->type === 'in') {
                $product->stock_quantity = max(0, $product->stock_quantity - $movement->quantity);
            } else {
                $product->stock_quantity = $product->stock_quantity + $movement->quantity;
            }
            $product->save();
        }

        // Free storage location if assigned
        if ($movement->storage_location_id) {
            $loc = \App\Models\StorageLocation::find($movement->storage_location_id);
            if ($loc) {
                $loc->is_filled = false;
                $loc->save();
            }
        }

        $movement->delete();

        return redirect()->route('admin.movements.index')->with('success', 'Movement cancelled.');
    }
}
