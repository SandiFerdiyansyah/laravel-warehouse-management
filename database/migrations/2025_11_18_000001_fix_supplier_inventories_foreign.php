<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop wrong foreign key that references `stores` and add the correct one referencing `suppliers`.
        if (Schema::hasTable('supplier_inventories')) {
            Schema::table('supplier_inventories', function (Blueprint $table) {
                // drop existing FK on supplier_id if it exists
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Throwable $e) {
                    // ignore if it doesn't exist
                }

                // add correct FK to suppliers.id
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('supplier_inventories')) {
            Schema::table('supplier_inventories', function (Blueprint $table) {
                // drop the fk created in up()
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Throwable $e) {
                }

                // restore previous (incorrect) FK to stores to revert behaviour if needed
                $table->foreign('supplier_id')->references('id')->on('stores')->onDelete('cascade');
            });
        }
    }
};
