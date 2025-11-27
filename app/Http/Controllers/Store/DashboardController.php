<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreShipment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = $user->store;

        // If store profile doesn't exist, redirect or show error
        if (!$store) {
            return redirect('/login')->with('error', 'Store profile not configured. Please contact administrator.');
        }

        // Get recent shipments received
        $recentShipments = StoreShipment::where('store_id', $store->id)
            ->where('status', 'delivered')
            ->latest()
            ->take(5)
            ->get();

        // Get inventory for this store using StoreShipment records (delivered shipments)
        $shipments = StoreShipment::where('store_id', $store->id)
            ->where('status', 'delivered')
            ->with('product')
            ->get();

        // Aggregate quantities by product
        $inventory = $shipments->groupBy('product_id')
            ->map(function ($group, $productId) {
                $product = $group->first()->product;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $group->sum('quantity'),
                ];
            })
            ->values()
            ->filter(fn ($item) => $item['quantity'] > 0);

        // Calculate sales metrics (placeholder - will be implemented with actual sales data)
        $totalRevenue = 0;
        $totalSold = 0;

        return view('store.dashboard', [
            'store' => $store,
            'recentShipments' => $recentShipments,
            'inventory' => $inventory,
            'totalRevenue' => $totalRevenue,
            'totalSold' => $totalSold,
        ]);
    }
}
