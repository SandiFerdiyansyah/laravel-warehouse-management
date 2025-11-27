<?php

require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;

$user = User::where('email', 'store@warehouse.com')->first();

if ($user) {
    echo "✓ Store User Found: " . $user->name . "\n";
    echo "  ID: " . $user->id . "\n";
    echo "  Role ID: " . $user->role_id . "\n";
    echo "  Role Name: " . ($user->role ? $user->role->name : "NO ROLE LOADED") . "\n";
    echo "  Store Profile: " . ($user->store ? "EXISTS (ID:" . $user->store->id . ")" : "MISSING") . "\n";
} else {
    echo "✗ Store User NOT FOUND\n";
}
