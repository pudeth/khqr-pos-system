@extends('layouts.app')

@section('title', 'Pospay-POS-Dashboard')

@section('content')
<div class="h-screen flex flex-col bg-gray-100">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-6 py-3 border-b-4 border-gray-900 shadow-lg">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                @php
                    $storeName = \App\Models\StoreSetting::get('store_name', 'Pospay');
                    $storeTagline = \App\Models\StoreSetting::get('store_tagline', 'POS System');
                    $storeLogo = \App\Models\StoreSetting::get('store_logo');
                @endphp
                
                @if($storeLogo)
                    <img src="{{ asset('storage/' . $storeLogo) }}" 
                         alt="Store Logo" 
                         class="h-10 w-10 rounded-lg border-2 border-gray-400 object-cover">
                @endif
                
                <div>
                    <h1 class="text-2xl font-bold tracking-wide">{{ $storeName }} POS System</h1>
                    <div class="khmer-text text-lg text-gray-300 mt-1">ប្រព័ន្ធលក់ដូរ</div>
                    <div class="flex items-center space-x-2 mt-1">
                        <i class="fas fa-clock text-sm text-gray-300"></i>
                        <span id="currentTime" class="text-sm font-semibold text-gray-300"></span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- User Info with Avatar -->
                @auth
                <div class="flex items-center space-x-2 bg-gray-600 px-3 py-1 rounded border border-gray-500">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-6 h-6 rounded-full border border-gray-400">
                    @endif
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                </div>
                @endauth
                
                <!-- Admin Dashboard Link (Only for Admins) -->
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 px-4 py-2 rounded border border-gray-500 hover:bg-gray-500 transition">
                            <i class="fas fa-chart-line mr-2"></i>Dashboard
                        </a>
                    @endif
                @endauth
                
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 px-4 py-2 rounded border border-red-800 hover:bg-red-500 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Discount Products Scrolling Banner -->
    <div class="bg-gradient-to-r from-red-700 via-red-600 to-red-700 border-b-2 border-red-900 overflow-hidden relative shadow-lg" style="height: 50px;">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div id="discountBanner" class="flex items-center h-full whitespace-nowrap absolute z-10" style="animation: scrollLeft 60s linear infinite;">
            <!-- Products will be inserted here by JavaScript -->
        </div>
        <!-- Gradient overlays for fade effect -->
        <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-red-700 to-transparent z-20 pointer-events-none"></div>
        <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-red-700 to-transparent z-20 pointer-events-none"></div>
    </div>

    <style>
        @keyframes scrollLeft {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        .discount-item {
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* Smart Search Dropdown Styles */
        #searchDropdown {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        #searchDropdown::-webkit-scrollbar {
            width: 6px;
        }
        
        #searchDropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        #searchDropdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        #searchDropdown::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Highlight search terms */
        mark {
            background-color: #fef08a;
            color: #92400e;
            padding: 1px 2px;
            border-radius: 2px;
            font-weight: 600;
        }
        
        /* Smooth transitions for dropdown items */
        #searchDropdown > div {
            transition: background-color 0.15s ease;
        }
        
        /* Loading state for search */
        .search-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
    </style>

    <div class="flex-1 flex overflow-hidden bg-gray-100">
        <!-- Products Section -->
        <div class="flex-1 p-4 overflow-auto">
            <!-- Search and Filter -->
            <div class="mb-4 bg-white p-4 border-2 border-gray-300 shadow">
                <div class="flex space-x-3">
                    <div class="flex-1 relative">
                        <input type="text" id="searchProduct" placeholder="Smart search... / ស្វែងរកឆាប់..." 
                            class="w-full px-3 py-2 border-2 border-gray-300 focus:outline-none focus:border-gray-500 mixed-text khmer-input"
                            autocomplete="off">
                        <!-- Smart Search Dropdown -->
                        <div id="searchDropdown" class="hidden absolute top-full left-0 right-0 bg-white border-2 border-gray-300 border-t-0 shadow-lg z-50 max-h-96 overflow-y-auto">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                    <select id="categoryFilter" class="px-3 py-2 border-2 border-gray-300 bg-white focus:outline-none focus:border-gray-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                <div class="bg-white border-2 border-gray-400 shadow-md hover:shadow-lg cursor-pointer transition-shadow overflow-hidden" 
                    onclick='addToCart(@json($product))'>
                    <!-- Product Image -->
                    <div class="relative border-b-2 border-gray-300">
                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-40 object-cover">
                        @else
                            <div class="bg-gray-200 h-40 flex items-center justify-center border-b border-gray-300">
                                <i class="fas fa-box text-5xl text-gray-500"></i>
                            </div>
                        @endif
                        
                        <!-- Stock Badge -->
                        @if($product->stock > 0)
                            <div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 text-xs font-bold border border-green-800">
                                IN STOCK
                            </div>
                        @else
                            <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs font-bold border border-red-800">
                                OUT OF STOCK
                            </div>
                        @endif

                        <!-- Discount Badge -->
                        @if(isset($product->discount) && $product->discount > 0)
                            <div class="absolute top-2 left-2 bg-red-700 text-white px-2 py-1 text-xs font-bold border border-red-900">
                                -{{ $product->discount }}% OFF
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="p-3">
                        <!-- Category -->
                        <div class="mb-2">
                            <span class="inline-block bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 border border-gray-400">
                                {{ $product->category->name }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h3 class="font-bold text-gray-900 text-sm mb-2 line-clamp-2 min-h-[2.5rem]">
                            {{ $product->name }}
                        </h3>

                        <!-- Product Description -->
                        @if(isset($product->description) && $product->description)
                            <p class="text-xs text-gray-600 mb-2 line-clamp-2">
                                {{ $product->description }}
                            </p>
                        @endif

                        <!-- Price and Stock Info -->
                        <div class="border-t border-gray-300 pt-2 mb-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    @php
                                        $finalPrice = $product->discount > 0 
                                            ? $product->price * (1 - $product->discount / 100)
                                            : $product->price;
                                    @endphp
                                    <p class="text-xl font-bold text-gray-900">
                                        ${{ number_format($finalPrice, 2) }}
                                    </p>
                                    @if(isset($product->discount) && $product->discount > 0)
                                        <p class="text-xs text-gray-500 line-through">
                                            ${{ number_format($product->price, 2) }}
                                        </p>
                                        <p class="text-xs text-green-700 font-semibold">
                                            {{ $product->discount }}% OFF
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-600">Stock:</p>
                                    <p class="text-sm font-bold {{ $product->stock < 10 ? 'text-red-700' : 'text-green-700' }}">
                                        {{ $product->stock }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <div class="flex space-x-2">
                            <a href="{{ route('pos.product.show', $product->id) }}" onclick="event.stopPropagation()" class="flex-1 text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-3 border-2 border-gray-800 transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                VIEW
                            </a>
                            <button class="flex-1 bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-3 border-2 border-gray-900 transition-colors">
                                <i class="fas fa-cart-plus mr-1"></i>
                                ADD
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Section -->
        <div class="w-96 bg-white border-l-4 border-gray-400 flex flex-col shadow-lg">
            <div class="p-4 bg-gray-700 text-white border-b-2 border-gray-900">
                <h2 class="text-xl font-bold tracking-wide">CURRENT SALE</h2>
                <div class="khmer-text text-sm text-gray-300 mt-1">ការលក់បច្ចុប្បន្ន</div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-auto p-4 bg-gray-50" id="cartItems">
                <div class="text-gray-500 text-center mt-8">
                    <p class="font-semibold">No items in cart</p>
                    <p class="khmer-text text-sm mt-1">មិនមានទំនិញក្នុងកន្ត្រក</p>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="border-t-2 border-gray-400 p-4 space-y-2 bg-white">
                <div class="flex justify-between text-sm">
                    <span class="font-semibold">Subtotal:</span>
                    <span id="subtotal" class="font-bold">$0.00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-semibold">Tax (0%):</span>
                    <span id="tax" class="font-bold">$0.00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-semibold">Discount:</span>
                    <span id="discount" class="font-bold">$0.00</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t-2 border-gray-400 pt-2 mt-2">
                    <span>TOTAL:</span>
                    <span id="total">$0.00</span>
                </div>

                <div class="space-y-2 mt-4">
                    <button onclick="openPaymentModal()" class="w-full bg-green-700 text-white py-3 border-2 border-green-900 hover:bg-green-800 font-bold transition">
                        <i class="fas fa-check mr-2"></i>COMPLETE SALE
                    </button>
                    <button onclick="clearCart()" class="w-full bg-red-700 text-white py-2 border-2 border-red-900 hover:bg-red-800 font-bold transition">
                        <i class="fas fa-trash mr-2"></i>CLEAR CART
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white border-4 border-gray-800 shadow-2xl p-6 w-full max-w-lg">
        <h3 class="text-2xl font-bold mb-4 pb-2 border-b-2 border-gray-300">Complete Payment</h3>
                <div class="khmer-text text-lg text-gray-600 mb-4">បញ្ចប់ការទូទាត់</div>
        
        <!-- Customer Information Section -->
        <div class="mb-6 p-4 bg-gray-50 border-2 border-gray-300">
            <h4 class="text-lg font-bold mb-3 text-gray-700">Customer Information (Optional)</h4>
            <div class="khmer-text text-sm text-gray-600 mb-3">ព័ត៌មានអតិថិជន (ស្រេចចិត្ត)</div>
            
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Phone Number</label>
                    <div class="flex">
                        <input type="text" id="customerPhone" placeholder="Enter phone number" 
                               class="flex-1 border-2 border-gray-400 px-3 py-2" onblur="checkCustomerByPhone()">
                        <button onclick="checkCustomerByPhone()" class="bg-blue-600 text-white px-3 py-2 border-2 border-blue-800 hover:bg-blue-700 ml-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Customer Name</label>
                    <input type="text" id="customerName" placeholder="Enter customer name" 
                           class="w-full border-2 border-gray-400 px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Address (Street/House Number)</label>
                    <input type="text" id="customerAddress" placeholder="Enter address" 
                           class="w-full border-2 border-gray-400 px-3 py-2">
                </div>
            </div>
            
            <!-- Customer Points Display -->
            <div id="customerPointsInfo" class="hidden mt-3 p-3 bg-blue-50 border-2 border-blue-300">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-blue-800">Available Points:</span>
                    <span id="availablePoints" class="text-xl font-bold text-blue-600">0</span>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-bold text-blue-700">Use Points (1 point = $1):</label>
                    <input type="number" id="pointsToUse" min="0" max="0" value="0" 
                           class="border-2 border-blue-400 px-2 py-1 w-20 text-center" onchange="updatePointsDiscount()">
                    <button onclick="useMaxPoints()" class="bg-blue-600 text-white px-2 py-1 text-xs border border-blue-800 hover:bg-blue-700">
                        MAX
                    </button>
                </div>
                <div id="pointsDiscount" class="text-sm text-green-600 font-bold mt-1"></div>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-bold mb-3 text-gray-700">PAYMENT METHOD</label>
            <div class="flex justify-center">
                <button onclick="selectPaymentMethod('KHQR')" class="payment-method-btn border-4 border-gray-700 bg-gray-100 hover:bg-gray-200 py-4 px-8 w-full max-w-xs transition">
                    <i class="fas fa-qrcode text-4xl text-gray-700"></i>
                    <p class="text-lg font-bold mt-2">KHQR PAYMENT</p>
                    <p class="text-xs text-gray-600 mt-1">Scan to pay with Bakong</p>
                </button>
            </div>
        </div>

        <div class="mb-4 space-y-2">
            <div class="flex justify-between">
                <label class="text-sm font-bold text-gray-700">SUBTOTAL:</label>
                <span id="modalSubtotal" class="font-bold">$0.00</span>
            </div>
            <div id="pointsDiscountRow" class="hidden flex justify-between text-green-600">
                <label class="text-sm font-bold">POINTS DISCOUNT:</label>
                <span id="modalPointsDiscount" class="font-bold">-$0.00</span>
            </div>
            <div class="flex justify-between text-xl font-bold border-t-2 border-gray-300 pt-2">
                <label class="text-gray-700">TOTAL TO PAY:</label>
                <span id="totalAmount" class="text-green-600">$0.00</span>
            </div>
        </div>

        <div class="flex justify-end space-x-2 pt-3 border-t-2 border-gray-300">
            <button onclick="closePaymentModal()" class="px-4 py-2 border-2 border-gray-400 bg-gray-200 hover:bg-gray-300 font-bold transition">CANCEL</button>
            <button onclick="processSale()" class="px-6 py-2 bg-green-700 text-white border-2 border-green-900 hover:bg-green-800 font-bold transition">
                COMPLETE
            </button>
        </div>
    </div>
</div>

<!-- KHQR Modal -->
<div id="khqrModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white border-4 border-gray-800 shadow-2xl p-6 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-4 pb-2 border-b-2 border-gray-300">Scan KHQR Code</h3>
        
        <div class="text-center mb-4 p-4 bg-gray-50 border-2 border-gray-300">
            <div id="khqrCode" class="inline-block"></div>
            <p class="text-sm text-gray-700 mt-3 font-semibold">Scan with Bakong app to pay</p>
            <p class="text-lg font-bold text-gray-900 mt-2" id="khqrAmount"></p>
        </div>

        <div id="khqrStatus" class="mb-4 p-3 border-2 bg-yellow-50 border-yellow-600 text-yellow-900 text-center font-semibold">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            <span id="khqrStatusText">Waiting for payment...</span>
        </div>

        <div class="text-xs text-gray-600 text-center mb-4 bg-gray-100 p-3 border border-gray-300">
            <p class="font-bold">Payment expires in <span id="khqrTimer" class="text-red-700">30:00</span></p>
            <p class="mt-1">Checking automatically every 5 seconds</p>
        </div>

        <div class="flex justify-end space-x-2 pt-3 border-t-2 border-gray-300">
            <button onclick="cancelKHQRPayment()" class="px-4 py-2 border-2 border-gray-400 bg-gray-200 hover:bg-gray-300 font-bold transition">CANCEL</button>
            <button onclick="checkKHQRPaymentManually()" class="px-4 py-2 bg-gray-700 text-white border-2 border-gray-900 hover:bg-gray-800 font-bold transition">
                <i class="fas fa-sync-alt mr-2"></i>CHECK NOW
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// Update current time
function updateCurrentTime() {
    const now = new Date();
    const options = { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: true
    };
    document.getElementById('currentTime').textContent = now.toLocaleString('en-US', options);
}

// Update time immediately and then every second
updateCurrentTime();
setInterval(updateCurrentTime, 1000);

// Load cart from localStorage on page load
window.addEventListener('DOMContentLoaded', function() {
    const savedCart = localStorage.getItem('posCart');
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
            updateCart();
        } catch (e) {
            console.error('Error loading cart:', e);
            localStorage.removeItem('posCart');
        }
    }
});

// Save cart to localStorage whenever it changes
function saveCart() {
    localStorage.setItem('posCart', JSON.stringify(cart));
}

// Discount Banner Animation
const allProducts = @json($products);
const discountProducts = allProducts.filter(p => p.discount && p.discount > 0);

function shuffleArray(array) {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
}

function createDiscountBanner() {
    const banner = document.getElementById('discountBanner');
    if (!banner || discountProducts.length === 0) {
        banner.innerHTML = '<span class="inline-flex items-center mx-6 text-white font-bold"><i class="fas fa-star mr-2 text-yellow-300"></i>No special offers available at the moment</span>';
        return;
    }
    
    // Shuffle products randomly
    const shuffledProducts = shuffleArray(discountProducts);
    
    // Create banner content with enhanced styling
    const bannerContent = shuffledProducts.map((product, index) => {
        const originalPrice = parseFloat(product.price);
        const discountedPrice = originalPrice * (1 - product.discount / 100);
        const savings = (originalPrice - discountedPrice).toFixed(2);
        
        return `
            <span class="inline-flex items-center mx-8 px-4 py-2 bg-white bg-opacity-10 rounded border border-white border-opacity-30 discount-item" style="animation-delay: ${index * 0.2}s;">
                <span class="bg-yellow-400 text-red-900 px-2 py-1 rounded font-black text-sm mr-3 shadow">
                    ${product.discount}% OFF
                </span>
                <i class="fas fa-fire text-yellow-300 mr-2 text-lg"></i>
                <span class="text-white font-bold text-base mr-3">${product.name}</span>
                <span class="text-gray-200 text-sm mr-2">Was</span>
                <span class="line-through text-gray-300 text-sm mr-2">$${originalPrice.toFixed(2)}</span>
                <span class="text-yellow-300 text-sm mr-2">Now</span>
                <span class="text-yellow-300 font-black text-lg mr-2">$${discountedPrice.toFixed(2)}</span>
                <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                    Save $${savings}
                </span>
            </span>
        `;
    }).join('');
    
    // Duplicate content for seamless loop
    banner.innerHTML = bannerContent + bannerContent;
}

// Initialize banner
createDiscountBanner();

// Refresh banner with new random order every 60 seconds
setInterval(() => {
    createDiscountBanner();
}, 60000);

let cart = [];
let selectedPaymentMethod = 'KHQR';
let khqrCheckInterval = null;
let khqrTimerInterval = null;
let khqrTimeRemaining = 1800;
let currentKHQRData = null;
let currentCustomer = null;

// Customer functions
async function checkCustomerByPhone() {
    const phone = document.getElementById('customerPhone').value.trim();
    if (!phone) {
        document.getElementById('customerPointsInfo').classList.add('hidden');
        currentCustomer = null;
        return;
    }

    try {
        const response = await fetch('/pos/customer-by-phone', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone: phone })
        });

        const data = await response.json();
        
        if (data.success) {
            currentCustomer = data.customer;
            
            // Fill in customer details
            if (data.customer.name && !document.getElementById('customerName').value) {
                document.getElementById('customerName').value = data.customer.name;
            }
            if (data.customer.address && !document.getElementById('customerAddress').value) {
                document.getElementById('customerAddress').value = data.customer.address;
            }
            
            // Show points info
            document.getElementById('availablePoints').textContent = data.customer.available_points;
            document.getElementById('pointsToUse').max = data.customer.available_points;
            document.getElementById('customerPointsInfo').classList.remove('hidden');
            
        } else {
            currentCustomer = null;
            document.getElementById('customerPointsInfo').classList.add('hidden');
        }
    } catch (error) {
        console.error('Error checking customer:', error);
        currentCustomer = null;
        document.getElementById('customerPointsInfo').classList.add('hidden');
    }
}

function useMaxPoints() {
    if (currentCustomer) {
        const maxPoints = currentCustomer.available_points;
        const total = parseFloat(document.getElementById('total').textContent.replace('$', ''));
        const pointsToUse = Math.min(maxPoints, Math.floor(total));
        
        document.getElementById('pointsToUse').value = pointsToUse;
        updatePointsDiscount();
    }
}

function updatePointsDiscount() {
    const pointsToUse = parseInt(document.getElementById('pointsToUse').value) || 0;
    const subtotal = parseFloat(document.getElementById('modalSubtotal').textContent.replace('$', ''));
    
    if (pointsToUse > 0) {
        const discount = pointsToUse; // 1 point = $1
        const finalTotal = Math.max(0, subtotal - discount);
        
        document.getElementById('modalPointsDiscount').textContent = '-$' + discount.toFixed(2);
        document.getElementById('totalAmount').textContent = '$' + finalTotal.toFixed(2);
        document.getElementById('pointsDiscountRow').classList.remove('hidden');
        
        if (discount >= subtotal) {
            // Payment fully covered by points
            document.getElementById('pointsDiscount').innerHTML = `
                <span class="text-green-600 font-bold">🎉 Fully paid with points!</span><br>
                <span class="text-sm text-green-600">No KHQR payment required - click COMPLETE to finish</span>
            `;
        } else {
            document.getElementById('pointsDiscount').textContent = `You will save $${discount.toFixed(2)}`;
        }
    } else {
        document.getElementById('totalAmount').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('pointsDiscountRow').classList.add('hidden');
        document.getElementById('pointsDiscount').textContent = '';
    }
}

function addToCart(product) {
    const existingItem = cart.find(item => item.product_id === product.id);
    
    // Calculate final price with discount
    const finalPrice = product.discount > 0 
        ? parseFloat(product.price) * (1 - product.discount / 100)
        : parseFloat(product.price);
    
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
            price: finalPrice,
            quantity: 1,
            stock: product.stock,
            image: product.image || null
        });
    }
    
    updateCart();
    saveCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
    saveCart();
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    
    if (newQuantity <= 0) {
        removeFromCart(index);
    } else if (newQuantity <= item.stock) {
        item.quantity = newQuantity;
        updateCart();
        saveCart();
    } else {
        alert('Insufficient stock');
    }
}

function updateCart() {
    const cartItemsDiv = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = `
            <div class="text-gray-500 text-center mt-8">
                <p class="font-semibold">No items in cart</p>
                <p class="khmer-text text-sm mt-1">មិនមានទំនិញក្នុងកន្ត្រក</p>
            </div>
        `;
    } else {
        cartItemsDiv.innerHTML = cart.map((item, index) => `
            <div class="bg-white p-3 border-2 border-gray-400 mb-2 shadow">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h4 class="font-bold text-sm text-gray-900">${item.name}</h4>
                        <p class="text-sm text-gray-700 font-semibold">$${item.price.toFixed(2)}</p>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-red-700 hover:text-red-900 font-bold text-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex items-center justify-between border-t border-gray-300 pt-2">
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(${index}, -1)" class="bg-gray-300 border-2 border-gray-500 w-8 h-8 hover:bg-gray-400 font-bold">-</button>
                        <span class="w-12 text-center font-bold text-gray-900">${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)" class="bg-gray-300 border-2 border-gray-500 w-8 h-8 hover:bg-gray-400 font-bold">+</button>
                    </div>
                    <span class="font-bold text-gray-900">$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            </div>
        `).join('');
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = 0;
    const discount = 0;
    const total = subtotal + tax - discount;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('discount').textContent = '$' + discount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

function clearCart() {
    if (confirm('Clear all items from cart?')) {
        cart = [];
        updateCart();
        saveCart();
    }
}

function openPaymentModal() {
    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('$', ''));
    const total = parseFloat(document.getElementById('total').textContent.replace('$', ''));
    
    // Update modal display
    document.getElementById('modalSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
    
    // Reset customer fields
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerName').value = '';
    document.getElementById('customerAddress').value = '';
    document.getElementById('pointsToUse').value = '0';
    document.getElementById('customerPointsInfo').classList.add('hidden');
    document.getElementById('pointsDiscountRow').classList.add('hidden');
    
    selectedPaymentMethod = 'KHQR';
    currentCustomer = null;
    
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = 'KHQR';
}

async function processSale() {
    const originalTotal = parseFloat(document.getElementById('modalSubtotal').textContent.replace('$', ''));
    const finalTotal = parseFloat(document.getElementById('totalAmount').textContent.replace('$', ''));
    const paid = finalTotal;
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Get customer information
    const customerPhone = document.getElementById('customerPhone').value.trim();
    const customerName = document.getElementById('customerName').value.trim();
    const customerAddress = document.getElementById('customerAddress').value.trim();
    const pointsToUse = parseInt(document.getElementById('pointsToUse').value) || 0;
    
    try {
        const response = await fetch('/pos/complete-sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                items: cart,
                subtotal: subtotal,
                tax: 0,
                discount: 0,
                total: originalTotal, // Original total before points discount
                paid_amount: paid,
                payment_method: selectedPaymentMethod,
                customer_phone: customerPhone || null,
                customer_name: customerName || null,
                customer_address: customerAddress || null,
                points_to_use: pointsToUse
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Store customer info for success message
            if (data.customer_info) {
                window.lastSaleCustomerInfo = data.customer_info;
            }
            
            if (data.paid_with_points) {
                // Payment completed entirely with points
                closePaymentModal();
                showPointsPaymentSuccess(data);
            } else if (data.khqr) {
                // Regular KHQR payment needed
                closePaymentModal();
                showKHQRModal(data.khqr, finalTotal, data.customer_info);
            } else {
                alert('Error: Payment method not determined');
            }
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error processing sale: ' + error.message);
    }
}

function showPointsPaymentSuccess(data) {
    // Clear cart immediately
    cart = [];
    updateCart();
    saveCart();
    
    // Show success message with points information
    let message = '🎉 Payment Completed with Points!\n\n';
    message += `Invoice: ${data.sale.invoice_number}\n`;
    message += `Total: $${data.sale.total}\n`;
    
    if (data.customer_info) {
        const info = data.customer_info;
        message += `\n💳 Points Used: ${info.points_used} points`;
        message += `\n💰 Discount Applied: $${info.points_discount}`;
        if (info.points_earned > 0) {
            message += `\n⭐ Points Earned: ${info.points_earned} points`;
        }
        message += `\n📊 Available Points: ${info.available_points}`;
    }
    
    message += '\n\n✅ No KHQR payment required!';
    message += '\nReceipt sent via Telegram.';
    
    alert(message);
    
    // Clear customer info
    currentCustomer = null;
    window.lastSaleCustomerInfo = null;
}

function showKHQRModal(khqrData, amount, customerInfo = null) {
    currentKHQRData = khqrData;
    khqrTimeRemaining = 1800;
    
    const qrContainer = document.getElementById('khqrCode');
    qrContainer.innerHTML = '';
    new QRCode(qrContainer, {
        text: khqrData.qr_code,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });
    
    document.getElementById('khqrAmount').textContent = '$' + amount.toFixed(2);
    document.getElementById('khqrModal').classList.remove('hidden');
    
    startKHQRCheck();
}

function startKHQRCheck() {
    khqrCheckInterval = setInterval(checkKHQRPaymentStatus, 5000);
    
    khqrTimerInterval = setInterval(() => {
        khqrTimeRemaining--;
        const mins = Math.floor(khqrTimeRemaining / 60);
        const secs = khqrTimeRemaining % 60;
        document.getElementById('khqrTimer').textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        
        if (khqrTimeRemaining <= 0) {
            stopKHQRCheck();
            updateKHQRStatus('expired', 'Payment expired! Please try again.');
        }
    }, 1000);
    
    checkKHQRPaymentStatus();
}

function stopKHQRCheck() {
    if (khqrCheckInterval) {
        clearInterval(khqrCheckInterval);
        khqrCheckInterval = null;
    }
    if (khqrTimerInterval) {
        clearInterval(khqrTimerInterval);
        khqrTimerInterval = null;
    }
}

async function checkKHQRPaymentStatus() {
    if (!currentKHQRData) return;
    
    try {
        const response = await fetch('/pos/check-khqr-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                md5: currentKHQRData.md5
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.status === 'SUCCESS') {
            stopKHQRCheck();
            updateKHQRStatus('success', 'Payment successful!');
            setTimeout(() => {
                closeKHQRModal();
                cart = [];
                updateCart();
                saveCart(); // Clear localStorage
                
                // Show customer points information if available
                let message = 'Payment completed successfully!';
                if (currentCustomer && window.lastSaleCustomerInfo) {
                    const info = window.lastSaleCustomerInfo;
                    if (info.points_earned > 0) {
                        message += `\n\nCustomer earned ${info.points_earned} points!`;
                    }
                    if (info.points_used > 0) {
                        message += `\nUsed ${info.points_used} points (saved $${info.points_discount})`;
                    }
                    message += `\nAvailable points: ${info.available_points}`;
                }
                
                alert(message);
                
                // Clear customer info
                currentCustomer = null;
                window.lastSaleCustomerInfo = null;
            }, 2000);
        } else if (data.status === 'EXPIRED') {
            stopKHQRCheck();
            updateKHQRStatus('expired', 'Payment expired!');
        } else {
            updateKHQRStatus('pending', 'Waiting for payment...');
        }
    } catch (error) {
        console.error('Error checking payment:', error);
    }
}

function checkKHQRPaymentManually() {
    checkKHQRPaymentStatus();
}

function updateKHQRStatus(status, message) {
    const statusDiv = document.getElementById('khqrStatus');
    const statusText = document.getElementById('khqrStatusText');
    
    statusDiv.className = 'mb-4 p-3 border-2 text-center font-semibold';
    
    if (status === 'success') {
        statusDiv.classList.add('bg-green-50', 'border-green-600', 'text-green-900');
        statusText.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
    } else if (status === 'expired') {
        statusDiv.classList.add('bg-red-50', 'border-red-600', 'text-red-900');
        statusText.innerHTML = '<i class="fas fa-times-circle mr-2"></i>' + message;
    } else {
        statusDiv.classList.add('bg-yellow-50', 'border-yellow-600', 'text-yellow-900');
        statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + message;
    }
}

function cancelKHQRPayment() {
    if (confirm('Cancel this payment? The sale will not be completed.')) {
        stopKHQRCheck();
        closeKHQRModal();
    }
}

function closeKHQRModal() {
    stopKHQRCheck();
    document.getElementById('khqrModal').classList.add('hidden');
    document.getElementById('khqrCode').innerHTML = '';
    currentKHQRData = null;
}

document.getElementById('searchProduct').addEventListener('input', handleSmartSearch);
document.getElementById('categoryFilter').addEventListener('change', filterProducts);

// Smart Search Variables
let searchTimeout = null;
let searchDropdown = document.getElementById('searchDropdown');
let isDropdownVisible = false;

function handleSmartSearch(e) {
    const searchTerm = e.target.value.trim();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    if (searchTerm.length === 0) {
        hideSearchDropdown();
        filterProducts(); // Show all products
        return;
    }
    
    if (searchTerm.length < 2) {
        hideSearchDropdown();
        return;
    }
    
    // Debounce search to avoid too many requests
    searchTimeout = setTimeout(() => {
        performSmartSearch(searchTerm);
    }, 300);
}

async function performSmartSearch(searchTerm) {
    try {
        // Show loading state
        showSearchLoading();
        
        const category = document.getElementById('categoryFilter').value;
        const response = await fetch(`/pos/smart-search?search=${encodeURIComponent(searchTerm)}&category_id=${category}`);
        const products = await response.json();
        
        if (products.length > 0) {
            showSearchDropdown(products, searchTerm);
        } else {
            showNoResults(searchTerm);
        }
        
        // Also update the main grid
        filterProducts();
        
    } catch (error) {
        console.error('Smart search error:', error);
        showSearchError();
    }
}

function showSearchLoading() {
    const dropdown = document.getElementById('searchDropdown');
    dropdown.innerHTML = `
        <div class="flex items-center justify-center p-4">
            <div class="search-loading w-full h-12 rounded flex items-center justify-center">
                <i class="fas fa-spinner fa-spin text-gray-500 mr-2"></i>
                <span class="text-gray-500 font-semibold">Searching...</span>
            </div>
        </div>
    `;
    dropdown.classList.remove('hidden');
    isDropdownVisible = true;
}

function showNoResults(searchTerm) {
    const dropdown = document.getElementById('searchDropdown');
    dropdown.innerHTML = `
        <div class="flex items-center justify-center p-6 text-gray-500">
            <div class="text-center">
                <i class="fas fa-search text-3xl text-gray-400 mb-2"></i>
                <p class="font-semibold">No products found</p>
                <p class="khmer-text text-sm mt-1">រកមិនឃើញទំនិញ</p>
                <p class="text-sm mt-2">Try searching for "${searchTerm}" with different keywords</p>
            </div>
        </div>
    `;
    dropdown.classList.remove('hidden');
    isDropdownVisible = true;
}

function showSearchError() {
    const dropdown = document.getElementById('searchDropdown');
    dropdown.innerHTML = `
        <div class="flex items-center justify-center p-4 text-red-500">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span class="font-semibold">Search error. Please try again.</span>
        </div>
    `;
    dropdown.classList.remove('hidden');
    isDropdownVisible = true;
}

function showSearchDropdown(products, searchTerm) {
    const dropdown = document.getElementById('searchDropdown');
    
    const dropdownHTML = products.slice(0, 8).map(product => {
        const imageUrl = product.image 
            ? (product.image.startsWith('http') ? product.image : `/storage/${product.image}`)
            : null;
            
        const finalPrice = product.discount > 0 
            ? (parseFloat(product.price) * (1 - product.discount / 100))
            : parseFloat(product.price);
            
        const stockStatus = product.stock > 0 
            ? `<span class="text-green-600 text-xs font-semibold">In Stock (${product.stock})</span>`
            : `<span class="text-red-600 text-xs font-semibold">Out of Stock</span>`;
            
        const discountBadge = product.discount > 0 
            ? `<span class="bg-red-600 text-white px-2 py-1 text-xs font-bold rounded ml-2">${product.discount}% OFF</span>`
            : '';
            
        // Highlight matching text
        const highlightedName = highlightSearchTerm(product.name, searchTerm);
        
        return `
            <div class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-200 transition-colors"
                 onclick="selectProductFromDropdown(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                <div class="w-16 h-16 flex-shrink-0 mr-3 border border-gray-300 rounded overflow-hidden">
                    ${imageUrl 
                        ? `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">`
                        : `<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                             <i class="fas fa-box text-gray-500 text-lg"></i>
                           </div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="font-semibold text-gray-900 text-sm truncate pr-2">${highlightedName}</h4>
                        <div class="flex items-center">
                            <span class="text-lg font-bold text-gray-900">$${finalPrice.toFixed(2)}</span>
                            ${discountBadge}
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">${product.category.name}</span>
                        ${stockStatus}
                    </div>
                    ${product.discount > 0 
                        ? `<div class="text-xs text-gray-500 mt-1">
                             <span class="line-through">$${parseFloat(product.price).toFixed(2)}</span>
                             <span class="text-green-600 font-semibold ml-1">Save $${(parseFloat(product.price) - finalPrice).toFixed(2)}</span>
                           </div>`
                        : ''
                    }
                </div>
                <div class="ml-3 flex-shrink-0">
                    <button class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-2 text-xs font-bold border border-gray-900 transition-colors">
                        <i class="fas fa-cart-plus mr-1"></i>ADD
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    dropdown.innerHTML = dropdownHTML;
    dropdown.classList.remove('hidden');
    isDropdownVisible = true;
}

function highlightSearchTerm(text, searchTerm) {
    if (!searchTerm) return text;
    
    const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 font-semibold">$1</mark>');
}

function hideSearchDropdown() {
    const dropdown = document.getElementById('searchDropdown');
    dropdown.classList.add('hidden');
    dropdown.innerHTML = '';
    isDropdownVisible = false;
}

function selectProductFromDropdown(product) {
    // Add product to cart
    addToCart(product);
    
    // Clear search and hide dropdown
    document.getElementById('searchProduct').value = '';
    hideSearchDropdown();
    
    // Show success feedback
    showAddToCartFeedback(product.name);
}

function showAddToCartFeedback(productName) {
    // Create temporary success message
    const feedback = document.createElement('div');
    feedback.className = 'fixed top-20 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 font-semibold';
    feedback.innerHTML = `<i class="fas fa-check mr-2"></i>Added "${productName}" to cart`;
    
    document.body.appendChild(feedback);
    
    // Remove after 2 seconds
    setTimeout(() => {
        if (feedback.parentNode) {
            feedback.parentNode.removeChild(feedback);
        }
    }, 2000);
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    const searchInput = document.getElementById('searchProduct');
    const dropdown = document.getElementById('searchDropdown');
    
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
        hideSearchDropdown();
    }
});

// Handle keyboard navigation
document.getElementById('searchProduct').addEventListener('keydown', function(e) {
    if (!isDropdownVisible) return;
    
    const dropdown = document.getElementById('searchDropdown');
    const items = dropdown.querySelectorAll('[onclick*="selectProductFromDropdown"]');
    
    if (e.key === 'Escape') {
        hideSearchDropdown();
        e.preventDefault();
    } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        // Could implement keyboard navigation here if needed
    }
});

function filterProducts() {
    const search = document.getElementById('searchProduct').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    
    fetch(`/pos/products?search=${search}&category_id=${category}`)
        .then(response => response.json())
        .then(products => {
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = products.map(product => {
                const stockBadge = product.stock > 0 
                    ? '<div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 text-xs font-bold border border-green-800">IN STOCK</div>'
                    : '<div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs font-bold border border-red-800">OUT OF STOCK</div>';
                
                const discountBadge = product.discount && product.discount > 0
                    ? `<div class="absolute top-2 left-2 bg-red-700 text-white px-2 py-1 text-xs font-bold border border-red-900">-${product.discount}% OFF</div>`
                    : '';
                
                const imageHtml = product.image
                    ? `<img src="${product.image}" alt="${product.name}" class="w-full h-40 object-cover">`
                    : '<div class="bg-gray-200 h-40 flex items-center justify-center border-b border-gray-300"><i class="fas fa-box text-5xl text-gray-500"></i></div>';
                
                const descriptionHtml = product.description
                    ? `<p class="text-xs text-gray-600 mb-2 line-clamp-2">${product.description}</p>`
                    : '';
                
                // Calculate final price with discount
                const originalPrice = parseFloat(product.price);
                const finalPrice = product.discount > 0 
                    ? originalPrice * (1 - product.discount / 100)
                    : originalPrice;
                
                const priceHtml = product.discount && product.discount > 0
                    ? `<p class="text-xl font-bold text-gray-900">$${finalPrice.toFixed(2)}</p>
                       <p class="text-xs text-gray-500 line-through">$${originalPrice.toFixed(2)}</p>
                       <p class="text-xs text-green-700 font-semibold">${product.discount}% OFF</p>`
                    : `<p class="text-xl font-bold text-gray-900">$${finalPrice.toFixed(2)}</p>`;
                
                const stockColor = product.stock < 10 ? 'text-red-700' : 'text-green-700';
                
                return `
                    <div class="bg-white border-2 border-gray-400 shadow-md hover:shadow-lg cursor-pointer transition-shadow overflow-hidden" 
                        onclick='addToCart(${JSON.stringify(product)})'>
                        <div class="relative border-b-2 border-gray-300">
                            ${imageHtml}
                            ${stockBadge}
                            ${discountBadge}
                        </div>
                        <div class="p-3">
                            <div class="mb-2">
                                <span class="inline-block bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 border border-gray-400">
                                    ${product.category.name}
                                </span>
                            </div>
                            <h3 class="font-bold text-gray-900 text-sm mb-2 line-clamp-2 min-h-[2.5rem]">
                                ${product.name}
                            </h3>
                            ${descriptionHtml}
                            <div class="border-t border-gray-300 pt-2 mb-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        ${priceHtml}
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-600">Stock:</p>
                                        <p class="text-sm font-bold ${stockColor}">${product.stock}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="/pos/product/${product.id}" onclick="event.stopPropagation()" class="flex-1 text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-3 border-2 border-gray-800 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    VIEW
                                </a>
                                <button class="flex-1 bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-3 border-2 border-gray-900 transition-colors">
                                    <i class="fas fa-cart-plus mr-1"></i>
                                    ADD
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        });
}
</script>
<script src="{{ asset('js/pos-cart-images.js') }}"></script>
@endpush
@endsection
