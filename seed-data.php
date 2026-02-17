<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Insert Users
DB::table('users')->insert([
    [
        'name' => 'Admin',
        'email' => 'admin@pos.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Cashier',
        'email' => 'cashier@pos.com',
        'password' => Hash::make('password'),
        'role' => 'cashier',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

echo "✓ Users created\n";

// Insert Categories
DB::table('categories')->insert([
    ['name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Clothing', 'description' => 'Apparel and fashion items', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Food & Beverages', 'description' => 'Food and drink products', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Books', 'description' => 'Books and publications', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Home & Garden', 'description' => 'Home and garden supplies', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
]);

echo "✓ Categories created\n";

// Insert Products
DB::table('products')->insert([
    ['category_id' => 1, 'name' => 'Wireless Mouse', 'sku' => 'ELEC-001', 'price' => 25.99, 'cost' => 15.00, 'stock' => 50, 'min_stock' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 1, 'name' => 'USB Cable', 'sku' => 'ELEC-002', 'price' => 9.99, 'cost' => 5.00, 'stock' => 100, 'min_stock' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 1, 'name' => 'Bluetooth Speaker', 'sku' => 'ELEC-003', 'price' => 49.99, 'cost' => 30.00, 'stock' => 30, 'min_stock' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 1, 'name' => 'Phone Charger', 'sku' => 'ELEC-004', 'price' => 19.99, 'cost' => 10.00, 'stock' => 75, 'min_stock' => 15, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 2, 'name' => 'T-Shirt', 'sku' => 'CLO-001', 'price' => 15.99, 'cost' => 8.00, 'stock' => 60, 'min_stock' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 2, 'name' => 'Jeans', 'sku' => 'CLO-002', 'price' => 39.99, 'cost' => 20.00, 'stock' => 40, 'min_stock' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 2, 'name' => 'Cap', 'sku' => 'CLO-003', 'price' => 12.99, 'cost' => 6.00, 'stock' => 45, 'min_stock' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 3, 'name' => 'Coffee', 'sku' => 'FOOD-001', 'price' => 8.99, 'cost' => 4.00, 'stock' => 80, 'min_stock' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 3, 'name' => 'Energy Drink', 'sku' => 'FOOD-002', 'price' => 2.99, 'cost' => 1.50, 'stock' => 120, 'min_stock' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 3, 'name' => 'Snack Bar', 'sku' => 'FOOD-003', 'price' => 1.99, 'cost' => 0.80, 'stock' => 150, 'min_stock' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 4, 'name' => 'Novel', 'sku' => 'BOOK-001', 'price' => 14.99, 'cost' => 8.00, 'stock' => 35, 'min_stock' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 4, 'name' => 'Magazine', 'sku' => 'BOOK-002', 'price' => 5.99, 'cost' => 3.00, 'stock' => 50, 'min_stock' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 5, 'name' => 'Plant Pot', 'sku' => 'HOME-001', 'price' => 12.99, 'cost' => 6.00, 'stock' => 40, 'min_stock' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 5, 'name' => 'Candle', 'sku' => 'HOME-002', 'price' => 7.99, 'cost' => 3.50, 'stock' => 60, 'min_stock' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['category_id' => 5, 'name' => 'Picture Frame', 'sku' => 'HOME-003', 'price' => 18.99, 'cost' => 10.00, 'stock' => 25, 'min_stock' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
]);

echo "✓ Products created\n";
echo "\n✅ Database seeded successfully!\n";
echo "\nLogin credentials:\n";
echo "Email: admin@pos.com\n";
echo "Password: password\n";
