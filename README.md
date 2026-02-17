# KHQR Payment System with Automatic Payment Checking

A robust Laravel-based KHQR (Khmer QR) payment system with automatic payment verification every 10 seconds.

## 🚀 Features

- **Automatic Payment Checking**: Payments are checked every 10 seconds in the background
- **Persistent Storage**: All payments are stored in database with full audit trail
- **30-minute Expiry**: QR codes expire after 30 minutes (configurable)
- **Telegram Notifications**: Automatic notifications when payments are successful
- **Real-time Frontend**: Live payment status updates in the browser
- **Retry Logic**: Automatic retry for failed API calls
- **Background Processing**: Works even when browser is closed

## 🔧 What Was Fixed

### Previous Issues:
1. ❌ Payment checking only worked when browser was open
2. ❌ 4-minute timeout regardless of payment status
3. ❌ No database storage - payments were lost on refresh
4. ❌ No background processing
5. ❌ No retry mechanism for API failures
6. ❌ Telegram notifications sent multiple times

### New Solution:
1. ✅ **Background Command**: `payments:check` runs every 10 seconds automatically
2. ✅ **Database Storage**: All payments stored with full tracking
3. ✅ **30-minute Expiry**: Extended timeout with proper expiry handling
4. ✅ **Smart Checking**: Avoids duplicate API calls and rate limiting
5. ✅ **Telegram Control**: Notifications sent only once per payment
6. ✅ **Error Handling**: Comprehensive logging and error recovery

## 📋 Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
Make sure your `.env` file has:
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khqr_payment
DB_USERNAME=root
DB_PASSWORD=

# Bakong API
BAKONG_API_URL=https://api-bakong.nbc.gov.kh
BAKONG_TOKEN=your_bakong_token_here

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_CHAT_ID=your_telegram_chat_id

# Merchant Info
MERCHANT_BAKONG_ID=your_bakong_id
MERCHANT_NAME="Your Business Name"
MERCHANT_CITY="PHNOM PENH"
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Start the Application
```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start automatic payment checking
php artisan schedule:work
```

Or use the provided scripts:
- **Windows CMD**: `start-scheduler.bat`
- **PowerShell**: `start-scheduler.ps1`

## 🎯 How It Works

### Payment Flow:
1. **Generate QR**: User creates payment QR code
2. **Store in DB**: Payment saved with PENDING status
3. **Auto-Check**: Background command checks every 10 seconds
4. **API Call**: Calls Bakong API to verify payment status
5. **Update Status**: Updates database when payment succeeds
6. **Send Notification**: Telegram notification sent once
7. **Frontend Update**: Browser shows real-time status

### Database Schema:
```sql
payments table:
- id, md5 (unique), qr_code, amount, currency
- status (PENDING/SUCCESS/FAILED/EXPIRED)
- expires_at, paid_at, telegram_sent
- check_attempts, last_checked_at
- bakong_response (JSON), transaction_id
```

## 🔍 Monitoring & Debugging

### Check Payment Status Manually:
```bash
php artisan payments:check
```

### View Logs:
```bash
tail -f storage/logs/laravel.log
```

### Database Queries:
```sql
-- View all payments
SELECT * FROM payments ORDER BY created_at DESC;

-- View pending payments
SELECT * FROM payments WHERE status = 'PENDING';

-- View successful payments
SELECT * FROM payments WHERE status = 'SUCCESS';
```

## 🛠 API Endpoints

- `POST /api/payment/generate-qr` - Generate QR code
- `POST /api/payment/check` - Check payment status
- `POST /api/payment/status` - Get payment by ID
- `POST /api/payment/verify` - Verify QR format
- `POST /api/payment/decode` - Decode QR data
- `POST /api/payment/deep-link` - Generate app deep links

## ⚙️ Configuration

### Adjust Check Frequency:
Edit `app/Console/Kernel.php`:
```php
// Check every 5 seconds
$schedule->command('payments:check')->everyFiveSeconds();

// Check every 30 seconds
$schedule->command('payments:check')->everyThirtySeconds();
```

### Change Expiry Time:
Edit `PaymentController.php`:
```php
'expires_at' => now()->addMinutes(60), // 1 hour expiry
```

### Limit Concurrent Checks:
Edit `CheckPendingPayments.php`:
```php
->limit(100) // Process max 100 at a time
```

## 🚨 Troubleshooting

### Payment Not Detected:
1. Check if scheduler is running: `php artisan schedule:work`
2. Verify Bakong token in `.env`
3. Check logs: `storage/logs/laravel.log`
4. Test API manually: `php artisan payments:check`

### Telegram Not Working:
1. Verify bot token and chat ID
2. Check if bot has permission to send messages
3. Test with: `php artisan tinker` then `app(TelegramService::class)->sendMessage('test')`

### Database Issues:
1. Run migrations: `php artisan migrate`
2. Check database connection: `php artisan tinker` then `DB::connection()->getPdo()`

## 📊 Performance Notes

- **API Rate Limiting**: Checks are spaced 30 seconds apart per payment
- **Database Indexing**: Optimized queries with proper indexes
- **Memory Usage**: Processes max 50 payments per run
- **Background Processing**: Non-blocking, won't affect web requests

## 🔐 Security

- **Input Validation**: All inputs validated and sanitized
- **SQL Injection**: Using Eloquent ORM with parameter binding
- **API Security**: Bearer token authentication for Bakong API
- **Error Handling**: Sensitive data not exposed in error messages

Your payment system is now fully automated and will check payments every 10 seconds in the background! 🎉