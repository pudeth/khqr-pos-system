Write-Host "Starting Laravel Scheduler for Payment Checking..." -ForegroundColor Green
Write-Host "This will check payments every 10 seconds automatically." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop." -ForegroundColor Yellow
Write-Host ""

php artisan schedule:work