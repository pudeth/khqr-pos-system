# POS System - Setup Guide

## Overview
A complete Point of Sale (POS) system with admin dashboard built with Laravel, connected to MySQL/PHPMyAdmin.

## Features
- ✅ Full POS interface for cashiers
- ✅ Admin dashboard with analytics
- ✅ Product management
- ✅ Category management
- ✅ Sales tracking and reporting
- ✅ Inventory management with low stock alerts
- ✅ Multiple payment methods (Cash, KHQR, Card)
- ✅ User authentication and roles
- ✅ Real-time cart management

## Database Tables Created
1. **users** - Admin and cashier accounts
2. **categories** - Product categories
3. **products** - Product inventory
4. **sales** - Sales transactions
5. **sale_items** - Individual items in each sale
6. **payments** - KHQR payment records (existing)

## Installation Steps

### 1. Database Configuration
Your database is already configured in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khqr_payment
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Run Setup
Execute the setup script:
```bash
setup-pos.bat
```

Or manually run:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### 3. Start the Server
```bash
php artisan serve
```

## Default Login Credentials

### Admin Account
- **Email:** admin@pos.com
- **Password:** password
- **Role:** Admin (full access)

### Cashier Account
- **Email:** cashier@pos.com
- **Password:** password
- **Role:** Cashier (POS access)

## Access URLs

- **Login:** http://localhost:8000/login
- **POS System:** http://localhost:8000/pos
- **Admin Dashboard:** http://localhost:8000/admin/dashboard
- **Products Management:** http://localhost:8000/admin/products
- **Categories Management:** http://localhost:8000/admin/categories
- **Sales History:** http://localhost:8000/admin/sales

## Sample Data Included

### Categories (5)
- Electronics
- Clothing
- Food & Beverages
- Books
- Home & Garden

### Products (15)
Sample products across all categories with:
- SKU codes
- Pricing
- Stock levels
- Cost tracking

## How to Use

### For Cashiers (POS)
1. Login with cashier credentials
2. Browse or search for products
3. Click products to add to cart
4. Adjust quantities as needed
5. Click "Complete Sale"
6. Select payment method
7. Enter amount paid
8. Complete transaction

### For Admins
1. Login with admin credentials
2. View dashboard analytics
3. Manage products and categories
4. View sales reports
5. Monitor inventory levels
6. Track low stock items

## Key Features Explained

### Product Management
- Add/Edit/Delete products
- Set pricing and cost
- Track inventory levels
- Set minimum stock alerts
- Organize by categories

### Sales Management
- View all transactions
- Filter by date/cashier
- View detailed receipts
- Track payment methods
- Export reports

### Inventory Tracking
- Real-time stock updates
- Low stock alerts
- Automatic stock deduction on sale
- Stock level monitoring

### Payment Methods
- **Cash:** Traditional cash payment
- **KHQR:** Integration with existing KHQR system
- **Card:** Credit/debit card payments

## Database Structure

### Products Table
- id, category_id, name, sku, description
- price, cost, stock, min_stock
- image, is_active, timestamps

### Sales Table
- id, invoice_number, user_id
- subtotal, tax, discount, total
- paid_amount, change_amount
- payment_method, payment_id
- customer_name, customer_phone
- notes, timestamps

### Sale Items Table
- id, sale_id, product_id
- product_name, price, quantity
- subtotal, timestamps

## Customization

### Adding New Categories
1. Go to Admin → Categories
2. Click "Add Category"
3. Fill in details
4. Save

### Adding New Products
1. Go to Admin → Products
2. Click "Add Product"
3. Fill in all required fields
4. Set initial stock level
5. Save

### Modifying Tax Rate
Edit `app/Http/Controllers/POSController.php`:
```php
$tax = $subtotal * 0.10; // 10% tax
```

### Changing Currency
Update views to display your preferred currency symbol.

## Troubleshooting

### Migration Errors
```bash
php artisan migrate:fresh --force
```

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
- Check PHPMyAdmin is running
- Verify database credentials in `.env`
- Ensure MySQL service is started

## Security Notes

⚠️ **Important:** Change default passwords in production!

```php
// Create new admin user
php artisan tinker
User::create([
    'name' => 'Your Name',
    'email' => 'your@email.com',
    'password' => Hash::make('your-secure-password'),
    'role' => 'admin',
    'is_active' => true
]);
```

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection
3. Clear cache: `php artisan cache:clear`
4. Review error messages in browser console

## Next Steps

1. ✅ Run setup script
2. ✅ Login and explore
3. ✅ Add your own products
4. ✅ Customize categories
5. ✅ Test POS workflow
6. ✅ Review sales reports

Enjoy your new POS System! 🎉
