<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Models\StorageLocation;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRequestController extends Controller
{
    /**
     * Resolve the main warehouse based on environment configuration or name fallback
     * Priority: MAIN_WAREHOUSE_ID -> MAIN_WAREHOUSE_CODE -> name 'gudang utama kota serang' -> first warehouse
     *
     * @return \App\Models\Warehouse|null
     */
    private function resolveMainWarehouse()
    {
        // 1) Check explicit ID from env
        $envId = env('MAIN_WAREHOUSE_ID');
        if ($envId) {
            $w = Warehouse::find($envId);
            if ($w) return $w;
        }

        // 2) Check warehouse_code from env
        $envCode = env('MAIN_WAREHOUSE_CODE');
        if ($envCode) {
            $w = Warehouse::where('warehouse_code', $envCode)->first();
            if ($w) return $w;
        }

        // 3) Fallback to name match (case-insensitive)
        $w = Warehouse::whereRaw('lower(name) = ?', [trim(strtolower('gudang utama kota serang'))])->first();
        if ($w) return $w;

        // 4) Last resort - first warehouse
        return Warehouse::with('storageLocations')->first();
    }
    /**
     * Tampilkan daftar permintaan barang toko
     */
    public function index()
    {
        $user = Auth::user();
        $store = $user->store;

        $requests = ProductRequest::where('store_id', $store->id)
            ->with(['product', 'warehouse', 'storageLocation', 'operator', 'admin', 'shipment'])
            ->latest()
            ->paginate(15);

        return view('store.product_requests.index', [
            'requests' => $requests,
        ]);
    }

    /**
     * Tampilkan form buat permintaan barang
     * Toko hanya melihat gudang dengan stok, bukan lokasi storage detail
     */
    public function create()
    {
        $products = Product::all();

        // Resolve the admin's main warehouse (env override supported)
        $mainWarehouse = $this->resolveMainWarehouse();
        if ($mainWarehouse) {
            $mainWarehouse->load(['storageLocations' => function ($query) {
                $query->where('quantity', '>', 0);
            }]);
        }

        $warehouses = $mainWarehouse ? collect([$mainWarehouse]) : collect();

        // Create warehouse options with stock info
        $warehouseStockOptions = [];
        foreach ($warehouses as $warehouse) {
            $warehouseStockOptions[$warehouse->id] = [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'code' => $warehouse->warehouse_code,
                'location' => $warehouse->location,
            ];
        }

        return view('store.product_requests.create', [
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouseStockOptions' => $warehouseStockOptions,
        ]);
    }

    /**
     * Simpan permintaan barang baru
     * Warehouse_id otomatis diset ke gudang utama admin
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_requested' => 'required|integer|min:1',
        ]);

        // Resolve main warehouse otomatis (dari env atau fallback)
        $mainWarehouse = $this->resolveMainWarehouse();
        if (!$mainWarehouse) {
            return back()->with('error', 'Gudang utama tidak ditemukan. Hubungi administrator.');
        }

        // Cek apakah gudang utama punya stock untuk produk ini
        $totalStock = $mainWarehouse->storageLocations()
            ->where('product_id', $validated['product_id'])
            ->where('quantity', '>', 0)
            ->sum('quantity');

        // Jika stock 0, fallback ke product.stock_quantity
        if ($totalStock <= 0) {
            $product = \App\Models\Product::find($validated['product_id']);
            if ($product) {
                $totalStock = (int)($product->stock_quantity ?? 0);
            }
        }

        if ($totalStock < $validated['quantity_requested']) {
            return back()->withErrors([
                'quantity_requested' => 'Stock di gudang tidak mencukupi. Stock tersedia: ' . $totalStock,
            ])->withInput();
        }

        ProductRequest::create([
            'store_id' => $store->id,
            'product_id' => $validated['product_id'],
            'warehouse_id' => $mainWarehouse->id,
            'quantity_requested' => $validated['quantity_requested'],
            'status' => 'pending',
        ]);

        return redirect()->route('store.product_requests.index')
            ->with('success', 'Permintaan barang berhasil dibuat. Admin akan memproses dan meneruskan ke operator.');
    }

    /**
     * Tampilkan detail permintaan
     */
    public function show($id)
    {
        $user = Auth::user();
        $store = $user->store;

        $request = ProductRequest::where('store_id', $store->id)
            ->with(['product', 'warehouse', 'storageLocation', 'operator', 'admin', 'shipment'])
            ->findOrFail($id);

        return view('store.product_requests.show', [
            'request' => $request,
        ]);
    }

    /**
     * Konfirmasi penerimaan barang
     */
    public function confirmDelivery($id)
    {
        $user = Auth::user();
        $store = $user->store;

        $request = ProductRequest::where('store_id', $store->id)->findOrFail($id);

        if ($request->status !== 'shipped') {
            return back()->with('error', 'Hanya permintaan dengan status shipped yang bisa dikonfirmasi.');
        }

        $request->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        if ($request->shipment) {
            $request->shipment->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        return back()->with('success', 'Barang berhasil diterima dan dikonfirmasi.');
    }

    /**
     * Get warehouse stock for product (API endpoint)
     */
    public function getWarehouseStock(Request $request)
    {
        $productId = $request->query('product_id');
        $warehouseId = $request->query('warehouse_id');

        if (!$productId) {
            return response()->json(['error' => 'product_id required'], 400);
        }

        // If warehouse_id not provided, use the resolved main warehouse
        if (!$warehouseId) {
            $main = $this->resolveMainWarehouse();
            if (!$main) {
                return response()->json(['error' => 'warehouse_id required and main warehouse could not be resolved'], 400);
            }
            $warehouseId = $main->id;
        }

        $warehouse = Warehouse::findOrFail($warehouseId);
        $stock = $warehouse->storageLocations()
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->sum('quantity');

        // Fallback: if warehouse has no storage location entries for this product,
        // but the Product has a global stock_quantity (admin-managed), expose that
        // value for the main admin warehouse so stores can create requests.
        $isFallback = false;
        if ($stock <= 0) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $main = $this->resolveMainWarehouse();
                if ($main && $main->id == $warehouse->id && ($product->stock_quantity ?? 0) > 0) {
                    $stock = (int)$product->stock_quantity;
                    $isFallback = true;
                }
            }
        }

        return response()->json(['stock' => $stock, 'is_fallback' => $isFallback]);
    }

}

