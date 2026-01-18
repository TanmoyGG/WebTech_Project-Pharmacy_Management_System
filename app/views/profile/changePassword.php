<?php 
$pageTitle = 'Change Password';
include_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .form-card { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; }
    .form-input { width: 100%; padding: 10px 12px; border: 2px solid #ecf0f1; border-radius: 5px; font-size: 13px; font-family: inherit; transition: all 0.3s ease; }
    .form-input:focus { outline: none; border-color: #667eea; background: #f9fafb; }
    .form-help { font-size: 11px; color: #95a5a6; margin-top: 4px; }
    .button-group { display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #ecf0f1; }
    .btn { padding: 10px 20px; border: none; border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5568d3; }
    .btn-secondary { background: #ecf0f1; color: #2c3e50; }
    .btn-secondary:hover { background: #bdc3c7; }
    .info-box { background: #f0f4ff; border-left: 4px solid #667eea; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; }
    .password-requirements { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 12px; line-height: 1.6; }
    .password-requirements li { margin-left: 20px; }
</style>

<div class="form-card">
    <div class="info-box">
        <strong>🔐 Security Tip:</strong> Use a strong password with uppercase, lowercase, numbers, and special characters.
    </div>
    
    <div class="password-requirements">
        <strong>Password Requirements:</strong>
        <ul>
            <li>At least 6 characters long</li>
        </ul>
    </div>
    
    <form method="POST" action="<?php echo BASE_URL; ?>profile/updatePassword">
        <!-- Current Password -->
        <div class="form-group">
            <label class="form-label">Current Password *</label>
            <input type="password" name="current_password" class="form-input" required>
            <p class="form-help">Enter your current password for verification</p>
        </div>
        
        <!-- New Password -->
        <div class="form-group">
            <label class="form-label">New Password *</label>
            <input type="password" name="new_password" class="form-input" id="new_password" required>
            <p class="form-help">Choose a strong password</p>
        </div>
        
        <!-- Confirm Password -->
        <div class="form-group">
            <label class="form-label">Confirm New Password *</label>
            <input type="password" name="confirm_password" class="form-input" id="confirm_password" required>
            <p class="form-help">Re-enter your new password to confirm</p>
        </div>
        
        <!-- Buttons -->
        <div class="button-group">
            <button type="submit" class="btn btn-primary">
                🔒 Update Password
            </button>
            <a href="<?php echo BASE_URL; ?>profile/view" class="btn btn-secondary">
                Back to Profile
            </a>
        </div>
    </form>
</div>

<script>
    // Client-side validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('New password and confirmation do not match!');
            return false;
        }
        
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
            return false;
        }
    });
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
