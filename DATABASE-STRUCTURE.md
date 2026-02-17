# 📊 POS System - Database Structure

## Database: khqr_payment

### Connection Details
- **Host:** 127.0.0.1
- **Port:** 3306
- **Database:** khqr_payment
- **Username:** root
- **Password:** (empty)

---

## 📋 Tables Overview

### 1. users
**Purpose:** Store admin and cashier accounts

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR(191) | User's full name |
| email | VARCHAR(191) | Login email (unique) |
| password | VARCHAR(191) | Hashed password |
| role | ENUM('admin','cashier') | User role |
| is_active | BOOLEAN | Account status |
| remember_token | VARCHAR(100) | Session token |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

**Sample Data:**
- Admin: admin@pos.com / password
- Cashier: cashier@pos.com / password

---

### 2. categories
**Purpose:** Organize products into categories

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR(191) | Category name |
| description | TEXT | Category description |
| is_active | BOOLEAN | Active status |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

**Sample Data:**
- Electronics
- Clothing
- Food & Beverages
- Books
- Home & Garden

---

### 3. products
**Purpose:** Store product inventory

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| category_id | BIGINT | Foreign key to categories |
| name | VARCHAR(191) | Product name |
| sku | VARCHAR(100) | Stock keeping unit (unique) |
| description | TEXT | Product description |
| price | DECIMAL(10,2) | Selling price |
| cost | DECIMAL(10,2) | Cost price |
| stock | INTEGER | Current stock level |
| min_stock | INTEGER | Minimum stock alert level |
| image | VARCHAR(255) | Product image path |
| is_active | BOOLEAN | Active status |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

**Indexes:**
- sku (unique)
- category_id (foreign key)

**Sample Data:** 15 products across 5 categories

---

### 4. sales
**Purpose:** Store sales transactions

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| invoice_number | VARCHAR(100) | Unique invoice (INV-YYYYMMDD-0001) |
| user_id | BIGINT | Foreign key to users (cashier) |
| subtotal | DECIMAL(10,2) | Sum of items |
| tax | DECIMAL(10,2) | Tax amount |
| discount | DECIMAL(10,2) | Discount amount |
| total | DECIMAL(10,2) | Final total |
| paid_amount | DECIMAL(10,2) | Amount paid by customer |
| change_amount | DECIMAL(10,2) | Change given |
| payment_method | ENUM('CASH','KHQR','CARD') | Payment type |
| payment_id | BIGINT | Foreign key to payments (for KHQR) |
| customer_name | VARCHAR(191) | Customer name (optional) |
| customer_phone | VARCHAR(50) | Customer phone (optional) |
| notes | TEXT | Additional notes |
| created_at | TIMESTAMP | Sale date/time |
| updated_at | TIMESTAMP | Last update |

**Indexes:**
- invoice_number (unique)
- user_id (foreign key)
- payment_id (foreign key)
- created_at (for reports)

---

### 5. sale_items
**Purpose:** Store individual items in each sale

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| sale_id | BIGINT | Foreign key to sales |
| product_id | BIGINT | Foreign key to products |
| product_name | VARCHAR(191) | Product name (snapshot) |
| price | DECIMAL(10,2) | Price at time of sale |
| quantity | INTEGER | Quantity sold |
| subtotal | DECIMAL(10,2) | price × quantity |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

**Indexes:**
- sale_id (foreign key)
- product_id (foreign key)

---

### 6. payments (Existing)
**Purpose:** Store KHQR payment records

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| md5 | VARCHAR(32) | Unique payment identifier |
| qr_code | TEXT | QR code data |
| amount | DECIMAL(10,2) | Payment amount |
| currency | VARCHAR(3) | Currency code (USD) |
| bill_number | VARCHAR(255) | Bill reference |
| mobile_number | VARCHAR(255) | Customer mobile |
| store_label | VARCHAR(255) | Store identifier |
| terminal_label | VARCHAR(255) | Terminal identifier |
| merchant_name | VARCHAR(255) | Merchant name |
| status | ENUM | PENDING/SUCCESS/FAILED/EXPIRED |
| bakong_response | JSON | API response |
| transaction_id | VARCHAR(255) | Transaction ID |
| expires_at | TIMESTAMP | Expiration time |
| paid_at | TIMESTAMP | Payment time |
| telegram_sent | BOOLEAN | Notification sent |
| check_attempts | INTEGER | Check count |
| last_checked_at | TIMESTAMP | Last check time |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

---

## 🔗 Relationships

### One-to-Many
- **categories → products**: One category has many products
- **users → sales**: One user (cashier) has many sales
- **sales → sale_items**: One sale has many items
- **products → sale_items**: One product appears in many sales

### Optional One-to-One
- **payments → sales**: One KHQR payment can be linked to one sale

---

## 📊 Database Diagram

```
┌─────────────┐
│   users     │
│  (2 rows)   │
└──────┬──────┘
       │
       │ user_id
       ▼
┌─────────────┐      ┌──────────────┐
│ categories  │      │   payments   │
│  (5 rows)   │      │  (existing)  │
└──────┬──────┘      └──────┬───────┘
       │                    │
       │ category_id        │ payment_id
       ▼                    ▼
┌─────────────┐      ┌─────────────┐
│  products   │      │    sales    │
│ (15 rows)   │◄─────┤  (dynamic)  │
└──────┬──────┘      └──────┬──────┘
       │                    │
       │ product_id         │ sale_id
       │                    │
       └────────┬───────────┘
                ▼
         ┌─────────────┐
         │ sale_items  │
         │  (dynamic)  │
         └─────────────┘
```

---

## 🔍 Key Queries

### Get Today's Sales Total
```sql
SELECT SUM(total) as today_sales 
FROM sales 
WHERE DATE(created_at) = CURDATE();
```

### Get Low Stock Products
```sql
SELECT * FROM products 
WHERE stock <= min_stock 
AND is_active = 1;
```

### Get Sales with Items
```sql
SELECT s.*, u.name as cashier_name, 
       COUNT(si.id) as item_count
FROM sales s
LEFT JOIN users u ON s.user_id = u.id
LEFT JOIN sale_items si ON s.id = si.sale_id
GROUP BY s.id
ORDER BY s.created_at DESC;
```

### Get Product Sales Report
```sql
SELECT p.name, p.sku, 
       SUM(si.quantity) as total_sold,
       SUM(si.subtotal) as total_revenue
FROM products p
LEFT JOIN sale_items si ON p.id = si.product_id
GROUP BY p.id
ORDER BY total_sold DESC;
```

### Get Category Performance
```sql
SELECT c.name, 
       COUNT(DISTINCT p.id) as product_count,
       SUM(si.quantity) as items_sold,
       SUM(si.subtotal) as revenue
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
LEFT JOIN sale_items si ON p.id = si.product_id
GROUP BY c.id;
```

---

## 🛠️ Maintenance

### Backup Database
```bash
mysqldump -u root khqr_payment > backup.sql
```

### Restore Database
```bash
mysql -u root khqr_payment < backup.sql
```

### Reset to Fresh State
```bash
php artisan migrate:fresh
php seed-data.php
```

### View Table Structure
```sql
DESCRIBE table_name;
```

### Check Table Sizes
```sql
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)"
FROM information_schema.TABLES
WHERE table_schema = "khqr_payment"
ORDER BY (data_length + index_length) DESC;
```

---

## 📈 Performance Indexes

All tables include appropriate indexes for:
- Primary keys (id)
- Foreign keys (category_id, user_id, product_id, sale_id)
- Unique constraints (email, sku, invoice_number)
- Search fields (created_at for date filtering)

---

## 🔒 Security Notes

- Passwords are hashed using bcrypt
- Foreign key constraints maintain referential integrity
- Soft deletes not implemented (use is_active flags)
- CSRF protection on all forms
- SQL injection protection via Laravel's query builder

---

## 📝 Migration Files

All migrations are located in `database/migrations/`:
1. `2026_02_04_032402_create_payments_table.php` (existing)
2. `2026_02_10_000001_create_categories_table.php`
3. `2026_02_10_000002_create_products_table.php`
4. `2026_02_10_000003_create_sales_table.php`
5. `2026_02_10_000004_create_sale_items_table.php`
6. `2026_02_10_000005_create_users_table.php`

Run migrations: `php artisan migrate`
