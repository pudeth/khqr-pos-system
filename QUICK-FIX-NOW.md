# ⚡ QUICK FIX - Do This Now!

## 🎯 3-Step Fix (5 minutes)

### Step 1: Start the Server (1 minute)

Double-click this file:
```
start-with-google-oauth.bat
```

Wait for: `Server started on http://localhost:8000`

**Keep this window open!**

---

### Step 2: Update Google Console (2 minutes)

1. **Open:** https://console.cloud.google.com/apis/credentials

2. **Click** on your OAuth 2.0 Client ID:
   ```
   298504334910-cagsl2v0d900onh9e5qpg2ftservda1f.apps.googleusercontent.com
   ```

3. **Scroll down** to "Authorized redirect URIs"

4. **Click** "+ ADD URI"

5. **Paste** this EXACT URL:
   ```
   http://localhost:8000/auth/google/callback
   ```

6. **Click** "SAVE" at the bottom

---

### Step 3: Test It (2 minutes)

1. **Open browser:** http://localhost:8000/login

2. **Click:** "Continue with Google" button

3. **Sign in** with any Google account

4. **Success!** You should be logged in and redirected to POS

---

## ✅ That's It!

Your Google OAuth login is now working!

---

## 🔍 What We Fixed

**The Problem:**
- Apache on port 80 wasn't routing URLs through Laravel
- Google callback URL was getting 404 error

**The Solution:**
- Use Laravel's built-in server on port 8000
- Update Google Console redirect URI to match
- Everything works perfectly!

---

## 📝 For Production

When you deploy to production:

1. Update `.env`:
```env
APP_URL=https://yourdomain.com
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

2. Update Google Console redirect URI:
```
https://yourdomain.com/auth/google/callback
```

3. Clear config:
```bash
php artisan config:clear
```

---

## 🆘 Troubleshooting

### "redirect_uri_mismatch" error?
- Make sure you clicked SAVE in Google Console
- Wait 1-2 minutes for changes to take effect
- Check the URI is EXACTLY: `http://localhost:8000/auth/google/callback`

### Still getting 404?
- Make sure the server is running (check the terminal window)
- Try: http://localhost:8000/login (not just localhost)
- Clear browser cache and try again

### "Unable to login with Google"?
- Check `storage/logs/laravel.log` for error details
- Make sure database is running
- Run: `php artisan migrate` to ensure tables exist

---

## 🎊 Success!

Once you see the POS screen after Google login, you're all set!

New customers will be automatically created with role "customer" and can start shopping immediately.
