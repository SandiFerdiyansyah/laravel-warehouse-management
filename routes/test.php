<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Supplier;

// Test route untuk cek supplier data
Route::get('/test-supplier', function () {
    $user = User::where('email', 'supplier@warehouse.com')->first();
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    $supplier = $user->supplier;
    
    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name,
        ],
        'supplier' => $supplier ? [
            'id' => $supplier->id,
            'user_id' => $supplier->user_id,
            'name' => $supplier->name,
            'contact_person' => $supplier->contact_person,
            'phone' => $supplier->phone,
            'address' => $supplier->address,
        ] : null,
        'has_supplier_profile' => !!$supplier,
    ]);
});
