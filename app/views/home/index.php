<?php $pageTitle = 'Welcome'; 
include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-hero">
    <div class="hero-content">
        <p class="eyebrow">Welcome</p>
        <h1>Run your pharmacy with clarity and control.</h1>
        <p class="lead">Track inventory, process orders, and serve customers faster with a simple, role-based workflow for admins, inventory managers, and customers.</p>
        <div class="hero-actions">
            <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-success">Create Account</a>
            <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-primary">Login</a>
            <a href="<?php echo BASE_URL; ?>/home/about" class="btn btn-secondary">Learn More</a>
        </div>
    </div>
</div>

<div class="grid-3 mt-20">
    <div class="highlight-card">
        <h3>Inventory at a Glance</h3>
        <p>Monitor low stock and expiry dates before they become problems.</p>
    </div>
    <div class="highlight-card">
        <h3>Fast Order Flow</h3>
        <p>Cart, checkout, order history, and receipts kept tidy for customers.</p>
    </div>
    <div class="highlight-card">
        <h3>Admin Insights</h3>
        <p>Dashboards, reports, and system settings in one secure space.</p>
    </div>
</div>

<div class="card mt-20">
    <div class="card-header">How it works</div>
    <div class="grid-3">
        <div class="mini-feature">
            <h3>1) Sign up</h3>
            <p>Admins invite team members or customers self-register to start ordering.</p>
        </div>
        <div class="mini-feature">
            <h3>2) Add products</h3>
            <p>Inventory managers add medicines with pricing, stock, and expiry tracking.</p>
        </div>
        <div class="mini-feature">
            <h3>3) Track & fulfill</h3>
            <p>Customers place orders, managers fulfill, admins monitor revenue and stock.</p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
