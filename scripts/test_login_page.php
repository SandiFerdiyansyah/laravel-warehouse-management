<?php
// Simple HTTP request to test login page
$url = 'http://127.0.0.1:8000/login';
$options = [
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ Failed to fetch login page\n";
    exit(1);
}

echo "✓ Login page fetched successfully\n";
echo "Response length: " . strlen($response) . " bytes\n";
echo "First 500 chars:\n";
echo substr($response, 0, 500) . "\n";
echo "\n";

// Check for specific content
if (strpos($response, 'Warehouse Management') !== false) {
    echo "✓ Found 'Warehouse Management' text\n";
}

if (strpos($response, 'form') !== false || strpos($response, '<form') !== false) {
    echo "✓ Found form tag\n";
}

if (strpos($response, 'email') !== false) {
    echo "✓ Found email field\n";
}

if (strpos($response, 'password') !== false) {
    echo "✓ Found password field\n";
}

if (strpos($response, 'class=') !== false) {
    echo "✓ Found class attributes (CSS might be loaded)\n";
} else {
    echo "⚠️ No class attributes found - CSS classes missing!\n";
}

if (strpos($response, 'tailwindcss.com') !== false) {
    echo "✓ Tailwind CSS CDN loaded\n";
} else {
    echo "⚠️ Tailwind CSS CDN not found in HTML\n";
}

// Check for errors
if (strpos($response, 'error') !== false || strpos($response, 'Error') !== false) {
    echo "⚠️ Found error text in response\n";
}
