<?php $pageTitle = 'Register'; include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width: 520px; margin: 40px auto;">
	<div class="card-header">Create an Account</div>
	<div class="card-body">
		<form id="registerForm" action="<?php echo BASE_URL; ?>/auth/register" method="POST" onsubmit="return validateForm('registerForm');">
			<div class="grid-2">
				<div class="form-group">
					<label for="name">Full Name</label>
					<input type="text" id="name" name="name" placeholder="Your name" required>
				</div>

				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" placeholder="you@example.com" required>
				</div>
			</div>

			<div class="grid-2">
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" id="password" name="password" placeholder="Minimum 6 characters" required>
				</div>

				<div class="form-group">
					<label for="confirm_password">Confirm Password</label>
					<input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
				</div>
			</div>

			<!-- Default role is customer -->
			<input type="hidden" name="role" value="customer">

			<div class="form-group">
				<button type="submit" class="btn btn-success btn-block">Register</button>
			</div>
		</form>

		<p class="text-muted" style="margin-top: 10px;">
			Already have an account? <a href="<?php echo BASE_URL; ?>/auth/login">Login</a>
		</p>
	</div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
