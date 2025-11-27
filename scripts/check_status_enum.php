<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Product Requests Table Schema:\n";
$columns = DB::select('DESCRIBE product_requests');
foreach ($columns as $col) {
    if ($col->Field === 'status') {
        echo "Status column:\n";
        echo "  Type: {$col->Type}\n";
        echo "  Null: {$col->Null}\n";
        echo "  Default: {$col->Default}\n";
    }
}

// Check migrations to see what enum values are defined
echo "\nChecking current status values in database:\n";
$requests = DB::table('product_requests')->distinct('status')->pluck('status');
foreach ($requests as $s) {
    echo "  - {$s}\n";
}
