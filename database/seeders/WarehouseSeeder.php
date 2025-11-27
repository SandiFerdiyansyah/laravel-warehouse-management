<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'warehouse_code' => 'WH-001',
                'name' => 'Gudang Pusat Jakarta',
                'location' => 'Jl. Industri No. 123, Jakarta',
                'description' => 'Gudang pusat untuk distribusi ke toko-toko di Jawa',
            ],
            [
                'warehouse_code' => 'WH-002',
                'name' => 'Gudang Surabaya',
                'location' => 'Jl. Pergudangan No. 45, Surabaya',
                'description' => 'Gudang regional untuk distribusi ke toko-toko di Jawa Timur',
            ],
            [
                'warehouse_code' => 'WH-003',
                'name' => 'Gudang Bandung',
                'location' => 'Jl. Ciwidey No. 78, Bandung',
                'description' => 'Gudang regional untuk distribusi ke toko-toko di Jawa Barat',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['warehouse_code' => $warehouse['warehouse_code']],
                $warehouse
            );
        }
    }
}
