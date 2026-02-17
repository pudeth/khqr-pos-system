<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('payment')->group(function () {
    // Generate KHQR
    Route::post('/generate-qr', [PaymentController::class, 'generateQR']);
    
    // Check payment status
    Route::post('/check', [PaymentController::class, 'checkPayment']);
    
    // Get payment status by ID
    Route::post('/status', [PaymentController::class, 'getPaymentStatus']);
    
    // Verify QR code
    Route::post('/verify', [PaymentController::class, 'verifyQR']);
    
    // Decode QR code
    Route::post('/decode', [PaymentController::class, 'decodeQR']);
    
    // Generate deep link
    Route::post('/deep-link', [PaymentController::class, 'generateDeepLink']);
});
