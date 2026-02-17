@echo off
echo ========================================
echo Fixing SSL Certificate Issue for Google OAuth
echo ========================================
echo.

REM Check if cacert.pem already exists
if exist cacert.pem (
    echo Certificate bundle already exists!
    echo Skipping download...
    echo.
    goto :test
)

REM Download the latest CA certificate bundle from curl.se
echo [1/2] Downloading CA certificate bundle...
powershell -Command "try { Invoke-WebRequest -Uri 'https://curl.se/ca/cacert.pem' -OutFile 'cacert.pem' -UseBasicParsing; exit 0 } catch { exit 1 }"

if not exist cacert.pem (
    echo ERROR: Failed to download certificate bundle
    echo Please download manually from: https://curl.se/ca/cacert.pem
    echo Save it as cacert.pem in this directory
    pause
    exit /b 1
)

echo SUCCESS: Certificate bundle downloaded
echo.

:test
REM Get the current directory
set CURRENT_DIR=%cd%
set CERT_PATH=%CURRENT_DIR%\cacert.pem

echo [2/2] Certificate bundle location:
echo %CERT_PATH%
echo.

echo ========================================
echo AUTOMATIC FIX APPLIED
echo ========================================
echo.
echo The application has been configured to use the certificate
echo automatically through AppServiceProvider.php
echo.
echo No manual php.ini editing required!
echo.

echo ========================================
echo TESTING GOOGLE OAUTH
echo ========================================
echo.
echo Starting Laravel development server...
echo.
echo After the server starts:
echo 1. Open your browser to: http://localhost:8000/login
echo 2. Click "Sign in with Google"
echo 3. The SSL error should be fixed!
echo.
echo Press Ctrl+C to stop the server when done testing
echo.
echo ========================================
echo.

php artisan serve
