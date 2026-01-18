<?php 
$pageTitle = 'System Configuration';
include_once __DIR__ . '/../layouts/header.php';
$settings = $settings ?? [];
?>

<div style="max-width: 800px; margin: 0 auto;">
    <h1 style="color: #2c3e50; margin-bottom: 20px; margin-top: 0;">
        <i class="fas fa-cog"></i> System Configuration
    </h1>

    <div class="card">
        <div class="card-header">Configuration Settings</div>
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>admin/saveSystemConfig" id="systemConfigForm">
            
            <!-- Pharmacy Name -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 15px;">
                    <i class="fas fa-clinic-medical"></i> Pharmacy Name
                </label>
                <input type="text" 
                       name="pharmacy_name" 
                       value="<?php echo htmlspecialchars($settings['pharmacy_name'] ?? 'PharmaHealth Plus'); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="Enter pharmacy name">
                <small style="color: #6c757d; font-size: 12px;">This name will appear in invoices and reports</small>
            </div>

            <!-- Contact Email -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 15px;">
                    <i class="fas fa-envelope"></i> Contact Email
                </label>
                <input type="email" 
                       name="contact_email" 
                       value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'contact@pharmahealth.com'); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="email@example.com">
                <small style="color: #6c757d; font-size: 12px;">Customer support email address</small>
            </div>

            <!-- Contact Phone -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 15px;">
                    <i class="fas fa-phone"></i> Contact Phone
                </label>
                <input type="tel" 
                       name="contact_phone" 
                       value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '+880 XXXX-XXXXXX'); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="+880 XXXX-XXXXXX">
                <small style="color: #6c757d; font-size: 12px;">Primary contact number for inquiries</small>
            </div>

            <!-- Address -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 15px;">
                    <i class="fas fa-map-marker-alt"></i> Pharmacy Address
                </label>
                <textarea name="address" 
                          rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px; resize: vertical;"
                          placeholder="Enter complete address"><?php echo htmlspecialchars($settings['address'] ?? 'Dhaka, Bangladesh'); ?></textarea>
                <small style="color: #6c757d; font-size: 12px;">Full physical address of the pharmacy</small>
            </div>

            <!-- Tax Rate -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 15px;">
                    <i class="fas fa-percentage"></i> Tax Rate (%)
                </label>
                <input type="number" 
                       name="tax_rate" 
                       step="0.01"
                       min="0"
                       max="100"
                       value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '12.5'); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;"
                       placeholder="0.00">
                <small style="color: #6c757d; font-size: 12px;">⚠️ Enter the applicable tax rate (e.g., 12.5 for 12.5% VAT). This will automatically apply to ALL customer purchases at checkout!</small>
            </div>

            <!-- Divider -->
            <div style="border-top: 2px solid #f1f3f5; margin: 30px 0;"></div>

            <!-- Additional Settings Info -->
            <div style="background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; border-radius: 5px; margin-bottom: 25px;">
                <h4 style="margin: 0 0 10px 0; color: #004085; font-size: 15px;">
                    <i class="fas fa-info-circle"></i> Configuration Information
                </h4>
                <p style="margin: 0; color: #004085; font-size: 13px; line-height: 1.6;">
                    These settings control how your pharmacy system operates. Changes take effect immediately after saving.
                    Make sure all information is accurate before saving.
                </p>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 15px;">
                <button type="submit" 
                        style="flex: 1; background: #28a745; color: white; padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-save"></i> Save Configuration
                </button>
                <a href="<?php echo BASE_URL; ?>admin/dashboard" 
                   style="flex: 1; background: #6c757d; color: white; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

        </form>

        <!-- Current Settings Preview (From Database) -->
        <div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #f1f3f5;">
            <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 18px;">
                <i class="fas fa-database"></i> Current Settings (Fetched from Database)
            </h3>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <?php if (!empty($settings)): ?>
                    <div style="margin-bottom: 12px;">
                        <strong style="color: #495057;">Pharmacy Name:</strong> 
                        <span style="color: #2c3e50;"><?php echo !empty($settings['pharmacy_name']) ? htmlspecialchars($settings['pharmacy_name']) : '<em style="color: #999;">Not set</em>'; ?></span>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <strong style="color: #495057;">Email:</strong> 
                        <span style="color: #2c3e50;"><?php echo !empty($settings['contact_email']) ? htmlspecialchars($settings['contact_email']) : '<em style="color: #999;">Not set</em>'; ?></span>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <strong style="color: #495057;">Phone:</strong> 
                        <span style="color: #2c3e50;"><?php echo !empty($settings['contact_phone']) ? htmlspecialchars($settings['contact_phone']) : '<em style="color: #999;">Not set</em>'; ?></span>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <strong style="color: #495057;">Address:</strong> 
                        <span style="color: #2c3e50;"><?php echo !empty($settings['address']) ? htmlspecialchars($settings['address']) : '<em style="color: #999;">Not set</em>'; ?></span>
                    </div>
                    <div>
                        <strong style="color: #495057;">Tax Rate:</strong> 
                        <span style="color: #28a745; font-weight: 600;"><?php echo !empty($settings['tax_rate']) ? htmlspecialchars($settings['tax_rate']) : '<em style="color: #999;">Not set</em>'; ?>%</span>
                    </div>
                <?php else: ?>
                    <p style="color: #999; text-align: center; margin: 0;">No settings found in database. Please save the form above to initialize.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>