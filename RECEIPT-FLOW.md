# Payment to Receipt Flow

## Complete Transaction Flow

```
┌─────────────────┐
│  POS: Create    │
│  Sale + Items   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Generate KHQR  │
│  Payment Record │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Customer Pays  │
│  via Bakong     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Check Payment  │ ◄─── Manual Check (POS)
│  Status         │ ◄─── Auto Check (Scheduler)
└────────┬────────┘
         │
         ▼
    ┌────────┐
    │SUCCESS?│
    └───┬────┘
        │ YES
        ▼
┌─────────────────┐
│  Mark Payment   │
│  as SUCCESS     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Find Sale      │
│  + Items        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Send Receipt   │
│  to Telegram    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Mark Telegram  │
│  Sent = TRUE    │
└─────────────────┘
```

## Key Components

### 1. Sale Creation (POSController::completeSale)
- Creates sale record with items
- Generates KHQR payment
- Links payment to sale via `payment_id`

### 2. Payment Checking (Multiple Entry Points)
- **POS Frontend**: Polls payment status every 3 seconds
- **Manual Check**: PaymentController::checkPayment
- **Auto Check**: CheckPendingPayments command (runs every minute)

### 3. Receipt Generation (TelegramService::sendReceipt)
- Formats complete receipt with all details
- Sends to configured Telegram chat
- Returns success/failure status

### 4. Tracking (Payment Model)
- `telegram_sent` flag prevents duplicate receipts
- `status` tracks payment state (PENDING/SUCCESS/EXPIRED)
- `transaction_id` stores Bakong confirmation

## Database Relationships

```
payments (1) ──┐
               │
               ▼
sales (1) ─────┬──► users (cashier)
               │
               ▼
sale_items (N) ──► products
```

## Configuration Required

1. **Telegram Bot** (.env)
   - TELEGRAM_BOT_TOKEN
   - TELEGRAM_CHAT_ID

2. **Bakong KHQR** (.env)
   - BAKONG_API_URL
   - BAKONG_API_KEY
   - Merchant details

3. **Scheduler** (must be running)
   ```bash
   start-scheduler.bat
   ```

## Testing Checklist

- [ ] Telegram bot configured and tested
- [ ] KHQR generation working
- [ ] Payment checking functional
- [ ] Receipt format correct
- [ ] Scheduler running
- [ ] No duplicate receipts sent
- [ ] Fallback works (payment without sale)
