<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pospay-POS-Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Neo-Brutalism Colors */
        .bg-neo-yellow { background-color: #fef08a; }
        .bg-neo-pink { background-color: #fda4af; }
        .bg-neo-blue { background-color: #93c5fd; }
        .bg-neo-green { background-color: #86efac; }
        .bg-neo-purple { background-color: #c4b5fd; }
        .bg-neo-orange { background-color: #fdba74; }

        /* Neo Card */
        .neo-card {
            border: 4px solid #000;
            box-shadow: 8px 8px 0 #000;
            border-radius: 16px;
            transition: all 0.2s ease;
        }

        .neo-card:hover {
            transform: translateY(-2px);
            box-shadow: 10px 10px 0 #000;
        }

        /* Neo Input */
        .neo-input {
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.2s ease;
            background: white;
        }

        .neo-input:focus {
            outline: none;
            transform: translateY(-2px);
            box-shadow: 6px 6px 0 #000;
        }

        .neo-input.error {
            border-color: #ef4444;
            box-shadow: 4px 4px 0 #ef4444;
        }

        /* Neo Button */
        .neo-btn {
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            border-radius: 8px;
            padding: 14px 24px;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .neo-btn:hover {
            transform: translateY(-2px);
            box-shadow: 6px 6px 0 #000;
        }

        .neo-btn:active {
            transform: translateY(2px);
            box-shadow: 2px 2px 0 #000;
        }

        /* Neo Checkbox */
        .neo-checkbox {
            width: 24px;
            height: 24px;
            border: 3px solid #000;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Float Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Gradient Background */
        .gradient-bg {
            background: linear-gradient(135deg, #fef08a 0%, #fda4af 25%, #93c5fd 50%, #c4b5fd 75%, #86efac 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Logo Badge */
        .neo-badge {
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            border-radius: 12px;
            padding: 8px 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    
    <div class="neo-card bg-white p-8 md:p-12 w-full max-w-md float-animation">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block neo-badge bg-neo-yellow mb-4">
                <span class="text-2xl">🏪</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-black uppercase tracking-tight mb-2">
                POSPAY-POS-DASHBOARD
            </h1>
            <p class="text-lg font-bold text-black uppercase tracking-wide">
                KHQR Payment System - Sign in to continue
            </p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <!-- Email Field -->
            <div>
                <label class="block text-black text-sm font-bold mb-2 uppercase tracking-wide">
                    📧 Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="neo-input w-full @error('email') error @enderror" 
                    placeholder="admin@pos.com"
                    required 
                    autofocus
                >
                @error('email')
                    <p class="text-red-500 text-sm font-bold mt-2">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-black text-sm font-bold mb-2 uppercase tracking-wide">
                    🔒 Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    class="neo-input w-full @error('password') error @enderror" 
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <p class="text-red-500 text-sm font-bold mt-2">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="remember" 
                    id="remember"
                    class="neo-checkbox"
                >
                <label for="remember" class="ml-3 text-sm font-bold text-black uppercase tracking-wide cursor-pointer">
                    Remember me
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="neo-btn bg-neo-blue w-full text-black">
                🚀 Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t-3 border-black"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-4 text-sm font-bold text-black uppercase">Or</span>
            </div>
        </div>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google') }}" class="neo-btn bg-white w-full text-black flex items-center justify-center gap-3 hover:bg-gray-50">
            <svg class="w-6 h-6" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>Continue with Google</span>
        </a>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs font-bold text-black uppercase tracking-wide">
                Pay From Bank: KHQR Payment System 💳
            </p>
        </div>
    </div>

</body>
</html>
