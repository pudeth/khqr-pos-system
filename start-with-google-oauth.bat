@echo off
cls
echo.
echo ========================================
echo   POS System with Google OAuth
echo ========================================
echo.
echo Starting Laravel development server...
echo.
echo IMPORTANT: 
echo After server starts, update Google Console:
echo.
echo 1. Go to: https://console.cloud.google.com/apis/credentials
echo 2. Add redirect URI: http://localhost:8000/auth/google/callback
echo 3. Save
echo.
echo Then test at: http://localhost:8000/login
echo.
echo ========================================
echo.

REM Update .env for port 8000
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://localhost:8000' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'GOOGLE_REDIRECT_URI=.*', 'GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback' | Set-Content .env"

REM Clear config
php artisan config:clear >nul 2>&1

REM Start server
php artisan serve

pause
