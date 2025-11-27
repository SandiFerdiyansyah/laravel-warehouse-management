<?php
/**
 * Product Request Workflow - Diagnostic Script
 * Tests all components of the 3-tier workflow
 * 
 * Usage: php artisan tinker
 *        include('test_workflow_diagnostic.php');
 */

echo "=== Product Request Workflow Diagnostic ===\n\n";

// 1. Check Database Structure
echo "1. DATABASE STRUCTURE\n";
echo "   Checking tables...\n";

$tables = [
    'products' => ['id', 'name', 'sku', 'product_category_id'],
    'warehouses' => ['id', 'name', 'warehouse_code', 'location'],
    'storage_locations' => ['id', 'warehouse_id', 'product_id', 'quantity', 'location_code'],
    'product_requests' => ['id', 'product_id', 'warehouse_id', 'storage_location_id', 'store_id', 'status'],
    'stores' => ['id', 'name', 'store_code'],
];

foreach ($tables as $table => $columns) {
    $count = DB::table($table)->count();
    echo "   ✓ {$table}: {$count} records\n";
}

// 2. Check Test Data
echo "\n2. TEST DATA\n";

$products = \App\Models\Product::count();
$warehouses = \App\Models\Warehouse::count();
$storageLocations = \App\Models\StorageLocation::count();
$stores = \App\Models\Store::count();
$productRequests = \App\Models\ProductRequest::count();

echo "   ✓ Products: {$products}\n";
echo "   ✓ Warehouses: {$warehouses}\n";
echo "   ✓ Storage Locations: {$storageLocations}\n";
echo "   ✓ Stores: {$stores}\n";
echo "   ✓ Product Requests: {$productRequests}\n";

// 3. Check Warehouse Stock Distribution
echo "\n3. WAREHOUSE STOCK DISTRIBUTION\n";

$warehouses = \App\Models\Warehouse::with('storageLocations')->get();
foreach ($warehouses as $w) {
    $totalStock = $w->storageLocations->sum('quantity');
    $locationsWithStock = $w->storageLocations->where('quantity', '>', 0)->count();
    echo "   " . $w->name . " (" . $w->warehouse_code . "): " . $totalStock . " units in " . $locationsWithStock . " locations\n";
    
    // Show stock by product
    $products = $w->storageLocations->groupBy('product_id');
    foreach ($products as $productId => $locations) {
        if ($productId) {
            $product = \App\Models\Product::find($productId);
            $stock = $locations->sum('quantity');
            echo "      - " . $product->name . ": " . $stock . " units\n";
        }
    }
}

// 4. Check Test Users
echo "\n4. TEST USERS\n";

$roles = ['admin', 'operator', 'warehouse', 'store', 'supplier'];
foreach ($roles as $role) {
    $users = \App\Models\User::whereHas('role', function ($q) use ($role) {
        $q->where('name', $role);
    })->count();
    echo "   ✓ " . $role . ": " . $users . " users\n";
}

// 5. Check Model Relationships
echo "\n5. MODEL RELATIONSHIPS\n";

$product = \App\Models\Product::first();
$warehouse = \App\Models\Warehouse::first();
$storage = \App\Models\StorageLocation::first();

if ($product && $warehouse && $storage) {
    echo "   ✓ Product has " . $product->storageLocations()->count() . " storage locations\n";
    echo "   ✓ Warehouse has " . $warehouse->storageLocations()->count() . " storage locations\n";
    echo "   ✓ Warehouse has " . $warehouse->productRequests()->count() . " product requests\n";
    echo "   ✓ Storage Location product: " . ($storage->product ? $storage->product->name : 'null') . "\n";
    echo "   ✓ Storage Location warehouse: " . ($storage->warehouse ? $storage->warehouse->name : 'null') . "\n";
}

// 6. Check API Endpoints
echo "\n6. API ENDPOINTS\n";

$routes = \Illuminate\Support\Facades\Route::getRoutes();
$productRequestRoutes = $routes->filter(function ($route) {
    return strpos($route->uri, 'product-request') !== false;
});

echo "   Product Request Routes Found:\n";
foreach ($productRequestRoutes as $route) {
    foreach ($route->methods as $method) {
        if ($method !== 'HEAD') {
            echo "   ✓ " . $method . ": " . $route->uri . "\n";
        }
    }
}

// 7. Check Scopes
echo "\n7. PRODUCTREQUEST SCOPES\n";

$totalRequests = \App\Models\ProductRequest::count();
$pending = \App\Models\ProductRequest::pending()->count();
$needsWarehouseSelection = \App\Models\ProductRequest::needsWarehouseSelection()->count();
$needsVerification = \App\Models\ProductRequest::needsVerification()->count();
$awaitingApproval = \App\Models\ProductRequest::awaitingApproval()->count();

echo "   ✓ Total Requests: " . $totalRequests . "\n";
echo "   ✓ Pending: " . $pending . "\n";
echo "   ✓ Needs Warehouse Selection: " . $needsWarehouseSelection . "\n";
echo "   ✓ Needs Operator Verification: " . $needsVerification . "\n";
echo "   ✓ Awaiting Approval: " . $awaitingApproval . "\n";

// 8. Sample Workflow Simulation
echo "\n8. SAMPLE WORKFLOW SIMULATION\n";

$store = \App\Models\Store::first();
$product = \App\Models\Product::first();
$warehouse = \App\Models\Warehouse::first();

if ($store && $product && $warehouse) {
    echo "   Creating sample request...\n";
    
    $req = \App\Models\ProductRequest::create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'store_id' => $store->id,
        'quantity_requested' => 5,
        'status' => 'pending',
    ]);
    
    echo "   ✓ Created request ID: " . $req->id . "\n";
    echo "   ✓ Status: " . $req->status . "\n";
    echo "   ✓ Warehouse ID: " . $req->warehouse_id . "\n";
    echo "   ✓ Storage Location ID: " . ($req->storage_location_id ?? 'NULL') . "\n";
    
    // Find available storage
    $storage = \App\Models\StorageLocation::where('warehouse_id', $warehouse->id)
        ->where('product_id', $product->id)
        ->where('quantity', '>=', 5)
        ->first();
    
    if ($storage) {
        echo "   ✓ Found suitable storage location: " . $storage->location_code . "\n";
        
        // Simulate warehouse selection
        $req->update(['storage_location_id' => $storage->id]);
        echo "   ✓ Warehouse selected storage location\n";
        
        // Simulate operator verification
        $operator = \App\Models\User::whereHas('role', function ($q) {
            $q->where('name', 'operator');
        })->first();
        
        if ($operator) {
            $req->update([
                'status' => 'verified',
                'quantity_verified' => 5,
                'operator_id' => $operator->id,
            ]);
            echo "   ✓ Operator verified request\n";
            echo "   ✓ Final status: " . $req->status . "\n";
        }
    }
    
    // Cleanup
    $req->delete();
    echo "   ✓ Sample request cleaned up\n";
}

echo "\n=== Diagnostic Complete ===\n";
