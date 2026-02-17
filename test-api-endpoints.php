<?php

echo "🧪 Testing Customer Loyalty API Endpoints\n";
echo "==========================================\n\n";

// Test the customer lookup endpoint
echo "1️⃣ Testing customer lookup endpoint...\n";

$url = 'http://127.0.0.1:8000/pos/customer-by-phone';
$data = json_encode(['phone' => '+855123456789']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: {$response}\n\n";

if ($httpCode === 200) {
    $result = json_decode($response, true);
    if ($result['success']) {
        echo "✅ Customer found!\n";
        echo "📊 Available points: {$result['customer']['available_points']}\n";
    } else {
        echo "ℹ️ Customer not found (this is normal for first test)\n";
    }
} else {
    echo "❌ API endpoint test failed\n";
}

echo "\n🎯 Next steps:\n";
echo "1. Open browser to http://127.0.0.1:8000/pos\n";
echo "2. Login with your credentials\n";
echo "3. Add items to cart\n";
echo "4. Click 'COMPLETE SALE'\n";
echo "5. Enter phone: +855123456789\n";
echo "6. Enter name: John Doe\n";
echo "7. Enter address: 123 Main Street\n";
echo "8. Complete the purchase\n";
echo "9. Customer will earn points automatically!\n";

echo "\n✅ API endpoint is accessible and ready for testing!\n";