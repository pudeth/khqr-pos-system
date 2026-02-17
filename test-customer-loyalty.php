<?php

require_once 'vendor/autoload.php';

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;

// Test the customer loyalty system
echo "Testing Customer Loyalty Points System\n";
echo "=====================================\n\n";

try {
    // Test 1: Create a customer
    echo "1. Creating test customer...\n";
    $customer = Customer::findOrCreateByPhone(
        '+855123456789',
        'John Doe',
        '123 Main Street'
    );
    echo "Customer created: {$customer->name} ({$customer->phone})\n";
    echo "Initial points: {$customer->available_points}\n\n";

    // Test 2: Simulate a $150 purchase (should earn 1 point)
    echo "2. Simulating \$150 purchase...\n";
    $pointsEarned = Customer::calculatePointsFromAmount(150);
    echo "Points to be earned: {$pointsEarned}\n";
    
    $customer->addPoints($pointsEarned, null, 150);
    $customer->refresh();
    
    echo "Customer points after purchase: {$customer->available_points}\n";
    echo "Total spent: \${$customer->total_spent}\n\n";

    // Test 3: Simulate another $75 purchase (should earn 0 points)
    echo "3. Simulating \$75 purchase...\n";
    $pointsEarned2 = Customer::calculatePointsFromAmount(75);
    echo "Points to be earned: {$pointsEarned2}\n";
    
    $customer->addPoints($pointsEarned2, null, 75);
    $customer->refresh();
    
    echo "Customer points after second purchase: {$customer->available_points}\n";
    echo "Total spent: \${$customer->total_spent}\n\n";

    // Test 4: Use 1 point for payment
    echo "4. Using 1 point for payment...\n";
    $amountRedeemed = $customer->usePoints(1);
    $customer->refresh();
    
    echo "Amount redeemed: \${$amountRedeemed}\n";
    echo "Remaining points: {$customer->available_points}\n\n";

    // Test 5: Show points history
    echo "5. Points history:\n";
    $history = $customer->pointsHistory()->orderBy('created_at', 'desc')->get();
    foreach ($history as $record) {
        echo "- {$record->type}: {$record->points} points";
        if ($record->amount_spent) {
            echo " (spent: \${$record->amount_spent})";
        }
        if ($record->amount_redeemed) {
            echo " (redeemed: \${$record->amount_redeemed})";
        }
        echo " - {$record->description}\n";
    }

    echo "\n✅ Customer loyalty system test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}