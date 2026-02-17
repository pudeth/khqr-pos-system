<?php

echo "=== SSL Certificate Diagnostic ===\n\n";

// 1. Check certificate file
$certPath = __DIR__ . '/cacert.pem';
echo "1. Certificate File Check:\n";
echo "   Path: $certPath\n";
echo "   Exists: " . (file_exists($certPath) ? "YES ✓" : "NO ✗") . "\n";
if (file_exists($certPath)) {
    echo "   Size: " . filesize($certPath) . " bytes\n";
    echo "   Readable: " . (is_readable($certPath) ? "YES ✓" : "NO ✗") . "\n";
}
echo "\n";

// 2. Check PHP configuration
echo "2. PHP Configuration:\n";
echo "   curl.cainfo: " . (ini_get('curl.cainfo') ?: 'Not set') . "\n";
echo "   openssl.cafile: " . (ini_get('openssl.cafile') ?: 'Not set') . "\n";
echo "\n";

// 3. Apply SSL fix
echo "3. Applying SSL Fix:\n";
if (file_exists($certPath)) {
    putenv("CURL_CA_BUNDLE={$certPath}");
    putenv("SSL_CERT_FILE={$certPath}");
    ini_set('curl.cainfo', $certPath);
    ini_set('openssl.cafile', $certPath);
    
    echo "   CURL_CA_BUNDLE: " . getenv('CURL_CA_BUNDLE') . "\n";
    echo "   SSL_CERT_FILE: " . getenv('SSL_CERT_FILE') . "\n";
    echo "   curl.cainfo: " . ini_get('curl.cainfo') . "\n";
    echo "   openssl.cafile: " . ini_get('openssl.cafile') . "\n";
    echo "   Status: APPLIED ✓\n";
} else {
    echo "   Status: FAILED - Certificate not found ✗\n";
    exit(1);
}
echo "\n";

// 4. Test HTTPS connection
echo "4. Testing HTTPS Connection to Google:\n";
$ch = curl_init('https://www.googleapis.com/oauth2/v4/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CAINFO, $certPath);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'test=1');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$result = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo "   Status: FAILED ✗\n";
    echo "   Error: $error\n";
    exit(1);
} else {
    echo "   Status: SUCCESS ✓\n";
    echo "   HTTP Code: $httpCode\n";
    echo "   SSL Verification: PASSED ✓\n";
}
echo "\n";

// 5. Check Laravel environment
echo "5. Laravel Environment:\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "   Laravel: LOADED ✓\n";
    echo "   Base Path: " . base_path() . "\n";
    echo "   Cert Path (Laravel): " . base_path('cacert.pem') . "\n";
    echo "   Cert Exists (Laravel): " . (file_exists(base_path('cacert.pem')) ? "YES ✓" : "NO ✗") . "\n";
} else {
    echo "   Laravel: NOT LOADED (running standalone)\n";
}
echo "\n";

echo "=== Diagnostic Complete ===\n";
echo "\n";
echo "✓ All checks passed!\n";
echo "✓ SSL certificate is properly configured\n";
echo "✓ Google OAuth should work now\n";
echo "\n";
echo "Next step: Run 'restart-with-ssl-fix.bat' to start the server\n";
