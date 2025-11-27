<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$product = Product::with('productPrices')->first();
if (! $product) {
    echo "No products found\n";
    exit(0);
}

echo "Product: {$product->name} (ID: {$product->id})\n";
echo "Prices count: " . $product->productPrices->count() . "\n";
foreach ($product->productPrices as $p) {
    echo " - Store ID: {$p->store_id}, Price: {$p->selling_price}\n";
}
