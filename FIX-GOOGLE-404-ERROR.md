# 🔧 Fix Google OAuth 404 Error

## Problem
When Google redirects back to your app, you get "404 Not Found" error.

## Root Cause
Apache is not routing `/auth/google/callback` through Laravel's `index.php`.

## ✅ Solutions (Try in order)

### Solution 1: Check Apache Configuration

Your Apache DocumentRoot must point to the `/public` folder.

**For XAMPP:**
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find: `DocumentRoot`
3. Change to:
```apache
DocumentRoot "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public"
<Directory "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public">
```

4. Restart Apache

### Solution 2: Enable mod_rewrite

**For XAMPP:**
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` to uncomment it
4. Find: `AllowOverride None`
5. Change to: `AllowOverride All`
6. Restart Apache

### Solution 3: Use PHP Built-in Server (Easiest!)

Instead of Apache, use Laravel's built-in server:

```bash
php artisan serve --port=80
```

Or use port 8000:
```bash
php artisan serve
```

Then update Google Console redirect URI to:
```
http://localhost:8000/auth/google/callback
```

And update `.env`:
```env
APP_URL=http://localhost:8000
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Then run:
```bash
php artisan config:clear
```

### Solution 4: Create Virtual Host (Recommended for Development)

**For XAMPP:**

1. Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

2. Add:
```apache
<VirtualHost *:80>
    ServerName pos.local
    DocumentRoot "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public"
    
    <Directory "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Open: `C:\Windows\System32\drivers\etc\hosts` (as Administrator)

4. Add:
```
127.0.0.1 pos.local
```

5. Restart Apache

6. Update `.env`:
```env
APP_URL=http://pos.local
GOOGLE_REDIRECT_URI=http://pos.local/auth/google/callback
```

7. Update Google Console redirect URI to:
```
http://pos.local/auth/google/callback
```

8. Clear config:
```bash
php artisan config:clear
```

9. Access: http://pos.local/login

## 🧪 Test Your Fix

### 1. Test .htaccess is working:
Visit: http://localhost/test-google-callback.php

If you see a message, Apache is working but not routing through Laravel.

### 2. Test Laravel routing:
```bash
php artisan route:list --path=auth/google
```

Should show both routes.

### 3. Test direct access:
Visit: http://localhost/login

Should show login page.

### 4. Test Google OAuth:
1. Click "Continue with Google"
2. Should redirect to Google
3. After login, should come back to your app (not 404)

## 🎯 Quick Fix (Recommended)

**Use Laravel's built-in server:**

1. Stop Apache/XAMPP

2. Run:
```bash
php artisan serve
```

3. Update `.env`:
```env
APP_URL=http://localhost:8000
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

4. Clear config:
```bash
php artisan config:clear
```

5. Update Google Console redirect URI:
```
http://localhost:8000/auth/google/callback
```

6. Test: http://localhost:8000/login

## ✅ Verify Google Console Settings

Go to: https://console.cloud.google.com/apis/credentials

Make sure "Authorized redirect URIs" includes the EXACT URL:
- If using Apache on port 80: `http://localhost/auth/google/callback`
- If using `php artisan serve`: `http://localhost:8000/auth/google/callback`
- If using virtual host: `http://pos.local/auth/google/callback`

## 📝 Current Setup Check

Run diagnostic:
```bash
diagnose-google-oauth.bat
```

This will show your current configuration and help identify the issue.

## 🆘 Still Not Working?

Check Laravel logs:
```
storage/logs/laravel.log
```

The error message will tell you exactly what's wrong.
