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
        Schema::create('product_request_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_request_id')->constrained('product_requests')->onDelete('cascade');
            $table->string('tracking_number')->unique();
            $table->enum('status', ['in_transit', 'delivered'])->default('in_transit');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('product_request_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_request_shipments');
    }
};
