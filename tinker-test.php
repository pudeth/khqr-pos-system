<?php
// Test customer creation
$customer = App\Models\Customer::findOrCreateByPhone('+855123456789', 'John Doe', '123 Main Street');
echo "Customer: {$customer->name} ({$customer->phone})\n";
echo "Initial points: {$customer->available_points}\n";

// Test $150 purchase (should earn 1 point)
$points = App\Models\Customer::calculatePointsFromAmount(150);
echo "Points for \$150: {$points}\n";
$customer->addPoints($points, null, 150);
$customer->refresh();
echo "Points after \$150: {$customer->available_points}\n";
echo "Total spent: \${$customer->total_spent}\n";

// Test $75 purchase (should earn 0 points)
$points2 = App\Models\Customer::calculatePointsFromAmount(75);
echo "Points for \$75: {$points2}\n";
$customer->addPoints($points2, null, 75);
$customer->refresh();
echo "Points after \$75: {$customer->available_points}\n";
echo "Total spent: \${$customer->total_spent}\n";

// Use 1 point
$redeemed = $customer->usePoints(1);
$customer->refresh();
echo "Redeemed: \${$redeemed}\n";
echo "Remaining points: {$customer->available_points}\n";

echo "Test completed successfully!\n";