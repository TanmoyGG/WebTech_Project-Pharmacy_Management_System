# Pharmacy Management System

## Description
A comprehensive web-based Pharmacy Management System built with PHP, MySQL, HTML, CSS, and JavaScript following **MVC Architecture with Procedural Programming**. No OOP classes used - all functionality implemented through procedural functions for simplicity and direct control.

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
- **Backend:** PHP 7.4+ (100% Procedural Programming - No OOP Classes)
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Server:** XAMPP (Apache 2.4+)
- **Architecture:** MVC (Procedural Style)

## Installation
1. Place the project folder in `xampp/htdocs/`
2. Create MySQL database: `pharmacy_management`
3. Import schema: `mysql -u root pharmacy_management < database/schema.sql`
4. Update database credentials in `config/database.php`
5. Access at: `http://localhost/WebTech_Project-Pharmacy_Management_System/public/`

## Project Structure (MVC with Procedural Programming)

```
WebTech_Project-Pharmacy_Management_System/
├── app/
│   ├── controllers/              # All procedural functions
│   │   ├── AuthController.php    # Functions: auth_login(), auth_register(), auth_logout()
│   │   ├── AdminController.php   # Functions: admin_dashboard(), admin_userManagement()
│   │   ├── InventoryManagerController.php
│   │   ├── CustomerController.php
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── UserController.php
│   │   ├── ReportController.php
│   │   ├── HomeController.php
│   │   └── ProfileController.php
│   │
│   ├── models/                   # Data operation functions
│   │   ├── User.php              # userGetById(), userCreate(), userUpdate()
│   │   ├── Product.php           # productGetById(), productSearch(), productGetLowStock()
│   │   ├── Category.php          # categoryGetAll(), categoryCreate(), categoryUpdate()
│   │   ├── Order.php             # orderGetByUser(), orderCreate(), orderUpdateStatus()
│   │   ├── OrderItem.php         # orderItemCreate(), orderItemGetByOrder()
│   │   ├── Cart.php              # cartGetOrCreate(), cartAddItem(), cartGetTotal()
│   │   ├── Transaction.php       # transactionCreate(), transactionGetByDateRange()
│   │   └── SystemConfig.php      # configGet(), configSet(), configGetTaxRate()
│   │
│   └── views/                    # View templates (HTML + PHP)
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
│       ├── profile/
│       │   ├── view.php
│       │   └── edit.php
│       └── home/
│           ├── index.php
│           ├── about.php
│           └── contact.php
│
├── public/                       # Web root - entry point
│   ├── index.php                 # Application bootstrap
│   ├── css/
│   │   ├── style.css             # Main stylesheet
│   │   ├── admin.css
│   │   ├── inventory.css
│   │   └── customer.css
│   ├── js/
│   │   ├── main.js               # Main JavaScript
│   │   ├── admin.js
│   │   ├── inventory.js
│   │   └── customer.js
│   ├── images/                   # Static images
│   ├── uploads/                  # User uploaded files
│   └── .htaccess                 # URL rewriting rules
│
├── config/                       # Configuration files
│   ├── config.php                # App settings & constants
│   └── database.php              # Database initialization
│
├── core/                         # Core procedural functions
│   ├── App.php                   # initApp(), routeRequest(), render()
│   ├── Controller.php            # isLoggedIn(), requireAuth(), requireRole()
│   ├── Database.php              # query(), fetchAll(), execute(), insertRecord()
│   └── Model.php                 # updateRecord(), deleteRecord(), getPaginated()
│
├── helpers/                      # Helper utility functions
│   ├── session_helper.php        # setUserSession(), destroyUserSession()
│   ├── url_helper.php            # url(), redirectTo(), getQueryParam()
│   └── validation_helper.php     # validateEmail(), sanitize(), hashPassword()
│
├── routes/                       # Routing configuration
│   └── web.php                   # Routing documentation
│
├── database/                     # Database files
│   └── schema.sql                # MySQL database schema
│
├── .htaccess                     # Root URL rewriting
├── .gitignore                    # Git ignore file
└── README.md                     # This file
```

## Procedural Programming Approach

### Naming Convention
- **Controllers:** `controllername_actionname()`
  - Example: `auth_login()`, `admin_dashboard()`, `customer_home()`

- **Models:** `model_operation()`
  - Example: `userGetById()`, `productSearch()`, `orderCreate()`

- **Core Functions:** Descriptive function names
  - `initApp()`, `routeRequest()`, `render()`, `query()`, `execute()`

### Routing System
The application uses a simple procedural routing system:

```
Request URL: /auth/login
    ↓
routeRequest() parses URL
    ↓
Loads AuthController.php
    ↓
Calls auth_login() function
    ↓
render('auth/login') outputs view
```

### Database Operations
All database operations use procedural functions:

```php
// Fetch data
$user = fetchOne('SELECT * FROM users WHERE id = ?', 'i', [$userId]);
$users = fetchAll('SELECT * FROM users');

// Insert data
$userId = insertRecord('users', $userData);

// Update data
updateRecord('users', ['name' => 'New Name'], 'id = ?', [$userId]);

// Delete data
deleteRecord('users', 'id = ?', [$userId]);

// Query with results
$result = query('SELECT * FROM products WHERE category_id = ?', 'i', [$categoryId]);
```

### Model Functions
Model files contain specific business logic functions:

```php
// User Model (User.php)
userGetByEmail($email)
userCreate($name, $email, $password, $role)
userUpdate($userId, $data)
userUpdatePassword($userId, $newPassword)
userSetStatus($userId, $status)

// Product Model (Product.php)
productGetById($id)
productSearch($searchTerm)
productGetLowStock($threshold)
productGetExpiring($days)
productReduceStock($productId, $quantity)

// Cart Model (Cart.php)
cartGetOrCreate($userId)
cartAddItem($cartId, $productId, $quantity)
cartGetTotal($cartId)
cartClear($cartId)
```

### Controller Examples

```php
// Authentication Controller
function auth_login() {
    if (isLoggedIn()) {
        redirectTo('admin/dashboard');
    }
    render('auth/login');
}

function auth_loginProcess() {
    if (!isPost()) redirectTo('auth/login');
    
    $email = sanitizeEmail(getPost('email'));
    $password = getPost('password');
    
    $user = userGetByEmail($email);
    if ($user && verifyPassword($password, $user['password'])) {
        setUserSession($user['id'], $user['name'], $user['email'], $user['role']);
        redirectTo('admin/dashboard');
    }
    
    setFlash('Invalid credentials', 'error');
    redirectTo('auth/login');
}

// Admin Controller
function admin_dashboard() {
    requireRole('admin');
    
    $totalUsers = countRecords('users');
    $totalProducts = countRecords('products');
    $totalRevenue = orderGetTotalRevenue();
    
    render('admin/dashboard', [
        'totalUsers' => $totalUsers,
        'totalProducts' => $totalProducts,
        'totalRevenue' => $totalRevenue
    ]);
}

// Customer Controller
function customer_addToCart() {
    if (!isPost()) redirectTo('customer/home');
    
    $productId = getPost('product_id');
    $quantity = getPost('quantity', 1);
    
    $cartId = cartGetOrCreate(getCurrentUserId());
    cartAddItem($cartId, $productId, $quantity);
    
    setFlash('Product added to cart', 'success');
    redirectTo('customer/cart');
}
```

## Advantages of This Procedural Approach

✅ **Simplicity** - Direct function calls, easy to understand  
✅ **No Class Overhead** - Lower memory footprint  
✅ **Easy Debugging** - Straightforward function tracing  
✅ **Quick Development** - Minimal boilerplate code  
✅ **Direct Database Access** - No ORM complexity  
✅ **Full MVC Organization** - Still maintains clean structure  

## Database Setup

Create the database and import schema:
```bash
mysql -u root -p
CREATE DATABASE pharmacy_management;
USE pharmacy_management;
SOURCE database/schema.sql;
```

## Configuration

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'pharmacy_management');
```

## URL Structure

- **Home:** `/` or `/home/index`
- **Login:** `/auth/login`
- **Register:** `/auth/register`
- **Admin Dashboard:** `/admin/dashboard` (requires admin role)
- **Inventory Manager:** `/inventory_manager/dashboard`
- **Customer Store:** `/customer/home`
- **Profile:** `/profile/view` and `/profile/edit`

## File Guidelines

- All procedural functions (no classes)
- Use consistent naming convention: `functionName()`
- Include docstrings for complex functions
- Keep controller functions focused on single responsibility
- Use model functions for database operations
- Always use helper functions for validation and sanitization

## Contributors
- **Student 1:** Master Admin Module Development
- **Student 2:** Inventory Manager Module Development
- **Student 3:** Customer Module Development

## License
Educational Project - 2026

## Support
For issues or questions, contact the development team or create an issue in the repository.
