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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Shipping information columns
            $table->string('tracking_number')->nullable()->after('status');
            $table->enum('courier_type', ['truck', 'express'])->nullable()->after('tracking_number');
            $table->date('estimated_delivery')->nullable()->after('courier_type');
            $table->text('shipping_notes')->nullable()->after('estimated_delivery');
            $table->dateTime('shipped_at')->nullable()->after('shipping_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_number',
                'courier_type',
                'estimated_delivery',
                'shipping_notes',
                'shipped_at',
            ]);
        });
    }
};
