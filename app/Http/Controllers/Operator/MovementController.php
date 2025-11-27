<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ProductMovement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index()
    {
        $movements = ProductMovement::with('product')
            ->where('user_id', auth()->id())
            ->orderBy('timestamp', 'desc')
            ->paginate(20);

        return view('operator.movements.index', compact('movements'));
    }

    public function destroy(ProductMovement $movement)
    {
        // Only owner can delete
        if ($movement->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        // If movement was incoming and not approved, free storage location handling is not implemented here.
        if ($movement->type === 'in' && !$movement->approved && $movement->storage_location_id) {
            $loc = \App\Models\StorageLocation::find($movement->storage_location_id);
            if ($loc) {
                $loc->is_filled = false;
                $loc->save();
            }
        }

        $movement->delete();

        return back()->with('success', 'Movement deleted.');
    }
}
