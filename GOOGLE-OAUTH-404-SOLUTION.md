# 🚨 Google OAuth 404 Error - SOLUTION

## The Problem
You're getting "404 Not Found" when Google redirects to:
```
http://localhost/auth/google/callback
```

## Why This Happens
Apache is NOT routing the URL through Laravel's `public/index.php`. 

The request goes directly to Apache, which looks for a physical file at `/auth/google/callback` and doesn't find it.

## ✅ EASIEST SOLUTION (Do This First!)

### Use Laravel's Built-in Server

**Step 1:** Run this script:
```bash
fix-google-oauth.bat
```
Choose option 1 (Laravel server)

**Step 2:** Update Google Console
1. Go to: https://console.cloud.google.com/apis/credentials
2. Click your OAuth Client ID
3. Under "Authorized redirect URIs", add:
   ```
   http://localhost:8000/auth/google/callback
   ```
4. Click Save

**Step 3:** Test
1. Visit: http://localhost:8000/login
2. Click "Continue with Google"
3. ✅ Should work!

---

## Alternative: Fix Apache (If you must use Apache)

### Check 1: DocumentRoot
Your Apache must serve from the `/public` folder.

**Current issue:** Apache is serving from project root, not `/public`

**Fix for XAMPP:**
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find lines with `DocumentRoot`
3. Change to:
```apache
DocumentRoot "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public"

<Directory "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

### Check 2: mod_rewrite
Must be enabled for .htaccess to work.

**Fix for XAMPP:**
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` (uncomment it)
4. Save and restart Apache

### Check 3: AllowOverride
Must be set to `All` for .htaccess to work.

**Fix for XAMPP:**
1. In `httpd.conf`, find your Directory section
2. Change `AllowOverride None` to `AllowOverride All`
3. Save and restart Apache

### Check 4: .htaccess
Must exist in `/public` folder.

**Already created for you!** File: `public/.htaccess`

### Check 5: Restart Apache
After making changes:
1. Stop Apache in XAMPP Control Panel
2. Start Apache again

### Check 6: Test
Visit: http://localhost/login

If you see the login page, Apache is working correctly.

---

## 🎯 Recommended Setup (Best for Development)

### Create a Virtual Host

This is the cleanest solution for development.

**Step 1:** Edit XAMPP vhosts
1. Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add at the end:
```apache
<VirtualHost *:80>
    ServerName pos.local
    DocumentRoot "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public"
    
    <Directory "D:/BIU2 Y2_S2/1.API_Frame/MiniMarts + KHqr + Telegram + SQL Database/paymentcheckinng/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/pos-error.log"
    CustomLog "logs/pos-access.log" common
</VirtualHost>
```

**Step 2:** Edit hosts file
1. Open as Administrator: `C:\Windows\System32\drivers\etc\hosts`
2. Add:
```
127.0.0.1 pos.local
```

**Step 3:** Update .env
```env
APP_URL=http://pos.local
GOOGLE_REDIRECT_URI=http://pos.local/auth/google/callback
```

**Step 4:** Clear config
```bash
php artisan config:clear
```

**Step 5:** Update Google Console
Add redirect URI:
```
http://pos.local/auth/google/callback
```

**Step 6:** Restart Apache and test
Visit: http://pos.local/login

---

## 🧪 Verify Your Fix

### Test 1: Can you access login page?
```
http://localhost/login
```
or
```
http://localhost:8000/login
```

✅ If YES: Server is working
❌ If NO: Check DocumentRoot

### Test 2: Are routes registered?
```bash
php artisan route:list --path=auth/google
```

Should show:
```
GET auth/google
GET auth/google/callback
```

✅ If YES: Laravel routes are good
❌ If NO: Check routes/web.php

### Test 3: Is .htaccess working?
Create: `public/test.php`
```php
<?php echo "htaccess works!";
```

Visit: `http://localhost/test.php`

✅ If you see "htaccess works!": Apache is serving from /public
❌ If 404: DocumentRoot is wrong

### Test 4: Google OAuth flow
1. Click "Continue with Google"
2. Sign in with Google
3. Should redirect back to your app

✅ If YES: Everything works!
❌ If 404: Check Google Console redirect URI matches exactly

---

## 📋 Quick Checklist

- [ ] .htaccess exists in /public folder
- [ ] mod_rewrite is enabled in Apache
- [ ] AllowOverride is set to All
- [ ] DocumentRoot points to /public folder
- [ ] Apache has been restarted
- [ ] Google Console redirect URI matches .env
- [ ] Config cache is cleared (`php artisan config:clear`)

---

## 🆘 Still Not Working?

### Check Laravel logs:
```
storage/logs/laravel.log
```

### Check Apache error logs:
```
C:\xampp\apache\logs\error.log
```

### Run diagnostics:
```bash
diagnose-google-oauth.bat
```

### Test callback directly:
Visit: `http://localhost/test-google-callback.php`

If you see a message, Apache is working but not routing through Laravel.

---

## 💡 Summary

**The 404 error happens because:**
- Apache doesn't know to route `/auth/google/callback` through Laravel
- This is fixed by either:
  1. Using Laravel's server (easiest)
  2. Configuring Apache DocumentRoot to `/public`
  3. Creating a virtual host (best for development)

**Choose the easiest solution for you and follow the steps above!**
