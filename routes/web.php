<?php
// Web Routes Configuration (Procedural)
// Maps URLs to controller functions

/*
    Route Structure: 'url/pattern' => 'controller_function'
    
    URL Pattern Examples:
    - auth/login => calls auth_login() function in AuthController.php
    - admin/dashboard => calls admin_dashboard() function in AdminController.php
    - product/view/5 => calls product_view(5) function in ProductController.php
    
    Access Parameters from URL:
    - Use getUrlParam(0), getUrlParam(1), etc.
    - Or accept them as function parameters in the controller
*/

// This file is referenced but routing is handled dynamically in App.php
// The routing logic automatically converts URLs to function calls:
//
// URL: /auth/login
// Controller File: app/controllers/AuthController.php
// Function Called: auth_login()
//
// URL: /admin/dashboard/1
// Controller File: app/controllers/AdminController.php
// Function Called: admin_dashboard(1)
//
// URL: /product/view/5
// Controller File: app/controllers/ProductController.php
// Function Called: product_view(5)

// Naming Convention:
// - Controller File: [Name]Controller.php
// - Function Name: [lowercase_name]_[method_name]()
//
// Examples:
// - AuthController.php -> auth_login(), auth_register()
// - AdminController.php -> admin_dashboard(), admin_user_management()
// - ProductController.php -> product_view(), product_edit()
?>