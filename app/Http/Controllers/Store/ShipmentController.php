<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreShipment;
use App\Models\ProductMovement;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = $user->store;

        // Get all shipments for this store
        $shipments = StoreShipment::where('store_id', $store->id)
            ->with(['product', 'storageLocation'])
            ->latest()
            ->paginate(15);

        return view('store.shipments.index', [
            'shipments' => $shipments,
            'store' => $store,
        ]);
    }

    public function receive(StoreShipment $shipment)
    {
        $user = Auth::user();
        $store = $user->store;

        // Verify ownership
        if ($shipment->store_id !== $store->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Validate receipt
        request()->validate([
            'received_quantity' => 'required|numeric|min:1|max:' . $shipment->quantity,
        ]);

        // Create incoming product movement (stock IN for store)
        ProductMovement::create([
            'product_id' => $shipment->product_id,
            'storage_location_id' => $shipment->storage_location_id,
            'type' => 'in',
            'quantity' => request('received_quantity'),
            'reference_type' => 'store_shipment',
            'reference_id' => $shipment->id,
            'user_id' => $user->id,
            'is_approved' => true,
            'approved_at' => now(),
            'notes' => "Shipment received by store: {$store->name}",
        ]);

        // Update shipment status if all received
        if (request('received_quantity') >= $shipment->quantity) {
            $shipment->update([
                'status' => 'delivered',
                'received_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Shipment received successfully');
    }
}
