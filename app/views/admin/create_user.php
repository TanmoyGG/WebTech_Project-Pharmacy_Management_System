<?php 
$pageTitle = 'Create User';
include_once __DIR__ . '/../layouts/header.php';
?>
<!-- Create User Form -->
<div class="container" style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
        <a href="<?php echo BASE_URL; ?>admin/users" style="color: #6c757d; text-decoration: none; font-size: 24px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 style="color: #2c3e50; margin: 0;">
            <i class="fas fa-user-plus"></i> Create New User
        </h1>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="POST" action="<?php echo BASE_URL; ?>admin/createUserProcess" id="createUserForm">
            
            <!-- Name -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Full Name <span style="color: #dc3545;">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       required 
                       minlength="3"
                       value="<?php echo htmlspecialchars(getPost('name', '')); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="Enter full name">
            </div>

            <!-- Email -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Email Address <span style="color: #dc3545;">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       required
                       value="<?php echo htmlspecialchars(getPost('email', '')); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="user@example.com">
            </div>

            <!-- Password -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Password <span style="color: #dc3545;">*</span>
                </label>
                <input type="password" 
                       name="password" 
                       required 
                       minlength="6"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="Minimum 6 characters">
                <small style="color: #6c757d; font-size: 12px;">Password must be at least 6 characters long</small>
            </div>

            <!-- Role -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    User Role <span style="color: #dc3545;">*</span>
                </label>
                <select name="role" 
                        required
                        style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select Role --</option>
                    <option value="customer" <?php echo getPost('role', '') === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="inventory_manager" <?php echo getPost('role', '') === 'inventory_manager' ? 'selected' : ''; ?>>Inventory Manager</option>
                    <option value="admin" <?php echo getPost('role', '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <!-- Phone -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Phone Number
                </label>
                <input type="tel" 
                       name="phone"
                       value="<?php echo htmlspecialchars(getPost('phone', '')); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="+8801XXXXXXXXX">
            </div>

            <!-- Address -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Address
                </label>
                <textarea name="address" 
                          rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px; resize: vertical;"
                          placeholder="Enter full address"><?php echo htmlspecialchars(getPost('address', '')); ?></textarea>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" 
                        style="flex: 1; background: #28a745; color: white; padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-save"></i> Create User
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
