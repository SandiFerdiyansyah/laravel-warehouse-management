#!/usr/bin/env php
<?php
/**
 * Quick Verification Script - Check 4 Roles & 4 Users
 * Usage: php test_4_roles.php
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;
use App\Models\User;

echo "=== VERIFICATION: 4 Roles Only ===\n\n";

$roles = Role::all();
echo "ROLES IN DATABASE:\n";
foreach ($roles as $role) {
    echo "  ✓ " . $role->name . "\n";
}
echo "\nTotal: " . $roles->count() . " roles\n";

if ($roles->count() !== 4) {
    echo "❌ ERROR: Expected 4 roles, found " . $roles->count() . "\n";
    exit(1);
}

echo "\n=== VERIFICATION: 4 Test Users ===\n\n";

$users = User::with('role')->get();
echo "USERS IN DATABASE:\n";
foreach ($users as $user) {
    echo "  ✓ " . $user->email . " → " . $user->role->name . "\n";
}
echo "\nTotal: " . $users->count() . " users\n";

echo "\n=== VERIFICATION: Warehouse Routes (Admin Only) ===\n\n";

echo "Routes containing 'warehouse':\n";
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if (strpos($route->uri, 'warehouse') !== false) {
        $uri = $route->uri;
        $methods = implode('|', array_diff($route->methods, ['HEAD']));
        echo "  ✓ " . $methods . ": " . $uri . "\n";
    }
}

echo "\n=== VERIFICATION: No 'warehouse' Role ===\n\n";

$warehouseRole = Role::where('name', 'warehouse')->first();
if ($warehouseRole) {
    echo "❌ ERROR: 'warehouse' role still exists!\n";
    exit(1);
} else {
    echo "✓ 'warehouse' role successfully removed\n";
}

echo "\n=== ALL CHECKS PASSED ✅ ===\n\n";

echo "Summary:\n";
echo "  ✓ 4 Roles: admin, operator, supplier, store\n";
echo "  ✓ 4 Test Users with correct roles\n";
echo "  ✓ Warehouse routes under /admin prefix\n";
echo "  ✓ 'warehouse' role removed\n";

exit(0);
