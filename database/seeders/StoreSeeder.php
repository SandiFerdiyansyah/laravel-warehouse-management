<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure store role exists
        $role = Role::firstOrCreate(['name' => 'store']);

        // Create test store user
        $user = User::firstOrCreate(
            ['email' => 'store@warehouse.com'],
            [
                'name' => 'Test Store',
                'password' => Hash::make('password123'),
                'role_id' => $role->id,
            ]
        );

        // Create store profile
        Store::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Test Store',
                'contact_person' => 'John Doe',
                'phone' => '08123456789',
                'address' => 'Jl. Test No. 123',
            ]
        );
    }
}
