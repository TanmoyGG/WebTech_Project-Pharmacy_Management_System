<?php 
$pageTitle = 'Edit Profile';
include_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .form-card { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; }
    .form-input, .form-textarea { width: 100%; padding: 10px 12px; border: 2px solid #ecf0f1; border-radius: 5px; font-size: 13px; font-family: inherit; transition: all 0.3s ease; }
    .form-input:focus, .form-textarea:focus { outline: none; border-color: #667eea; background: #f9fafb; }
    .form-textarea { resize: vertical; min-height: 100px; }
    .form-help { font-size: 11px; color: #95a5a6; margin-top: 4px; }
    .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
    .form-row.full { grid-template-columns: 1fr; }
    .button-group { display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #ecf0f1; }
    .btn { padding: 10px 20px; border: none; border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5568d3; }
    .btn-secondary { background: #ecf0f1; color: #2c3e50; }
    .btn-secondary:hover { background: #bdc3c7; }
    .info-box { background: #f0f4ff; border-left: 4px solid #667eea; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; }
</style>

<div class="form-card">
    <div class="info-box">
        <strong>ℹ️ Note:</strong> Keep your information up to date to receive important notifications and improve your experience.
    </div>
    
    <form method="POST" action="<?php echo BASE_URL; ?>profile/update">
        <!-- Name and Email -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                <p class="form-help">Enter your first and last name</p>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                <p class="form-help">Email cannot be changed</p>
            </div>
        </div>
        
        <!-- Phone and DOB -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                <p class="form-help">E.g., +880-1234-567890</p>
            </div>
            <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-input" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">
                <p class="form-help">Optional - Help us customize your experience</p>
            </div>
        </div>
        
        <!-- Address -->
        <div class="form-group form-row full">
            <div>
                <label class="form-label">Address</label>
                <textarea name="address" class="form-textarea" placeholder="Enter your full address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                <p class="form-help">Your delivery or residential address</p>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="button-group">
            <button type="submit" class="btn btn-primary">
                💾 Save Changes
            </button>
            <a href="<?php echo BASE_URL; ?>profile/view" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
