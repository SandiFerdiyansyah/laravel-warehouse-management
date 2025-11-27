<?php
require_once 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

// Check if supplier user exists
$user = \App\Models\User::where('email', 'supplier@warehouse.com')->first();

if (!$user) {
    echo "ERROR: Supplier user not found!\n";
    exit(1);
}

echo "✓ User found: " . $user->email . " (Role: " . $user->role->name . ")\n";

// Check if supplier profile exists
if ($user->supplier) {
    echo "✓ Supplier profile found: " . $user->supplier->name . "\n";
    echo "✓ Supplier ID: " . $user->supplier->id . "\n";
    echo "✓ Contact Person: " . $user->supplier->contact_person . "\n";
    echo "\nSUCCESS: Supplier account is properly configured!\n";
} else {
    echo "ERROR: Supplier profile not found for user!\n";
    exit(1);
}
?>
