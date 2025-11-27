<?php
/**
 * Quick Test Script for reCAPTCHA API
 * 
 * Usage:
 * 1. Run: php test_recaptcha_api.php
 * 2. Paste token from browser console
 * 3. Check response
 */

require 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$secret = $_ENV['RECAPTCHA_SECRET'];
$siteKey = $_ENV['RECAPTCHA_SITE_KEY'];

echo "=== reCAPTCHA API Test ===\n";
echo "Site Key: " . $siteKey . "\n";
echo "Secret Key: " . substr($secret, 0, 20) . "...\n\n";

// Prompt for token
echo "Paste the reCAPTCHA token from browser console (or 'test' for test token):\n";
$token = trim(fgets(STDIN));

if (empty($token)) {
    echo "No token provided. Exiting.\n";
    exit(1);
}

// Send to Google API
echo "\nSending request to Google reCAPTCHA API...\n";

$url = 'https://www.google.com/recaptcha/api/siteverify';
$postData = http_build_query([
    'secret' => $secret,
    'response' => $token,
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "\nHTTP Status Code: $httpCode\n";

if (!empty($curlError)) {
    echo "cURL Error: $curlError\n";
    exit(1);
}

echo "\nResponse from Google API:\n";
$decoded = json_decode($response, true);
echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

// Check result
echo "\n=== Result ===\n";
if (isset($decoded['success']) && $decoded['success'] === true) {
    echo "✓ Token is VALID\n";
    echo "Score: " . ($decoded['score'] ?? 'N/A') . "\n";
    echo "Action: " . ($decoded['action'] ?? 'N/A') . "\n";
} else {
    echo "✗ Token is INVALID\n";
    if (isset($decoded['error-codes'])) {
        echo "Error codes: " . json_encode($decoded['error-codes']) . "\n";
    }
}
