# Pharmacy Management System

## Description
A comprehensive web-based Pharmacy Management System built with PHP, MySQL, HTML, CSS, and JavaScript following MVC architecture.

## Features

### Common Features (All Users)
- Login/Sign in
- Registration/Signup
- Forget Password
- Change Password
- Profile (Update + View)
- Logout

### Master Admin (Student 1)
- User Management: Create, update, or deactivate Inventory Manager and Customer accounts
- Sales Analytics Dashboard: View total revenue, most sold products, and daily order counts
- Transaction History: View a complete log of all purchases made on the platform
- System Configuration: Edit website settings (pharmacy name, contact info, tax rates)
- Inventory Oversight: View and control all medicines and inventory
- Report Generation: Export monthly sales or stock reports as PDF

### Inventory Manager (Student 2)
- Product Entry: Add new medicines with details (name, generic name, category, etc.)
- Category Management: Organize medicines into groups (Painkillers, Antibiotics, Vitamins)
- Stock Adjustment: Manage medicine stock (increase/decrease quantity)
- Expiry Tracking: Monitor and list expiration dates for medicines
- Low Stock Alerts: Receive notifications when medicine falls below certain quantity
- Price Management: Set and update retail prices and apply discounts
- View orders placed by customers

### Customer (Student 3)
- Browse available medicines
- Product Search & Filter: Search by name or filter by category/price
- Digital Shopping Cart: Add multiple medicines to cart before checkout
- Order Placement: Confirm purchases and provide delivery addresses
- Order History: View list of past orders and their status (Pending/Shipped)

## Technologies
- Backend: PHP (MVC Architecture)
- Database: MySQL
- Frontend: HTML5, CSS3, JavaScript
- Server: XAMPP (Apache)

## Installation
1. Place the project folder in xampp/htdocs/
2. Import database schema from database/schema.sql
3. Configure database settings in config/database.php
4. Access the application at http://localhost/WebTech_Project-Pharmacy_Management_System/public/

## Project Structure (MVC Architecture)
```
WebTech_Project-Pharmacy_Management_System/
├── app/
│   ├── controllers/              # Application controllers
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   ├── InventoryManagerController.php
│   │   ├── CustomerController.php
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── UserController.php
│   │   └── ReportController.php
│   ├── models/                   # Data models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Cart.php
│   │   ├── Transaction.php
│   │   └── SystemConfig.php
│   └── views/                    # View templates
│       ├── layouts/
│       │   ├── header.php
│       │   ├── footer.php
│       │   └── sidebar.php
│       ├── auth/
│       │   ├── login.php
│       │   ├── register.php
│       │   ├── forgot_password.php
│       │   └── change_password.php
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── user_management.php
│       │   ├── sales_analytics.php
│       │   ├── transaction_history.php
│       │   ├── system_config.php
│       │   ├── inventory_oversight.php
│       │   └── reports.php
│       ├── inventory_manager/
│       │   ├── dashboard.php
│       │   ├── product_entry.php
│       │   ├── category_management.php
│       │   ├── stock_adjustment.php
│       │   ├── expiry_tracking.php
│       │   ├── low_stock_alerts.php
│       │   ├── price_management.php
│       │   └── view_orders.php
│       ├── customer/
│       │   ├── home.php
│       │   ├── browse_medicines.php
│       │   ├── product_search.php
│       │   ├── cart.php
│       │   ├── checkout.php
│       │   └── order_history.php
│       └── profile/
│           ├── view.php
│           └── edit.php
├── public/                       # Public folder (entry point)
│   ├── index.php
│   ├── css/
│   │   ├── style.css
│   │   ├── admin.css
│   │   ├── inventory.css
│   │   └── customer.css
│   ├── js/
│   │   ├── main.js
│   │   ├── admin.js
│   │   ├── inventory.js
│   │   └── customer.js
│   ├── images/
│   └── uploads/
├── config/                       # Configuration files
│   ├── database.php
│   └── config.php
├── core/                         # Core MVC framework classes
│   ├── App.php
│   ├── Controller.php
│   ├── Database.php
│   └── Model.php
├── helpers/                      # Helper functions
│   ├── session_helper.php
│   ├── url_helper.php
│   └── validation_helper.php
├── routes/                       # Application routes
│   └── web.php
├── database/                     # Database files
│   └── schema.sql
├── .htaccess
└── README.md
```

## Contributors
- Student 1: Master Admin Module
- Student 2: Inventory Manager Module
- Student 3: Customer Module