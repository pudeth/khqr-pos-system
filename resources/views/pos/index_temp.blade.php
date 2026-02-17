@extends('layouts.app')

@section('title', 'Pospay-POS-Dashboard')

@section('content')
<div class="h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Pospay-POS-Dashboard</h1>
        <div class="flex items-center space-x-4">
            <span>{{ auth()->user()->name }}</span>
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-700 px-4 py-2 rounded hover:bg-blue-800">
                <i class="fas fa-chart-line mr-2"></i>Dashboard
            </a>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Products Section -->
        <div class="flex-1 p-6 overflow-auto">
            <!-- Search and Filter -->
            <div class="mb-4 flex space-x-4">
                <input type="text" id="searchProduct" placeholder="Search products..." 
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select id="categoryFilter" class="px-4 py-2 border rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl cursor-pointer transition-all duration-300 overflow-hidden border border-gray-200 hover:border-blue-400" 
                    onclick='addToCart(@json($product))'>
                    <!-- Product Image -->
                    <div class="relative">
                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 h-48 flex items-center justify-center">
                                <i class="fas fa-box text-6xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Stock Badge -->
                        @if($product->stock > 0)
                            <div class="absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                                In Stock
                            </div>
                        @else
                            <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                                Out of Stock
                            </div>
                        @endif

                        <!-- Discount Badge (if applicable) -->
                        @if(isset($product->discount) && $product->discount > 0)
                            <div class="absolute top-3 left-3 bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                -{{ $product->discount }}%
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="p-4">
                        <!-- Category -->
                        <div class="mb-2">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $product->category->name }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h3 class="font-bold text-gray-800 text-base mb-2 line-clamp-2 min-h-[3rem]">
                            {{ $product->name }}
                        </h3>

                        <!-- Product Description (if available) -->
                        @if(isset($product->description) && $product->description)
                            <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                                {{ $product->description }}
                            </p>
                        @endif

                        <!-- Price and Stock Info -->
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-2xl font-bold text-blue-600">
                                    ${{ number_format($product->price, 2) }}
                                </p>
                                @if(isset($product->discount) && $product->discount > 0)
                                    <p class="text-xs text-gray-500 line-through">
                                        ${{ number_format($product->price / (1 - $product->discount/100), 2) }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 font-medium">Available</p>
                                <p class="text-sm font-bold {{ $product->stock < 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $product->stock }} units
                                </p>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-cart-plus"></i>
                            <span>Add to Cart</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Section -->
        <div class="w-96 bg-white border-l flex flex-col">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold">Current Sale</h2>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-auto p-4" id="cartItems">
                <p class="text-gray-500 text-center mt-8">No items in cart</p>
            </div>

            <!-- Cart Summary -->
            <div class="border-t p-4 space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax (0%):</span>
                    <span id="tax">$0.00</span>
                </div>
                <div class="flex justify-between">
                    <span>Discount:</span>
                    <span id="discount">$0.00</span>
                </div>
                <div class="flex justify-between text-xl font-bold border-t pt-2">
                    <span>Total:</span>
                    <span id="total">$0.00</span>
                </div>

                <div class="space-y-2 mt-4">
                    <button onclick="openPaymentModal()" class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 font-bold">
                        <i class="fas fa-check mr-2"></i>Complete Sale
                    </button>
                    <button onclick="clearCart()" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-trash mr-2"></i>Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Complete Payment</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Payment Method</label>
            <div class="flex justify-center">
                <button onclick="selectPaymentMethod('KHQR')" class="payment-method-btn border-2 border-blue-500 bg-blue-50 py-4 px-8 rounded-lg w-full max-w-xs">
                    <i class="fas fa-qrcode text-4xl text-blue-500"></i>
                    <p class="text-lg font-semibold mt-2">KHQR Payment</p>
                    <p class="text-xs text-gray-600 mt-1">Scan to pay with Bakong</p>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Total Amount</label>
            <input type="text" id="totalAmount" readonly class="w-full border rounded px-3 py-2 bg-gray-100 text-xl font-bold text-center">
        </div>

        <div class="flex justify-end space-x-2">
            <button onclick="closePaymentModal()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
            <button onclick="processSale()" class="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600 font-bold">
                Complete
            </button>
        </div>
    </div>
</div>

<!-- KHQR Modal -->
<div id="khqrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Scan KHQR Code</h3>
        
        <div class="text-center mb-4">
            <div id="khqrCode" class="inline-block"></div>
            <p class="text-sm text-gray-600 mt-2">Scan with Bakong app to pay</p>
            <p class="text-lg font-bold text-blue-600 mt-2" id="khqrAmount"></p>
        </div>

        <div id="khqrStatus" class="mb-4 p-3 rounded bg-yellow-100 text-yellow-800 text-center">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            <span id="khqrStatusText">Waiting for payment...</span>
        </div>

        <div class="text-xs text-gray-500 text-center mb-4">
            <p>Payment will expire in <span id="khqrTimer" class="font-bold">30:00</span></p>
            <p class="mt-1">Checking automatically every 5 seconds</p>
        </div>

        <div class="flex justify-end space-x-2">
            <button onclick="cancelKHQRPayment()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
            <button onclick="checkKHQRPaymentManually()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                <i class="fas fa-sync-alt mr-2"></i>Check Now
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let cart = [];
let selectedPaymentMethod = 'KHQR';
let khqrCheckInterval = null;
let khqrTimerInterval = null;
let khqrTimeRemaining = 1800;
let currentKHQRData = null;

function addToCart(product) {
    const existingItem = cart.find(item => item.product_id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
        } else {
            alert('Insufficient stock');
            return;
        }
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            stock: product.stock,
            image: product.image || null
        });
    }
    
    updateCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    
    if (newQuantity <= 0) {
        removeFromCart(index);
    } else if (newQuantity <= item.stock) {
        item.quantity = newQuantity;
        updateCart();
    } else {
        alert('Insufficient stock');
    }
}

function updateCart() {
    const cartItemsDiv = document.getElementById('cartItems');
    
    if (cart.length === 0) {
