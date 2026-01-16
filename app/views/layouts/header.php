<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Pharmacy Management System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="<?php echo BASE_URL; ?>">🏥 Pharmacy Management System</a>
            </div>
            
            <?php if (isLoggedIn()): ?>
                <div class="navbar-menu">
                    <?php 
                    $role = getCurrentUserRole();
                    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
                    ?>
                    
                    <!-- Admin Menu -->
                    <?php if ($role === 'admin'): ?>
                        <a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a>
                        <a href="<?php echo BASE_URL; ?>admin/products">Products</a>
                        <a href="<?php echo BASE_URL; ?>admin/orders">Orders</a>
                        <a href="<?php echo BASE_URL; ?>admin/users">Users</a>
                        <a href="<?php echo BASE_URL; ?>admin/reports">Reports</a>
                    
                    <!-- Inventory Manager Menu -->
                    <?php elseif ($role === 'inventory_manager'): ?>
                        <a href="<?php echo BASE_URL; ?>inventory_manager/dashboard">Dashboard</a>
                        <a href="<?php echo BASE_URL; ?>inventory_manager/orders">Orders</a>
                        <a href="<?php echo BASE_URL; ?>inventory_manager/products">Products</a>
                        <a href="<?php echo BASE_URL; ?>inventory_manager/low_stock">Low Stock</a>
                        <a href="<?php echo BASE_URL; ?>inventory_manager/expiring">Expiring Items</a>
                    
                    <!-- Customer Menu -->
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>customer/home">Home</a>
                        <a href="<?php echo BASE_URL; ?>customer/browseMedicines">Browse Medicines</a>
                        <a href="<?php echo BASE_URL; ?>customer/cart">
                            Cart
                            <?php 
                            $cartCount = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
                            if ($cartCount > 0): ?>
                                <span class="badge"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo BASE_URL; ?>customer/orderHistory">My Orders</a>
                    <?php endif; ?>
                    
                    <!-- User Dropdown -->
                    <div class="navbar-user">
                        <span class="user-name">👤 <?php echo htmlspecialchars($userName); ?></span>
                        <div class="dropdown">
                            <a href="<?php echo BASE_URL; ?>profile">My Profile</a>
                            <a href="<?php echo BASE_URL; ?>auth/logout">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="navbar-menu">
                    <a href="<?php echo BASE_URL; ?>auth/login">Login</a>
                    <a href="<?php echo BASE_URL; ?>auth/register">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php $flash = getFlash(); if ($flash): ?>
        <div class="container">
            <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Container -->
    <main class="container">
