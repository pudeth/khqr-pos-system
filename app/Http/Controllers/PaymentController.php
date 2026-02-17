<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\KHQRService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected KHQRService $khqrService;
    protected TelegramService $telegramService;

    public function __construct(KHQRService $khqrService, TelegramService $telegramService)
    {
        $this->khqrService = $khqrService;
        $this->telegramService = $telegramService;
    }

    /**
     * Generate KHQR for payment
     */
    public function generateQR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'in:USD,KHR',
            'bill_number' => 'nullable|string',
            'mobile_number' => 'nullable|string',
            'store_label' => 'nullable|string',
            'terminal_label' => 'nullable|string',
            'type' => 'in:individual,merchant',
        ]);

        $type = $validated['type'] ?? 'individual';

        $result = $type === 'merchant'
            ? $this->khqrService->generateMerchantQR($validated)
            : $this->khqrService->generateIndividualQR($validated);

        if (isset($result['data'])) {
            // Save payment to database
            $payment = Payment::create([
                'md5' => $result['data']['md5'],
                'qr_code' => $result['data']['qr'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'] ?? 'USD',
                'bill_number' => $validated['bill_number'] ?? null,
                'mobile_number' => $validated['mobile_number'] ?? null,
                'store_label' => $validated['store_label'] ?? null,
                'terminal_label' => $validated['terminal_label'] ?? null,
                'merchant_name' => config('services.bakong.merchant.name'),
                'expires_at' => now()->addMinutes(30), // 30 minutes expiry
            ]);

            Log::info('Payment created', [
                'payment_id' => $payment->id,
                'md5' => $payment->md5,
                'amount' => $payment->amount,
                'currency' => $payment->currency
            ]);

            return response()->json([
                'success' => true,
                'qr_code' => $result['data']['qr'],
                'md5' => $result['data']['md5'],
                'payment_id' => $payment->id,
                'expires_at' => $payment->expires_at->toISOString(),
                'message' => 'QR generated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to generate QR',
        ], 400);
    }

    /**
     * Check payment status
     */
    public function checkPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'md5' => 'required|string',
        ]);

        $payment = Payment::where('md5', $validated['md5'])->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        // Check if already successful
        if ($payment->status === 'SUCCESS') {
            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'message' => 'Payment already completed!',
                'data' => [
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'paid_at' => $payment->paid_at,
                    'transaction_id' => $payment->transaction_id,
                ],
            ]);
        }

        // Check if expired
        if ($payment->isExpired()) {
            $payment->markAsExpired();
            return response()->json([
                'success' => false,
                'status' => 'EXPIRED',
                'message' => 'Payment has expired',
            ]);
        }

        // Check with Bakong API
        $result = $this->khqrService->checkPayment($validated['md5']);
        $payment->incrementCheckAttempts();
        
        Log::info('Manual payment check', [
            'payment_id' => $payment->id,
            'md5' => $payment->md5,
            'bakong_response' => $result
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

            // Send Telegram receipt if not sent yet
            if (!$payment->telegram_sent) {
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
                    
                    $telegramSent = $this->telegramService->sendReceipt($receiptData);
                } else {
                    // Fallback to simple payment notification
                    $telegramSent = $this->telegramService->sendPaymentSuccess([
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'bill_number' => $payment->bill_number ?? 'N/A',
                        'store_label' => $payment->store_label ?? 'N/A',
                        'mobile_number' => $payment->mobile_number ?? 'N/A',
                        'transaction_id' => $payment->transaction_id ?? 'N/A',
                    ]);
                }

                if ($telegramSent) {
                    $payment->markTelegramSent();
                }
            }

            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'message' => 'Payment completed successfully!',
                'data' => [
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'paid_at' => $payment->paid_at,
                    'transaction_id' => $payment->transaction_id,
                    'telegram_sent' => $payment->telegram_sent,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $result['responseMessage'] ?? 'Payment not yet completed',
            'data' => [
                'check_attempts' => $payment->check_attempts,
                'last_checked_at' => $payment->last_checked_at,
                'expires_at' => $payment->expires_at,
            ],
        ]);
    }

    /**
     * Get payment status by ID
     */
    public function getPaymentStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_id' => 'required|integer|exists:payments,id',
        ]);

        $payment = Payment::findOrFail($validated['payment_id']);

        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'md5' => $payment->md5,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'created_at' => $payment->created_at,
                'expires_at' => $payment->expires_at,
                'paid_at' => $payment->paid_at,
                'check_attempts' => $payment->check_attempts,
                'last_checked_at' => $payment->last_checked_at,
                'telegram_sent' => $payment->telegram_sent,
            ],
        ]);
    }

    /**
     * Verify QR code
     */
    public function verifyQR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $result = $this->khqrService->verifyQR($validated['qr_code']);

        return response()->json($result);
    }

    /**
     * Decode QR code
     */
    public function decodeQR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $result = $this->khqrService->decodeQR($validated['qr_code']);

        return response()->json($result);
    }

    /**
     * Generate deep link
     */
    public function generateDeepLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $result = $this->khqrService->generateDeepLink($validated['qr_code']);

        return response()->json($result);
    }
}
