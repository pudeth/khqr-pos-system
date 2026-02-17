<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Services\KHQRService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    protected KHQRService $khqrService;
    protected TelegramService $telegramService;

    public function __construct(KHQRService $khqrService, TelegramService $telegramService)
    {
        $this->khqrService = $khqrService;
        $this->telegramService = $telegramService;
    }

    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->with('category')
            ->get();
        
        return view('pos.index', compact('categories', 'products'));
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('is_active', true)->where('stock', '>', 0);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($query->with('category')->get());
    }

    public function smartSearch(Request $request)
    {
        $searchTerm = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        
        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }
        
        $query = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->with('category');

        // Apply category filter if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Smart search: prioritize exact matches, then partial matches
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('sku', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
        });

        // Order by relevance: exact name matches first, then partial matches
        $products = $query->get()->sortBy(function($product) use ($searchTerm) {
            $name = strtolower($product->name);
            $search = strtolower($searchTerm);
            
            // Exact match gets highest priority
            if ($name === $search) return 0;
            
            // Starts with search term gets second priority
            if (strpos($name, $search) === 0) return 1;
            
            // Contains search term gets third priority
            if (strpos($name, $search) !== false) return 2;
            
            // SKU or description matches get lower priority
            return 3;
        })->values();

        return response()->json($products->take(10));
    }

    public function showProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        
        return view('pos.product-detail', compact('product', 'categories'));
    }

    public function getCustomerByPhone(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        $customer = Customer::where('phone', $validated['phone'])->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'phone' => $customer->phone,
                    'name' => $customer->name,
                    'address' => $customer->address,
                    'available_points' => $customer->available_points,
                    'total_points' => $customer->total_points,
                    'total_spent' => $customer->total_spent,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer not found',
        ]);
    }

    public function completeSale(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:KHQR', // Only KHQR allowed
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'customer_address' => 'nullable|string',
            'points_to_use' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Handle customer and points
            $customer = null;
            $customerId = null;
            $pointsUsed = 0;
            $pointsDiscount = 0;
            $finalTotal = $validated['total'];
            $isPaidWithPoints = false;

            if (!empty($validated['customer_phone'])) {
                $customer = Customer::findOrCreateByPhone(
                    $validated['customer_phone'],
                    $validated['customer_name'] ?? null,
                    $validated['customer_address'] ?? null
                );
                $customerId = $customer->id;

                // Handle points usage
                if (!empty($validated['points_to_use']) && $validated['points_to_use'] > 0) {
                    $pointsToUse = min($validated['points_to_use'], $customer->available_points);
                    
                    if ($pointsToUse > 0) {
                        $pointsDiscount = $customer->usePoints($pointsToUse);
                        $pointsUsed = $pointsToUse;
                        $finalTotal = max(0, $validated['total'] - $pointsDiscount);
                        
                        // Check if payment is fully covered by points
                        $isPaidWithPoints = ($pointsDiscount >= $validated['total']);
                    }
                }
            }

            // Handle payment method based on final total
            $paymentId = null;
            $qrCode = null;
            $md5 = null;
            $payment = null;

            if ($isPaidWithPoints) {
                // Payment fully covered by points - create a completed payment record
                $payment = Payment::create([
                    'md5' => 'POINTS-' . uniqid(),
                    'qr_code' => null,
                    'amount' => 0, // No KHQR payment needed
                    'currency' => 'USD',
                    'bill_number' => Sale::generateInvoiceNumber(),
                    'mobile_number' => $validated['customer_phone'] ?? '',
                    'store_label' => config('services.bakong.merchant.name', 'POS Store'),
                    'terminal_label' => 'POS-' . auth()->id(),
                    'merchant_name' => config('services.bakong.merchant.name'),
                    'status' => 'SUCCESS', // Mark as successful immediately
                    'paid_at' => now(),
                    'expires_at' => now()->addMinutes(30),
                ]);

                $paymentId = $payment->id;
            } else {
                // Generate KHQR for remaining amount
                $khqrData = [
                    'amount' => $finalTotal, // Use final total after points discount
                    'currency' => 'USD',
                    'bill_number' => Sale::generateInvoiceNumber(),
                    'mobile_number' => $validated['customer_phone'] ?? '',
                    'store_label' => config('services.bakong.merchant.name', 'POS Store'),
                    'terminal_label' => 'POS-' . auth()->id(),
                ];

                $result = $this->khqrService->generateIndividualQR($khqrData);

                if (isset($result['data'])) {
                    // Save payment record
                    $payment = Payment::create([
                        'md5' => $result['data']['md5'],
                        'qr_code' => $result['data']['qr'],
                        'amount' => $finalTotal, // Use final total after points discount
                        'currency' => 'USD',
                        'bill_number' => $khqrData['bill_number'],
                        'mobile_number' => $khqrData['mobile_number'],
                        'store_label' => $khqrData['store_label'],
                        'terminal_label' => $khqrData['terminal_label'],
                        'merchant_name' => config('services.bakong.merchant.name'),
                        'expires_at' => now()->addMinutes(30),
                    ]);

                    $paymentId = $payment->id;
                    $qrCode = $result['data']['qr'];
                    $md5 = $result['data']['md5'];
                } else {
                    throw new \Exception('Failed to generate KHQR code');
                }
            }

            // Create sale
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'user_id' => auth()->id(),
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'total' => $validated['total'],
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $finalTotal, // Calculate change from final total
                'payment_method' => $validated['payment_method'],
                'payment_id' => $paymentId,
                'customer_id' => $customerId,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'points_used' => $pointsUsed,
                'points_discount' => $pointsDiscount,
            ]);

            // Create sale items and update stock
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Update stock
                $product->decrement('stock', $item['quantity']);
            }

            // Calculate and award points for this purchase
            $pointsEarned = 0;
            if ($customer && $validated['total'] > 0) {
                $pointsEarned = Customer::calculatePointsFromAmount($validated['total']);
                if ($pointsEarned > 0) {
                    $customer->addPoints($pointsEarned, $sale->id, $validated['total']);
                    $sale->update(['points_earned' => $pointsEarned]);
                }
            }

            DB::commit();

            // Send Telegram notification for all sales
            try {
                $saleNotificationData = [
                    'invoice_number' => $sale->invoice_number,
                    'total' => $sale->total,
                    'payment_method' => $isPaidWithPoints ? 'POINTS' : $sale->payment_method,
                    'cashier' => auth()->user()->name,
                    'customer_name' => $sale->customer_name,
                    'items' => $sale->items->map(function($item) {
                        return [
                            'name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'subtotal' => $item->subtotal,
                        ];
                    })->toArray(),
                ];
                
                $this->telegramService->sendSaleNotification($saleNotificationData);
            } catch (\Exception $e) {
                // Log but don't fail the sale if Telegram fails
                \Log::warning('Telegram notification failed: ' . $e->getMessage());
            }

            if ($isPaidWithPoints) {
                // Payment completed with points - send receipt immediately
                try {
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
                        'points_used' => $pointsUsed,
                        'points_discount' => $pointsDiscount,
                        'payment_method' => 'LOYALTY POINTS',
                        'cashier' => auth()->user()->name,
                    ];
                    
                    $this->telegramService->sendReceipt($receiptData);
                } catch (\Exception $e) {
                    \Log::warning('Telegram receipt failed: ' . $e->getMessage());
                }

                $response = [
                    'success' => true,
                    'sale' => $sale->load('items', 'customer'),
                    'message' => 'Sale completed successfully with points!',
                    'paid_with_points' => true,
                    'customer_info' => $customer ? [
                        'points_earned' => $pointsEarned,
                        'points_used' => $pointsUsed,
                        'points_discount' => $pointsDiscount,
                        'available_points' => $customer->available_points,
                        'total_points' => $customer->total_points,
                    ] : null,
                ];
            } else {
                $response = [
                    'success' => true,
                    'sale' => $sale->load('items', 'customer'),
                    'message' => 'Sale completed successfully',
                    'paid_with_points' => false,
                    'customer_info' => $customer ? [
                        'points_earned' => $pointsEarned,
                        'points_used' => $pointsUsed,
                        'points_discount' => $pointsDiscount,
                        'available_points' => $customer->available_points,
                        'total_points' => $customer->total_points,
                    ] : null,
                    'khqr' => [
                        'qr_code' => $qrCode,
                        'md5' => $md5,
                        'payment_id' => $paymentId,
                        'amount' => $finalTotal, // Show final amount to pay
                        'expires_at' => now()->addMinutes(30)->toISOString(),
                    ],
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function checkKHQRPayment(Request $request)
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
                'message' => 'Payment completed!',
            ]);
        }

        // Check if expired
        if ($payment->isExpired()) {
            $payment->markAsExpired();
            return response()->json([
                'success' => false,
                'status' => 'EXPIRED',
                'message' => 'Payment expired',
            ]);
        }

        // Check with Bakong API
        $result = $this->khqrService->checkPayment($validated['md5']);
        $payment->incrementCheckAttempts();

        $status = $result['data']['status'] ?? $result['responseMessage'] ?? 'PENDING';
        $isSuccess = $status === 'SUCCESS' || 
                    (isset($result['responseCode']) && $result['responseCode'] === 0) ||
                    (isset($result['data']['status']) && $result['data']['status'] === 'SUCCESS');
        
        if ($isSuccess) {
            $transactionId = $result['data']['hash'] ?? 
                           $result['data']['transaction_id'] ?? 
                           null;

            $payment->markAsSuccess($result, $transactionId);

            // Send Telegram receipt with sale details
            if (!$payment->telegram_sent) {
                // Find the sale associated with this payment
                $sale = Sale::where('payment_id', $payment->id)->with('items', 'user')->first();
                
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
                    
                    // Send complete receipt
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
                    
                    $this->telegramService->sendReceipt($receiptData);
                } else {
                    // Fallback to simple payment notification
                    $this->telegramService->sendPaymentSuccess([
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'bill_number' => $payment->bill_number ?? 'N/A',
                        'transaction_id' => $payment->transaction_id ?? 'N/A',
                    ]);
                }
                
                $payment->markTelegramSent();
            }

            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'message' => 'Payment completed!',
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => 'Payment pending',
        ]);
    }
}
