<?php

echo "=== Testing SSL Certificate Fix ===\n\n";

// Set the certificate path
$certPath = __DIR__ . '/cacert.pem';

if (!file_exists($certPath)) {
    echo "❌ ERROR: cacert.pem not found!\n";
    echo "Run: fix-ssl-certificate.bat\n";
    exit(1);
}

echo "✓ Certificate file found: $certPath\n";
echo "✓ File size: " . filesize($certPath) . " bytes\n\n";

// Set environment variables
putenv("CURL_CA_BUNDLE={$certPath}");
putenv("SSL_CERT_FILE={$certPath}");

echo "Testing HTTPS connection to Google...\n";

// Test cURL to Google
$ch = curl_init('https://www.googleapis.com/oauth2/v4/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CAINFO, $certPath);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'test=1');

$result = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo "❌ SSL Error: $error\n";
    exit(1);
}

echo "✓ SSL connection successful!\n";
echo "✓ HTTP Status: $httpCode\n";
echo "\n=== SSL Fix is Working! ===\n";
echo "\nYou can now use Google OAuth without SSL errors.\n";
echo "Test it at: http://localhost:8000/login\n";
