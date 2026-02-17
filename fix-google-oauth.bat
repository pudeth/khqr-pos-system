@echo off
echo ========================================
echo Google OAuth 404 Fix
echo ========================================
echo.

echo This script will help fix the 404 error.
echo.
echo Choose your setup:
echo.
echo 1. Use Laravel built-in server (RECOMMENDED - Easiest)
echo 2. Keep using Apache/XAMPP (Requires manual configuration)
echo.
set /p choice="Enter choice (1 or 2): "

if "%choice%"=="1" goto laravel_server
if "%choice%"=="2" goto apache_setup
goto end

:laravel_server
echo.
echo ========================================
echo Setting up for Laravel Server
echo ========================================
echo.

echo [1/3] Updating .env file...
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://localhost:8000' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'GOOGLE_REDIRECT_URI=.*', 'GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback' | Set-Content .env"
echo ✅ .env updated
echo.

echo [2/3] Clearing config cache...
php artisan config:clear
echo ✅ Config cleared
echo.

echo [3/3] Starting Laravel server...
echo.
echo ========================================
echo IMPORTANT: Update Google Console!
echo ========================================
echo.
echo Go to: https://console.cloud.google.com/apis/credentials
echo.
echo Add this redirect URI:
echo   http://localhost:8000/auth/google/callback
echo.
echo Then test at: http://localhost:8000/login
echo.
echo ========================================
echo Starting server now...
echo Press Ctrl+C to stop
echo ========================================
echo.
php artisan serve
goto end

:apache_setup
echo.
echo ========================================
echo Apache/XAMPP Setup Instructions
echo ========================================
echo.
echo To fix the 404 error with Apache, you need to:
echo.
echo 1. Make sure DocumentRoot points to /public folder
echo    Edit: C:\xampp\apache\conf\httpd.conf
echo    Set: DocumentRoot "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public"
echo.
echo 2. Enable mod_rewrite
echo    In httpd.conf, uncomment:
echo    LoadModule rewrite_module modules/mod_rewrite.so
echo.
echo 3. Allow .htaccess
echo    Change: AllowOverride None
echo    To: AllowOverride All
echo.
echo 4. Restart Apache
echo.
echo 5. Update Google Console redirect URI to:
echo    http://localhost/auth/google/callback
echo.
echo See FIX-GOOGLE-404-ERROR.md for detailed instructions.
echo.
pause
goto end

:end
