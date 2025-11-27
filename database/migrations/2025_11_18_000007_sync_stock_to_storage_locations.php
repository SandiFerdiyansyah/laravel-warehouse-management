<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Warehouse;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sinkronisasi products.stock_quantity ke storage_locations di gudang utama (WH-001).
     * Untuk setiap produk dengan stock > 0, buat/update storage location di gudang utama.
     */
    public function up()
    {
        $mainWarehouse = Warehouse::where('warehouse_code', 'WH-001')->first();
        
        if (!$mainWarehouse) {
            return; // Gudang utama tidak ditemukan, skip
        }

        $products = Product::where('stock_quantity', '>', 0)->get();

        foreach ($products as $product) {
            // Cek apakah sudah ada storage location untuk produk ini di gudang utama
            $existing = DB::table('storage_locations')
                ->where('warehouse_id', $mainWarehouse->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existing) {
                // Update quantity jika sudah ada
                DB::table('storage_locations')
                    ->where('id', $existing->id)
                    ->update(['quantity' => $product->stock_quantity]);
            } else {
                // Buat storage location baru dengan location code unik
                $locationCode = "MAIN-" . strtoupper(substr($product->sku, -6));
                
                DB::table('storage_locations')->insert([
                    'warehouse_id' => $mainWarehouse->id,
                    'product_id' => $product->id,
                    'location_code' => $locationCode,
                    'capacity' => $product->stock_quantity * 2, // kapasitas 2x stok
                    'quantity' => $product->stock_quantity,
                    'is_filled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Hapus semua storage locations dengan location_code MAIN-* dari gudang WH-001
        $mainWarehouse = Warehouse::where('warehouse_code', 'WH-001')->first();
        if ($mainWarehouse) {
            DB::table('storage_locations')
                ->where('warehouse_id', $mainWarehouse->id)
                ->where('location_code', 'like', 'MAIN-%')
                ->delete();
        }
    }
};
