# SSL Certificate Fix for Google OAuth

## Problem
`cURL error 60: SSL certificate problem: unable to get local issuer certificate`

This happens because PHP's cURL doesn't have the CA certificate bundle needed to verify Google's SSL certificate on Windows.

## ✅ AUTOMATIC FIX (Recommended - No php.ini editing needed!)

### Run the Fix Script
```bash
fix-ssl-certificate.bat
```

**What it does:**
1. Downloads the latest CA certificate bundle from curl.se
2. Saves it as `cacert.pem` in your project root
3. Automatically configures the app to use it (via AppServiceProvider)
4. Starts the Laravel server for testing

**No manual configuration needed!** The app will automatically use the certificate.

## How It Works

The fix is applied in `app/Providers/AppServiceProvider.php`:
- Checks if `cacert.pem` exists in project root
- Sets environment variables `CURL_CA_BUNDLE` and `SSL_CERT_FILE`
- Works automatically for all cURL requests including Google OAuth

## Testing

After running the fix script:
1. Open browser to: http://localhost:8000/login
2. Click "Sign in with Google"
3. SSL error should be gone! ✅

## Alternative: Manual php.ini Configuration

If you prefer to configure php.ini globally:

1. **Download CA Certificate Bundle**
   - Download from: https://curl.se/ca/cacert.pem
   - Save as `cacert.pem` in your project root

2. **Find your php.ini file**
   ```bash
   php --ini
   ```

3. **Edit php.ini**
   Add this line:
   ```ini
   curl.cainfo = "C:\path\to\your\project\cacert.pem"
   ```

4. **Restart your web server**

## Verification

Test the fix:
```bash
php test-google-oauth.php
```

Or visit: http://localhost:8000/login

## Why This Happens

Windows doesn't include a default CA certificate bundle like Linux/Mac. PHP's cURL needs trusted certificates to verify SSL connections to Google's servers.

## Security Note

Always download certificates from the official source (curl.se) to ensure authenticity.
