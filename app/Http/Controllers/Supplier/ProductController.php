<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

// QR code generation (same approach as Admin controller)
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $products = Product::where('supplier_id', $supplier->id)
            ->with('category')
            ->orderBy('name')
            ->paginate(15);

        // Load supplier inventory quantities for these products
        $productIds = $products->pluck('id')->toArray();
        $inventories = \App\Models\SupplierInventory::where('supplier_id', $supplier->id)
            ->whereIn('product_id', $productIds)
            ->get()
            ->pluck('quantity', 'product_id')
            ->toArray();

        return view('supplier.products.index', compact('products', 'supplier', 'inventories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('supplier.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $sku = 'PRD-' . strtoupper(Str::random(8));

        // Generate QR code SVG (mirror admin behavior)
        try {
            $qrCodeData = $sku;
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeImage = $writer->writeString($qrCodeData, 'UTF-8');
            $qrCodePath = 'qr-codes/' . $sku . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeImage);
        } catch (\Throwable $e) {
            // If QR generation fails, fallback to empty string (avoid DB insert failure)
            \Log::error('Supplier product QR generation failed', ['error' => $e->getMessage()]);
            $qrCodePath = '';
        }

        // Create product record (admin warehouse stock kept separate)
        $product = Product::create([
            'sku' => $sku,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'supplier_id' => $supplier->id,
            'price' => $request->price,
            'qr_code' => $qrCodePath,
        ]);

        // Create or update supplier-specific inventory record
        \App\Models\SupplierInventory::updateOrCreate(
            ['supplier_id' => $supplier->id, 'product_id' => $product->id],
            ['quantity' => (int) $request->stock_quantity]
        );

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorizeSupplierProduct($product);
        $product->load('category');
        $user = auth()->user();
        $supplier = $user->supplier;
        $inventory = \App\Models\SupplierInventory::where('supplier_id', $supplier->id)
            ->where('product_id', $product->id)
            ->first();

        $inventoryQuantity = $inventory ? $inventory->quantity : 0;

        return view('supplier.products.show', compact('product', 'inventoryQuantity'));
    }

    public function edit(Product $product)
    {
        $this->authorizeSupplierProduct($product);
        $categories = Category::orderBy('name')->get();
        return view('supplier.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeSupplierProduct($product);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->update($request->only(['name','description','category_id','price']));

        $user = auth()->user();
        $supplier = $user->supplier;

        // Update supplier inventory instead of product.stock_quantity
        \App\Models\SupplierInventory::updateOrCreate(
            ['supplier_id' => $supplier->id, 'product_id' => $product->id],
            ['quantity' => (int) $request->stock_quantity]
        );

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeSupplierProduct($product);

        // delete qr if exists
        if ($product->qr_code && Storage::disk('public')->exists($product->qr_code)) {
            Storage::disk('public')->delete($product->qr_code);
        }

        $product->delete();
        return redirect()->route('supplier.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    protected function authorizeSupplierProduct(Product $product)
    {
        $user = auth()->user();
        $supplier = $user->supplier;
        if (!$supplier || $product->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
