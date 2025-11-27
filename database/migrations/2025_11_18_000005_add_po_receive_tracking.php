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
        // Add columns to purchase_orders for receive tracking
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('received_by')->nullable()->after('status');
            $table->timestamp('received_at')->nullable()->after('received_by');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });

        // Add columns to purchase_order_items for receive quantity tracking
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->integer('received_quantity')->default(0)->after('quantity');
        });

        // Create PO receive audit log table
        Schema::create('po_receive_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('po_item_id')->constrained('purchase_order_items')->onDelete('cascade');
            $table->foreignId('operator_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('storage_location_id')->constrained('storage_locations')->onDelete('restrict');
            $table->integer('quantity_received');
            $table->timestamp('received_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['po_id', 'created_at']);
            $table->index(['operator_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_receive_logs');

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['received_by', 'received_at']);
        });
    }
};
