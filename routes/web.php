<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Health check route for Railway
Route::get('/health', function () {
    try {
        \DB::connection()->getPdo();
        return response()->json([
            'status' => 'ok',
            'database' => 'connected',
            'app' => config('app.name')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'database' => 'disconnected',
            'message' => 'Database not configured yet',
            'app' => config('app.name')
        ], 500);
    }
});

Route::get('/payment', function () {
    return view('payment');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// POS routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/pos/products', [POSController::class, 'getProducts']);
    Route::get('/pos/smart-search', [POSController::class, 'smartSearch']);
    Route::get('/pos/product/{id}', [POSController::class, 'showProduct'])->name('pos.product.show');
    Route::post('/pos/complete-sale', [POSController::class, 'completeSale']);
    Route::post('/pos/check-khqr-payment', [POSController::class, 'checkKHQRPayment']);
    Route::post('/pos/customer-by-phone', [POSController::class, 'getCustomerByPhone']);
});

// Admin routes (requires authentication)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/sales', [AdminController::class, 'sales'])->name('admin.sales');
    Route::get('/sales/{id}', [AdminController::class, 'saleDetails'])->name('admin.sale.details');
    
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::get('/products/search', [ProductController::class, 'search'])->name('admin.products.search');
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    
    // Customer Management
    Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::get('/customers/{id}', [AdminController::class, 'customerDetails'])->name('admin.customers.details');
    Route::get('/customers/{id}/edit', [AdminController::class, 'editCustomer'])->name('admin.customers.edit');
    Route::put('/customers/{id}', [AdminController::class, 'updateCustomer'])->name('admin.customers.update');
    Route::post('/customers/{id}/adjust-points', [AdminController::class, 'adjustCustomerPoints'])->name('admin.customers.adjust-points');
    Route::delete('/customers/{customerId}/transactions/{transactionId}', [AdminController::class, 'deleteCustomerPointTransaction'])->name('admin.customers.delete-transaction');
    
    // Store Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});
