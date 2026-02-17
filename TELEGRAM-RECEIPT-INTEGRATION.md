# Telegram Receipt Integration

## Overview
The POS system now automatically sends detailed receipts to your Telegram bot when payments are confirmed via KHQR.

## How It Works

### Payment Flow
1. **Sale Created** → KHQR code generated and payment record created
2. **Customer Scans QR** → Payment processed through Bakong
3. **Payment Confirmed** → System detects successful payment
4. **Receipt Sent** → Complete receipt automatically sent to Telegram

### Receipt Format
The Telegram receipt includes:
- 🏪 Store name and invoice number
- 👥 Customer information (if provided)
- 📦 Itemized list of products with quantities and prices
- 💰 Subtotal, tax, discount, and total
- ✅ Payment status and transaction ID
- 👤 Cashier name

## Integration Points

### 1. POS Controller (`POSController.php`)
When checking KHQR payment status, the system:
- Finds the associated sale record
- Gathers all sale items and details
- Sends complete receipt via Telegram

### 2. Payment Controller (`PaymentController.php`)
When manually checking payment status:
- Looks up associated sale if exists
- Sends receipt with full details
- Falls back to simple payment notification if no sale found

### 3. Scheduled Command (`CheckPendingPayments.php`)
Background job that runs every minute:
- Checks all pending payments
- Confirms successful payments with Bakong
- Sends receipts automatically when payment confirmed

## Testing

### Test Receipt Format
Run the test script to see how receipts look:
```bash
php test-receipt.php
```

### Test Real Payment Flow
1. Create a sale in the POS
2. Generate KHQR code
3. Scan and pay (or simulate payment)
4. Check your Telegram bot for the receipt

## Configuration

Make sure your `.env` file has:
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

## Features

✅ **Automatic Receipt Delivery** - No manual intervention needed
✅ **Complete Sale Details** - All items, prices, and totals included
✅ **Customer Information** - Name and phone if provided
✅ **Transaction Tracking** - Bakong transaction ID included
✅ **Formatted for Readability** - Clean, professional receipt format
✅ **Fallback Support** - Simple notification if sale details unavailable

## Receipt Example

```
🧾 PAYMENT RECEIPT
━━━━━━━━━━━━━━━━━━━━
🏪 My POS Store
📋 Invoice: INV-20260210-0001
🕐 2026-02-10 14:30:45

👥 Customer: John Doe
📱 Phone: +855123456789

📦 ITEMS:
━━━━━━━━━━━━━━━━━━━━
Coca Cola
  2 x $1.50 = $3.00
Sandwich
  1 x $5.00 = $5.00
Coffee
  3 x $2.50 = $7.50

━━━━━━━━━━━━━━━━━━━━
Subtotal: $15.50
Tax: $1.55

💰 TOTAL: $17.05
━━━━━━━━━━━━━━━━━━━━

💳 Payment: KHQR
✅ Status: PAID
🔑 Transaction: TXN123456789

👤 Served by: Admin User

━━━━━━━━━━━━━━━━━━━━
Thank you for your purchase! 🙏
```

## Notes

- Receipts are sent only once per payment (tracked via `telegram_sent` flag)
- If Telegram fails, the sale still completes successfully
- Background scheduler checks pending payments every minute
- Receipts include all sale items with proper formatting
