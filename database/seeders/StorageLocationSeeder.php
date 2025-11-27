<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageLocation;
use App\Models\Warehouse;
use App\Models\Product;

class StorageLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kita akan buat lokasi A-D, Rak 1-5, Baris 1-10
        // Menggunakan firstOrCreate agar aman dijalankan berkali-kali

        $racks = ['A', 'B', 'C', 'D'];
        $shelves = 5;
        $rows = 10;

        // Get all warehouses and products
        $warehouses = Warehouse::all();
        $products = Product::all();

        if ($warehouses->isEmpty() || $products->isEmpty()) {
            $this->command->info('Warehouse atau Product belum ada. Jalankan seeder yang sesuai terlebih dahulu.');
            return;
        }

        $warehouseIndex = 0;
        $productIndex = 0;

        foreach ($racks as $rackLetter) {
            for ($shelf = 1; $shelf <= $shelves; $shelf++) {
                for ($row = 1; $row <= $rows; $row++) {
                    
                    // Format kode: A-01-R1
                    $locationCode = sprintf('%s-%02d-R%d', $rackLetter, $shelf, $row);

                    // Distribute across warehouses
                    $warehouse = $warehouses[$warehouseIndex % $warehouses->count()];
                    $product = $products[$productIndex % $products->count()];

                    // PERBAIKAN: Gunakan firstOrCreate
                    // Ini akan mencari 'location_code', 
                    // dan hanya membuatnya jika belum ada.
                    StorageLocation::firstOrCreate(
                        ['location_code' => $locationCode], // Kunci unik untuk dicari
                        [
                            'warehouse_id' => $warehouse->id,
                            'product_id' => $product->id,
                            'capacity' => 100, // Data yang diisi jika belum ada
                            'quantity' => rand(10, 100), // Random stock quantity
                            'is_filled' => false,
                        ]
                    );

                    $productIndex++;
                    if ($productIndex % 5 == 0) {
                        $warehouseIndex++;
                    }
                }
            }
        }
    }
}
