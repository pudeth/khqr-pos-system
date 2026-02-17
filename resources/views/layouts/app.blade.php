<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pospay-POS-Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/khmer-fonts.css') }}">
    
    <!-- Khmer Font Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Dangrek&display=swap" rel="stylesheet">
    
    <style>
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
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    @yield('content')
    
    <script>
        // CSRF token setup for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>
    @stack('scripts')
</body>
</html>
