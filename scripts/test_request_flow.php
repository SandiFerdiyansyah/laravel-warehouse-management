<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductRequest;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

// Simulate store user login
$storeUser = \App\Models\User::where('email', 'store@warehouse.com')->first();
if (!$storeUser) {
    echo "Store user not found\n";
    exit(1);
}

Auth::login($storeUser);

echo "=== Testing Product Request Store Flow ===\n\n";

// Get test data
$product = Product::first();
$store = $storeUser->store;

if (!$product || !$store) {
    echo "Product or Store not found\n";
    exit(1);
}

echo "Product: {$product->name} (ID: {$product->id})\n";
echo "Store: {$store->name} (ID: {$store->id})\n";

// Test 1: Create request with valid quantity
echo "\nTest 1: Create product request\n";
$request = ProductRequest::create([
    'store_id' => $store->id,
    'product_id' => $product->id,
    'warehouse_id' => 1, // Main warehouse
    'quantity_requested' => 5,
    'status' => 'pending',
]);

echo "✓ Request created: ID {$request->id}, Status: {$request->status}\n";
echo "  - Store: {$request->store->name}\n";
echo "  - Product: {$request->product->name}\n";
echo "  - Warehouse: {$request->warehouse->name}\n";
echo "  - Qty: {$request->quantity_requested}\n";

// Test 2: Query by status (for admin view)
echo "\nTest 2: Fetch pending requests for admin\n";
$pending = ProductRequest::where('status', 'pending')->get();
echo "✓ Pending requests: " . $pending->count() . "\n";
foreach ($pending as $pr) {
    echo "  - ID {$pr->id}: {$pr->store->name} requesting {$pr->quantity_requested} of {$pr->product->name}\n";
}

// Test 3: Verify workflow can transition to 'approved' for operator to fulfill
echo "\nTest 3: Simulate admin approval (update status to 'approved')\n";
$request->update(['status' => 'approved']);
$request->refresh();
echo "✓ Request updated: Status = {$request->status}\n";

// Test 4: Verify operator can see approved requests
echo "\nTest 4: Fetch approved requests for operator fulfillment\n";
$approved = ProductRequest::where('status', 'approved')->get();
echo "✓ Approved requests: " . $approved->count() . "\n";
foreach ($approved as $pr) {
    echo "  - ID {$pr->id}: {$pr->store->name} needs {$pr->quantity_requested} of {$pr->product->name}\n";
}

// Cleanup
$request->delete();
echo "\n✓ Test request cleaned up\n";
