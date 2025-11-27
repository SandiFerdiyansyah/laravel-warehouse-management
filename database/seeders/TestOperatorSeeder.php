<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\ProductMovement;
use Illuminate\Support\Facades\Storage;

class TestOperatorSeeder extends Seeder
{
    public function run()
    {
        // operator role
        $role = Role::where('name', 'operator')->first();
        if (!$role) return;

        $user = User::firstOrCreate(
            ['email' => 'operator_test@local'],
            ['name' => 'Operator Test', 'password' => bcrypt('secret123'), 'role_id' => $role->id]
        );

        $category = Category::first();
        $supplier = Supplier::first();

        if (!$category || !$supplier) return;

        $sku = 'TEST-PROD-OP';
        $qrPath = 'qr-codes/' . $sku . '.svg';
        if (!Storage::disk('public')->exists($qrPath)) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#fff"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="14">' . $sku . '</text></svg>';
            Storage::disk('public')->put($qrPath, $svg);
        }

        $product = Product::firstOrCreate(
            ['sku' => $sku],
            ['name' => 'Test Product Operator', 'category_id' => $category->id, 'supplier_id' => $supplier->id, 'price' => 1000, 'stock_quantity' => 0, 'qr_code' => $qrPath]
        );

        $location = StorageLocation::first();
        if (!$location) {
            $location = StorageLocation::create(['location_code' => 'LOC-TEST-1', 'capacity' => 1, 'is_filled' => false]);
        }

        // create a sample incoming movement (unapproved)
        ProductMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => 1,
            'timestamp' => now(),
            'approved' => false,
            'storage_location_id' => $location->id,
        ]);
    }
}
