<?php

// Bootstrap Laravel
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;

echo "🧪 Testing Customer Loyalty Points System\n";
echo "=========================================\n\n";

try {
    // Test 1: Create a new customer
    echo "1️⃣ Creating test customer...\n";
    $customer = Customer::findOrCreateByPhone(
        '+855123456789',
        'John Doe',
        '123 Main Street'
    );
    echo "✅ Customer created: {$customer->name} ({$customer->phone})\n";
    echo "📊 Initial points: {$customer->available_points}\n";
    echo "💰 Total spent: \${$customer->total_spent}\n\n";

    // Test 2: Test points calculation for $150 purchase
    echo "2️⃣ Testing \$150 purchase (should earn 1 point)...\n";
    $pointsEarned = Customer::calculatePointsFromAmount(150);
    echo "🎯 Points to be earned: {$pointsEarned}\n";
    
    $customer->addPoints($pointsEarned, null, 150);
    $customer->refresh();
    
    echo "✅ Customer points after purchase: {$customer->available_points}\n";
    echo "💰 Total spent: \${$customer->total_spent}\n\n";

    // Test 3: Test $75 purchase (should earn 0 points)
    echo "3️⃣ Testing \$75 purchase (should earn 0 points)...\n";
    $pointsEarned2 = Customer::calculatePointsFromAmount(75);
    echo "🎯 Points to be earned: {$pointsEarned2}\n";
    
    $customer->addPoints($pointsEarned2, null, 75);
    $customer->refresh();
    
    echo "✅ Customer points after second purchase: {$customer->available_points}\n";
    echo "💰 Total spent: \${$customer->total_spent}\n\n";

    // Test 4: Test another $50 purchase to reach $275 total (should earn 2 points total)
    echo "4️⃣ Testing \$50 purchase (total \$275, should earn 2 points total)...\n";
    $pointsEarned3 = Customer::calculatePointsFromAmount(50);
    echo "🎯 Points to be earned: {$pointsEarned3}\n";
    
    $customer->addPoints($pointsEarned3, null, 50);
    $customer->refresh();
    
    echo "✅ Customer points after third purchase: {$customer->available_points}\n";
    echo "💰 Total spent: \${$customer->total_spent}\n\n";

    // Test 5: Use 1 point for payment
    echo "5️⃣ Using 1 point for payment (1 point = \$1)...\n";
    $amountRedeemed = $customer->usePoints(1);
    $customer->refresh();
    
    echo "✅ Amount redeemed: \${$amountRedeemed}\n";
    echo "📊 Remaining points: {$customer->available_points}\n\n";

    // Test 6: Show points history
    echo "6️⃣ Points transaction history:\n";
    $history = $customer->pointsHistory()->orderBy('created_at', 'desc')->get();
    foreach ($history as $record) {
        $icon = $record->type === 'earned' ? '💰' : ($record->type === 'redeemed' ? '🎁' : '🔄');
        echo "{$icon} {$record->type}: {$record->points} points";
        if ($record->amount_spent) {
            echo " (spent: \${$record->amount_spent})";
        }
        if ($record->amount_redeemed) {
            echo " (redeemed: \${$record->amount_redeemed})";
        }
        echo " - {$record->description}\n";
    }

    echo "\n🎉 Customer loyalty system test completed successfully!\n";
    echo "📋 Final Summary:\n";
    echo "   Customer: {$customer->name}\n";
    echo "   Phone: {$customer->phone}\n";
    echo "   Address: {$customer->address}\n";
    echo "   Total Spent: \${$customer->total_spent}\n";
    echo "   Total Points Earned: {$customer->total_points}\n";
    echo "   Available Points: {$customer->available_points}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}