<!-- User Management -->
<div class="container" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #2c3e50; margin: 0;">
            <i class="fas fa-users"></i> User Management
        </h1>
        <a href="<?php echo BASE_URL; ?>admin/createUser" style="background: #28a745; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Create New User
        </a>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="GET" action="<?php echo BASE_URL; ?>admin/users" style="display: flex; gap: 15px; flex-wrap: wrap;">
            
            <!-- Search -->
            <input type="text" 
                   name="search" 
                   placeholder="Search by name, email, or phone..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   style="flex: 1; min-width: 250px; padding: 10px; border: 1px solid #ced4da; border-radius: 5px;">
            
            <!-- Role Filter -->
            <select name="role" style="padding: 10px; border: 1px solid #ced4da; border-radius: 5px;">
                <option value="">All Roles</option>
                <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="inventory_manager" <?php echo $role_filter === 'inventory_manager' ? 'selected' : ''; ?>>Inventory Manager</option>
                <option value="customer" <?php echo $role_filter === 'customer' ? 'selected' : ''; ?>>Customer</option>
            </select>
            
            <!-- Status Filter -->
            <select name="status" style="padding: 10px; border: 1px solid #ced4da; border-radius: 5px;">
                <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
            
            <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Filter
            </button>
            
            <a href="<?php echo BASE_URL; ?>admin/users" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>

    <!-- Users Table -->
    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <?php if (!empty($users)): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; text-align: left;">
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">ID</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Name</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Email</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Phone</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Role</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Status</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Joined</th>
                            <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): 
                            $roleColors = [
                                'admin' => '#dc3545',
                                'inventory_manager' => '#007bff',
                                'customer' => '#28a745'
                            ];
                            $roleColor = $roleColors[$user['role']] ?? '#6c757d';
                            $statusColor = $user['status'] === 'active' ? '#28a745' : '#dc3545';
                        ?>
                            <tr style="border-bottom: 1px solid #f1f3f5;">
                                <td style="padding: 12px; color: #495057; font-weight: 600;"><?php echo $user['id']; ?></td>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($user['name']); ?></div>
                                </td>
                                <td style="padding: 12px; color: #495057;">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td style="padding: 12px; color: #495057;">
                                    <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                                </td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: <?php echo $roleColor; ?>20; color: <?php echo $roleColor; ?>;">
                                        <?php echo strtoupper(str_replace('_', ' ', $user['role'])); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>;">
                                        <?php echo strtoupper($user['status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; color: #868e96; font-size: 13px;">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?php echo BASE_URL; ?>admin/editUser/<?php echo $user['id']; ?>" 
                                           style="background: #007bff; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        <?php if ($user['status'] === 'active'): ?>
                                            <a href="<?php echo BASE_URL; ?>admin/deactivateUser/<?php echo $user['id']; ?>" 
                                               onclick="return confirm('Are you sure you want to deactivate this user?');"
                                               style="background: #ffc107; color: #000; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">
                                                <i class="fas fa-ban"></i> Deactivate
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>admin/activateUser/<?php echo $user['id']; ?>" 
                                               style="background: #28a745; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">
                                                <i class="fas fa-check"></i> Activate
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo BASE_URL; ?>admin/deleteUser/<?php echo $user['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.');"
                                           style="background: #dc3545; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px;">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #868e96;">
                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                <p style="font-size: 18px; margin: 0;">No users found</p>
                <p style="font-size: 14px; margin-top: 10px;">Try adjusting your filters or search terms</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- User Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <i class="fas fa-users" style="font-size: 32px; opacity: 0.8; margin-bottom: 10px;"></i>
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Users</div>
            <div style="font-size: 28px; font-weight: bold;"><?php echo count($users); ?></div>
        </div>
    </div>

</div>
