<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductMovement;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhereHas('category', function($q) use ($term) {
                      $q->where('name', 'like', "%{$term}%");
                  })->orWhereHas('supplier', function($q) use ($term) {
                      $q->where('name', 'like', "%{$term}%");
                  });
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('operator.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'productMovements' => function($q) {
            $q->with('user')->orderBy('timestamp', 'desc');
        }]);

        return view('operator.products.show', compact('product'));
    }

    public function adjustForm(Product $product)
    {
        return view('operator.products.adjust', compact('product'));
    }

    public function submitAdjust(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $qty = (int) $request->quantity;

        if ($request->type === 'in') {
            $product->stock_quantity = $product->stock_quantity + $qty;
        } else {
            $product->stock_quantity = $product->stock_quantity - $qty;
            if ($product->stock_quantity < 0) {
                $product->stock_quantity = 0;
            }
        }
        $product->save();

        ProductMovement::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'quantity' => $qty,
            'notes' => $request->notes,
            'timestamp' => now(),
        ]);

        return redirect()->route('operator.products.show', $product)
            ->with('success', 'Stock adjusted successfully.');
    }
}
