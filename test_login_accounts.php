<?php
/**
 * Test script to verify all user accounts for manual password setup
 */

require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$accounts = [
    ['email' => 'admin@warehouse.com', 'password' => 'password123', 'role' => 'admin'],
    ['email' => 'operator@warehouse.com', 'password' => 'password123', 'role' => 'operator'],
    ['email' => 'supplier@warehouse.com', 'password' => 'password123', 'role' => 'supplier'],
    ['email' => 'store@warehouse.com', 'password' => 'password123', 'role' => 'store'],
];

echo "\n=== Checking and Setting Account Passwords ===\n\n";

foreach ($accounts as $account) {
    $user = User::where('email', $account['email'])->first();
    
    if (!$user) {
        echo "❌ User not found: {$account['email']}\n";
        continue;
    }
    
    // Update password to known value
    $user->password = Hash::make($account['password']);
    $user->save();
    
    echo "✅ {$account['email']} ({$account['role']})\n";
    echo "   Password set to: {$account['password']}\n";
    echo "   Try logging in with these credentials\n\n";
}

echo "\n=== All accounts ready for testing ===\n";
echo "Use the following credentials to test:\n\n";
foreach ($accounts as $account) {
    echo "Email: {$account['email']}\n";
    echo "Password: {$account['password']}\n";
    echo "Role: {$account['role']}\n\n";
}
