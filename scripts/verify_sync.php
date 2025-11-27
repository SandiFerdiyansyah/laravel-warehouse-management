<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Test API endpoint yang mengembalikan stock dan is_fallback flag
echo "Testing warehouse stock endpoint...\n\n";

// Test 1: Product dengan storage location (tidak fallback)
echo "Test 1: Product dengan storage location di WH-001\n";
$product1 = DB::table('products')->first();
$warehouse = DB::table('warehouses')->where('warehouse_code', 'WH-001')->first();
$hasStorage = DB::table('storage_locations')
    ->where('warehouse_id', $warehouse->id)
    ->where('product_id', $product1->id)
    ->where('quantity', '>', 0)
    ->exists();

echo "Product ID: {$product1->id}, Warehouse ID: {$warehouse->id}\n";
echo "Has storage location with qty>0: " . ($hasStorage ? 'YES' : 'NO') . "\n";

// Query endpoint
$stock = DB::table('storage_locations')
    ->where('warehouse_id', $warehouse->id)
    ->where('product_id', $product1->id)
    ->where('quantity', '>', 0)
    ->sum('quantity');

$isFallback = false;
if ($stock <= 0 && ($product1->stock_quantity ?? 0) > 0) {
    $stock = (int)$product1->stock_quantity;
    $isFallback = true;
}

echo "API Response: stock={$stock}, is_fallback={$isFallback}\n\n";

// Test 2: Check semua produk
echo "Test 2: All products sync status\n";
$allProducts = DB::table('products')->get();
foreach ($allProducts as $p) {
    $storageQty = DB::table('storage_locations')
        ->where('warehouse_id', $warehouse->id)
        ->where('product_id', $p->id)
        ->sum('quantity');
    
    $isFbk = $storageQty <= 0 && ($p->stock_quantity ?? 0) > 0;
    $displayStock = $storageQty > 0 ? $storageQty : (($p->stock_quantity ?? 0) > 0 ? (int)$p->stock_quantity : 0);
    
    echo "- Product {$p->id} (SKU: {$p->sku}): storage={$storageQty}, global={$p->stock_quantity}, display={$displayStock}, fallback={$isFbk}\n";
}
