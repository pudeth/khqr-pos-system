<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\KHQRService;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments extends Command
{
    protected $signature = 'payments:check';
    protected $description = 'Check pending payments status via Bakong API';

    public function __construct(
        protected KHQRService $khqrService,
        protected TelegramService $telegramService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking pending payments...');

        // Get pending payments that haven't expired
        $pendingPayments = Payment::where('status', 'PENDING')
            ->where('expires_at', '>', now())
            ->where(function ($query) {
                // Check payments that haven't been checked recently (last 30 seconds)
                $query->whereNull('last_checked_at')
                    ->orWhere('last_checked_at', '<', now()->subSeconds(30));
            })
            ->orderBy('created_at')
            ->limit(50) // Process max 50 at a time
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('No pending payments to check.');
            return self::SUCCESS;
        }

        $this->info("Found {$pendingPayments->count()} pending payments to check.");

        $successCount = 0;
        $errorCount = 0;

        foreach ($pendingPayments as $payment) {
            try {
                $this->checkPayment($payment);
                
                if ($payment->fresh()->status === 'SUCCESS') {
                    $successCount++;
                    $this->info("✅ Payment {$payment->md5} confirmed as SUCCESS");
                } else {
                    $this->line("⏳ Payment {$payment->md5} still pending");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Error checking payment {$payment->md5}: {$e->getMessage()}");
                Log::error("Payment check error", [
                    'payment_id' => $payment->id,
                    'md5' => $payment->md5,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Mark expired payments
        $expiredCount = Payment::where('status', 'PENDING')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'EXPIRED']);

        if ($expiredCount > 0) {
            $this->info("⏰ Marked {$expiredCount} payments as expired.");
        }

        $this->info("✅ Completed: {$successCount} successful, {$errorCount} errors, {$expiredCount} expired");

        return self::SUCCESS;
    }

    protected function checkPayment(Payment $payment): void
    {
        $payment->incrementCheckAttempts();

        $result = $this->khqrService->checkPayment($payment->md5);

        Log::info('Bakong API Response', [
            'payment_id' => $payment->id,
            'md5' => $payment->md5,
            'response' => $result
        ]);

        // Check if payment is successful
        $status = $result['data']['status'] ?? $result['responseMessage'] ?? 'PENDING';
        $isSuccess = $status === 'SUCCESS' || 
                    (isset($result['responseCode']) && $result['responseCode'] === 0) ||
                    (isset($result['data']['status']) && $result['data']['status'] === 'SUCCESS');

        if ($isSuccess) {
            $transactionId = $result['data']['hash'] ?? 
                           $result['data']['transaction_id'] ?? 
                           $result['data']['transactionId'] ?? 
                           null;

            $payment->markAsSuccess($result, $transactionId);

            // Send Telegram notification if not sent yet
            if (!$payment->telegram_sent) {
                $this->sendTelegramNotification($payment, $result);
            }
        }
    }

    protected function sendTelegramNotification(Payment $payment, array $bakongResponse): void
    {
        try {
            // Try to find associated sale for complete receipt
            $sale = \App\Models\Sale::where('payment_id', $payment->id)->with('items', 'user')->first();
            
            if ($sale) {
                // Extract Bakong response details
                $bakongData = $payment->bakong_response ?? [];
                $fromAccount = null;
                $referenceNumber = null;
                
                if (isset($bakongData['data'])) {
                    $fromAccount = $bakongData['data']['from_account_name'] ?? 
                                  $bakongData['data']['from_account'] ?? null;
                    $referenceNumber = $bakongData['data']['reference'] ?? 
                                      $bakongData['data']['ref'] ?? null;
                }
                
                // Send complete receipt with sale details
                $receiptData = [
                    'store_name' => config('services.bakong.merchant.name', 'POS Store'),
                    'invoice_number' => $sale->invoice_number,
                    'customer_name' => $sale->customer_name,
                    'customer_phone' => $sale->customer_phone,
                    'items' => $sale->items->map(function($item) {
                        return [
                            'name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->subtotal,
                        ];
                    })->toArray(),
                    'subtotal' => $sale->subtotal,
                    'tax' => $sale->tax,
                    'discount' => $sale->discount,
                    'total' => $sale->total,
                    'bank' => 'ACLEDA Bank Plc.',
                    'transaction_id' => $payment->transaction_id,
                    'reference_number' => $referenceNumber,
                    'from_account' => $fromAccount,
                    'cashier' => $sale->user->name ?? 'N/A',
                ];
                
                $success = $this->telegramService->sendReceipt($receiptData);
            } else {
                // Fallback to simple payment notification
                $success = $this->telegramService->sendPaymentSuccess([
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'bill_number' => $payment->bill_number ?? 'N/A',
                    'store_label' => $payment->store_label ?? 'N/A',
                    'mobile_number' => $payment->mobile_number ?? 'N/A',
                    'transaction_id' => $payment->transaction_id ?? 'N/A',
                ]);
            }

            if ($success) {
                $payment->markTelegramSent();
                $this->info("📱 Telegram receipt sent for payment {$payment->md5}");
            } else {
                $this->error("❌ Failed to send Telegram receipt for payment {$payment->md5}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Telegram error for payment {$payment->md5}: {$e->getMessage()}");
            Log::error('Telegram notification error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}