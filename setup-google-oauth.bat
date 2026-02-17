@echo off
echo ========================================
echo Installing Google OAuth for POS System
echo ========================================
echo.

echo [1/3] Installing Laravel Socialite...
call composer.phar require laravel/socialite
echo.

echo [2/3] Running database migrations...
php artisan migrate
echo.

echo [3/3] Clearing cache...
php artisan config:clear
php artisan cache:clear
echo.

echo ========================================
echo Google OAuth Setup Complete!
echo ========================================
echo.
echo IMPORTANT: Configure Google Cloud Console
echo.
echo 1. Go to: https://console.cloud.google.com/apis/credentials
echo 2. Select your OAuth 2.0 Client ID
echo 3. Add this to Authorized redirect URIs:
echo    http://localhost:8000/auth/google/callback
echo.
echo If using a different URL, update GOOGLE_REDIRECT_URI in .env
echo.
echo Test your setup:
echo 1. Start server: php artisan serve
echo 2. Visit: http://localhost:8000/login
echo 3. Click "Continue with Google"
echo.
pause
