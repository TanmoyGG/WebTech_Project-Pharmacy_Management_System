<?php 
$pageTitle = 'My Profile';
include_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .profile-card { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .profile-header { display: flex; align-items: center; gap: 30px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #ecf0f1; }
    .profile-avatar { width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; }
    .profile-info h2 { font-size: 24px; color: #2c3e50; margin-bottom: 5px; }
    .profile-info p { color: #7f8c8d; font-size: 14px; margin-bottom: 3px; }
    .profile-role { display: inline-block; background: #667eea; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 10px; }
    .profile-section { margin-bottom: 25px; }
    .section-title { font-size: 16px; color: #2c3e50; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #ecf0f1; font-weight: 600; }
    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .info-label { font-size: 11px; color: #95a5a6; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600; }
    .info-value { font-size: 14px; color: #2c3e50; font-weight: 500; }
    .info-value.empty { color: #bdc3c7; font-style: italic; }
    .action-buttons { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #ecf0f1; flex-wrap: wrap; }
    .btn { padding: 10px 20px; border: none; border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5568d3; }
    .btn-secondary { background: #ecf0f1; color: #2c3e50; }
    .btn-secondary:hover { background: #bdc3c7; }
</style>

<div class="profile-card">
    <div class="profile-header">
        <div class="profile-avatar">👤</div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <?php if (!empty($user['phone'])): ?>
                <p><?php echo htmlspecialchars($user['phone']); ?></p>
            <?php endif; ?>
            <span class="profile-role"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span>
        </div>
    </div>
    
    <!-- Personal Information Section -->
    <div class="profile-section">
        <h3 class="section-title">Personal Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <p class="info-label">Full Name</p>
                <p class="info-value"><?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <div class="info-item">
                <p class="info-label">Email Address</p>
                <p class="info-value"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="info-item">
                <p class="info-label">Phone Number</p>
                <p class="info-value <?php echo empty($user['phone']) ? 'empty' : ''; ?>">
                    <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?>
                </p>
            </div>
            <div class="info-item">
                <p class="info-label">Date of Birth</p>
                <p class="info-value <?php echo empty($user['dob']) ? 'empty' : ''; ?>">
                    <?php echo !empty($user['dob']) ? date('M d, Y', strtotime($user['dob'])) : 'Not provided'; ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Address Section -->
    <div class="profile-section">
        <h3 class="section-title">Address</h3>
        <p class="info-value <?php echo empty($user['address']) ? 'empty' : ''; ?>">
            <?php echo !empty($user['address']) ? nl2br(htmlspecialchars($user['address'])) : 'No address provided'; ?>
        </p>
    </div>
    
    <!-- Account Information Section -->
    <div class="profile-section">
        <h3 class="section-title">Account Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <p class="info-label">Account Role</p>
                <p class="info-value"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></p>
            </div>
            <div class="info-item">
                <p class="info-label">Account Status</p>
                <p class="info-value"><?php echo ucfirst($user['status']); ?></p>
            </div>
            <div class="info-item">
                <p class="info-label">Member Since</p>
                <p class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
            </div>
            <div class="info-item">
                <p class="info-label">Last Updated</p>
                <p class="info-value"><?php echo date('M d, Y H:i A', strtotime($user['updated_at'])); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="<?php echo BASE_URL; ?>profile/edit" class="btn btn-primary">
            ✏️ Edit Profile
        </a>
        <a href="<?php echo BASE_URL; ?>profile/changePassword" class="btn btn-secondary">
            🔑 Change Password
        </a>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
