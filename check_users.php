<?php
/**
 * Check user data dan password hashes
 */

require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "\n=== User Data & Password Check ===\n\n";

$users = User::with('role')->get();

foreach ($users as $user) {
    echo "Email: {$user->email}\n";
    echo "Name: {$user->name}\n";
    echo "Role: {$user->role->name}\n";
    echo "Password Hash (first 30 chars): " . substr($user->password, 0, 30) . "...\n";
    
    // Test if password123 matches
    $matches = Hash::check('password123', $user->password);
    echo "Password matches 'password123'? " . ($matches ? 'YES ✓' : 'NO ✗') . "\n";
    echo "---\n\n";
}

echo "\n=== Check Complete ===\n";
