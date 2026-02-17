<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Google OAuth Configuration ===\n\n";

$clientId = config('services.google.client_id');
$clientSecret = config('services.google.client_secret');
$redirectUri = config('services.google.redirect');

echo "Client ID: " . $clientId . "\n";
echo "Client Secret: " . substr($clientSecret, 0, 20) . "...\n";
echo "Redirect URI: " . $redirectUri . "\n\n";

// Verify credentials are set
if (empty($clientId)) {
    echo "❌ ERROR: Client ID is not set!\n";
    exit(1);
}

if (empty($clientSecret)) {
    echo "❌ ERROR: Client Secret is not set!\n";
    exit(1);
}

if (empty($redirectUri)) {
    echo "❌ ERROR: Redirect URI is not set!\n";
    exit(1);
}

echo "✓ All credentials are configured!\n\n";

echo "=== SSL Certificate Check ===\n\n";

$certPath = base_path('cacert.pem');
echo "Certificate Path: " . $certPath . "\n";
echo "Certificate Exists: " . (file_exists($certPath) ? "YES ✓" : "NO ✗") . "\n\n";

if (file_exists($certPath)) {
    echo "✓ SSL certificate is ready!\n\n";
} else {
    echo "❌ SSL certificate not found!\n";
    echo "Run: fix-ssl-certificate.bat\n\n";
    exit(1);
}

echo "=== Ready to Test ===\n\n";
echo "✓ Google OAuth credentials configured\n";
echo "✓ SSL certificate ready\n";
echo "✓ All systems ready!\n\n";
echo "Next step: Run 'restart-with-ssl-fix.bat'\n";
echo "Then visit: http://localhost:8000/login\n";
