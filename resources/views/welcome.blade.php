<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSPAY - BRUTALIST POS SYSTEM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700;900&family=IBM+Plex+Mono:wght@400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'IBM Plex Mono', monospace;
            overflow-x: hidden;
            background: #f5f5f5;
        }
        
        .heading-font {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.02em;
        }
        
        /* Brutalist Elements */
        .brutal-border {
            border: 4px solid #000;
            box-shadow: 8px 8px 0 #000;
        }
        
        .brutal-border-thick {
            border: 6px solid #000;
            box-shadow: 12px 12px 0 #000;
        }
        
        .brutal-shadow {
            box-shadow: 8px 8px 0 #000;
        }
        
        .brutal-shadow-hover:hover {
            transform: translate(4px, 4px);
            box-shadow: 4px 4px 0 #000;
        }
        
        /* Scroll Animations */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .scroll-slide-left {
            opacity: 0;
            transform: translateX(-100px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .scroll-slide-left.active {
            opacity: 1;
            transform: translateX(0);
        }
        
        .scroll-slide-right {
            opacity: 0;
            transform: translateX(100px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .scroll-slide-right.active {
            opacity: 1;
            transform: translateX(0);
        }
        
        .scroll-scale {
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .scroll-scale.active {
            opacity: 1;
            transform: scale(1);
        }
        
        /* Parallax Effect */
        .parallax {
            transition: transform 0.1s ease-out;
        }
        
        /* Glitch Effect */
        .glitch {
            position: relative;
        }
        
        .glitch:hover::before,
        .glitch:hover::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .glitch:hover::before {
            animation: glitch-1 0.3s infinite;
            color: #ff00ff;
            z-index: -1;
        }
        
        .glitch:hover::after {
            animation: glitch-2 0.3s infinite;
            color: #00ffff;
            z-index: -2;
        }
        
        @keyframes glitch-1 {
            0%, 100% { transform: translate(0); }
            33% { transform: translate(-2px, 2px); }
            66% { transform: translate(2px, -2px); }
        }
        
        @keyframes glitch-2 {
            0%, 100% { transform: translate(0); }
            33% { transform: translate(2px, -2px); }
            66% { transform: translate(-2px, 2px); }
        }
        
        /* Rotating Border */
        .rotating-border {
            position: relative;
            overflow: hidden;
        }
        
        .rotating-border::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #ff00ff, #00ffff, #ffff00, #ff00ff);
            background-size: 400%;
            animation: rotate-gradient 3s linear infinite;
            z-index: -1;
        }
        
        @keyframes rotate-gradient {
            0% { background-position: 0% 50%; }
            100% { background-position: 400% 50%; }
        }
        
        /* Noise Texture */
        .noise {
            position: relative;
        }
        
        .noise::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.03;
            pointer-events: none;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Button Hover */
        .brutal-btn {
            transition: all 0.2s ease;
        }
        
        .brutal-btn:hover {
            transform: translate(2px, 2px);
        }
        
        .brutal-btn:active {
            transform: translate(4px, 4px);
            box-shadow: 4px 4px 0 #000;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white border-b-4 border-black fixed w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-black flex items-center justify-center brutal-shadow">
                        <span class="text-white text-2xl font-bold">⚡</span>
                    </div>
                    <h1 class="text-3xl font-black heading-font">POSPAY</h1>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('pos.index') }}" class="bg-yellow-400 text-black px-5 py-2 border-2 border-black font-bold uppercase text-sm hover:bg-yellow-300 transition-all brutal-btn">
                            POS
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="bg-white text-black px-5 py-2 border-2 border-black font-bold uppercase text-sm hover:bg-gray-100 transition-all brutal-btn">
                            DASH
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-black text-white px-5 py-2 border-2 border-black font-bold uppercase text-sm hover:bg-gray-900 transition-all brutal-btn">
                                OUT
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-black text-white px-6 py-3 border-2 border-black font-bold uppercase text-sm hover:bg-gray-900 transition-all brutal-btn">
                            LOGIN
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-white pt-32 pb-20 relative noise overflow-hidden">
        <!-- Geometric Shapes -->
        <div class="absolute top-20 right-10 w-32 h-32 bg-yellow-400 border-4 border-black rotate-12 scroll-reveal"></div>
        <div class="absolute bottom-20 left-10 w-40 h-40 bg-pink-400 border-4 border-black -rotate-6 scroll-reveal"></div>
        <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-cyan-400 border-4 border-black rotate-45 scroll-reveal"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <div class="inline-block mb-8 scroll-scale">
                    <span class="bg-black text-white px-6 py-3 border-4 border-black font-bold uppercase text-sm brutal-shadow">
                        🚀 BRUTALIST POS
                    </span>
                </div>
                
                <h1 class="text-7xl md:text-8xl lg:text-9xl font-black heading-font mb-8 leading-none scroll-reveal glitch" data-text="SELL MORE">
                    SELL<br/>MORE
                </h1>
                
                <div class="max-w-3xl mx-auto mb-12 scroll-reveal">
                    <p class="text-xl md:text-2xl font-bold uppercase leading-relaxed">
                        NO BS. JUST PURE POWER. KHQR PAYMENTS + REAL-TIME ANALYTICS + SMART INVENTORY = YOUR SUCCESS
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-center gap-6 mb-16 scroll-reveal">
                    @auth
                        <a href="{{ route('pos.index') }}" class="brutal-btn bg-black text-white px-12 py-5 border-4 border-black font-black text-lg uppercase brutal-shadow-hover inline-block">
                            <span class="flex items-center justify-center space-x-3">
                                <span>OPEN POS</span>
                                <span class="text-2xl">→</span>
                            </span>
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="brutal-btn bg-yellow-400 text-black px-12 py-5 border-4 border-black font-black text-lg uppercase brutal-shadow-hover inline-block">
                            DASHBOARD
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="brutal-btn bg-black text-white px-14 py-6 border-4 border-black font-black text-xl uppercase brutal-shadow-hover inline-block">
                            <span class="flex items-center justify-center space-x-3">
                                <span>START NOW</span>
                                <span class="text-3xl">→</span>
                            </span>
                        </a>
                    @endauth
                </div>
                
                <!-- Stats Bar -->
                <div class="grid grid-cols-3 gap-4 max-w-3xl mx-auto scroll-reveal">
                    <div class="bg-yellow-400 border-4 border-black p-4 brutal-shadow">
                        <div class="text-3xl font-black heading-font">100%</div>
                        <div class="text-xs font-bold uppercase">SECURE</div>
                    </div>
                    <div class="bg-pink-400 border-4 border-black p-4 brutal-shadow">
                        <div class="text-3xl font-black heading-font">24/7</div>
                        <div class="text-xs font-bold uppercase">ONLINE</div>
                    </div>
                    <div class="bg-cyan-400 border-4 border-black p-4 brutal-shadow">
                        <div class="text-3xl font-black heading-font">FAST</div>
                        <div class="text-xs font-bold uppercase">CHECKOUT</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-gray-100 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20 scroll-reveal">
                <div class="inline-block bg-black text-white px-6 py-2 border-4 border-black font-bold uppercase text-sm mb-6 brutal-shadow">
                    FEATURES
                </div>
                <h2 class="text-6xl md:text-7xl font-black heading-font mb-6">
                    EVERYTHING<br/>YOU NEED
                </h2>
                <p class="text-xl font-bold uppercase max-w-2xl mx-auto">
                    POWERFUL TOOLS. ZERO COMPLEXITY.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="scroll-slide-left">
                    <div class="bg-yellow-400 border-4 border-black p-8 brutal-shadow-hover transition-all h-full">
                        <div class="w-20 h-20 bg-black flex items-center justify-center mb-6 border-4 border-black">
                            <span class="text-4xl">💳</span>
                        </div>
                        <h3 class="text-3xl font-black heading-font mb-4">KHQR PAYMENT</h3>
                        <p class="font-bold mb-6 leading-relaxed">
                            INSTANT QR CODE GENERATION. AUTO PAYMENT VERIFICATION. CAMBODIA'S #1 PAYMENT SYSTEM.
                        </p>
                        <ul class="space-y-3 font-bold text-sm">
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>INSTANT QR GENERATION</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>AUTO VERIFICATION</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>SECURE TRANSACTIONS</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="scroll-scale">
                    <div class="bg-pink-400 border-4 border-black p-8 brutal-shadow-hover transition-all h-full">
                        <div class="w-20 h-20 bg-black flex items-center justify-center mb-6 border-4 border-black">
                            <span class="text-4xl">📊</span>
                        </div>
                        <h3 class="text-3xl font-black heading-font mb-4">SALES ANALYTICS</h3>
                        <p class="font-bold mb-6 leading-relaxed">
                            REAL-TIME DASHBOARD. LIVE TRACKING. REVENUE REPORTS. KNOW YOUR NUMBERS.
                        </p>
                        <ul class="space-y-3 font-bold text-sm">
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>LIVE SALES TRACKING</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>REVENUE REPORTS</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>PERFORMANCE INSIGHTS</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="scroll-slide-right">
                    <div class="bg-cyan-400 border-4 border-black p-8 brutal-shadow-hover transition-all h-full">
                        <div class="w-20 h-20 bg-black flex items-center justify-center mb-6 border-4 border-black">
                            <span class="text-4xl">📦</span>
                        </div>
                        <h3 class="text-3xl font-black heading-font mb-4">SMART INVENTORY</h3>
                        <p class="font-bold mb-6 leading-relaxed">
                            STOCK MONITORING. LOW STOCK ALERTS. CATEGORY MANAGEMENT. STAY IN CONTROL.
                        </p>
                        <ul class="space-y-3 font-bold text-sm">
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>STOCK LEVEL MONITORING</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>LOW STOCK ALERTS</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-xl">▸</span>
                                <span>EASY MANAGEMENT</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-24 bg-black text-white relative overflow-hidden">
        <div class="absolute inset-0 noise"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div class="scroll-reveal">
                    <div class="bg-yellow-400 text-black p-8 border-4 border-white brutal-shadow-hover transition-all">
                        <div class="text-6xl font-black heading-font mb-3">⚡</div>
                        <div class="text-4xl font-black heading-font mb-2">FAST</div>
                        <div class="font-bold uppercase text-sm">LIGHTNING CHECKOUT</div>
                    </div>
                </div>
                <div class="scroll-reveal">
                    <div class="bg-pink-400 text-black p-8 border-4 border-white brutal-shadow-hover transition-all">
                        <div class="text-6xl font-black heading-font mb-3">🔒</div>
                        <div class="text-4xl font-black heading-font mb-2">SECURE</div>
                        <div class="font-bold uppercase text-sm">BANK-LEVEL SECURITY</div>
                    </div>
                </div>
                <div class="scroll-reveal">
                    <div class="bg-cyan-400 text-black p-8 border-4 border-white brutal-shadow-hover transition-all">
                        <div class="text-6xl font-black heading-font mb-3">✨</div>
                        <div class="text-4xl font-black heading-font mb-2">SIMPLE</div>
                        <div class="font-bold uppercase text-sm">INTUITIVE INTERFACE</div>
                    </div>
                </div>
                <div class="scroll-reveal">
                    <div class="bg-white text-black p-8 border-4 border-white brutal-shadow-hover transition-all">
                        <div class="text-6xl font-black heading-font mb-3">🚀</div>
                        <div class="text-4xl font-black heading-font mb-2">MODERN</div>
                        <div class="font-bold uppercase text-sm">LATEST TECHNOLOGY</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-28 bg-white relative overflow-hidden noise">
        <!-- Geometric Decorations -->
        <div class="absolute top-10 left-10 w-40 h-40 bg-yellow-400 border-4 border-black rotate-12 scroll-reveal"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-pink-400 border-4 border-black -rotate-12 scroll-reveal"></div>
        
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="scroll-scale">
                <div class="bg-black text-white p-16 border-8 border-black brutal-border-thick">
                    <h2 class="text-5xl md:text-7xl font-black heading-font mb-8 leading-tight">
                        READY TO<br/>DOMINATE?
                    </h2>
                    <p class="text-xl md:text-2xl font-bold uppercase mb-12 max-w-2xl mx-auto">
                        JOIN THOUSANDS OF BUSINESSES CRUSHING IT WITH POSPAY
                    </p>
                    @auth
                        <a href="{{ route('pos.index') }}" class="brutal-btn inline-block bg-yellow-400 text-black px-14 py-6 border-4 border-white font-black text-xl uppercase brutal-shadow-hover">
                            <span class="flex items-center justify-center space-x-3">
                                <span>START SELLING</span>
                                <span class="text-3xl">→</span>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="brutal-btn inline-block bg-yellow-400 text-black px-14 py-6 border-4 border-white font-black text-xl uppercase brutal-shadow-hover">
                            <span class="flex items-center justify-center space-x-3">
                                <span>GET STARTED</span>
                                <span class="text-3xl">→</span>
                            </span>
                        </a>
                    @endauth
                    
                    <div class="mt-10 flex flex-wrap justify-center items-center gap-6 text-white/80 font-bold text-sm uppercase">
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-400 text-xl">✓</span>
                            <span>NO CREDIT CARD</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-400 text-xl">✓</span>
                            <span>5 MIN SETUP</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-400 text-xl">✓</span>
                            <span>CANCEL ANYTIME</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white py-16 border-t-8 border-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-yellow-400 flex items-center justify-center border-4 border-white">
                            <span class="text-3xl">⚡</span>
                        </div>
                        <h3 class="text-4xl font-black heading-font">POSPAY</h3>
                    </div>
                    <p class="font-bold uppercase leading-relaxed mb-6 max-w-md text-gray-300">
                        BRUTALIST POS SYSTEM. KHQR PAYMENTS. BUILT FOR BUSINESSES THAT MOVE FAST.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 bg-white text-black hover:bg-yellow-400 flex items-center justify-center border-2 border-white transition-all font-bold">
                            F
                        </a>
                        <a href="#" class="w-12 h-12 bg-white text-black hover:bg-pink-400 flex items-center justify-center border-2 border-white transition-all font-bold">
                            T
                        </a>
                        <a href="#" class="w-12 h-12 bg-white text-black hover:bg-cyan-400 flex items-center justify-center border-2 border-white transition-all font-bold">
                            G
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-xl font-black heading-font mb-6 uppercase">QUICK LINKS</h4>
                    <ul class="space-y-3">
                        @auth
                            <li>
                                <a href="{{ route('pos.index') }}" class="font-bold uppercase text-sm hover:text-yellow-400 transition-colors flex items-center space-x-2">
                                    <span>▸</span>
                                    <span>POS SYSTEM</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="font-bold uppercase text-sm hover:text-yellow-400 transition-colors flex items-center space-x-2">
                                    <span>▸</span>
                                    <span>DASHBOARD</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.products') }}" class="font-bold uppercase text-sm hover:text-yellow-400 transition-colors flex items-center space-x-2">
                                    <span>▸</span>
                                    <span>PRODUCTS</span>
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}" class="font-bold uppercase text-sm hover:text-yellow-400 transition-colors flex items-center space-x-2">
                                    <span>▸</span>
                                    <span>LOGIN</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-xl font-black heading-font mb-6 uppercase">FEATURES</h4>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-center space-x-2 font-bold uppercase text-sm">
                            <span class="text-yellow-400">✓</span>
                            <span>KHQR PAYMENT</span>
                        </li>
                        <li class="flex items-center space-x-2 font-bold uppercase text-sm">
                            <span class="text-pink-400">✓</span>
                            <span>SALES ANALYTICS</span>
                        </li>
                        <li class="flex items-center space-x-2 font-bold uppercase text-sm">
                            <span class="text-cyan-400">✓</span>
                            <span>INVENTORY</span>
                        </li>
                        <li class="flex items-center space-x-2 font-bold uppercase text-sm">
                            <span class="text-white">✓</span>
                            <span>TELEGRAM ALERTS</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t-4 border-white pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="font-bold uppercase text-sm text-gray-300">
                        © {{ date('Y') }} POSPAY. ALL RIGHTS RESERVED.
                    </p>
                    <div class="flex space-x-6 text-sm font-bold uppercase">
                        <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors">PRIVACY</a>
                        <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors">TERMS</a>
                        <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors">CONTACT</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        // Scroll Animation Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, observerOptions);
        
        // Observe all scroll reveal elements
        document.querySelectorAll('.scroll-reveal, .scroll-slide-left, .scroll-slide-right, .scroll-scale').forEach(el => {
            observer.observe(el);
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 8px 0 #000';
            } else {
                navbar.style.boxShadow = 'none';
            }
        });
        
        // Parallax effect for geometric shapes
        document.addEventListener('mousemove', (e) => {
            const shapes = document.querySelectorAll('.parallax');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 20;
                const x = (mouseX - 0.5) * speed;
                const y = (mouseY - 0.5) * speed;
                shape.style.transform = `translate(${x}px, ${y}px) rotate(${shape.dataset.rotation || 0}deg)`;
            });
        });
    </script>
</body>
</html>
