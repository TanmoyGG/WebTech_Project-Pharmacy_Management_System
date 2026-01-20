<?php $pageTitle = 'Register'; include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-form-wrapper">
	<div class="card" style="max-width: 600px;">
		<div class="card-header">Create Your Customer Account</div>
		<div class="card-body">
		<!-- Display errors if any -->
		<?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
			<div class="alert alert-danger" style="margin-bottom: 20px;">
				<strong>Registration failed:</strong>
				<ul style="margin: 10px 0 0 20px; padding: 0;">
					<?php foreach ($_SESSION['errors'] as $field => $error): ?>
						<li><?php echo htmlspecialchars($error); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php unset($_SESSION['errors']); ?>
		<?php endif; ?>

		<form id="registerForm" action="<?php echo BASE_URL; ?>auth/registerProcess" method="POST" onsubmit="return validateRegistrationForm();">
			<div class="grid-2">
				<div class="form-group">
					<label for="name">Full Name <span style="color: red;">*</span></label>
					<input type="text" id="name" name="name" placeholder="Your full name" required value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
				</div>

				<div class="form-group">
					<label for="email">Email Address <span style="color: red;">*</span></label>
					<input type="email" id="email" name="email" placeholder="you@example.com" required value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
				</div>
			</div>

			<div class="grid-2">
				<div class="form-group">
					<label for="phone">Phone Number</label>
					<input type="tel" id="phone" name="phone" placeholder="+880 1234567890" value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>">
				</div>

				<div class="form-group">
					<label for="dob">Date of Birth</label>
					<input type="date" id="dob" name="dob" value="<?php echo isset($_SESSION['form_data']['dob']) ? htmlspecialchars($_SESSION['form_data']['dob']) : ''; ?>">
				</div>
			</div>

			<div class="form-group">
				<label for="address">Delivery Address</label>
				<textarea id="address" name="address" placeholder="Enter your delivery address" rows="3"><?php echo isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : ''; ?></textarea>
			</div>

			<div class="grid-2">
				<div class="form-group">
					<label for="password">Password <span style="color: red;">*</span></label>
					<input type="password" id="password" name="password" placeholder="Minimum 6 characters" required>
				</div>

				<div class="form-group">
					<label for="confirm_password">Confirm Password <span style="color: red;">*</span></label>
					<input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
				</div>
			</div>

			<!-- Hidden field: Role is always 'customer' for self-registration -->
			<input type="hidden" name="role" value="customer">

			<div class="form-group">
				<button type="submit" class="btn btn-success btn-block">Create Account</button>
			</div>
		</form>

		<p class="text-muted" style="margin-top: 15px; text-align: center;">
			Already have an account? <a href="<?php echo BASE_URL; ?>auth/login">Login here</a>
		</p>
	</div>
</div>
</div>

<?php unset($_SESSION['form_data']); include_once __DIR__ . '/../layouts/footer.php'; ?>

<script src="<?php echo BASE_URL; ?>/assets/js/auth.js"></script>
