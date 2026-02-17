<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║          DATABASE: khqr_payment - COMPLETE VIEW                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Connection info
echo "📡 Connection: mysql://root@127.0.0.1:3306/khqr_payment\n";
echo "✅ Status: Connected\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "TABLE 1: USERS (Authentication)\n";
echo "═══════════════════════════════════════════════════════════════\n";
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
    echo "  Created: {$user->created_at}\n";
    echo "---\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TABLE 2: CATEGORIES\n";
echo "═══════════════════════════════════════════════════════════════\n";
$categories = DB::table('categories')->get();
foreach ($categories as $cat) {
    $productCount = DB::table('products')->where('category_id', $cat->id)->count();
    echo "ID: {$cat->id} | {$cat->name} ({$productCount} products)\n";
    echo "  Description: {$cat->description}\n";
    echo "  Active: " . ($cat->is_active ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TABLE 3: PRODUCTS (Inventory)\n";
echo "═══════════════════════════════════════════════════════════════\n";
$products = DB::table('products')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->select('products.*', 'categories.name as category_name')
    ->get();

foreach ($products as $product) {
    $lowStock = $product->stock <= $product->min_stock ? ' ⚠️ LOW STOCK' : '';
    echo "ID: {$product->id} | {$product->name} (SKU: {$product->sku}){$lowStock}\n";
    echo "  Category: {$product->category_name}\n";
    echo "  Price: \${$product->price} | Cost: \${$product->cost}\n";
    echo "  Stock: {$product->stock} (Min: {$product->min_stock})\n";
    echo "  Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TABLE 4: SALES (Transactions)\n";
echo "═══════════════════════════════════════════════════════════════\n";
$sales = DB::table('sales')
    ->leftJoin('users', 'sales.user_id', '=', 'users.id')
    ->select('sales.*', 'users.name as cashier_name')
    ->orderBy('sales.created_at', 'desc')
    ->get();

if ($sales->count() > 0) {
    foreach ($sales as $sale) {
        echo "Invoice: {$sale->invoice_number}\n";
        echo "  Date: {$sale->created_at}\n";
        echo "  Cashier: {$sale->cashier_name}\n";
        echo "  Subtotal: \${$sale->subtotal}\n";
        echo "  Tax: \${$sale->tax} | Discount: \${$sale->discount}\n";
        echo "  Total: \${$sale->total}\n";
        echo "  Paid: \${$sale->paid_amount} | Change: \${$sale->change_amount}\n";
        echo "  Payment: {$sale->payment_method}\n";
        
        // Get items
        $items = DB::table('sale_items')->where('sale_id', $sale->id)->get();
        echo "  Items:\n";
        foreach ($items as $item) {
            echo "    - {$item->product_name} x{$item->quantity} @ \${$item->price} = \${$item->subtotal}\n";
        }
        echo "---\n";
    }
} else {
    echo "No sales yet. Make your first sale in the POS system!\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TABLE 5: SALE_ITEMS (Transaction Details)\n";
echo "═══════════════════════════════════════════════════════════════\n";
$saleItems = DB::table('sale_items')->get();
if ($saleItems->count() > 0) {
    echo "Total items sold: {$saleItems->count()}\n";
    $totalRevenue = DB::table('sale_items')->sum('subtotal');
    echo "Total revenue from items: \${$totalRevenue}\n";
} else {
    echo "No items sold yet.\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TABLE 6: PAYMENTS (KHQR Transactions)\n";
echo "═══════════════════════════════════════════════════════════════\n";
$payments = DB::table('payments')->get();
if ($payments->count() > 0) {
    foreach ($payments as $payment) {
        echo "ID: {$payment->id} | Amount: \${$payment->amount} {$payment->currency}\n";
        echo "  Status: {$payment->status}\n";
        echo "  Created: {$payment->created_at}\n";
        echo "---\n";
    }
} else {
    echo "No KHQR payments yet.\n";
}

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      SUMMARY STATISTICS                        ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$stats = [
    'Total Users' => DB::table('users')->count(),
    'Total Categories' => DB::table('categories')->count(),
    'Total Products' => DB::table('products')->count(),
    'Active Products' => DB::table('products')->where('is_active', 1)->count(),
    'Low Stock Products' => DB::table('products')->whereColumn('stock', '<=', 'min_stock')->count(),
    'Total Sales' => DB::table('sales')->count(),
    'Total Revenue' => '$' . number_format(DB::table('sales')->sum('total'), 2),
    'Today\'s Sales' => DB::table('sales')->whereDate('created_at', date('Y-m-d'))->count(),
    'Today\'s Revenue' => '$' . number_format(DB::table('sales')->whereDate('created_at', date('Y-m-d'))->sum('total'), 2),
];

foreach ($stats as $label => $value) {
    echo sprintf("%-25s: %s\n", $label, $value);
}

echo "\n✅ All data retrieved successfully!\n";
echo "🌐 Access POS: http://localhost:8000/pos\n";
echo "🔐 Login: admin@pos.com / password\n\n";
