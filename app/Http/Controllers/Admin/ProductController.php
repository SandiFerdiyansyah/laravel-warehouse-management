<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductMovement;

// PERBAIKAN: Import library QR Code secara manual
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'supplier'])
            ->orderBy('name')
            ->paginate(15);
        
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        // Generate unique SKU
        $sku = 'PRD-' . strtoupper(Str::random(8));
        
        // Generate QR code
        $qrCodeData = $sku;

        // --- PERBAIKAN FINAL: Memaksa menggunakan Imagick BackEnd ---
        // 1. Atur renderer untuk menggunakan Imagick BackEnd dengan ukuran 200px
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        
        // 2. Buat writer menggunakan renderer GD
        $writer = new Writer($renderer);
        
        // 3. Hasilkan gambar PNG sebagai string
        $qrCodeImage = $writer->writeString($qrCodeData, 'UTF-8');
        // --- Akhir Perbaikan ---

        $qrCodePath = 'qr-codes/' . $sku . '.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeImage);

        $product = Product::create([
            'sku' => $sku,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'qr_code' => $qrCodePath,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'productMovements' => function($query) {
            $query->with('user')->orderBy('timestamp', 'desc');
        }]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete QR code file
        if ($product->qr_code && Storage::disk('public')->exists($product->qr_code)) {
            Storage::disk('public')->delete($product->qr_code);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function scan()
    {
        return view('admin.products.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $product = Product::where('qr_code', 'like', '%' . $request->qr_code . '%')
            ->orWhere('sku', $request->qr_code)
            ->first();

        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        // Add to stock
        $product->increment('stock_quantity', 1);

        // Record movement
        ProductMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => 1,
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product added to stock successfully.');
    }
}