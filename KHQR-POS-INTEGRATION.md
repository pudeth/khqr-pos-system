# 🔵 KHQR Bakong Payment Integration - POS System

## ✅ Integration Complete!

Your POS System now supports **KHQR Bakong payments** for seamless digital transactions!

---

## 🎯 What's New

### KHQR Payment Features
- ✅ Generate KHQR codes for each sale
- ✅ Real-time payment verification
- ✅ Auto-check payment status every 5 seconds
- ✅ 30-minute payment expiry
- ✅ Telegram notifications on successful payment
- ✅ Automatic stock deduction after payment
- ✅ Payment tracking in database

---

## 🚀 How to Use KHQR in POS

### For Cashiers:

1. **Add Products to Cart**
   - Browse or search for products
   - Click products to add to cart
   - Adjust quantities as needed

2. **Select KHQR Payment**
   - Click "Complete Sale"
   - Select "KHQR" payment method
   - Click "Complete"

3. **Show QR Code to Customer**
   - A QR code will be displayed
   - Customer scans with Bakong app
   - System automatically checks for payment

4. **Payment Confirmation**
   - System checks every 5 seconds
   - Green checkmark appears when paid
   - Sale is automatically completed
   - Telegram notification sent

---

## 📱 Customer Payment Flow

1. Customer sees QR code on screen
2. Opens Bakong app (or any KHQR-compatible app)
3. Scans the QR code
4. Confirms payment in app
5. Payment is instantly verified
6. Receipt is generated

---

## 🔧 Technical Details

### Payment Process

```
1. Cart → Complete Sale → Select KHQR
2. System generates KHQR code
3. QR code displayed to customer
4. Customer scans and pays
5. System checks Bakong API every 5 seconds
6. Payment confirmed → Sale completed
7. Stock updated automatically
8. Telegram notification sent
```

### Database Tables Used

**payments** table:
- Stores KHQR payment details
- MD5 hash for tracking
- QR code string
- Payment status (PENDING/SUCCESS/EXPIRED)
- Expiry time (30 minutes)

**sales** table:
- Links to payment via payment_id
- Stores sale details
- Payment method = 'KHQR'

---

## ⚙️ Configuration

Your KHQR settings are in `.env`:

```env
# Bakong KHQR API
BAKONG_API_URL=https://api-bakong.nbc.gov.kh
BAKONG_TOKEN=your_token_here

# Merchant Info
MERCHANT_BAKONG_ID=your_bakong_id@bank
MERCHANT_NAME="Your Store Name"
MERCHANT_CITY="PHNOM PENH"
ACQUIRING_BANK="Your Bank"
```

---

## 🎨 POS Interface Updates

### Payment Modal
- **Cash** - Traditional cash payment
- **KHQR** - Bakong QR code payment ✨ NEW
- **Card** - Credit/debit card payment

### KHQR Modal Features
- Large QR code display (256x256px)
- Payment amount shown
- Real-time status updates
- Countdown timer (30:00)
- Auto-check indicator
- Manual check button
- Cancel option

---

## 📊 Payment Status Flow

```
PENDING → Waiting for customer to scan and pay
    ↓
SUCCESS → Payment received, sale completed
    ↓
EXPIRED → 30 minutes passed, payment cancelled
```

---

## 🔍 Payment Verification

### Automatic Checking
- Checks every **5 seconds**
- Runs for **30 minutes** (1800 seconds)
- Stops when payment is successful
- Stops when payment expires

### Manual Checking
- Click "Check Now" button
- Immediately queries Bakong API
- Updates status in real-time

---

## 💡 Features

### For Merchants
- ✅ No cash handling needed
- ✅ Instant payment confirmation
- ✅ Automatic record keeping
- ✅ Telegram notifications
- ✅ Reduced transaction errors
- ✅ Digital payment tracking

### For Customers
- ✅ Fast and secure payment
- ✅ No need for cash
- ✅ Works with any KHQR app
- ✅ Instant confirmation
- ✅ Digital receipt

---

## 🔐 Security Features

- ✅ MD5 hash for payment tracking
- ✅ 30-minute expiry for QR codes
- ✅ CRC16 checksum validation
- ✅ Secure API communication
- ✅ Payment status verification
- ✅ Database transaction safety

---

## 📱 Compatible Apps

Customers can pay using:
- **Bakong** (National Bank of Cambodia)
- **ABA Mobile**
- **ACLEDA Mobile**
- **Wing**
- **Pi Pay**
- Any KHQR-compatible banking app

---

## 🎯 Testing KHQR Payment

### Test Flow:

1. **Start Server**
   ```bash
   php artisan serve
   ```

2. **Login to POS**
   - URL: http://localhost:8000/pos
   - Email: admin@pos.com
   - Password: password

3. **Make a Test Sale**
   - Add products to cart
   - Click "Complete Sale"
   - Select "KHQR" payment
   - Click "Complete"

4. **View QR Code**
   - QR code appears
   - Status shows "Waiting for payment..."
   - Timer counts down from 30:00

5. **Simulate Payment** (Development)
   - In production, customer scans with Bakong app
   - In development, you can manually mark as paid in database

---

## 🗄️ Database Queries

### Check KHQR Payments
```sql
SELECT * FROM payments 
WHERE payment_method = 'KHQR' 
ORDER BY created_at DESC;
```

### Check Sales with KHQR
```sql
SELECT s.*, p.status as payment_status, p.md5
FROM sales s
LEFT JOIN payments p ON s.payment_id = p.id
WHERE s.payment_method = 'KHQR'
ORDER BY s.created_at DESC;
```

### Today's KHQR Revenue
```sql
SELECT SUM(total) as khqr_revenue
FROM sales
WHERE payment_method = 'KHQR'
AND DATE(created_at) = CURDATE();
```

---

## 🔄 Payment Lifecycle

### 1. Generation
```php
POST /pos/complete-sale
{
    "payment_method": "KHQR",
    "total": 25.99,
    "items": [...]
}
```

### 2. Verification
```php
POST /pos/check-khqr-payment
{
    "md5": "abc123..."
}
```

### 3. Completion
- Payment status → SUCCESS
- Sale record updated
- Stock deducted
- Telegram notification sent

---

## 📞 API Endpoints

### Generate KHQR (Internal)
- **Endpoint:** `/pos/complete-sale`
- **Method:** POST
- **Auth:** Required
- **Returns:** QR code, MD5, payment ID

### Check Payment Status
- **Endpoint:** `/pos/check-khqr-payment`
- **Method:** POST
- **Auth:** Required
- **Returns:** Payment status

---

## 🎨 UI Components

### KHQR Modal Elements
- QR Code Display (256x256px)
- Amount Display
- Status Indicator
- Timer Countdown
- Check Now Button
- Cancel Button

### Status Colors
- 🟡 Yellow: Waiting for payment
- 🟢 Green: Payment successful
- 🔴 Red: Payment expired

---

## 📈 Benefits

### Speed
- Payment in seconds
- No cash counting
- Instant verification

### Accuracy
- No calculation errors
- Automatic record keeping
- Digital audit trail

### Security
- No cash handling
- Encrypted transactions
- Verified payments

### Convenience
- Works with any KHQR app
- No special hardware needed
- Mobile-friendly

---

## 🛠️ Troubleshooting

### QR Code Not Generating
- Check Bakong API credentials in `.env`
- Verify internet connection
- Check Laravel logs: `storage/logs/laravel.log`

### Payment Not Detected
- Ensure customer completed payment in app
- Click "Check Now" to manually verify
- Check payment status in database
- Verify Bakong API token is valid

### Payment Expired
- Generate new QR code
- Complete sale again
- Customer must pay within 30 minutes

---

## 📝 Next Steps

1. ✅ Test KHQR payment flow
2. ✅ Train staff on KHQR process
3. ✅ Display KHQR acceptance signage
4. ✅ Monitor payment success rate
5. ✅ Review Telegram notifications

---

## 🎉 Summary

Your POS system now supports:
- ✅ Cash payments
- ✅ KHQR Bakong payments (NEW!)
- ✅ Card payments

All payment methods are fully integrated with:
- Inventory management
- Sales tracking
- Reporting
- Telegram notifications

**Start accepting KHQR payments today!** 🚀

---

**Integration Date:** February 10, 2026  
**Status:** ✅ FULLY OPERATIONAL
