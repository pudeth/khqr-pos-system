@echo off
echo Starting Laravel Scheduler for Payment Checking...
echo This will check payments every 10 seconds automatically.
echo Press Ctrl+C to stop.
echo.

php artisan schedule:work