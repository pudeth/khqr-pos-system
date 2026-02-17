# 🔧 Fix Google OAuth SSL Error - Quick Guide

## The Error
```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

## ⚡ Quick Fix (2 minutes)

### Step 1: Run the Fix Script
```bash
fix-ssl-certificate.bat
```

### Step 2: Test
The script will automatically start the server. Then:
1. Open: http://localhost:8000/login
2. Click "Sign in with Google"
3. Done! ✅

## What Was Fixed?

The script:
- ✅ Downloaded CA certificate bundle (cacert.pem)
- ✅ Configured AppServiceProvider to use it automatically
- ✅ No php.ini editing required!

## How It Works

The fix is in `app/Providers/AppServiceProvider.php`:
```php
protected function configureSslForSocialite(): void
{
    $certPath = base_path('cacert.pem');
    
    if (file_exists($certPath)) {
        putenv("CURL_CA_BUNDLE={$certPath}");
        putenv("SSL_CERT_FILE={$certPath}");
    }
}
```

This automatically configures cURL to trust Google's SSL certificates.

## Troubleshooting

### If the error persists:

1. **Check cacert.pem exists**
   ```bash
   dir cacert.pem
   ```

2. **Clear Laravel cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Restart the server**
   ```bash
   php artisan serve
   ```

### Still not working?

Try the manual php.ini method in `SSL-CERTIFICATE-FIX.md`

## Next Steps

Once fixed, you can:
- ✅ Login with Google
- ✅ Create customer accounts via Google OAuth
- ✅ Access the POS system

Happy coding! 🚀
