-- Insert Users
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
('Admin', 'admin@pos.com', '$2y$12$h4aipBjfryKrYOkd4Ok4ueafm0mBXEcA5/M6zJ2huxvty91WwudrO', 'admin', 1, NOW(), NOW()),
('Cashier', 'cashier@pos.com', '$2y$12$h4aipBjfryKrYOkd4Ok4ueafm0mBXEcA5/M6zJ2huxvty91WwudrO', 'cashier', 1, NOW(), NOW());

-- Insert Categories
INSERT INTO `categories` (`name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('Electronics', 'Electronic devices and accessories', 1, NOW(), NOW()),
('Clothing', 'Apparel and fashion items', 1, NOW(), NOW()),
('Food & Beverages', 'Food and drink products', 1, NOW(), NOW()),
('Books', 'Books and publications', 1, NOW(), NOW()),
('Home & Garden', 'Home and garden supplies', 1, NOW(), NOW());

-- Insert Products
INSERT INTO `products` (`category_id`, `name`, `sku`, `price`, `cost`, `stock`, `min_stock`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Wireless Mouse', 'ELEC-001', 25.99, 15.00, 50, 10, 1, NOW(), NOW()),
(1, 'USB Cable', 'ELEC-002', 9.99, 5.00, 100, 20, 1, NOW(), NOW()),
(1, 'Bluetooth Speaker', 'ELEC-003', 49.99, 30.00, 30, 5, 1, NOW(), NOW()),
(1, 'Phone Charger', 'ELEC-004', 19.99, 10.00, 75, 15, 1, NOW(), NOW()),
(2, 'T-Shirt', 'CLO-001', 15.99, 8.00, 60, 10, 1, NOW(), NOW()),
(2, 'Jeans', 'CLO-002', 39.99, 20.00, 40, 8, 1, NOW(), NOW()),
(2, 'Cap', 'CLO-003', 12.99, 6.00, 45, 10, 1, NOW(), NOW()),
(3, 'Coffee', 'FOOD-001', 8.99, 4.00, 80, 20, 1, NOW(), NOW()),
(3, 'Energy Drink', 'FOOD-002', 2.99, 1.50, 120, 30, 1, NOW(), NOW()),
(3, 'Snack Bar', 'FOOD-003', 1.99, 0.80, 150, 40, 1, NOW(), NOW()),
(4, 'Novel', 'BOOK-001', 14.99, 8.00, 35, 5, 1, NOW(), NOW()),
(4, 'Magazine', 'BOOK-002', 5.99, 3.00, 50, 10, 1, NOW(), NOW()),
(5, 'Plant Pot', 'HOME-001', 12.99, 6.00, 40, 8, 1, NOW(), NOW()),
(5, 'Candle', 'HOME-002', 7.99, 3.50, 60, 12, 1, NOW(), NOW()),
(5, 'Picture Frame', 'HOME-003', 18.99, 10.00, 25, 5, 1, NOW(), NOW());
