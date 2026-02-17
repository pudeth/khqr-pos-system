@echo off
echo Setting up Bilingual Products for POS System...
echo.

echo Step 1: Refreshing database with new bilingual products...
php artisan migrate:fresh --seed

echo.
echo Step 2: Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo ✅ Bilingual Products Setup Complete!
echo.
echo 📦 Products Added:
echo - Electronics / គ្រឿងអេឡិចត្រូនិច (5 items)
echo - Food & Beverages / អាហារ និងភេសជ្ជៈ (5 items)  
echo - Clothing / សម្លៀកបំពាក់ (4 items)
echo - Health & Beauty / សុខភាព និងសម្រស់ (4 items)
echo - Home & Living / ផ្ទះ និងការរស់នៅ (4 items)
echo - Stationery / សម្ភារៈការិយាល័យ (4 items)
echo - Snacks / ចំណីអាហារ (5 items)
echo.
echo 🌟 Total: 31 bilingual products with Khmer names!
echo.
echo You can now:
echo 1. Access POS at: http://localhost:8000/pos
echo 2. Login with: admin@pos.com / password
echo 3. Test smart search with Khmer product names
echo.
pause