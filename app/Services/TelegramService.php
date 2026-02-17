<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $chatId;
    protected string $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * Check if Telegram is configured
     */
    protected function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatId);
    }

    /**
     * Send message to Telegram
     */
    public function sendMessage(string $message, ?string $chatId = null): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('Telegram not configured, skipping message send');
            return false;
        }

        $targetChatId = $chatId ?? $this->chatId;

        try {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => 30,
            ])->post($this->apiUrl . $this->botToken . '/sendMessage', [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info('Telegram message sent successfully', [
                    'chat_id' => $targetChatId,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Telegram API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'chat_id' => $targetChatId
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram send failed: ' . $e->getMessage(), [
                'chat_id' => $targetChatId,
                'message' => $message
            ]);
            return false;
        }
    }

    /**
     * Send payment success notification
     */
    public function sendPaymentSuccess(array $paymentData): bool
    {
        $message = $this->formatPaymentMessage($paymentData);
        return $this->sendMessage($message);
    }

    /**
     * Send complete receipt with sale details
     */
    public function sendReceipt(array $receiptData): bool
    {
        $message = $this->formatReceiptMessage($receiptData);
        return $this->sendMessage($message);
    }

    /**
     * Send POS sale notification
     */
    public function sendSaleNotification(array $saleData): bool
    {
        $message = $this->formatSaleMessage($saleData);
        return $this->sendMessage($message);
    }

    /**
     * Format payment notification message
     */
    protected function formatPaymentMessage(array $data): string
    {
        return "✅ <b>Payment Successful!</b>\n\n"
            . "💰 Amount: <b>{$data['amount']} {$data['currency']}</b>\n"
            . "📋 Bill Number: " . ($data['bill_number'] ?? 'N/A') . "\n"
            . "🏪 Store: " . ($data['store_label'] ?? 'N/A') . "\n"
            . "📱 Phone: " . ($data['mobile_number'] ?? 'N/A') . "\n"
            . "🕐 Time: " . now()->format('Y-m-d H:i:s') . "\n"
            . "🔑 Transaction ID: " . ($data['transaction_id'] ?? 'N/A');
    }

    /**
     * Format POS sale notification message
     */
    protected function formatSaleMessage(array $data): string
    {
        $message = "🛒 <b>New Sale Completed!</b>\n\n";
        $message .= "📋 Invoice: <b>{$data['invoice_number']}</b>\n";
        $message .= "💰 Total: <b>\${$data['total']}</b>\n";
        $message .= "💳 Payment: <b>{$data['payment_method']}</b>\n";
        
        if (isset($data['cashier'])) {
            $message .= "👤 Cashier: {$data['cashier']}\n";
        }
        
        if (isset($data['customer_name'])) {
            $message .= "👥 Customer: {$data['customer_name']}\n";
        }
        
        $message .= "🕐 Time: " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        // Add items
        if (isset($data['items']) && count($data['items']) > 0) {
            $message .= "📦 <b>Items:</b>\n";
            foreach ($data['items'] as $item) {
                $message .= "  • {$item['name']} x{$item['quantity']} = \${$item['subtotal']}\n";
            }
        }
        
        return $message;
    }

    /**
     * Format complete receipt message
     */
    protected function formatReceiptMessage(array $data): string
    {
        $message = "🧾 <b>PAYMENT RECEIPT</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        
        // Store info
        $message .= "🏪 <b>" . ($data['store_name'] ?? 'POS Store') . "</b>\n";
        $message .= "📋 Invoice: <b>{$data['invoice_number']}</b>\n";
        $message .= "🕐 " . now()->format('M d, Y h:i A') . "\n\n";
        
        // Customer info
        if (isset($data['customer_name']) && $data['customer_name']) {
            $message .= "👥 Customer: {$data['customer_name']}\n";
        }
        if (isset($data['customer_phone']) && $data['customer_phone']) {
            $message .= "📱 Phone: {$data['customer_phone']}\n";
        }
        if (isset($data['customer_name']) || isset($data['customer_phone'])) {
            $message .= "\n";
        }
        
        // Items
        $message .= "📦 <b>ITEMS:</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        
        if (isset($data['items']) && count($data['items']) > 0) {
            foreach ($data['items'] as $item) {
                $itemName = $item['name'];
                $qty = $item['quantity'];
                $price = number_format($item['price'], 2);
                $subtotal = number_format($item['subtotal'], 2);
                
                $message .= "{$itemName}\n";
                $message .= "  {$qty} x \${$price} = <b>\${$subtotal}</b>\n";
            }
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━\n";
        
        // Totals
        $message .= "Subtotal: \$" . number_format($data['subtotal'], 2) . "\n";
        
        if (isset($data['tax']) && $data['tax'] > 0) {
            $message .= "Tax: \$" . number_format($data['tax'], 2) . "\n";
        }
        
        if (isset($data['discount']) && $data['discount'] > 0) {
            $message .= "Discount: -\$" . number_format($data['discount'], 2) . "\n";
        }
        
        $message .= "\n💰 <b>TOTAL: \$" . number_format($data['total'], 2) . "</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        
        // Payment info
        $message .= "💳 Payment Method: <b>KHQR (Bakong)</b>\n";
        $message .= "✅ Status: <b>PAID</b>\n";
        $message .= "🏦 Bank: <b>" . ($data['bank'] ?? 'ACLEDA Bank Plc.') . "</b>\n";
        
        if (isset($data['transaction_id']) && $data['transaction_id']) {
            $message .= "🔑 Bakong Hash: <code>{$data['transaction_id']}</code>\n";
        }
        
        if (isset($data['reference_number']) && $data['reference_number']) {
            $message .= "📄 Reference: <code>{$data['reference_number']}</code>\n";
        }
        
        if (isset($data['from_account']) && $data['from_account']) {
            $message .= "💼 From: {$data['from_account']}\n";
        }
        
        if (isset($data['cashier'])) {
            $message .= "\n👤 Served by: {$data['cashier']}\n";
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "Thank you for your purchase! 🙏\n";
        $message .= "Pay From Bank: " . ($data['bank'] ?? 'ACLEDA Bank Plc.') . " 🏦";
        
        return $message;
    }

    /**
     * Test Telegram connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Telegram bot token or chat ID not configured'
            ];
        }

        try {
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->get($this->apiUrl . $this->botToken . '/getMe');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'bot_name' => $data['result']['first_name'] ?? 'Unknown',
                    'bot_username' => $data['result']['username'] ?? 'Unknown',
                    'message' => 'Telegram bot connected successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to connect: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }
}
