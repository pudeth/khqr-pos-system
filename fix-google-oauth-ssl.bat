@echo off
echo ========================================
echo    Google OAuth SSL Certificate Fix
echo ========================================
echo.

echo Step 1: Downloading fresh SSL certificate...
php fix-ssl-for-google-oauth.php
echo.

echo Step 2: Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
echo ✅ Caches cleared
echo.

echo Step 3: Testing configuration...
php test-gmail-config.php
echo.

echo ========================================
echo SSL Fix Complete! 
echo ========================================
echo.
echo The SSL certificate error should now be resolved.
echo.
echo To test Google OAuth:
echo 1. Run: php artisan serve
echo 2. Visit: http://localhost:8000/login
echo 3. Click "Continue with Google"
echo.
echo If you still get redirect_uri_mismatch:
echo - Add this to Google Console: http://localhost:8000/auth/google/callback
echo.
pause