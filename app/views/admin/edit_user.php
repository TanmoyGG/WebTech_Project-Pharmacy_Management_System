<?php 
$pageTitle = 'Edit User';
include_once __DIR__ . '/../layouts/header.php';
?>
<!-- Edit User Form -->
<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
        <a href="<?php echo BASE_URL; ?>admin/users" style="color: #6c757d; text-decoration: none; font-size: 24px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 style="color: #2c3e50; margin: 0;">
            <i class="fas fa-user-edit"></i> Edit User
        </h1>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="POST" action="<?php echo BASE_URL; ?>admin/editUserProcess" id="editUserForm">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            
            <!-- User ID Display -->
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>User ID:</strong> <?php echo $user['id']; ?>
                <span style="margin-left: 20px;"><strong>Status:</strong> 
                    <span style="color: <?php echo $user['status'] === 'active' ? '#28a745' : '#dc3545'; ?>; font-weight: 600;">
                        <?php echo strtoupper($user['status']); ?>
                    </span>
                </span>
            </div>

            <!-- Name (read-only) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Full Name
                </label>
                <input type="text" 
                       name="name" 
                       readonly
                       value="<?php echo htmlspecialchars($user['name']); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
            </div>

            <!-- Email (read-only) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Email Address
                </label>
                <input type="email" 
                       name="email" 
                       readonly
                       value="<?php echo htmlspecialchars($user['email']); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
            </div>

            <!-- Password (optional for edit) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    New Password <span style="color: #6c757d; font-weight: 400; font-size: 12px;">(leave blank to keep current password)</span>
                </label>
                <input type="password" 
                       name="password" 
                       minlength="6"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="Enter new password or leave blank">
                <small style="color: #6c757d; font-size: 12px;">Password must be at least 6 characters if changed</small>
            </div>

            <!-- Role -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    User Role <span style="color: #dc3545;">*</span>
                </label>
                <select name="role" 
                        required
                        style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
                    <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="inventory_manager" <?php echo $user['role'] === 'inventory_manager' ? 'selected' : ''; ?>>Inventory Manager</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <!-- Phone -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Phone Number
                </label>
                  <input type="tel" 
                       name="phone"
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
            </div>

            <!-- Address -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Address
                </label>
                <textarea name="address" 
                          rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px; resize: vertical;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <!-- Account Information -->
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="margin-bottom: 8px;">
                    <strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
                </div>
                <div>
                    <strong>Last Updated:</strong> <?php echo date('F d, Y H:i', strtotime($user['updated_at'])); ?>
                </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" 
                        style="flex: 1; background: #007bff; color: white; padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-save"></i> Update User
                </button>
                <a href="<?php echo BASE_URL; ?>admin/users" 
                   style="flex: 1; background: #6c757d; color: white; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

        </form>
    </div>
</div>

    <?php include_once __DIR__ . '/../layouts/footer.php'; ?>
