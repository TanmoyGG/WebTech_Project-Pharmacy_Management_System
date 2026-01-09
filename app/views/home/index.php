<?php $pageTitle = 'Welcome'; include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width: 720px; margin: 40px auto;">
    <div class="card-header">Welcome to Pharmacy Management System</div>
    <div class="card-body">
        <p class="text-muted">A simple, functional interface to manage medicines, orders, and users.</p>

        <div class="grid-2 mt-20">
            <div class="card">
                <div class="card-header">New here?</div>
                <p>Create an account to start browsing medicines and place orders.</p>
                <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-success">Register</a>
            </div>
            <div class="card">
                <div class="card-header">Already have an account?</div>
                <p>Login to access your dashboard based on your role.</p>
                <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-primary">Login</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
