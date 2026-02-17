# 🚀 POS System - Quick Start Guide

## ✅ Setup Complete!

Your POS System has been successfully installed with all database tables created and sample data loaded.

## 📊 Database Tables in PHPMyAdmin

Open PHPMyAdmin (http://localhost/phpmyadmin) and check your `khqr_payment` database. You'll find these tables:

1. **users** - 2 users (Admin & Cashier)
2. **categories** - 5 categories
3. **products** - 15 sample products
4. **sales** - Sales transactions (empty, ready for use)
5. **sale_items** - Sale line items (empty, ready for use)
6. **payments** - KHQR payments (your existing table)

## 🔐 Login Credentials

### Admin Account (Full Access)
- **Email:** admin@pos.com
- **Password:** password

### Cashier Account (POS Only)
- **Email:** cashier@pos.com
- **Password:** password

## 🌐 Access URLs

Start the server first:
```bash
php artisan serve
```

Then access:
- **Login:** http://localhost:8000/login
- **POS System:** http://localhost:8000/pos
- **Admin Dashboard:** http://localhost:8000/admin/dashboard
- **Products:** http://localhost:8000/admin/products
- **Categories:** http://localhost:8000/admin/categories
- **Sales History:** http://localhost:8000/admin/sales

## 📦 Sample Data Loaded

### Categories (5)
1. Electronics
2. Clothing
3. Food & Beverages
4. Books
5. Home & Garden

### Products (15)
- Wireless Mouse ($25.99)
- USB Cable ($9.99)
- Bluetooth Speaker ($49.99)
- Phone Charger ($19.99)
- T-Shirt ($15.99)
- Jeans ($39.99)
- Cap ($12.99)
- Coffee ($8.99)
- Energy Drink ($2.99)
- Snack Bar ($1.99)
- Novel ($14.99)
- Magazine ($5.99)
- Plant Pot ($12.99)
- Candle ($7.99)
- Picture Frame ($18.99)

## 🎯 Quick Test Workflow

### 1. Login as Admin
1. Go to http://localhost:8000/login
2. Login with admin@pos.com / password
3. You'll see the dashboard with stats

### 2. Explore Admin Features
- View dashboard analytics
- Manage products (add/edit/delete)
- Manage categories
- View sales reports

### 3. Test POS System
1. Click "POS" in the sidebar
2. Browse products by category
3. Click products to add to cart
4. Adjust quantities
5. Click "Complete Sale"
6. Select payment method (Cash/KHQR/Card)
7. Enter amount paid
8. Complete transaction

### 4. View Sales
1. Go back to Admin Dashboard
2. Click "Sales" in sidebar
3. View all completed transactions
4. Click "View" to see sale details

## 🛠️ Common Tasks

### Add New Product
1. Admin → Products → Add Product
2. Fill in: Name, SKU, Category, Price, Stock
3. Save

### Add New Category
1. Admin → Categories → Add Category
2. Fill in: Name, Description
3. Save

### Process a Sale
1. POS → Select products
2. Complete Sale → Choose payment
3. Enter amount → Complete

### View Reports
1. Admin → Dashboard (overview)
2. Admin → Sales (detailed history)

## 📱 Features Overview

### POS Interface
- ✅ Product search and filtering
- ✅ Category-based browsing
- ✅ Real-time cart management
- ✅ Multiple payment methods
- ✅ Automatic change calculation
- ✅ Stock validation

### Admin Dashboard
- ✅ Today's sales summary
- ✅ Total sales tracking
- ✅ Product count
- ✅ Low stock alerts
- ✅ Recent sales list

### Product Management
- ✅ Add/Edit/Delete products
- ✅ SKU tracking
- ✅ Price and cost management
- ✅ Stock level monitoring
- ✅ Category organization
- ✅ Active/Inactive status

### Sales Management
- ✅ Complete transaction history
- ✅ Invoice generation
- ✅ Customer information
- ✅ Payment method tracking
- ✅ Detailed sale items

## 🔄 Reset Database

If you want to start fresh:
```bash
php artisan migrate:fresh
php seed-data.php
```

## 📝 Next Steps

1. ✅ Customize products for your business
2. ✅ Add your own categories
3. ✅ Update pricing
4. ✅ Set appropriate stock levels
5. ✅ Train staff on POS usage
6. ✅ Start making sales!

## 💡 Tips

- **Low Stock Alerts:** Products show in red when stock ≤ minimum stock level
- **Invoice Numbers:** Auto-generated as INV-YYYYMMDD-0001
- **Stock Updates:** Automatically deducted on each sale
- **Payment Methods:** Choose based on your customer's preference
- **Search:** Use product name or SKU to quickly find items

## 🎉 You're Ready!

Your POS system is fully operational and connected to your PHPMyAdmin database. Start by logging in and exploring the features!

**Need help?** Check POS-README.md for detailed documentation.
