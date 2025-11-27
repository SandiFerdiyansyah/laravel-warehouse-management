<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('store_shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('storage_location_id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            
            // Drop store_name column as we'll use store_id instead
            $table->dropColumn('store_name');
        });
    }

    public function down()
    {
        Schema::table('store_shipments', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
            $table->string('store_name');
        });
    }
};
