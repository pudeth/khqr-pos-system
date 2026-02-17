@echo off
echo ========================================
echo Google OAuth Diagnostics
echo ========================================
echo.

echo [1] Checking routes...
php artisan route:list --path=auth/google
echo.

echo [2] Checking .htaccess...
if exist "public\.htaccess" (
    echo ✅ .htaccess exists
) else (
    echo ❌ .htaccess missing!
)
echo.

echo [3] Testing configuration...
php test-google-oauth.php
echo.

echo [4] Current .env settings...
findstr "APP_URL" .env
findstr "GOOGLE_REDIRECT_URI" .env
echo.

echo ========================================
echo Troubleshooting Steps:
echo ========================================
echo.
echo If you see "404 Not Found":
echo.
echo 1. Make sure Apache mod_rewrite is enabled
echo 2. Make sure .htaccess is being read (AllowOverride All)
echo 3. Verify DocumentRoot points to /public folder
echo.
echo 4. In Google Console, add BOTH redirect URIs:
echo    - http://localhost/auth/google/callback
echo    - http://localhost:80/auth/google/callback
echo.
echo 5. Try accessing directly:
echo    http://localhost/test-google-callback.php
echo.
pause
