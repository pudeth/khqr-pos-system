<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "DATABASE CONNECTION STATUS\n";
echo "===========================================\n\n";

try {
    // Test connection
    DB::connection()->getPdo();
    echo "✅ Connected to database: " . DB::connection()->getDatabaseName() . "\n\n";
    
    // Show all tables
    echo "📊 TABLES IN DATABASE:\n";
    echo "-------------------------------------------\n";
    $tables = DB::select('SHOW TABLES');
    $count = 0;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $count++;
        
        // Get row count
        $rowCount = DB::table($tableName)->count();
        echo sprintf("%d. %-20s (%d rows)\n", $count, $tableName, $rowCount);
    }
    
    echo "\n===========================================\n";
    echo "SAMPLE DATA:\n";
    echo "===========================================\n\n";
    
    // Show users
    echo "👥 USERS:\n";
    $users = DB::table('users')->get(['id', 'name', 'email', 'role']);
    foreach ($users as $user) {
        echo "  - {$user->name} ({$user->email}) - Role: {$user->role}\n";
    }
    
    echo "\n📦 CATEGORIES:\n";
    $categories = DB::table('categories')->get(['id', 'name']);
    foreach ($categories as $category) {
        echo "  - {$category->name}\n";
    }
    
    echo "\n🛍️ PRODUCTS (first 5):\n";
    $products = DB::table('products')->limit(5)->get(['name', 'sku', 'price', 'stock']);
    foreach ($products as $product) {
        echo "  - {$product->name} (SKU: {$product->sku}) - \${$product->price} - Stock: {$product->stock}\n";
    }
    
    echo "\n✅ Database is fully connected and operational!\n";
    
} catch (\Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
