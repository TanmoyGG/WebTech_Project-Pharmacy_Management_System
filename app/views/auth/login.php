<?php $pageTitle = 'Login'; include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-form-wrapper">
	<div class="card" style="max-width: 480px;">
		<div class="card-header">Login</div>
		<div class="card-body">
		<form id="loginForm" action="<?php echo BASE_URL; ?>/auth/loginProcess" method="POST" onsubmit="return validateForm('loginForm');">
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" id="email" name="email" placeholder="you@example.com" required>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
			<div style="position: relative;">
				<input type="password" id="password" name="password" placeholder="Enter your password" required style="padding-right: 45px;">
				<button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: #666;">👁️</button>
			</div>
			</div>
		<div class="form-group" style="display: flex; align-items: center;">
			<input type="checkbox" id="remember_me" name="remember_me" style="margin-right: 8px; cursor: pointer;">
			<label for="remember_me" style="margin: 0; cursor: pointer;">Remember me for 30 days</label>
		</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block">Login</button>
			</div>
		</form>

		<p class="text-muted" style="margin-top: 10px;">
			Don't have an account? <a href="<?php echo BASE_URL; ?>/auth/register">Register</a>
		</p>
	</div>
</div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

<script src="<?php echo BASE_URL; ?>/assets/js/auth.js"></script>
