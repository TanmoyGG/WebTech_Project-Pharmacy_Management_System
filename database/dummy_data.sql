    -- Pharmacy Management System - Dummy Data for Testing
-- ⚠️ RUN THIS ENTIRE SCRIPT AT ONCE, NOT SECTION BY SECTION

-- DISABLE FOREIGN KEY CHECKS FOR ENTIRE OPERATION
SET FOREIGN_KEY_CHECKS = 0;

-- Step 1: CLEAR EXISTING DATA FIRST
DELETE FROM cart_items;
DELETE FROM carts;
DELETE FROM order_items;
DELETE FROM orders;
DELETE FROM transactions;
DELETE FROM users;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM system_config;

-- Step 2: Insert Users (admin, inventory_manager, customers)
INSERT INTO users (name, email, phone, password, role, status) VALUES
('Admin User', 'admin@pharmacy.com', '01700000001', 'password123', 'admin', 'active'),
('Inventory Manager', 'inventory@pharmacy.com', '01700000002', 'password123', 'inventory_manager', 'active'),
('Test Customer 1', 'customer1@pharmacy.com', '01700000003', 'password123', 'customer', 'active'),
('Test Customer 2', 'customer2@pharmacy.com', '01700000004', 'password123', 'customer', 'active'),
('Test Customer 3', 'customer3@pharmacy.com', '01700000005', 'password123', 'customer', 'active');

-- Step 3: Insert Categories (MUST run before products!)
INSERT INTO categories (name, description) VALUES
('Pain Relievers', 'Painkillers and anti-inflammatory medicines'),
('Antibiotics', 'Antibiotic medications for infections'),
('Vitamins & Supplements', 'Vitamins, minerals and dietary supplements'),
('Cough & Cold', 'Cough syrups and cold medicines'),
('Digestive', 'Antacids and digestive aids'),
('Allergy', 'Antihistamines and allergy relief');

-- Step 4: Insert Products
INSERT INTO products (name, generic_name, category_id, description, price, quantity, low_stock_threshold, manufacture_date, expiry_date, status) VALUES
('Napa Extra', 'Paracetamol 500mg', 1, 'Effective pain reliever and fever reducer', 2.50, 500, 50, '2024-01-01', '2026-01-01', 'available'),
('Aspirin', 'Acetylsalicylic Acid 500mg', 1, 'For pain relief and anti-inflammation', 3.00, 400, 40, '2024-01-01', '2026-01-01', 'available'),
('Ibuprofen Plus', 'Ibuprofen 400mg', 1, 'Strong painkiller for severe pain', 5.50, 300, 30, '2024-01-01', '2026-01-01', 'available'),
('Amoxicillin 500mg', 'Amoxicillin', 2, 'Antibiotic for bacterial infections', 15.00, 250, 25, '2024-01-01', '2026-01-01', 'available'),
('Azithromycin 500mg', 'Azithromycin', 2, 'Broad spectrum antibiotic', 20.00, 200, 20, '2024-01-01', '2026-01-01', 'available'),
('Cefalexin 500mg', 'Cefalexin', 2, 'First generation cephalosporin', 18.00, 180, 18, '2024-01-01', '2026-01-01', 'available'),
('Vitamin C 1000mg', 'Ascorbic Acid', 3, 'Immune system booster', 8.00, 600, 60, '2024-01-01', '2025-12-01', 'available'),
('Vitamin D3', 'Cholecalciferol', 3, 'For bone health and calcium absorption', 12.00, 400, 40, '2024-01-01', '2026-01-01', 'available'),
('Multivitamin Tablet', 'Multiple Vitamins & Minerals', 3, 'Complete daily vitamin supplement', 6.50, 350, 35, '2024-01-01', '2026-01-01', 'available'),
('Cough Syrup', 'Dextromethorphan + Phenylephrine', 4, 'Effective for dry and wet cough', 4.00, 280, 28, '2024-01-01', '2025-06-01', 'available'),
('Throat Lozenge', 'Menthol + Benzocaine', 4, 'Relief from sore throat', 3.50, 450, 45, '2024-01-01', '2025-12-01', 'available'),
('Cold Relief Tablet', 'Paracetamol + Pseudoephedrine', 4, 'For cold and flu symptoms', 5.00, 320, 32, '2024-01-01', '2026-01-01', 'available'),
('Antacid Tablet', 'Calcium Carbonate + Magnesium', 5, 'Fast relief from heartburn', 2.00, 550, 55, '2024-01-01', '2026-01-01', 'available'),
('Gas Relief Tablet', 'Simethicone', 5, 'Relieves bloating and gas', 3.00, 400, 40, '2024-01-01', '2026-01-01', 'available'),
('Omeprazole 20mg', 'Omeprazole', 5, 'Reduces stomach acid', 8.50, 220, 22, '2024-01-01', '2026-01-01', 'available'),
('Cetirizine 10mg', 'Cetirizine', 6, 'Non-drowsy allergy relief', 5.50, 380, 38, '2024-01-01', '2026-01-01', 'available'),
('Loratadine 10mg', 'Loratadine', 6, 'Long-acting antihistamine', 6.00, 300, 30, '2024-01-01', '2026-01-01', 'available'),
('Fexofenadine 120mg', 'Fexofenadine', 6, 'For mild to moderate allergies', 7.50, 250, 25, '2024-01-01', '2026-01-01', 'available'),
('Injectable Glucose 5%', 'Dextrose Solution', 3, 'For hydration and energy boost', 12.00, 100, 10, '2024-01-01', '2025-06-01', 'available'),
('Saline Solution 0.9%', 'Sodium Chloride', 3, 'For nasal irrigation and wounds', 3.50, 400, 40, '2024-01-01', '2025-12-01', 'available');

-- Step 5: Insert Orders
INSERT INTO orders (user_id, total_amount, status, delivery_address) VALUES
(3, 150.00, 'pending', 'Dhaka, Bangladesh'),
(3, 250.50, 'shipped', 'Dhaka, Bangladesh'),
(4, 85.00, 'completed', 'Chittagong, Bangladesh'),
(5, 320.00, 'pending', 'Sylhet, Bangladesh');

-- Step 6: Insert Order Items
INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES
(1, 1, 10, 2.50, 25.00),
(1, 3, 5, 5.50, 27.50),
(1, 7, 3, 8.00, 24.00),
(2, 4, 4, 15.00, 60.00),
(2, 9, 8, 6.50, 52.00),
(2, 13, 6, 2.00, 12.00),
(3, 2, 5, 3.00, 15.00),
(3, 10, 3, 4.00, 12.00),
(4, 5, 2, 20.00, 40.00),
(4, 16, 7, 5.50, 38.50),
(4, 14, 4, 3.00, 12.00);

-- Step 7: Insert Carts
INSERT INTO carts (user_id) VALUES
(3),
(4),
(5);

-- Step 8: Insert Cart Items
INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES
(1, 1, 2, 2.50),
(1, 7, 1, 8.00),
(2, 4, 1, 15.00),
(3, 2, 3, 3.00),
(3, 11, 2, 3.50);

-- Step 9: Insert Transactions
INSERT INTO transactions (order_id, user_id, amount, payment_method, status) VALUES
(1, 3, 150.00, 'credit_card', 'completed'),
(2, 3, 250.50, 'cash', 'completed'),
(3, 4, 85.00, 'credit_card', 'completed'),
(4, 5, 320.00, 'online_banking', 'pending');

-- Step 10: Insert System Configuration
INSERT INTO system_config (config_key, value) VALUES
('app_name', 'Pharmacy Management System'),
('app_version', '1.0.0'),
('currency', 'BDT'),
('low_stock_alert_threshold', '20'),
('expiry_warning_days', '30'),
('tax_rate', '5'),
('auto_invoice_number', '1000'),
('delivery_days', '3');

-- RE-ENABLE FOREIGN KEY CHECKS
SET FOREIGN_KEY_CHECKS = 1;

-- ✅ SUCCESS! Test data imported.
-- Login Credentials:
-- Admin: admin@pharmacy.com / password123
-- Inventory Manager: inventory@pharmacy.com / password123
-- Customer: customer1@pharmacy.com / password123 (or customer2, customer3)
