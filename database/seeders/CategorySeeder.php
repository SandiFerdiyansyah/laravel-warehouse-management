<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Produk elektronik dan gadget'],
            ['name' => 'Furniture', 'description' => 'Perabotan dan furniture kantor'],
            ['name' => 'Alat Tulis', 'description' => 'Stationery dan alat tulis kantor'],
            ['name' => 'Komputer', 'description' => 'Peralatan komputer dan aksesoris'],
            ['name' => 'Kabel & Konektor', 'description' => 'Berbagai jenis kabel dan konektor'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}