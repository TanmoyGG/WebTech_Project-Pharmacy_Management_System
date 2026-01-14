<?php $pageTitle = 'Login'; include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width: 480px; margin: 40px auto;">
	<div class="card-header">Login</div>
	<div class="card-body">
		<form id="loginForm" action="<?php echo BASE_URL; ?>/auth/loginProcess" method="POST" onsubmit="return validateForm('loginForm');">
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" id="email" name="email" placeholder="you@example.com" required>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" placeholder="Enter your password" required>
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

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
