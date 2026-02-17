@extends('layouts.app')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('pos.index') }}" class="inline-flex items-center bg-gray-600 text-white px-4 py-2 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                <i class="fas fa-arrow-left mr-2"></i>
                BACK TO POS
            </a>
            
            <!-- Cart Indicator -->
            <a href="{{ route('pos.index') }}" class="inline-flex items-center bg-gray-700 text-white px-4 py-2 border-2 border-gray-900 hover:bg-gray-800 font-bold transition relative">
                <i class="fas fa-shopping-cart mr-2"></i>
                VIEW CART
                <span id="cartBadge" class="hidden absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full border-2 border-white">
                    0
                </span>
            </a>
        </div>

        <!-- Product Detail Card -->
        <div class="bg-white border-4 border-gray-800 shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-6 py-4 border-b-4 border-gray-900">
                <h1 class="text-3xl font-bold tracking-wide">PRODUCT DETAILS</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                <!-- Left Column - Image -->
                <div>
                    <div class="border-4 border-gray-400 bg-gray-50 overflow-hidden shadow-lg">
                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-96 object-cover">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-box text-9xl text-gray-400"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Badges -->
                    <div class="flex space-x-3 mt-4">
                        @if($product->stock > 0)
                            <span class="bg-green-600 text-white px-4 py-2 text-sm font-bold border-2 border-green-800 shadow">
                                <i class="fas fa-check-circle mr-1"></i>IN STOCK
                            </span>
                        @else
                            <span class="bg-red-600 text-white px-4 py-2 text-sm font-bold border-2 border-red-800 shadow">
                                <i class="fas fa-times-circle mr-1"></i>OUT OF STOCK
                            </span>
                        @endif

                        @if($product->discount > 0)
                            <span class="bg-red-700 text-white px-4 py-2 text-sm font-bold border-2 border-red-900 shadow">
                                <i class="fas fa-tag mr-1"></i>{{ $product->discount }}% OFF
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div>
                    <!-- Product Name -->
                    <h2 class="text-4xl font-black text-gray-900 mb-4 border-b-4 border-gray-300 pb-4">
                        {{ $product->name }}
                    </h2>

                    <!-- Price Section -->
                    <div class="bg-gray-50 border-2 border-gray-400 p-6 mb-6 shadow">
                        <label class="block text-xs font-bold text-gray-600 mb-2">PRICE</label>
                        @php
                            $finalPrice = $product->discount > 0 
                                ? $product->price * (1 - $product->discount / 100)
                                : $product->price;
                        @endphp
                        <div class="flex items-baseline space-x-3">
                            <span class="text-5xl font-black text-gray-900">${{ number_format($finalPrice, 2) }}</span>
                            @if($product->discount > 0)
                                <span class="text-2xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        @if($product->discount > 0)
                            <p class="text-green-700 font-bold mt-2">
                                <i class="fas fa-tag mr-1"></i>
                                You save ${{ number_format($product->price - $finalPrice, 2) }} ({{ $product->discount }}% OFF)
                            </p>
                        @endif
                    </div>

                    <!-- Product Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 border-2 border-gray-400 p-4">
                            <label class="block text-xs font-bold text-gray-600 mb-1">SKU</label>
                            <p class="text-lg font-bold text-gray-900">{{ $product->sku }}</p>
                        </div>

                        <div class="bg-gray-50 border-2 border-gray-400 p-4">
                            <label class="block text-xs font-bold text-gray-600 mb-1">CATEGORY</label>
                            <p class="text-lg font-bold text-gray-900">{{ $product->category->name }}</p>
                        </div>

                        <div class="bg-gray-50 border-2 border-gray-400 p-4">
                            <label class="block text-xs font-bold text-gray-600 mb-1">STOCK</label>
                            <p class="text-lg font-bold {{ $product->stock < 10 ? 'text-red-700' : 'text-green-700' }}">
                                {{ $product->stock }} units
                            </p>
                        </div>

                        <div class="bg-gray-50 border-2 border-gray-400 p-4">
                            <label class="block text-xs font-bold text-gray-600 mb-1">STATUS</label>
                            <p class="text-lg font-bold {{ $product->is_active ? 'text-green-700' : 'text-red-700' }}">
                                {{ $product->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($product->description)
                        <div class="bg-gray-50 border-2 border-gray-400 p-4 mb-6">
                            <label class="block text-xs font-bold text-gray-600 mb-2">DESCRIPTION</label>
                            <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <a href="{{ route('pos.index') }}" class="flex-1 text-center bg-gray-600 text-white px-6 py-4 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                            <i class="fas fa-arrow-left mr-2"></i>BACK TO POS
                        </a>
                        <button onclick="addToCartNoRedirect()" class="flex-1 bg-gray-700 text-white px-6 py-4 border-2 border-gray-900 hover:bg-gray-800 font-bold transition" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-cart-plus mr-2"></i>
                            <span id="addToCartText">ADD TO CART</span>
                        </button>
                    </div>

                    <!-- Cart Status -->
                    <div id="cartStatus" class="hidden mt-4 p-4 bg-green-50 border-2 border-green-600 text-green-900 font-bold text-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span id="cartStatusText"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-gray-400 pb-2">
                MORE FROM {{ strtoupper($product->category->name) }}
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($product->category->products()->where('id', '!=', $product->id)->where('is_active', true)->where('stock', '>', 0)->limit(4)->get() as $relatedProduct)
                    <a href="{{ route('pos.product.show', $relatedProduct->id) }}" class="bg-white border-2 border-gray-400 shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="relative border-b-2 border-gray-300">
                            @if($relatedProduct->image)
                                <img src="{{ filter_var($relatedProduct->image, FILTER_VALIDATE_URL) ? $relatedProduct->image : asset('storage/' . $relatedProduct->image) }}" 
                                     alt="{{ $relatedProduct->name }}" 
                                     class="w-full h-32 object-cover">
                            @else
                                <div class="bg-gray-200 h-32 flex items-center justify-center">
                                    <i class="fas fa-box text-4xl text-gray-500"></i>
                                </div>
                            @endif
                            
                            @if($relatedProduct->discount > 0)
                                <div class="absolute top-2 left-2 bg-red-700 text-white px-2 py-1 text-xs font-bold border border-red-900">
                                    -{{ $relatedProduct->discount }}%
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-3">
                            <h4 class="font-bold text-gray-900 text-sm mb-2 line-clamp-2 min-h-[2.5rem]">
                                {{ $relatedProduct->name }}
                            </h4>
                            @php
                                $relatedFinalPrice = $relatedProduct->discount > 0 
                                    ? $relatedProduct->price * (1 - $relatedProduct->discount / 100)
                                    : $relatedProduct->price;
                            @endphp
                            <p class="text-lg font-bold text-gray-900">${{ number_format($relatedFinalPrice, 2) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const product = @json($product);
let cartItemCount = 0;

// Load cart from localStorage on page load
window.addEventListener('DOMContentLoaded', function() {
    loadCartCount();
    updateCartBadge();
});

function loadCartCount() {
    const savedCart = localStorage.getItem('posCart');
    if (savedCart) {
        try {
            const cart = JSON.parse(savedCart);
            cartItemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
        } catch (e) {
            console.error('Error loading cart:', e);
        }
    }
}

function updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    if (cartItemCount > 0) {
        badge.textContent = cartItemCount;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function addToCartNoRedirect() {
    // Calculate final price
    const finalPrice = product.discount > 0 
        ? parseFloat(product.price) * (1 - product.discount / 100)
        : parseFloat(product.price);
    
    // Get existing cart from localStorage
    let cart = [];
    const savedCart = localStorage.getItem('posCart');
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
        } catch (e) {
            console.error('Error parsing cart:', e);
        }
    }
    
    // Check if product already in cart
    const existingItem = cart.find(item => item.product_id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
            cartItemCount++;
        } else {
            showNotification('Cannot add more - insufficient stock!', 'error');
            return;
        }
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            price: finalPrice,
            quantity: 1,
            stock: product.stock,
            image: product.image || null
        });
        cartItemCount++;
    }
    
    // Save cart back to localStorage
    localStorage.setItem('posCart', JSON.stringify(cart));
    
    // Update cart badge
    updateCartBadge();
    
    // Show success notification
    showNotification(`${product.name} added to cart! (${cartItemCount} items total)`, 'success');
    
    // Animate button
    animateButton();
}

function showNotification(message, type = 'success') {
    const statusDiv = document.getElementById('cartStatus');
    const statusText = document.getElementById('cartStatusText');
    
    statusDiv.className = 'mt-4 p-4 border-2 font-bold text-center';
    
    if (type === 'success') {
        statusDiv.classList.add('bg-green-50', 'border-green-600', 'text-green-900');
        statusText.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
    } else {
        statusDiv.classList.add('bg-red-50', 'border-red-600', 'text-red-900');
        statusText.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + message;
    }
    
    statusDiv.classList.remove('hidden');
    
    // Hide after 3 seconds
    setTimeout(() => {
        statusDiv.classList.add('hidden');
    }, 3000);
}

function animateButton() {
    const button = document.querySelector('button[onclick="addToCartNoRedirect()"]');
    const buttonText = document.getElementById('addToCartText');
    
    // Change button text temporarily
    const originalText = buttonText.textContent;
    buttonText.innerHTML = '<i class="fas fa-check mr-2"></i>ADDED!';
    button.classList.add('bg-green-700', 'border-green-900');
    button.classList.remove('bg-gray-700', 'border-gray-900');
    
    // Reset after 1 second
    setTimeout(() => {
        buttonText.textContent = originalText;
        button.classList.remove('bg-green-700', 'border-green-900');
        button.classList.add('bg-gray-700', 'border-gray-900');
    }, 1000);
}

// Keep the old function for backward compatibility (if needed)
function addToCartAndReturn() {
    addToCartNoRedirect();
    setTimeout(() => {
        window.location.href = '{{ route("pos.index") }}';
    }, 1500);
}
</script>
@endpush
@endsection
