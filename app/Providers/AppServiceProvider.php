<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix SSL certificate issue for Google OAuth on Windows
        $this->configureSslForSocialite();
    }

    /**
     * Configure SSL certificate for Socialite (Google OAuth)
     */
    protected function configureSslForSocialite(): void
    {
        // Check if cacert.pem exists in project root
        $certPath = base_path('cacert.pem');
        
        if (file_exists($certPath)) {
            // Set environment variables for cURL
            putenv("CURL_CA_BUNDLE={$certPath}");
            putenv("SSL_CERT_FILE={$certPath}");
            
            // Set ini settings for immediate effect
            ini_set('curl.cainfo', $certPath);
            ini_set('openssl.cafile', $certPath);
            
            // Configure default HTTP context for all requests
            $context = [
                'ssl' => [
                    'cafile' => $certPath,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ],
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Laravel/Socialite',
                ]
            ];
            
            // Set default stream context
            $streamContext = stream_context_create($context);
            stream_context_set_default($context);
            
            // Configure Guzzle HTTP client for Socialite
            $this->configureGuzzleForSsl($certPath);
            
            // Log for debugging
            \Log::info('SSL Certificate configured for Google OAuth', [
                'path' => $certPath,
                'exists' => true,
                'size' => filesize($certPath),
                'curl_ca_bundle' => getenv('CURL_CA_BUNDLE'),
                'ssl_cert_file' => getenv('SSL_CERT_FILE')
            ]);
        } else {
            \Log::error('SSL Certificate not found - Google OAuth will fail', [
                'expected_path' => $certPath
            ]);
        }
    }
    
    /**
     * Configure Guzzle HTTP client for SSL
     */
    protected function configureGuzzleForSsl($certPath)
    {
        // Set default Guzzle configuration for Socialite
        $this->app->bind(\GuzzleHttp\Client::class, function () use ($certPath) {
            return new \GuzzleHttp\Client([
                'verify' => $certPath,
                'timeout' => 30,
                'connect_timeout' => 10,
                'curl' => [
                    CURLOPT_CAINFO => $certPath,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                ]
            ]);
        });
    }
}
