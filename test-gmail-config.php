<?php

// Bootstrap Laravel to access configuration
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Gmail Login Configuration Test (Laravel) ===\n\n";

// Test 1: Configuration values
echo "1. Laravel Configuration:\n";
$googleConfig = config('services.google');
if ($googleConfig) {
    echo "   ✅ Google service configured\n";
    echo "   Client ID: " . (isset($googleConfig['client_id']) ? substr($googleConfig['client_id'], 0, 20) . '...' : 'NOT SET') . "\n";
    echo "   Client Secret: " . (isset($googleConfig['client_secret']) ? 'SET' : 'NOT SET') . "\n";
    echo "   Redirect URI: " . ($googleConfig['redirect'] ?? 'NOT SET') . "\n";
} else {
    echo "   ❌ Google service not configured\n";
}

// Test 2: Environment variables
echo "\n2. Environment Variables:\n";
echo "   APP_URL: " . config('app.url') . "\n";
echo "   APP_ENV: " . config('app.env') . "\n";

// Test 3: SSL Certificate
echo "\n3. SSL Certificate:\n";
$certPath = base_path('cacert.pem');
if (file_exists($certPath)) {
    echo "   ✅ cacert.pem exists (" . filesize($certPath) . " bytes)\n";
} else {
    echo "   ❌ cacert.pem missing\n";
}

// Test 4: Database connection
echo "\n4. Database Connection:\n";
try {
    $users = \App\Models\User::count();
    echo "   ✅ Database connected ($users users)\n";
    
    // Check if we have google_id column
    $schema = \Illuminate\Support\Facades\Schema::hasColumn('users', 'google_id');
    echo "   " . ($schema ? "✅" : "❌") . " google_id column exists\n";
    
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 5: Routes
echo "\n5. Routes:\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$googleRoutes = 0;
foreach ($routes as $route) {
    if (strpos($route->uri(), 'auth/google') !== false) {
        $googleRoutes++;
    }
}
echo "   " . ($googleRoutes > 0 ? "✅" : "❌") . " Google OAuth routes found ($googleRoutes)\n";

echo "\n=== Test Complete ===\n";

if ($googleConfig && isset($googleConfig['client_id']) && $googleConfig['client_id']) {
    echo "\n✅ Configuration looks good!\n";
    echo "\nTo test Gmail login:\n";
    echo "1. Run: php artisan serve\n";
    echo "2. Visit: http://localhost:8000/login\n";
    echo "3. Click 'Continue with Google'\n";
    echo "\nIf you get redirect_uri_mismatch:\n";
    echo "- Add this URL to Google Console: http://localhost:8000/auth/google/callback\n";
} else {
    echo "\n❌ Configuration issues found!\n";
    echo "Check your .env file and run: php artisan config:clear\n";
}

?>