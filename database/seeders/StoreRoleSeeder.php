<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class StoreRoleSeeder extends Seeder
{
    public function run()
    {
        // Check if store role already exists
        if (!Role::where('name', 'store')->exists()) {
            Role::create(['name' => 'store']);
        }
    }
}
