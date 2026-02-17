@extends('layouts.admin')

@section('title', 'Pospay Products')
@section('header', 'PRODUCTS MANAGEMENT')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <button onclick="openAddModal()" class="neo-btn bg-neo-blue text-black px-8 py-4 rounded-xl font-black text-lg">
        <i class="fas fa-plus mr-2"></i>ADD PRODUCT
    </button>
    
    <!-- Quick Stats -->
    <div class="flex space-x-4">
        <div class="neo-card bg-neo-green px-4 py-2 rounded-lg">
            <span class="font-black text-sm">TOTAL: {{ $products->total() }}</span>
        </div>
        <div class="neo-card bg-neo-yellow px-4 py-2 rounded-lg">
            <span class="font-black text-sm">ACTIVE: {{ $products->where('is_active', true)->count() }}</span>
        </div>
    </div>
</div>

<!-- Smart Search & Filters -->
<div class="neo-card bg-white rounded-xl p-6 mb-6 border-3 border-black shadow-[4px_4px_0_#000]">
    <form method="GET" action="{{ route('admin.products') }}" class="space-y-4">
        <!-- Search Bar -->
        <div class="flex space-x-4">
            <div class="flex-1 relative">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="searchInput"
                           value="{{ request('search') }}"
                           placeholder="🔍 Search products by name, SKU, description, or category..."
                           class="neo-input w-full rounded-lg px-4 py-3 pl-12 font-bold text-lg"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Search Suggestions Dropdown -->
                <div id="searchSuggestions" class="hidden absolute top-full left-0 right-0 bg-white border-3 border-black rounded-lg mt-2 shadow-[4px_4px_0_#000] z-50 max-h-96 overflow-y-auto">
                    <!-- Suggestions will be populated here -->
                </div>
            </div>
            <button type="submit" class="neo-btn bg-neo-blue text-black px-8 py-3 rounded-lg font-black">
                SEARCH
            </button>
            @if(request()->hasAny(['search', 'category_filter', 'status_filter', 'stock_filter']))
                <a href="{{ route('admin.products') }}" class="neo-btn bg-gray-300 text-black px-6 py-3 rounded-lg font-black">
                    CLEAR
                </a>
            @endif
        </div>
        
        <!-- Advanced Filters -->
        <div class="grid grid-cols-4 gap-4">
            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-black mb-2 uppercase">Category</label>
                <select name="category_filter" class="neo-input w-full rounded-lg px-3 py-2 font-bold text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_filter') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-black mb-2 uppercase">Status</label>
                <select name="status_filter" class="neo-input w-full rounded-lg px-3 py-2 font-bold text-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <!-- Stock Filter -->
            <div>
                <label class="block text-xs font-black mb-2 uppercase">Stock Status</label>
                <select name="stock_filter" class="neo-input w-full rounded-lg px-3 py-2 font-bold text-sm">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('stock_filter') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_filter') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_filter') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            
            <!-- Sort Options -->
            <div>
                <label class="block text-xs font-black mb-2 uppercase">Sort By</label>
                <div class="flex space-x-2">
                    <select name="sort_by" class="neo-input flex-1 rounded-lg px-3 py-2 font-bold text-sm">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                        <option value="stock" {{ request('sort_by') == 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="category" {{ request('sort_by') == 'category' ? 'selected' : '' }}>Category</option>
                    </select>
                    <select name="sort_order" class="neo-input rounded-lg px-3 py-2 font-bold text-sm">
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑</option>
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Quick Filter Buttons -->
        <div class="flex space-x-2 pt-2">
            <button type="button" onclick="quickFilter('low_stock')" class="neo-btn bg-neo-pink text-black px-4 py-2 rounded-lg text-sm font-black">
                LOW STOCK
            </button>
            <button type="button" onclick="quickFilter('out_of_stock')" class="neo-btn bg-red-400 text-black px-4 py-2 rounded-lg text-sm font-black">
                OUT OF STOCK
            </button>
            <button type="button" onclick="quickFilter('inactive')" class="neo-btn bg-gray-400 text-black px-4 py-2 rounded-lg text-sm font-black">
                INACTIVE
            </button>
        </div>
    </form>
</div>

<!-- Search Results Info -->
@if(request()->hasAny(['search', 'category_filter', 'status_filter', 'stock_filter']))
<div class="mb-4 p-4 bg-neo-yellow rounded-lg border-3 border-black">
    <div class="flex items-center justify-between">
        <div>
            <span class="font-black text-sm">
                SHOWING {{ $products->count() }} OF {{ $products->total() }} PRODUCTS
                @if(request('search'))
                    FOR "{{ request('search') }}"
                @endif
            </span>
        </div>
        <div class="text-xs font-bold">
            @if(request('category_filter'))
                <span class="neo-badge bg-neo-purple px-2 py-1 rounded mr-2">
                    {{ $categories->find(request('category_filter'))->name ?? 'Category' }}
                </span>
            @endif
            @if(request('status_filter'))
                <span class="neo-badge bg-neo-blue px-2 py-1 rounded mr-2">
                    {{ ucfirst(request('status_filter')) }}
                </span>
            @endif
            @if(request('stock_filter'))
                <span class="neo-badge bg-neo-green px-2 py-1 rounded mr-2">
                    {{ str_replace('_', ' ', ucfirst(request('stock_filter'))) }}
                </span>
            @endif
        </div>
    </div>
</div>
@endif

<div class="neo-card bg-white rounded-xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-400 to-purple-400 p-6 border-b-4 border-black">
        <h3 class="text-2xl font-black text-white uppercase tracking-wide">All Products</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full neo-table">
            <thead>
                <tr>
                    <th class="px-6 py-4 text-left">Image</th>
                    <th class="px-6 py-4 text-left">SKU</th>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Category</th>
                    <th class="px-6 py-4 text-left">Price</th>
                    <th class="px-6 py-4 text-left">Stock</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="transition-all duration-200">
                    <td class="px-6 py-4">
                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-16 h-16 object-cover rounded-lg border-3 border-black shadow-[3px_3px_0_#000]">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg border-3 border-black flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-black">{{ $product->sku }}</td>
                    <td class="px-6 py-4 font-bold">{{ $product->name }}</td>
                    <td class="px-6 py-4">
                        <span class="neo-badge bg-neo-purple px-3 py-1 rounded-lg inline-block">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-black text-lg">
                        @if($product->hasDiscount())
                            <div>
                                <span class="line-through text-gray-400 text-sm">${{ number_format($product->price, 2) }}</span>
                                <span class="text-neo-pink ml-2">${{ number_format($product->getDiscountedPrice(), 2) }}</span>
                                <span class="neo-badge bg-neo-pink px-2 py-1 rounded text-xs ml-2">-{{ $product->discount }}%</span>
                            </div>
                        @else
                            ${{ number_format($product->price, 2) }}
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="neo-badge {{ $product->isLowStock() ? 'bg-neo-pink' : 'bg-neo-green' }} px-3 py-1 rounded-lg inline-block">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="neo-badge {{ $product->is_active ? 'bg-neo-green' : 'bg-gray-300' }} px-3 py-1 rounded-lg inline-block">
                            {{ $product->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick='editProduct(@json($product))' class="neo-btn bg-neo-yellow text-black px-4 py-2 rounded-lg mr-2">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="/admin/products/{{ $product->id }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="neo-btn bg-neo-pink text-black px-4 py-2 rounded-lg">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>

<!-- Add/Edit Modal -->
<div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="neo-card bg-white rounded-xl p-8 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <h3 id="modalTitle" class="text-3xl font-black mb-6 uppercase">Add Product</h3>
        <form id="productForm" method="POST" action="/admin/products" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="productId" name="_method" value="POST">
            
            <!-- Image Upload Section -->
            <div class="mb-6 p-6 bg-neo-yellow rounded-xl border-3 border-black shadow-[4px_4px_0_#000]">
                <label class="block text-sm font-black mb-4 uppercase">📸 Product Image</label>
                
                <!-- Image Type Selection -->
                <div class="flex space-x-4 mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="image_type" value="upload" checked onchange="toggleImageInput()" class="w-5 h-5 mr-2">
                        <span class="font-bold uppercase text-sm">Upload File</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="image_type" value="url" onchange="toggleImageInput()" class="w-5 h-5 mr-2">
                        <span class="font-bold uppercase text-sm">Image URL</span>
                    </label>
                    <label id="keepImageOption" class="flex items-center cursor-pointer hidden">
                        <input type="radio" name="image_type" value="keep" onchange="toggleImageInput()" class="w-5 h-5 mr-2">
                        <span class="font-bold uppercase text-sm">Keep Current</span>
                    </label>
                </div>

                <!-- File Upload Input -->
                <div id="uploadInput" class="mb-4">
                    <input type="file" name="image_file" id="image_file" accept="image/*" 
                           onchange="previewImage(event)"
                           class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                    <p class="text-xs font-bold mt-2 text-gray-600">Max 2MB • JPG, PNG, GIF, WEBP</p>
                </div>

                <!-- URL Input -->
                <div id="urlInput" class="mb-4 hidden">
                    <input type="url" name="image_url" id="image_url" placeholder="https://example.com/image.jpg"
                           onchange="previewImageUrl()"
                           class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                    <p class="text-xs font-bold mt-2 text-gray-600">Enter full image URL</p>
                </div>

                <!-- Image Preview -->
                <div id="imagePreview" class="hidden mt-4">
                    <p class="text-xs font-black mb-2 uppercase">Preview:</p>
                    <img id="previewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-3 border-black shadow-[4px_4px_0_#000]">
                </div>

                <!-- Current Image (Edit Mode) -->
                <div id="currentImage" class="hidden mt-4">
                    <p class="text-xs font-black mb-2 uppercase">Current Image:</p>
                    <img id="currentImg" src="" alt="Current" class="w-32 h-32 object-cover rounded-lg border-3 border-black shadow-[4px_4px_0_#000]">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Name *</label>
                    <input type="text" name="name" id="name" required class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">SKU *</label>
                    <input type="text" name="sku" id="sku" required class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Category *</label>
                    <select name="category_id" id="category_id" required class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Price *</label>
                    <input type="number" step="0.01" name="price" id="price" required class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Discount (%)</label>
                    <input type="number" step="0.01" name="discount" id="discount" min="0" max="100" placeholder="0.00" class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Cost</label>
                    <input type="number" step="0.01" name="cost" id="cost" class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Stock *</label>
                    <input type="number" name="stock" id="stock" required class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-black mb-2 uppercase">Min Stock</label>
                    <input type="number" name="min_stock" id="min_stock" class="neo-input w-full rounded-lg px-4 py-3 font-bold">
                </div>
                <div>
                    <label class="flex items-center mt-8">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-6 h-6 mr-3 border-3 border-black">
                        <span class="text-sm font-black uppercase">Active</span>
                    </label>
                </div>
            </div>
            <div class="mt-6">
                <label class="block text-sm font-black mb-2 uppercase">Description</label>
                <textarea name="description" id="description" rows="3" class="neo-input w-full rounded-lg px-4 py-3 font-bold"></textarea>
            </div>
            
            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" onclick="closeModal()" class="neo-btn bg-gray-300 text-black px-6 py-3 rounded-lg">CANCEL</button>
                <button type="submit" class="neo-btn bg-neo-green text-black px-8 py-3 rounded-lg">SAVE</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let searchTimeout;
let currentSearchQuery = '';

// Live search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsDiv = document.getElementById('searchSuggestions');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            hideSuggestions();
            return;
        }
        
        if (query === currentSearchQuery) {
            return;
        }
        
        currentSearchQuery = query;
        
        searchTimeout = setTimeout(() => {
            fetchSearchSuggestions(query);
        }, 300);
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            hideSuggestions();
        }
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const suggestions = suggestionsDiv.querySelectorAll('.suggestion-item');
        const activeSuggestion = suggestionsDiv.querySelector('.suggestion-item.active');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.classList.remove('active');
                const next = activeSuggestion.nextElementSibling;
                if (next) {
                    next.classList.add('active');
                } else {
                    suggestions[0]?.classList.add('active');
                }
            } else {
                suggestions[0]?.classList.add('active');
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.classList.remove('active');
                const prev = activeSuggestion.previousElementSibling;
                if (prev) {
                    prev.classList.add('active');
                } else {
                    suggestions[suggestions.length - 1]?.classList.add('active');
                }
            } else {
                suggestions[suggestions.length - 1]?.classList.add('active');
            }
        } else if (e.key === 'Enter') {
            if (activeSuggestion) {
                e.preventDefault();
                activeSuggestion.click();
            }
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });
}

function fetchSearchSuggestions(query) {
    fetch(`/admin/products/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(products => {
            displaySuggestions(products);
        })
        .catch(error => {
            console.error('Search error:', error);
            hideSuggestions();
        });
}

function displaySuggestions(products) {
    const suggestionsDiv = document.getElementById('searchSuggestions');
    
    if (products.length === 0) {
        suggestionsDiv.innerHTML = '<div class="p-4 text-center text-gray-500 font-bold">No products found</div>';
        suggestionsDiv.classList.remove('hidden');
        return;
    }
    
    const suggestionsHTML = products.map(product => `
        <div class="suggestion-item p-4 border-b border-gray-200 hover:bg-neo-yellow cursor-pointer transition-colors duration-200" 
             onclick="selectProduct('${product.name}')">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 flex-shrink-0">
                    ${product.image ? 
                        `<img src="${product.image}" alt="${product.name}" class="w-12 h-12 object-cover rounded-lg border-2 border-black">` :
                        `<div class="w-12 h-12 bg-gray-200 rounded-lg border-2 border-black flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-black text-sm truncate">${product.name}</div>
                    <div class="text-xs text-gray-600 font-bold">SKU: ${product.sku} • ${product.category}</div>
                    <div class="text-xs font-bold">
                        <span class="text-neo-blue">$${parseFloat(product.price).toFixed(2)}</span>
                        <span class="ml-2 ${product.stock > 0 ? 'text-neo-green' : 'text-neo-pink'}">
                            Stock: ${product.stock}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    suggestionsDiv.innerHTML = suggestionsHTML;
    suggestionsDiv.classList.remove('hidden');
}

function selectProduct(productName) {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = productName;
    hideSuggestions();
    searchInput.form.submit();
}

function hideSuggestions() {
    document.getElementById('searchSuggestions').classList.add('hidden');
}

// Quick filter function
function quickFilter(filterType) {
    const form = document.querySelector('form[method="GET"]');
    const stockFilter = form.querySelector('select[name="stock_filter"]');
    const statusFilter = form.querySelector('select[name="status_filter"]');
    
    // Clear other filters
    stockFilter.value = '';
    statusFilter.value = '';
    
    if (filterType === 'low_stock' || filterType === 'out_of_stock') {
        stockFilter.value = filterType;
    } else if (filterType === 'inactive') {
        statusFilter.value = filterType;
    }
    
    form.submit();
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    const filterSelects = document.querySelectorAll('select[name="category_filter"], select[name="status_filter"], select[name="stock_filter"], select[name="sort_by"], select[name="sort_order"]');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Search on Enter key
    const searchInput = document.querySelector('input[name="search"]');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
});

function toggleImageInput() {
    const imageType = document.querySelector('input[name="image_type"]:checked').value;
    const uploadInput = document.getElementById('uploadInput');
    const urlInput = document.getElementById('urlInput');
    const imagePreview = document.getElementById('imagePreview');
    const currentImage = document.getElementById('currentImage');
    
    uploadInput.classList.add('hidden');
    urlInput.classList.add('hidden');
    
    if (imageType === 'upload') {
        uploadInput.classList.remove('hidden');
        currentImage.classList.add('hidden');
    } else if (imageType === 'url') {
        urlInput.classList.remove('hidden');
        currentImage.classList.add('hidden');
    } else if (imageType === 'keep') {
        currentImage.classList.remove('hidden');
        imagePreview.classList.add('hidden');
    }
}

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

function previewImageUrl() {
    const url = document.getElementById('image_url').value;
    if (url) {
        document.getElementById('previewImg').src = url;
        document.getElementById('imagePreview').classList.remove('hidden');
    }
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'ADD PRODUCT';
    document.getElementById('productForm').action = '/admin/products';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = 'POST';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('currentImage').classList.add('hidden');
    document.getElementById('keepImageOption').classList.add('hidden');
    document.querySelector('input[name="image_type"][value="upload"]').checked = true;
    toggleImageInput();
    document.getElementById('productModal').classList.remove('hidden');
}

function editProduct(product) {
    document.getElementById('modalTitle').textContent = 'EDIT PRODUCT';
    document.getElementById('productForm').action = '/admin/products/' + product.id;
    document.getElementById('productId').value = 'PUT';
    document.getElementById('name').value = product.name;
    document.getElementById('sku').value = product.sku;
    document.getElementById('category_id').value = product.category_id;
    document.getElementById('price').value = product.price;
    document.getElementById('discount').value = product.discount || 0;
    document.getElementById('cost').value = product.cost;
    document.getElementById('stock').value = product.stock;
    document.getElementById('min_stock').value = product.min_stock;
    document.getElementById('description').value = product.description || '';
    document.getElementById('is_active').checked = product.is_active;
    
    // Show current image if exists
    if (product.image) {
        const isUrl = product.image.startsWith('http');
        const imageSrc = isUrl ? product.image : '/storage/' + product.image;
        document.getElementById('currentImg').src = imageSrc;
        document.getElementById('currentImage').classList.remove('hidden');
        document.getElementById('keepImageOption').classList.remove('hidden');
        document.querySelector('input[name="image_type"][value="keep"]').checked = true;
        toggleImageInput();
    } else {
        document.getElementById('currentImage').classList.add('hidden');
        document.getElementById('keepImageOption').classList.add('hidden');
        document.querySelector('input[name="image_type"][value="upload"]').checked = true;
        toggleImageInput();
    }
    
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('productModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('productModal').classList.add('hidden');
}
</script>
@endpush
@endsection
