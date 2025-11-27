<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreProductPrice;
use Illuminate\Support\Facades\Auth;
use App\Models\StoreShipment;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = $user->store;

        // Get all products with their selling prices for this store
        $products = Product::with(['productPrices' => function ($query) use ($store) {
            $query->where('store_id', $store->id);
        }])->get();

        // Calculate current store stock using delivered store shipments
        $shipments = StoreShipment::where('store_id', $store->id)
            ->where('status', 'delivered')
            ->get()
            ->groupBy('product_id')
            ->map(fn($group) => $group->sum('quantity'))
            ->toArray();

        return view('store.products.index', [
            'products' => $products,
            'store' => $store,
            'storeStock' => $shipments,
        ]);
    }

    public function show(Product $product)
    {
        $user = Auth::user();
        $store = $user->store;

        // Get selling price for this store
        $sellingPrice = $product->productPrices()
            ->where('store_id', $store->id)
            ->first();

        return view('store.products.show', [
            'product' => $product,
            'sellingPrice' => $sellingPrice,
            'store' => $store,
        ]);
    }

    public function updatePrice(Product $product)
    {
        $user = Auth::user();
        $store = $user->store;

        // Validate selling price
        request()->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        // Create or update store product price
        StoreProductPrice::updateOrCreate(
            [
                'store_id' => $store->id,
                'product_id' => $product->id,
            ],
            [
                'selling_price' => request('selling_price'),
            ]
        );

        return redirect()->back()->with('success', 'Harga jual berhasil diperbarui');
    }
}
