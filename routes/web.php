<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Test route untuk debugging
Route::get('/test-supplier', function () {
    $user = User::where('email', 'supplier@warehouse.com')->first();
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    $supplier = $user->supplier;
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name,
        ],
        'supplier' => $supplier ? [
            'id' => $supplier->id,
            'user_id' => $supplier->user_id,
            'name' => $supplier->name,
            'contact_person' => $supplier->contact_person,
            'phone' => $supplier->phone,
            'address' => $supplier->address,
        ] : null,
        'has_supplier_profile' => !!$supplier,
    ]);
});

// Development test route - test login untuk semua users
Route::get('/test-login', function () {
    if (config('app.env') !== 'local') {
        abort(403, 'Hanya tersedia di development');
    }
    
    $credentials = [
        ['email' => 'admin@warehouse.com', 'password' => 'password123', 'role' => 'admin'],
        ['email' => 'operator@warehouse.com', 'password' => 'password123', 'role' => 'operator'],
        ['email' => 'supplier@warehouse.com', 'password' => 'password123', 'role' => 'supplier'],
        ['email' => 'store@warehouse.com', 'password' => 'password123', 'role' => 'store'],
    ];
    
    $results = [];
    
    foreach ($credentials as $cred) {
        $user = User::where('email', $cred['email'])->first();
        
        if (!$user) {
            $results[] = [
                'email' => $cred['email'],
                'status' => '❌ User not found',
            ];
            continue;
        }
        
        // Test authentication
        if (Auth::attempt(['email' => $cred['email'], 'password' => $cred['password']], false)) {
            $results[] = [
                'email' => $cred['email'],
                'role' => $cred['role'],
                'status' => '✅ Login berhasil',
                'user_id' => $user->id,
            ];
            Auth::logout();
        } else {
            $results[] = [
                'email' => $cred['email'],
                'role' => $cred['role'],
                'status' => '❌ Password salah',
            ];
        }
    }
    
    return response()->json(['test_results' => $results]);
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'directLogout'])->name('logout.direct');

// reCAPTCHA Challenge Routes (for medium-risk scores 0.3-0.5)
Route::get('/login/challenge', [LoginController::class, 'showChallenge'])->name('login.challenge');
Route::post('/login/verify-challenge', [LoginController::class, 'verifyChallenge'])->name('login.verify-challenge');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/scan', [App\Http\Controllers\Admin\ProductController::class, 'scan'])->name('products.scan');
    Route::post('/products/process-scan', [App\Http\Controllers\Admin\ProductController::class, 'processScan'])->name('products.processScan');
    
    // Categories
    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Suppliers
    Route::get('/suppliers', [App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [App\Http\Controllers\Admin\SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [App\Http\Controllers\Admin\SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [App\Http\Controllers\Admin\SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [App\Http\Controllers\Admin\SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('/suppliers/{supplier}/details', [App\Http\Controllers\Admin\SupplierController::class, 'details'])->name('suppliers.details');
    
    // Stores
    Route::get('/stores', [App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/create', [App\Http\Controllers\Admin\StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores', [App\Http\Controllers\Admin\StoreController::class, 'store'])->name('stores.store');
    Route::get('/stores/{store}/edit', [App\Http\Controllers\Admin\StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{store}', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{store}', [App\Http\Controllers\Admin\StoreController::class, 'destroy'])->name('stores.destroy');
    
    // Storage Locations
    Route::get('/storage', [App\Http\Controllers\Admin\StorageController::class, 'index'])->name('storage.index');
    Route::get('/storage/create', [App\Http\Controllers\Admin\StorageController::class, 'create'])->name('storage.create');
    Route::post('/storage', [App\Http\Controllers\Admin\StorageController::class, 'store'])->name('storage.store');
    Route::get('/storage/{storageLocation}/edit', [App\Http\Controllers\Admin\StorageController::class, 'edit'])->name('storage.edit');
    Route::put('/storage/{storageLocation}', [App\Http\Controllers\Admin\StorageController::class, 'update'])->name('storage.update');
    Route::delete('/storage/{storageLocation}', [App\Http\Controllers\Admin\StorageController::class, 'destroy'])->name('storage.destroy');
    
    // Purchase Orders
    Route::get('/po', [App\Http\Controllers\Admin\POController::class, 'index'])->name('po.index');
    Route::get('/po/create', [App\Http\Controllers\Admin\POController::class, 'create'])->name('po.create');
    Route::post('/po', [App\Http\Controllers\Admin\POController::class, 'store'])->name('po.store');
    Route::get('/po/{purchaseOrder}', [App\Http\Controllers\Admin\POController::class, 'show'])->name('po.show');
    Route::get('/po/{purchaseOrder}/edit', [App\Http\Controllers\Admin\POController::class, 'edit'])->name('po.edit');
    Route::put('/po/{purchaseOrder}', [App\Http\Controllers\Admin\POController::class, 'update'])->name('po.update');
    Route::post('/po/{purchaseOrder}/receive', [App\Http\Controllers\Admin\POController::class, 'receive'])->name('po.receive');
    Route::post('/po/{purchaseOrder}/cancel', [App\Http\Controllers\Admin\POController::class, 'cancel'])->name('po.cancel');
    // Product Movements monitoring
    Route::get('/movements', [App\Http\Controllers\Admin\ProductMovementController::class, 'index'])->name('movements.index');
    Route::post('/movements/{movement}/approve', [App\Http\Controllers\Admin\ProductMovementController::class, 'approve'])->name('movements.approve');
    Route::get('/movements/{movement}', [App\Http\Controllers\Admin\ProductMovementController::class, 'show'])->name('movements.show');
    Route::post('/movements/{movement}/cancel', [App\Http\Controllers\Admin\ProductMovementController::class, 'cancel'])->name('movements.cancel');
    
    // Store Shipments (Pengiriman Stok ke Toko)
    Route::get('/shipments', [App\Http\Controllers\Admin\StoreShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [App\Http\Controllers\Admin\StoreShipmentController::class, 'create'])->name('shipments.create');
    Route::get('/shipments/product/{product}/locations', [App\Http\Controllers\Admin\StoreShipmentController::class, 'productLocations'])->name('shipments.product.locations');
    Route::post('/shipments', [App\Http\Controllers\Admin\StoreShipmentController::class, 'store'])->name('shipments.store');
    
    // Product Requests (Permintaan Barang dari Toko)
    Route::get('/product-requests', [App\Http\Controllers\Admin\ProductRequestController::class, 'index'])->name('product_requests.index');
    Route::get('/product-requests/{id}', [App\Http\Controllers\Admin\ProductRequestController::class, 'show'])->name('product_requests.show');
    Route::post('/product-requests/{id}/approve', [App\Http\Controllers\Admin\ProductRequestController::class, 'processApproval'])->name('product_requests.approve');
    Route::post('/product-requests/{id}/delivered', [App\Http\Controllers\Admin\ProductRequestController::class, 'markDelivered'])->name('product_requests.delivered');
    
    // Warehouse - Select Storage Location (Admin manages warehouse requests)
    Route::get('/warehouse/product-requests', [App\Http\Controllers\Warehouse\WarehouseRequestController::class, 'index'])->name('warehouse_product_requests.index');
    Route::get('/warehouse/product-requests/{id}/select-location', [App\Http\Controllers\Warehouse\WarehouseRequestController::class, 'selectLocation'])->name('warehouse_product_requests.select_location');
    Route::put('/warehouse/product-requests/{id}/store-location', [App\Http\Controllers\Warehouse\WarehouseRequestController::class, 'storeLocation'])->name('warehouse_product_requests.store_location');
});

// Operator Routes
Route::middleware(['auth'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Operator\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/locations/filter/{status}', [App\Http\Controllers\Operator\DashboardController::class, 'filterLocations'])->name('locations.filter');
    
    // Scan
    Route::get('/scan', [App\Http\Controllers\Operator\ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/process', [App\Http\Controllers\Operator\ScanController::class, 'process'])->name('scan.process');
    Route::post('/scan/verify', [App\Http\Controllers\Operator\ScanController::class, 'verifyProduct'])->name('scan.verify');
    Route::get('/recent-scans', [App\Http\Controllers\Operator\ScanController::class, 'recentScans'])->name('scan.recent');

    // Movements management (operator)
    Route::get('/movements', [App\Http\Controllers\Operator\MovementController::class, 'index'])->name('movements.index');
    Route::delete('/movements/{movement}', [App\Http\Controllers\Operator\MovementController::class, 'destroy'])->name('movements.destroy');
    
    // Products (Operator)
    Route::get('/products', [App\Http\Controllers\Operator\ProductController::class, 'index'])->name('products.index');
    // View single product
    Route::get('/products/{product}', [App\Http\Controllers\Operator\ProductController::class, 'show'])->name('products.show');
    // Show adjust form
    Route::get('/products/{product}/adjust', [App\Http\Controllers\Operator\ProductController::class, 'adjustForm'])->name('products.adjust');
    // Submit adjustment
    Route::post('/products/{product}/adjust', [App\Http\Controllers\Operator\ProductController::class, 'submitAdjust'])->name('products.adjust.submit');

    // Shipments for operator to receive
    Route::get('/shipments', [App\Http\Controllers\Operator\StoreShipmentController::class, 'index'])->name('shipments.index');
    Route::post('/shipments/{shipment}/receive', [App\Http\Controllers\Operator\StoreShipmentController::class, 'receive'])->name('shipments.receive');
    
    // Product Requests (Verifikasi Permintaan Barang)
    Route::get('/product-requests', [App\Http\Controllers\Operator\ProductRequestController::class, 'index'])->name('product_requests.index');
    Route::get('/product-requests/{id}/verify', [App\Http\Controllers\Operator\ProductRequestController::class, 'verify'])->name('product_requests.verify');
    Route::post('/product-requests/{id}/verify', [App\Http\Controllers\Operator\ProductRequestController::class, 'storeVerification'])->name('product_requests.verify.store');
    
    // Purchase Orders (Penerimaan & Scan Barang)
    Route::get('/purchase-orders', [App\Http\Controllers\Operator\PurchaseOrderController::class, 'index'])->name('po.index');
    Route::get('/purchase-orders/{purchaseOrder}', [App\Http\Controllers\Operator\PurchaseOrderController::class, 'show'])->name('po.show');
    Route::post('/purchase-orders/{purchaseOrder}/complete', [App\Http\Controllers\Operator\PurchaseOrderController::class, 'complete'])->name('po.complete');
    Route::post('/purchase-orders/scan-item', [App\Http\Controllers\Operator\PurchaseOrderController::class, 'scanItem'])->name('po.scan-item');
    Route::get('/purchase-orders/storage-locations', [App\Http\Controllers\Operator\PurchaseOrderController::class, 'getStorageLocations'])->name('po.storage-locations');
    Route::post('/purchase-orders/undo-scan', [App\Http\Controllers\Operator\PurchaseOrderController::class, 'undoScan'])->name('po.undo-scan');
});

// Supplier Routes
Route::middleware(['auth'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{purchaseOrder}', [App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{purchaseOrder}/approve', [App\Http\Controllers\Supplier\OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{purchaseOrder}/ship', [App\Http\Controllers\Supplier\OrderController::class, 'ship'])->name('orders.ship');
    Route::put('/orders/{purchaseOrder}/shipping', [App\Http\Controllers\Supplier\OrderController::class, 'updateShipping'])->name('orders.updateShipping');
    Route::delete('/orders/{purchaseOrder}/shipping', [App\Http\Controllers\Supplier\OrderController::class, 'deleteShipping'])->name('orders.deleteShipping');

    // Products management for supplier (CRUD limited to supplier's products)
    Route::get('/products', [App\Http\Controllers\Supplier\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Supplier\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Supplier\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [App\Http\Controllers\Supplier\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Supplier\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Supplier\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Supplier\ProductController::class, 'destroy'])->name('products.destroy');
});

// Store Routes
Route::middleware(['auth', 'role:store'])->prefix('store')->name('store.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Store\DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::get('/products', [App\Http\Controllers\Store\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [App\Http\Controllers\Store\ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product}/price', [App\Http\Controllers\Store\ProductController::class, 'updatePrice'])->name('products.updatePrice');
    
    // Shipments (receiving shipments from admin)
    Route::get('/shipments', [App\Http\Controllers\Store\ShipmentController::class, 'index'])->name('shipments.index');
    Route::post('/shipments/{shipment}/receive', [App\Http\Controllers\Store\ShipmentController::class, 'receive'])->name('shipments.receive');
    
    // Product Requests (Permintaan Barang untuk Toko)
    Route::get('/product-requests', [App\Http\Controllers\Store\ProductRequestController::class, 'index'])->name('product_requests.index');
    Route::get('/product-requests/create', [App\Http\Controllers\Store\ProductRequestController::class, 'create'])->name('product_requests.create');
    Route::post('/product-requests', [App\Http\Controllers\Store\ProductRequestController::class, 'store'])->name('product_requests.store');
    Route::get('/product-requests/{id}', [App\Http\Controllers\Store\ProductRequestController::class, 'show'])->name('product_requests.show');
    Route::post('/product-requests/{id}/confirm-delivery', [App\Http\Controllers\Store\ProductRequestController::class, 'confirmDelivery'])->name('product_requests.confirm_delivery');
    
    // API for warehouse stock
    Route::get('/product-requests/warehouse-stock', [App\Http\Controllers\Store\ProductRequestController::class, 'getWarehouseStock'])->name('product_requests.warehouse_stock');
});

// Root redirect
Route::get('/', function () {
    return redirect('/login');
});