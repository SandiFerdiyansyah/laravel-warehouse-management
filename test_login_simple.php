<?php
/**
 * Simple test script to verify login works for all roles
 */

require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

$credentials = [
    ['email' => 'admin@warehouse.com', 'password' => 'password123', 'role' => 'admin'],
    ['email' => 'operator@warehouse.com', 'password' => 'password123', 'role' => 'operator'],
    ['email' => 'supplier@warehouse.com', 'password' => 'password123', 'role' => 'supplier'],
    ['email' => 'store@warehouse.com', 'password' => 'password123', 'role' => 'store'],
];

echo "\n=== Testing Login Credentials ===\n\n";

foreach ($credentials as $cred) {
    $user = User::where('email', $cred['email'])->first();
    
    if (!$user) {
        echo "❌ {$cred['email']} - User not found!\n";
        continue;
    }
    
    // Check if password is correct by attempting Auth::attempt
    if (Auth::attempt(['email' => $cred['email'], 'password' => $cred['password']], false)) {
        echo "✅ {$cred['email']} ({$cred['role']}) - Login successful!\n";
        Auth::logout();
    } else {
        echo "❌ {$cred['email']} ({$cred['role']}) - Password verification failed!\n";
        echo "   Hint: Password hash mismatch or user not found\n";
    }
}

echo "\n=== Test Complete ===\n\n";
