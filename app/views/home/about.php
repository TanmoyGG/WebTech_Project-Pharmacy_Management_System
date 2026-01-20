<?php $pageTitle = 'About'; 
include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-hero">
    <div class="hero-content">
        <p class="eyebrow">About This Project</p>
        <h1>Pharmacy Management System</h1>
        <p class="lead">A procedural PHP MVC web app to manage medicines, inventory, orders, and users with a clean, role-based experience.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?php echo BASE_URL; ?>auth/register">Get Started</a>
            <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>home/index">Back to Home</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">What This App Offers</div>
    <div class="grid-3">
        <div class="mini-feature">
            <h3>Role-Based Dashboards</h3>
            <p>Separate flows for Admin, Inventory Manager, and Customer to keep responsibilities clear.</p>
        </div>
        <div class="mini-feature">
            <h3>Inventory & Stock</h3>
            <p>Track products, low stock, and expiry dates so essential medicines are always available.</p>
        </div>
        <div class="mini-feature">
            <h3>Orders & Payments</h3>
            <p>Browse medicines, manage carts, and place orders with a simple, consistent checkout flow.</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Project Snapshot</div>
    <ul class="feature-list">
        <li><strong>Architecture:</strong> Procedural PHP with MVC separation (Controllers → Models → Views).</li>
        <li><strong>Security:</strong> Prepared statements, password hashing (bcrypt), session-based auth.</li>
        <li><strong>Validation:</strong> Centralized helpers for input sanitization and password/email checks.</li>
        <li><strong>UX:</strong> Clean navigation, flash messages, and responsive layout for desktop/mobile.</li>
    </ul>
</div>

<div class="card">
    <div class="card-header">Meet the Developers</div>
    <div class="dev-list">
        <div class="dev-card">
            <div class="dev-name">Tanmoy Das</div>
            <a class="dev-link" href="https://github.com/TanmoyGG" target="_blank" rel="noopener">github.com/TanmoyGG</a>
        </div>
        <div class="dev-card">
            <div class="dev-name">Meherun Nessa Suchana</div>
            <a class="dev-link" href="https://github.com/MeherunNessaSuchana" target="_blank" rel="noopener">github.com/MeherunNessaSuchana</a>
        </div>
        <div class="dev-card">
            <div class="dev-name">Fatema Binte Islam Neha</div>
            <a class="dev-link" href="https://github.com/fatemaneha" target="_blank" rel="noopener">github.com/fatemaneha</a>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
