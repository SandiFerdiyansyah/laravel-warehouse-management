<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->info('Kategori belum ada. Jalankan CategorySeeder terlebih dahulu.');
            return;
        }

        // Create or get default supplier
        $defaultSupplier = Supplier::firstOrCreate(
            ['name' => 'Default Supplier'],
            [
                'contact_person' => 'Admin',
                'phone' => '081234567890',
                'address' => 'Default Warehouse Address',
            ]
        );

        $products = [
            [
                'sku' => 'PRD-001-LAPTOP',
                'name' => 'Laptop HP ProBook',
                'description' => 'Laptop HP ProBook 15 inch untuk produktivitas',
                'category_id' => $categories->where('name', 'Komputer')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 15000000,
                'stock_quantity' => 50,
                'qr_code' => 'QR-PRD-001',
            ],
            [
                'sku' => 'PRD-002-MOUSE',
                'name' => 'Mouse Logitech Wireless',
                'description' => 'Mouse wireless presisi tinggi',
                'category_id' => $categories->where('name', 'Komputer')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 250000,
                'stock_quantity' => 100,
                'qr_code' => 'QR-PRD-002',
            ],
            [
                'sku' => 'PRD-003-KEYBOARD',
                'name' => 'Keyboard Mechanical RGB',
                'description' => 'Keyboard mechanical dengan LED RGB',
                'category_id' => $categories->where('name', 'Komputer')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 500000,
                'stock_quantity' => 75,
                'qr_code' => 'QR-PRD-003',
            ],
            [
                'sku' => 'PRD-004-MONITOR',
                'name' => 'Monitor LG 24 Inch FHD',
                'description' => 'Monitor LED 24 inch Full HD',
                'category_id' => $categories->where('name', 'Elektronik')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 2500000,
                'stock_quantity' => 30,
                'qr_code' => 'QR-PRD-004',
            ],
            [
                'sku' => 'PRD-005-KURSI',
                'name' => 'Kursi Gaming Ergonomis',
                'description' => 'Kursi gaming dengan desain ergonomis',
                'category_id' => $categories->where('name', 'Furniture')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 3000000,
                'stock_quantity' => 25,
                'qr_code' => 'QR-PRD-005',
            ],
            [
                'sku' => 'PRD-006-MEJA',
                'name' => 'Meja Kerja Minimalis',
                'description' => 'Meja kerja dengan design minimalis modern',
                'category_id' => $categories->where('name', 'Furniture')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 2500000,
                'stock_quantity' => 20,
                'qr_code' => 'QR-PRD-006',
            ],
            [
                'sku' => 'PRD-007-KERTAS',
                'name' => 'Kertas A4 Putih 80gsm',
                'description' => 'Rim kertas A4 putih 80 gram per meter persegi',
                'category_id' => $categories->where('name', 'Alat Tulis')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 50000,
                'stock_quantity' => 500,
                'qr_code' => 'QR-PRD-007',
            ],
            [
                'sku' => 'PRD-008-PENSIL',
                'name' => 'Pensil HB Faber Castell',
                'description' => 'Pensil HB berkualitas premium dari Faber Castell',
                'category_id' => $categories->where('name', 'Alat Tulis')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 3000,
                'stock_quantity' => 1000,
                'qr_code' => 'QR-PRD-008',
            ],
            [
                'sku' => 'PRD-009-KABEL',
                'name' => 'Kabel USB Type-C 2 Meter',
                'description' => 'Kabel USB Type-C panjang 2 meter',
                'category_id' => $categories->where('name', 'Kabel & Konektor')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 75000,
                'stock_quantity' => 200,
                'qr_code' => 'QR-PRD-009',
            ],
            [
                'sku' => 'PRD-010-HDMI',
                'name' => 'Kabel HDMI 2.0 3 Meter',
                'description' => 'Kabel HDMI 2.0 standar 3 meter',
                'category_id' => $categories->where('name', 'Kabel & Konektor')->first()->id,
                'supplier_id' => $defaultSupplier->id,
                'price' => 100000,
                'stock_quantity' => 150,
                'qr_code' => 'QR-PRD-010',
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
