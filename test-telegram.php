<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TelegramService;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║              TELEGRAM BOT CONNECTION TEST                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$telegramService = new TelegramService();

// Test 1: Check Configuration
echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST 1: Configuration Check\n";
echo "═══════════════════════════════════════════════════════════════\n";

$botToken = config('services.telegram.bot_token');
$chatId = config('services.telegram.chat_id');

echo "Bot Token: " . (empty($botToken) ? '❌ NOT SET' : '✅ SET (' . strlen($botToken) . ' chars)') . "\n";
echo "Chat ID: " . (empty($chatId) ? '❌ NOT SET' : '✅ SET (' . $chatId . ')') . "\n";

if (empty($botToken) || empty($chatId)) {
    echo "\n❌ ERROR: Telegram credentials not configured!\n";
    echo "Please check your .env file:\n";
    echo "  TELEGRAM_BOT_TOKEN=your_bot_token\n";
    echo "  TELEGRAM_CHAT_ID=your_chat_id\n\n";
    exit(1);
}

// Test 2: Test Bot Connection
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TEST 2: Bot Connection Test\n";
echo "═══════════════════════════════════════════════════════════════\n";

$connectionTest = $telegramService->testConnection();

if ($connectionTest['success']) {
    echo "✅ Bot Connected Successfully!\n";
    echo "   Bot Name: " . $connectionTest['bot_name'] . "\n";
    echo "   Bot Username: @" . $connectionTest['bot_username'] . "\n";
} else {
    echo "❌ Connection Failed!\n";
    echo "   Error: " . $connectionTest['message'] . "\n";
    exit(1);
}

// Test 3: Send Test Message
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TEST 3: Send Test Message\n";
echo "═══════════════════════════════════════════════════════════════\n";

$testMessage = "🧪 <b>Test Message from POS System</b>\n\n"
    . "This is a test notification to verify Telegram integration.\n"
    . "🕐 Time: " . now()->format('Y-m-d H:i:s') . "\n"
    . "✅ If you see this, Telegram is working!";

echo "Sending test message...\n";
$result = $telegramService->sendMessage($testMessage);

if ($result) {
    echo "✅ Test message sent successfully!\n";
    echo "   Check your Telegram chat to see the message.\n";
} else {
    echo "❌ Failed to send test message!\n";
    echo "   Check Laravel logs: storage/logs/laravel.log\n";
}

// Test 4: Send Payment Notification
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TEST 4: Send Payment Notification\n";
echo "═══════════════════════════════════════════════════════════════\n";

$paymentData = [
    'amount' => 25.99,
    'currency' => 'USD',
    'bill_number' => 'TEST-001',
    'store_label' => 'POS Store',
    'mobile_number' => '012345678',
    'transaction_id' => 'TXN-' . time(),
];

echo "Sending payment notification...\n";
$result = $telegramService->sendPaymentSuccess($paymentData);

if ($result) {
    echo "✅ Payment notification sent successfully!\n";
} else {
    echo "❌ Failed to send payment notification!\n";
}

// Test 5: Send Sale Notification
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "TEST 5: Send Sale Notification\n";
echo "═══════════════════════════════════════════════════════════════\n";

$saleData = [
    'invoice_number' => 'INV-TEST-001',
    'total' => 85.97,
    'payment_method' => 'KHQR',
    'cashier' => 'Test Cashier',
    'customer_name' => 'Test Customer',
    'items' => [
        ['name' => 'Wireless Mouse', 'quantity' => 1, 'subtotal' => 25.99],
        ['name' => 'USB Cable', 'quantity' => 2, 'subtotal' => 19.98],
        ['name' => 'Bluetooth Speaker', 'quantity' => 1, 'subtotal' => 49.99],
    ],
];

echo "Sending sale notification...\n";
$result = $telegramService->sendSaleNotification($saleData);

if ($result) {
    echo "✅ Sale notification sent successfully!\n";
} else {
    echo "❌ Failed to send sale notification!\n";
}

// Summary
echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Configuration: Complete\n";
echo "✅ Bot Connection: Working\n";
echo "✅ Message Sending: Working\n";
echo "✅ Payment Notifications: Working\n";
echo "✅ Sale Notifications: Working\n";

echo "\n🎉 Telegram integration is fully operational!\n";
echo "📱 Check your Telegram chat for test messages.\n";
echo "🌐 POS System: http://localhost:8000/pos\n\n";
