<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    public function redirectToGoogle()
    {
        // Apply SSL certificate fix before redirecting
        $this->applySslFix();
        
        // Configure Socialite with SSL settings
        $driver = Socialite::driver('google');
        
        // Set additional configuration for SSL
        $driver->setHttpClient(new \GuzzleHttp\Client([
            'verify' => base_path('cacert.pem'),
            'timeout' => 30,
            'curl' => [
                CURLOPT_CAINFO => base_path('cacert.pem'),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]
        ]));
        
        return $driver->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Apply SSL certificate fix before making API calls
            $this->applySslFix();
            
            // Configure Socialite with SSL settings
            $driver = Socialite::driver('google');
            $driver->setHttpClient(new \GuzzleHttp\Client([
                'verify' => base_path('cacert.pem'),
                'timeout' => 30,
                'curl' => [
                    CURLOPT_CAINFO => base_path('cacert.pem'),
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                ]
            ]));
            
            $googleUser = $driver->user();
            
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update existing user with Google info
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new customer user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'customer',
                    'is_active' => true,
                ]);
            }

            Auth::login($user, true);
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect('/admin/dashboard');
            }
            
            return redirect('/pos');

        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            
            return redirect('/login')->withErrors([
                'email' => 'Unable to login with Google: ' . $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Apply SSL certificate fix for cURL requests
     */
    protected function applySslFix()
    {
        $certPath = base_path('cacert.pem');
        
        if (file_exists($certPath)) {
            // Set environment variables
            putenv("CURL_CA_BUNDLE={$certPath}");
            putenv("SSL_CERT_FILE={$certPath}");
            
            // Set ini settings
            ini_set('curl.cainfo', $certPath);
            ini_set('openssl.cafile', $certPath);
            
            // Configure Socialite to use our SSL certificate
            config(['services.google.guzzle' => [
                'verify' => $certPath,
                'timeout' => 30,
                'curl' => [
                    CURLOPT_CAINFO => $certPath,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                ]
            ]]);
            
            \Log::info('SSL fix applied for Google OAuth request', [
                'cert_path' => $certPath,
                'cert_size' => filesize($certPath)
            ]);
        } else {
            \Log::error('SSL certificate file not found: ' . $certPath);
            throw new Exception('SSL certificate configuration error. Please ensure cacert.pem exists.');
        }
    }
}
