<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add warehouse_id to storage_locations
        Schema::table('storage_locations', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('id')->constrained('warehouses')->onDelete('cascade');
        });

        // Add warehouse_id and make storage_location_id nullable in product_requests
        Schema::table('product_requests', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('product_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('storage_location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_requests', function (Blueprint $table) {
            $table->dropForeignKey(['warehouse_id']);
            $table->dropColumn('warehouse_id');
            $table->foreignId('storage_location_id')->constrained('storage_locations')->onDelete('restrict')->change();
        });

        Schema::table('storage_locations', function (Blueprint $table) {
            $table->dropForeignKey(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });
    }
};
