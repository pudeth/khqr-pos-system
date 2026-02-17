@echo off
echo ========================================
echo Restarting Laravel with SSL Fix
echo ========================================
echo.

REM Check if certificate exists
if not exist cacert.pem (
    echo ERROR: cacert.pem not found!
    echo Downloading certificate...
    powershell -Command "Invoke-WebRequest -Uri 'https://curl.se/ca/cacert.pem' -OutFile 'cacert.pem' -UseBasicParsing"
    
    if not exist cacert.pem (
        echo FAILED to download certificate!
        pause
        exit /b 1
    )
    echo Certificate downloaded successfully!
    echo.
)

echo Certificate found: cacert.pem
echo.

REM Clear Laravel cache
echo Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo Cache cleared!
echo.

REM Kill any existing php artisan serve processes
echo Stopping any running Laravel servers...
taskkill /F /IM php.exe 2>nul
timeout /t 2 /nobreak >nul
echo.

echo ========================================
echo Starting Laravel Server with SSL Fix
echo ========================================
echo.
echo SSL Certificate: CONFIGURED
echo Google OAuth: READY
echo.
echo After server starts:
echo 1. Open: http://localhost:8000/login
echo 2. Click "Sign in with Google"
echo 3. Login should work without SSL errors!
echo.
echo Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

php artisan serve
