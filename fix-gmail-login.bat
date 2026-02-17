@echo off
echo ===================================
echo    Gmail Login Fix Script
echo ===================================
echo.

echo Step 1: Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo ✅ Caches cleared
echo.

echo Step 2: Testing Google OAuth configuration...
php test-gmail-config.php
echo.

echo Step 3: Starting Laravel development server...
echo ⚠️  IMPORTANT: Keep this window open while testing
echo.
echo After server starts:
echo 1. Open browser to: http://localhost:8000/login
echo 2. Click "Continue with Google"
echo 3. If you get "redirect_uri_mismatch" error:
echo    - Go to: https://console.cloud.google.com/apis/credentials
echo    - Add: http://localhost:8000/auth/google/callback
echo.
echo Starting server in 3 seconds...
timeout /t 3 /nobreak > nul
php artisan serve --host=localhost --port=8000