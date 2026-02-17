<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\TelegramService;

echo "Testing Telegram Receipt Format...\n\n";

$telegramService = new TelegramService();

// Test receipt data
$receiptData = [
    'store_name' => 'PuDeth Smart-PAY',
    'invoice_number' => 'INV-20260210-0001',
    'customer_name' => 'John Doe',
    'customer_phone' => '+855123456789',
    'items' => [
        [
            'name' => 'Coca Cola',
            'quantity' => 2,
            'price' => 1.50,
            'subtotal' => 3.00,
        ],
        [
            'name' => 'Sandwich',
            'quantity' => 1,
            'price' => 5.00,
            'subtotal' => 5.00,
        ],
        [
            'name' => 'Coffee',
            'quantity' => 3,
            'price' => 2.50,
            'subtotal' => 7.50,
        ],
    ],
    'subtotal' => 15.50,
    'tax' => 1.55,
    'discount' => 0.00,
    'total' => 17.05,
    'bank' => 'ACLEDA Bank Plc.',
    'transaction_id' => 'a39eb77b',
    'reference_number' => '100FT36774348398',
    'from_account' => 'Pu Deth (004 164 074)',
    'cashier' => 'Admin User',
];

echo "Sending test receipt to Telegram...\n";
$result = $telegramService->sendReceipt($receiptData);

if ($result) {
    echo "✅ Receipt sent successfully!\n";
} else {
    echo "❌ Failed to send receipt.\n";
}
