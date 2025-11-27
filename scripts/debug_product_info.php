<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

$sku = $argv[1] ?? null;
if (!$sku) {
    echo "Usage: php scripts/debug_product_info.php <SKU>\n";
    exit(1);
}

$product = Product::where('sku', $sku)->first();
if (!$product) {
    echo "Product not found for SKU {$sku}\n";
    exit(1);
}

echo "Product ID: {$product->id}\n";
echo "Name: {$product->name}\n";

$warehouses = Warehouse::with('storageLocations')->get();
foreach ($warehouses as $w) {
    $stock = $w->storageLocations()->where('product_id', $product->id)->sum('quantity');
    echo "Warehouse {$w->warehouse_code} ({$w->name}) stock for product: {$stock}\n";
}

// Simulate controller fallback logic for a specific warehouse id if provided
$testWarehouseId = $argv[2] ?? null;
if ($testWarehouseId) {
    $w = Warehouse::find($testWarehouseId);
    if ($w) {
        $stock = $w->storageLocations()->where('product_id', $product->id)->where('quantity', '>', 0)->sum('quantity');
        $main = null;
        $envId = env('MAIN_WAREHOUSE_ID');
        if ($envId) $main = Warehouse::find($envId);
        if (!$main) {
            $envCode = env('MAIN_WAREHOUSE_CODE');
            if ($envCode) $main = Warehouse::where('warehouse_code', $envCode)->first();
        }
        if (!$main) {
            $main = Warehouse::whereRaw('lower(name) = ?', [trim(strtolower('gudang utama kota serang'))])->first();
        }
        if (!$main) $main = Warehouse::with('storageLocations')->first();

        echo "\nController-simulated stock for warehouse id {$testWarehouseId}: {$stock}\n";
        if ($stock <= 0) {
            echo " - No storage locations with qty >0 for this product in that warehouse.\n";
            if ($main && $main->id == $w->id) {
                echo " - Warehouse is main warehouse. Falling back to product.stock_quantity = {$product->stock_quantity}\n";
            }
        }
    }
}

// Show any storage locations for this product
$locations = DB::table('storage_locations')->where('product_id', $product->id)->get();
echo "Storage locations count: " . $locations->count() . "\n";
foreach ($locations as $loc) {
    echo " - Loc ID: {$loc->id}, warehouse_id: {$loc->warehouse_id}, qty: {$loc->quantity}, code: {$loc->location_code}\n";
}
