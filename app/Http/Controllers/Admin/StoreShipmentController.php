<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreShipment;
use App\Models\Product;
use App\Models\StorageLocation;
use Illuminate\Support\Facades\Auth;

class StoreShipmentController extends Controller
{
    public function index()
    {
        $shipments = StoreShipment::with(['product', 'storageLocation'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.shipments.index', compact('shipments'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $locations = StorageLocation::orderBy('location_code')->get();
        $stores = \App\Models\Store::with('user')->orderBy('name')->get();
        return view('admin.shipments.create', compact('products', 'locations', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'store_id' => 'required|exists:stores,id',
            'locations' => 'required|array|min:1',
            'locations.*.storage_location_id' => 'required|exists:storage_locations,id',
            'locations.*.quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $storeId = $request->store_id;
        $locations = $request->locations;
        $createdBy = Auth::id();
        
        // Get store
        $store = \App\Models\Store::find($storeId);
        if (!$store) {
            return redirect()->back()->withInput()->with('error', 'Toko tidak ditemukan');
        }

        // Validate that each quantity does not exceed available stock per location
        $errors = [];
        foreach ($locations as $idx => $loc) {
            $locId = $loc['storage_location_id'];
            $qty = $loc['quantity'];

            // Get available qty at this location
            $availableQty = \Illuminate\Support\Facades\DB::table('product_movements')
                ->where('product_id', $productId)
                ->where('storage_location_id', $locId)
                ->selectRaw("SUM(CASE WHEN type = 'in' THEN quantity ELSE -quantity END) as available")
                ->value('available') ?? 0;

            if ($qty > $availableQty) {
                $errors[] = "Lokasi #" . ($idx + 1) . ": quantity ($qty) melebihi stok tersedia ($availableQty)";
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', implode(', ', $errors));
        }

        // Create a shipment record for each location
        foreach ($locations as $loc) {
            StoreShipment::create([
                'product_id' => $productId,
                'storage_location_id' => $loc['storage_location_id'],
                'store_id' => $storeId,
                'quantity' => $loc['quantity'],
                'created_by' => $createdBy,
            ]);
        }

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment created for ' . count($locations) . ' location(s)');
    }

    // Return JSON list of storage locations that currently hold the product and their quantities
    public function productLocations($productId)
    {
        // aggregate movements per storage location
        $rows = \Illuminate\Support\Facades\DB::table('product_movements')
            ->select('storage_location_id', \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN type = 'in' THEN quantity ELSE -quantity END) as qty"))
            ->where('product_id', $productId)
            ->whereNotNull('storage_location_id')
            ->groupBy('storage_location_id')
            ->havingRaw('qty > 0')
            ->get();

        $result = [];
        foreach ($rows as $r) {
            $loc = \App\Models\StorageLocation::find($r->storage_location_id);
            if ($loc) {
                $result[] = [
                    'id' => $loc->id,
                    'location_code' => $loc->location_code,
                    'qty' => (int) $r->qty,
                    'capacity' => $loc->capacity,
                ];
            }
        }

        return response()->json($result);
    }
}
