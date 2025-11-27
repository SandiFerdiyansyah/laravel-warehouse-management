<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        $operatorRole = Role::where('name', 'operator')->first();
        
        User::firstOrCreate(
            ['email' => 'operator@example.com'],
            ['name' => 'Operator',
            'password' => Hash::make('password123'),
            'role_id' => $operatorRole->id,
        ]);

        // Store user (Toko)
        $storeRole = Role::where('name', 'store')->first();
        
        User::firstOrCreate(
            ['email' => 'store@example.com'],
            ['name' => 'Toko',
            'password' => Hash::make('password123'),
            'role_id' => $storeRole->id,
        ]);

        // Supplier user
        $supplierRole = Role::where('name', 'supplier')->first();

        if ($supplierRole) {
            $supplierUser = User::firstOrCreate(
                ['email' => 'supplier@example.com'],
                [
                    'name' => 'Supplier',
                    'password' => Hash::make('password123'),
                    'role_id' => $supplierRole->id,
                ]
            );

            // Create supplier profile for supplier user
            Supplier::firstOrCreate(
                ['user_id' => $supplierUser->id],
                [
                    'name' => 'Supplier',
                    'contact_person' => 'Supplier Contact',
                    'phone' => '081234567890',
                    'address' => 'Supplier Address',
                ]
            );
        }
    }
}