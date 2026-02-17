# Customer Loyalty Points System

## Overview
The POS system now includes a customer loyalty points program where customers earn points for purchases and can redeem them for discounts.

## Features

### 1. Customer Information Collection
- **Phone Number**: Required to identify returning customers
- **Name**: Optional customer name
- **Address**: Optional street/house number

### 2. Points System
- **Earning Rate**: 1 point for every $100 spent
- **Redemption Rate**: 1 point = $1 discount
- **Automatic Calculation**: Points are automatically calculated and awarded after successful payment

### 3. Points Usage
- Customers can use their available points during checkout
- Points discount is applied before generating the KHQR payment
- Maximum points usage is limited to available points and total purchase amount

## How It Works

### For Cashiers:
1. **During Checkout**: 
   - Enter customer phone number (optional)
   - System automatically loads existing customer information
   - Shows available points balance
   - Allow customer to choose how many points to use

2. **Points Usage**:
   - Enter number of points to use (or click "MAX" button)
   - System calculates discount (1 point = $1)
   - Final payment amount is reduced by points discount

3. **After Payment**:
   - System automatically awards points based on original purchase amount
   - Customer receives notification of points earned/used

### For Customers:
1. **Earning Points**:
   - Spend $100 → Earn 1 point
   - Spend $250 → Earn 2 points
   - Points are awarded after successful payment

2. **Using Points**:
   - 1 point = $1 discount
   - Can use points partially or fully
   - Remaining balance carries over to future purchases

## Database Structure

### Customers Table
- `phone`: Unique customer identifier
- `name`: Customer name (optional)
- `address`: Street/house number (optional)
- `total_spent`: Lifetime spending amount
- `total_points`: Total points ever earned
- `available_points`: Current usable points balance

### Customer Points Table
- Transaction history for all point activities
- Types: `earned`, `redeemed`, `refunded`
- Links to sales for tracking

### Sales Table (Updated)
- `customer_id`: Links to customer record
- `customer_address`: Stored address for receipt
- `points_earned`: Points awarded for this sale
- `points_used`: Points redeemed for this sale
- `points_discount`: Dollar amount of points discount

## API Endpoints

### Get Customer by Phone
```
POST /pos/customer-by-phone
Body: { "phone": "+855123456789" }
Response: { "success": true, "customer": {...} }
```

### Complete Sale (Updated)
```
POST /pos/complete-sale
Body: {
  "items": [...],
  "total": 150.00,
  "customer_phone": "+855123456789",
  "customer_name": "John Doe",
  "customer_address": "123 Main St",
  "points_to_use": 5
}
```

## Testing

### Manual Testing:
1. Go to POS system (`/pos`)
2. Add items to cart
3. Click "COMPLETE SALE"
4. Enter customer phone number
5. System loads customer info and shows available points
6. Choose points to use
7. Complete payment with KHQR
8. Verify points are awarded/deducted correctly

### Test Scenarios:
1. **New Customer**: First purchase should create customer record and award points
2. **Returning Customer**: Should load existing info and show point balance
3. **Points Usage**: Should apply discount and reduce available points
4. **Points Earning**: Should award points based on original purchase amount (before points discount)

## Business Rules

1. **Points Earning**: Based on original purchase amount, not final amount after points discount
2. **Points Expiry**: Currently no expiry (can be added later)
3. **Minimum Purchase**: No minimum purchase required for points earning
4. **Maximum Points Usage**: Cannot use more points than available or more than purchase amount
5. **Fractional Points**: Only whole points are awarded (floor calculation)

## Future Enhancements

1. **Points Expiry**: Add expiration dates for points
2. **Bonus Points**: Special promotions with bonus point multipliers
3. **Tier System**: Different earning rates based on customer spending levels
4. **Points Transfer**: Allow customers to transfer points to others
5. **Refund Handling**: Automatic points adjustment for refunded sales