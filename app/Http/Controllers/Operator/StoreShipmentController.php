<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreShipment;
use App\Models\ProductMovement;
use Illuminate\Support\Facades\Auth;

class StoreShipmentController extends Controller
{
    public function index()
    {
        // show pending shipments for operator to receive (all for now)
        $shipments = StoreShipment::with(['product', 'storageLocation'])->where('status', 'pending')->orderBy('created_at', 'asc')->get();
        return view('operator.shipments.index', compact('shipments'));
    }

    public function receive(Request $request, StoreShipment $shipment)
    {
        // mark as received and create an incoming product movement
        if ($shipment->status !== 'pending') {
            return redirect()->back()->with('error', 'Shipment is not pending');
        }

        // create movement (unapproved) for operator to see
        $movement = ProductMovement::create([
            'product_id' => $shipment->product_id,
            'type' => 'in',
            'quantity' => $shipment->quantity,
            'user_id' => Auth::id(),
            'storage_location_id' => $shipment->storage_location_id,
            'approved' => false,
            'timestamp' => now(),
        ]);

        $shipment->status = 'received';
        $shipment->save();

        return redirect()->route('operator.shipments.index')->with('success', 'Shipment received and movement created');
    }
}
