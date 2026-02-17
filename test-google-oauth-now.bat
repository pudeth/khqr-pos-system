@echo off
echo ========================================
echo Testing Google OAuth with SSL Fix
echo ========================================
echo.
echo Starting Laravel development server...
echo.
echo After server starts:
echo 1. Open: http://localhost:8000/login
echo 2. Click "Sign in with Google"
echo 3. SSL error should be FIXED!
echo.
echo Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

php artisan serve
