# Enhanced Telegram Receipt Format

## Overview
The Telegram receipt now includes complete Bakong transaction details matching the official payment receipt format.

## New Information Included

### Bank Details
- **Bank Name**: ACLEDA Bank Plc. (or configured bank)
- **Transaction Hash**: Bakong hash ID (e.g., `a39eb77b`)
- **Reference Number**: Full transaction reference (e.g., `100FT36774348398`)
- **From Account**: Payer's account name and number (e.g., `Pu Deth (004 164 074)`)

### Receipt Format

```
🧾 PAYMENT RECEIPT
━━━━━━━━━━━━━━━━━━━━

🏪 PuDeth Smart-PAY
📋 Invoice: INV-20260210-0001
🕐 Feb 10, 2026 12:58 PM

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

💳 Payment Method: KHQR (Bakong)
✅ Status: PAID
🏦 Bank: ACLEDA Bank Plc.
🔑 Bakong Hash: a39eb77b
📄 Reference: 100FT36774348398
💼 From: Pu Deth (004 164 074)

👤 Served by: Admin User

━━━━━━━━━━━━━━━━━━━━
Thank you for your purchase! 🙏
Powered by ABA Bank 🏦
```

## Data Sources

### From Payment Model
- `transaction_id` → Bakong Hash
- `bakong_response['data']['reference']` → Reference Number
- `bakong_response['data']['from_account_name']` → From Account

### From Sale Model
- `invoice_number` → Invoice
- `customer_name` → Customer
- `customer_phone` → Phone
- `items` → Product list
- `subtotal`, `tax`, `discount`, `total` → Amounts

### From Configuration
- `MERCHANT_NAME` → Store name
- Bank name (ACLEDA Bank Plc.)

## Implementation Details

### TelegramService Enhancement
The `formatReceiptMessage()` method now:
1. Extracts Bakong transaction details from payment response
2. Formats date/time in readable format (Feb 10, 2026 12:58 PM)
3. Includes bank name and transaction identifiers
4. Uses monospace formatting for transaction codes
5. Adds ABA Bank branding footer

### Controller Updates
All three payment checking points now pass enhanced data:
- **POSController**: Extracts Bakong data when checking KHQR payment
- **PaymentController**: Includes transaction details in receipt
- **CheckPendingPayments**: Scheduler sends complete receipt with all info

## Testing

Run the test script to see the enhanced format:
```bash
php test-receipt.php
```

Check your Telegram bot for the complete receipt with all transaction details!

## Benefits

✅ **Complete Transaction Record** - All Bakong details included
✅ **Professional Format** - Matches official receipt style
✅ **Easy Verification** - Transaction hash and reference for tracking
✅ **Customer Transparency** - Shows payer account information
✅ **Bank Branding** - Includes bank name and logo reference
✅ **Audit Trail** - Complete information for accounting

## Configuration

Ensure your `.env` has the correct merchant name:
```env
MERCHANT_NAME="PuDeth Smart-PAY"
```

The bank name is automatically set to **ACLEDA Bank Plc.** based on your Bakong ID (`@aclb`).
