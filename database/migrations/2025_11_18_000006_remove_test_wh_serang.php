<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration will delete any test warehouse created with code 'WH-SERANG'.
     */
    public function up()
    {
        DB::table('warehouses')->where('warehouse_code', 'WH-SERANG')->delete();
    }

    /**
     * Reverse the migrations.
     * No-op: we don't recreate the test warehouse on rollback.
     */
    public function down()
    {
        // intentionally left blank
    }
};
