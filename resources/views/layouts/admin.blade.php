<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pospay Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/khmer-fonts.css') }}">
    
    <!-- Khmer Font Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Dangrek&display=swap" rel="stylesheet">
    <style>
        /* Neo-Brutalism Custom Styles */
        * {
            font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* Khmer Font Classes */
        .khmer-text {
            font-family: 'Khmer OS Dangrek', serif;
            font-weight: normal;
            line-height: 1.6;
        }
        
        .khmer-title {
            font-family: 'Khmer OS Dangrek', serif;
            font-weight: bold;
            line-height: 1.4;
        }
        
        .khmer-small {
            font-family: 'Khmer OS Dangrek', serif;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .khmer-large {
            font-family: 'Khmer OS Dangrek', serif;
            font-size: 1.25rem;
            font-weight: bold;
            line-height: 1.3;
        }
        
        /* Mixed text support (English + Khmer) */
        .mixed-text {
            font-family: 'Khmer OS Dangrek', serif, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Ensure proper rendering for Khmer characters */
        .khmer-text, .khmer-title, .khmer-small, .khmer-large, .mixed-text {
            text-rendering: optimizeLegibility;
            -webkit-font-feature-settings: "liga" 1, "calt" 1;
            font-feature-settings: "liga" 1, "calt" 1;
        }
        
        .neo-card {
            border: 4px solid #000;
            box-shadow: 8px 8px 0 #000;
            transition: all 0.2s ease;
        }
        
        .neo-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 10px 10px 0 #000;
        }
        
        .neo-btn {
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.15s ease;
        }
        
        .neo-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #000;
        }
        
        .neo-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #000;
        }
        
        .neo-input {
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            transition: all 0.2s ease;
        }
        
        .neo-input:focus {
            outline: none;
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #000;
        }
        
        .neo-sidebar {
            border-right: 5px solid #000;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .neo-sidebar-item {
            border: 3px solid transparent;
            transition: all 0.2s ease;
        }
        
        .neo-sidebar-item:hover {
            border: 3px solid #000;
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .neo-sidebar-item.active {
            border: 3px solid #000;
            background: #fff;
            color: #000;
            box-shadow: 4px 4px 0 rgba(0,0,0,0.3);
        }
        
        .neo-stat-card {
            border: 4px solid #000;
            box-shadow: 6px 6px 0 #000;
            position: relative;
            overflow: hidden;
        }
        
        .neo-stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s ease;
        }
        
        .neo-stat-card:hover::before {
            animation: shine 1s ease;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        .neo-table {
            border: 4px solid #000;
        }
        
        .neo-table th {
            background: #000;
            color: #fff;
            border: 3px solid #000;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .neo-table td {
            border: 2px solid #000;
        }
        
        .neo-table tr:hover {
            background: #fef3c7;
            transform: scale(1.01);
        }
        
        .neo-badge {
            border: 2px solid #000;
            box-shadow: 2px 2px 0 #000;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }
        
        .neo-header {
            border-bottom: 5px solid #000;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        /* Color Palette */
        .bg-neo-yellow { background: #fef08a; }
        .bg-neo-pink { background: #fda4af; }
        .bg-neo-blue { background: #93c5fd; }
        .bg-neo-green { background: #86efac; }
        .bg-neo-purple { background: #c4b5fd; }
        .bg-neo-orange { background: #fdba74; }
        
        .text-neo-dark { color: #0f172a; }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-yellow-100 via-pink-100 to-blue-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Neo-Brutalism Sidebar -->
        <aside class="w-72 neo-sidebar text-white flex-shrink-0">
            <div class="p-6 border-b-4 border-black">
                @php
                    $storeName = \App\Models\StoreSetting::get('store_name', 'POSPAY');
                    $storeTagline = \App\Models\StoreSetting::get('store_tagline', 'KHQR PAYMENT SYSTEM');
                    $storeLogo = \App\Models\StoreSetting::get('store_logo');
                @endphp
                
                <div class="flex items-center space-x-4">
                    @if($storeLogo)
                        <img src="{{ asset('storage/' . $storeLogo) }}" 
                             alt="Store Logo" 
                             class="h-12 w-12 rounded-lg border-2 border-white object-cover">
                    @endif
                    <div>
                        <h1 class="text-3xl font-black tracking-tight">{{ strtoupper($storeName) }} ADMIN</h1>
                        <p class="text-sm font-bold mt-1 opacity-90">{{ strtoupper($storeTagline) }}</p>
                    </div>
                </div>
            </div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="neo-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-chart-line mr-3 text-lg"></i> DASHBOARD
                </a>
                <a href="{{ route('pos.index') }}" class="neo-sidebar-item block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-cash-register mr-3 text-lg"></i> POSPAY DASHBOARD
                </a>
                <a href="{{ route('admin.sales') }}" class="neo-sidebar-item {{ request()->routeIs('admin.sales') ? 'active' : '' }} block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-receipt mr-3 text-lg"></i> SALES
                </a>
                <a href="{{ route('admin.products') }}" class="neo-sidebar-item {{ request()->routeIs('admin.products') ? 'active' : '' }} block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-box mr-3 text-lg"></i> PRODUCTS
                </a>
                <a href="{{ route('admin.categories') }}" class="neo-sidebar-item {{ request()->routeIs('admin.categories') ? 'active' : '' }} block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-tags mr-3 text-lg"></i> CATEGORIES
                </a>
                <a href="{{ route('admin.customers') }}" class="neo-sidebar-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }} block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-users mr-3 text-lg"></i> CUSTOMERS
                </a>
                <a href="{{ route('admin.settings') }}" class="neo-sidebar-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }} block px-4 py-4 rounded-lg font-bold">
                    <i class="fas fa-cog mr-3 text-lg"></i> SETTINGS
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-8">
                    @csrf
                    <button type="submit" class="neo-sidebar-item w-full text-left px-4 py-4 rounded-lg font-bold hover:bg-red-500">
                        <i class="fas fa-sign-out-alt mr-3 text-lg"></i> LOGOUT
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Neo-Brutalism Header -->
            <header class="neo-header sticky top-0 z-10">
                <div class="px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-black text-white tracking-tight">@yield('header', 'DASHBOARD')</h2>
                        <p class="text-sm font-bold text-white opacity-90 mt-1">{{ now()->format('l, F j, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="neo-card bg-white px-6 py-3 rounded-lg">
                            <p class="text-xs font-bold text-gray-600">CURRENT TIME</p>
                            <p class="text-xl font-black" id="currentTime">{{ now()->format('H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8">
                @if(session('success'))
                    <div class="neo-card bg-neo-green border-black p-4 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="neo-card bg-red-300 border-black p-4 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                            <span class="font-bold">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Update time every second
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: false });
            const timeEl = document.getElementById('currentTime');
            if (timeEl) timeEl.textContent = timeStr;
        }, 1000);
    </script>
    @stack('scripts')
</body>
</html>
