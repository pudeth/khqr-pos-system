<?php

/**
 * Google OAuth Configuration Test
 * Run this to verify your Google OAuth setup
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "========================================\n";
echo "Google OAuth Configuration Test\n";
echo "========================================\n\n";

// Check if Socialite is installed
echo "✓ Checking Laravel Socialite...\n";
if (class_exists('Laravel\Socialite\Facades\Socialite')) {
    echo "  ✅ Laravel Socialite is installed\n\n";
} else {
    echo "  ❌ Laravel Socialite NOT found\n";
    echo "  Run: composer require laravel/socialite\n\n";
    exit(1);
}

// Check environment variables
echo "✓ Checking Google OAuth credentials...\n";
$clientId = env('GOOGLE_CLIENT_ID');
$clientSecret = env('GOOGLE_CLIENT_SECRET');
$redirectUri = env('GOOGLE_REDIRECT_URI');

if ($clientId) {
    echo "  ✅ GOOGLE_CLIENT_ID: " . substr($clientId, 0, 20) . "...\n";
} else {
    echo "  ❌ GOOGLE_CLIENT_ID not set in .env\n";
}

if ($clientSecret) {
    echo "  ✅ GOOGLE_CLIENT_SECRET: " . substr($clientSecret, 0, 15) . "...\n";
} else {
    echo "  ❌ GOOGLE_CLIENT_SECRET not set in .env\n";
}

if ($redirectUri) {
    echo "  ✅ GOOGLE_REDIRECT_URI: $redirectUri\n";
} else {
    echo "  ❌ GOOGLE_REDIRECT_URI not set in .env\n";
}

echo "\n";

// Check database columns
echo "✓ Checking database schema...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field IN ('google_id', 'avatar')");
    
    $hasGoogleId = false;
    $hasAvatar = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'google_id') {
            $hasGoogleId = true;
            echo "  ✅ Column 'google_id' exists\n";
        }
        if ($column->Field === 'avatar') {
            $hasAvatar = true;
            echo "  ✅ Column 'avatar' exists\n";
        }
    }
    
    if (!$hasGoogleId) {
        echo "  ❌ Column 'google_id' missing - Run: php artisan migrate\n";
    }
    if (!$hasAvatar) {
        echo "  ❌ Column 'avatar' missing - Run: php artisan migrate\n";
    }
} catch (Exception $e) {
    echo "  ❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Check routes
echo "✓ Checking routes...\n";
$routes = app('router')->getRoutes();
$hasGoogleRoute = false;
$hasCallbackRoute = false;

foreach ($routes as $route) {
    if ($route->uri() === 'auth/google') {
        $hasGoogleRoute = true;
        echo "  ✅ Route: /auth/google\n";
    }
    if ($route->uri() === 'auth/google/callback') {
        $hasCallbackRoute = true;
        echo "  ✅ Route: /auth/google/callback\n";
    }
}

if (!$hasGoogleRoute) {
    echo "  ❌ Route /auth/google not found\n";
}
if (!$hasCallbackRoute) {
    echo "  ❌ Route /auth/google/callback not found\n";
}

echo "\n========================================\n";

// Final summary
$allGood = class_exists('Laravel\Socialite\Facades\Socialite') 
    && $clientId 
    && $clientSecret 
    && $redirectUri 
    && $hasGoogleRoute 
    && $hasCallbackRoute;

if ($allGood) {
    echo "✅ ALL CHECKS PASSED!\n";
    echo "========================================\n\n";
    echo "Next steps:\n";
    echo "1. Add redirect URI to Google Console:\n";
    echo "   $redirectUri\n\n";
    echo "2. Test login at:\n";
    echo "   " . env('APP_URL') . "/login\n\n";
    echo "3. Click 'Continue with Google'\n\n";
} else {
    echo "⚠️  SOME CHECKS FAILED\n";
    echo "========================================\n\n";
    echo "Please fix the issues above and run again.\n\n";
}
