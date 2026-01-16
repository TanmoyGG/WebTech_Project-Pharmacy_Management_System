-- Database: pharmacy_management
CREATE DATABASE IF NOT EXISTS pharmacy_management;
USE pharmacy_management;

-- 1. Users Table (Unified for Admin, Inventory Manager, and Customer)
-- Handles Common Features: Login, Registration, Profile, Password Management
CREATE TABLE users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	role ENUM('admin', 'inventory_manager', 'customer') NOT NULL,
	phone VARCHAR(20),
	dob DATE,
	address TEXT,
	status ENUM('active', 'inactive') DEFAULT 'active',
	reset_token VARCHAR(255) DEFAULT NULL,
	reset_expiry DATETIME DEFAULT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Categories Table
-- For Student 2: Category Management
CREATE TABLE categories (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL UNIQUE,
	description TEXT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Products Table (Medicines)
-- For Student 2: Product Entry, Expiry Tracking, Price Management
CREATE TABLE products (
	id INT AUTO_INCREMENT PRIMARY KEY,
	category_id INT,
	name VARCHAR(150) NOT NULL,
	generic_name VARCHAR(150),
	description TEXT,
	price DECIMAL(10, 2) NOT NULL,
	quantity INT NOT NULL DEFAULT 0, 
	low_stock_threshold INT DEFAULT 10, 
	manufacture_date DATE,
	expiry_date DATE, -- Student 2: Expiry Tracking
	status ENUM('available', 'discontinued') DEFAULT 'available',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 4. Carts Table
-- For Student 3: Digital Shopping Cart
CREATE TABLE carts (
	id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	status ENUM('active', 'closed') DEFAULT 'active',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Cart Items Table
CREATE TABLE cart_items (
	id INT AUTO_INCREMENT PRIMARY KEY,
	cart_id INT NOT NULL,
	product_id INT NOT NULL,
	quantity INT NOT NULL DEFAULT 1,
	price DECIMAL(10, 2) NOT NULL, -- Price at the time of adding to cart
	FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
	FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 6. Orders Table
-- For Student 3: Order Placement & History; Student 1: Sales Analytics
CREATE TABLE orders (
	id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	total_amount DECIMAL(10, 2) NOT NULL,
	status ENUM('pending', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
	delivery_address TEXT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Order Items Table
CREATE TABLE order_items (
	id INT AUTO_INCREMENT PRIMARY KEY,
	order_id INT NOT NULL,
	product_id INT NOT NULL,
	quantity INT NOT NULL,
	price DECIMAL(10, 2) NOT NULL, -- Retail price at time of purchase
	subtotal DECIMAL(10, 2) NOT NULL,
	FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
	FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 8. Transactions Table
-- For Student 1: Transaction History & Revenue Tracking
CREATE TABLE transactions (
	id INT AUTO_INCREMENT PRIMARY KEY,
	order_id INT NOT NULL,
	user_id INT NOT NULL,
	amount DECIMAL(10, 2) NOT NULL,
	payment_method VARCHAR(50) DEFAULT 'Cash',
	status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
	transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 9. System Configuration Table
-- For Student 1: System Configuration
CREATE TABLE system_config (
	id INT AUTO_INCREMENT PRIMARY KEY,
	config_key VARCHAR(50) NOT NULL UNIQUE,
	value TEXT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 10. Reports Meta (Optional tracking of generated reports)
-- For Student 1: Report Generation
CREATE TABLE reports (
	id INT AUTO_INCREMENT PRIMARY KEY,
	report_type VARCHAR(50) NOT NULL, -- 'sales', 'stock'
	generated_by INT NOT NULL,
	file_path VARCHAR(255),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (generated_by) REFERENCES users(id)
);

-- Insert Default Admin (Password: admin123)
INSERT INTO users (name, email, password, role, status) 
VALUES ('Master Admin', 'admin@pharmacy.com', '$2y$10$8WvS.vG9Vn7K0l5r5I3O0.7Y7O3O.m6K2o6S.m6K2o6S.m6K2o6S.', 'admin', 'active');

-- Insert Initial System Configs
INSERT INTO system_config (config_key, value) VALUES 
('pharmacy_name', 'My Pharmacy'),
('contact_info', 'contact@pharmacy.com'),
('tax_rate', '5.0');
