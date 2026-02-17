@echo off
echo ========================================
echo POS System Setup
echo ========================================
echo.

echo Step 1: Running migrations...
php artisan migrate:fresh
echo.

echo Step 2: Seeding database with sample data...
php seed-data.php
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo You can now access:
echo - POS System: http://localhost:8000/pos
echo - Admin Dashboard: http://localhost:8000/admin/dashboard
echo - Login Page: http://localhost:8000/login
echo.
echo To start the server, run: php artisan serve
echo ========================================
pause
