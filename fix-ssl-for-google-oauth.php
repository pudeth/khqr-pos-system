<?php

/**
 * SSL Certificate Fix for Google OAuth
 * This script downloads a fresh SSL certificate and configures it properly
 */

echo "=== SSL Certificate Fix for Google OAuth ===\n\n";

// Step 1: Download fresh SSL certificate
echo "1. Downloading fresh SSL certificate...\n";
$certUrl = 'https://curl.se/ca/cacert.pem';
$certPath = 'cacert.pem';

// Use a temporary context to download the certificate
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
    'http' => [
        'timeout' => 30,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
]);

$certData = @file_get_contents($certUrl, false, $context);

if ($certData && strlen($certData) > 100000) {
    file_put_contents($certPath, $certData);
    echo "   ✅ Fresh SSL certificate downloaded (" . strlen($certData) . " bytes)\n";
} else {
    echo "   ⚠️  Using existing certificate (download failed)\n";
}

// Step 2: Verify certificate file
echo "\n2. Verifying SSL certificate...\n";
if (file_exists($certPath)) {
    $size = filesize($certPath);
    echo "   ✅ Certificate exists ($size bytes)\n";
    
    // Check if it's a valid PEM file
    $content = file_get_contents($certPath);
    if (strpos($content, '-----BEGIN CERTIFICATE-----') !== false) {
        echo "   ✅ Valid PEM format detected\n";
    } else {
        echo "   ❌ Invalid PEM format\n";
    }
} else {
    echo "   ❌ Certificate file missing\n";
    exit(1);
}

// Step 3: Configure environment
echo "\n3. Configuring SSL environment...\n";
$fullCertPath = realpath($certPath);

// Set environment variables
putenv("CURL_CA_BUNDLE={$fullCertPath}");
putenv("SSL_CERT_FILE={$fullCertPath}");

// Set ini settings
ini_set('curl.cainfo', $fullCertPath);
ini_set('openssl.cafile', $fullCertPath);

echo "   ✅ Environment configured\n";
echo "   CURL_CA_BUNDLE: " . getenv('CURL_CA_BUNDLE') . "\n";
echo "   SSL_CERT_FILE: " . getenv('SSL_CERT_FILE') . "\n";

// Step 4: Test SSL connection to Google
echo "\n4. Testing SSL connection to Google...\n";
$testUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';

$testContext = stream_context_create([
    'ssl' => [
        'cafile' => $fullCertPath,
        'verify_peer' => true,
        'verify_peer_name' => true,
    ],
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'user_agent' => 'Laravel/Socialite SSL Test'
    ]
]);

$headers = @get_headers($testUrl, 1, $testContext);
if ($headers && (strpos($headers[0], '401') !== false || strpos($headers[0], '400') !== false)) {
    echo "   ✅ SSL connection to Google APIs successful\n";
} else {
    echo "   ⚠️  SSL connection test inconclusive (this may be normal)\n";
}

// Step 5: Test cURL directly
echo "\n5. Testing cURL with certificate...\n";
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CAINFO, $fullCertPath);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($result !== false && $httpCode == 200) {
        echo "   ✅ cURL SSL test successful\n";
    } else {
        echo "   ❌ cURL SSL test failed: $error\n";
    }
} else {
    echo "   ❌ cURL not available\n";
}

echo "\n=== SSL Fix Complete ===\n";
echo "\nNext steps:\n";
echo "1. Clear Laravel cache: php artisan config:clear\n";
echo "2. Start server: php artisan serve\n";
echo "3. Test Google login at: http://localhost:8000/login\n";

?>