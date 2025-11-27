<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\StoreShipment;

$user = User::where('email', 'store@warehouse.com')->with('store')->first();
if (! $user) {
    echo "Store user not found\n";
    exit(1);
}
$store = $user->store;
if (! $store) {
    echo "Store profile missing for user\n";
    exit(1);
}

$shipments = StoreShipment::where('store_id', $store->id)
    ->where('status', 'delivered')
    ->with('product')
    ->get();

$inventory = $shipments->groupBy('product_id')
    ->map(function ($group, $productId) {
        $product = $group->first()->product;
        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => $group->sum('quantity'),
        ];
    })
    ->values()
    ->filter(fn ($item) => $item['quantity'] > 0);

echo "Inventory for store: {$store->name}\n";
foreach ($inventory as $item) {
    echo "- {$item['name']} ({$item['sku']}): {$item['quantity']}\n";
}
if ($inventory->isEmpty()) {
    echo "No inventory records found (no delivered shipments).\n";
}
