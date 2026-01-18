<?php 
$pageTitle = 'Reports';
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="margin-top: 20px;">
    <h1 style="color: #2c3e50; margin-bottom: 20px;">
        <i class="fas fa-chart-bar"></i> Report Generation
    </h1>

    <!-- Sales Report -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h2 style="color: #2c3e50; margin-bottom: 0px; font-size: 18px;">
                <i class="fas fa-chart-line"></i> Sales Report
            </h2>
        </div>
        <div class="card-body">
            
            <!-- Report Type -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Report Type
                </label>
                <select name="report_type" 
                        id="reportType"
                        style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
                    <option value="monthly">Monthly Report</option>
                    <option value="yearly">Yearly Report</option>
                </select>
            </div>

            <!-- Month Selection (for monthly) -->
            <div id="monthSelection">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Month
                </label>
                <select name="month" 
                        style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
                    <?php
                    $months = [
                        '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                        '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                    ];
                    $currentMonth = date('m');
                    foreach ($months as $num => $name):
                    ?>
                        <option value="<?php echo $num; ?>" <?php echo $num === $currentMonth ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Year Selection -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">
                    Year
                </label>
                <select name="year" 
                        style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
                    <?php
                    $currentYear = date('Y');
                    for ($i = 0; $i <= 5; $i++):
                        $year = $currentYear - $i;
                    ?>
                        <option value="<?php echo $year; ?>" <?php echo $i === 0 ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Generate Button -->
            <button type="submit" 
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-file-pdf"></i> Generate Sales Report
            </button>
        </form>
    </div>

    <!-- Stock Report -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px;">
            <i class="fas fa-boxes"></i> Stock Report
        </h2>
        
        <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.6;">
            Generate a comprehensive report of current stock levels, including low stock items, expired products, and inventory valuation.
        </p>

        <a href="<?php echo BASE_URL; ?>admin/generateStockReport" 
           style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 14px 24px; border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-pdf"></i> Generate Stock Report
        </a>
    </div>

    <!-- User Activity Report -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px;">
            <i class="fas fa-users"></i> User Activity Report
        </h2>
        
        <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.6;">
            Generate a report showing user registrations, active users, and account status summary.
        </p>

        <a href="<?php echo BASE_URL; ?>admin/generateUserReport" 
           style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 14px 24px; border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-pdf"></i> Generate User Report
        </a>
    </div>

    <!-- Report Info -->
    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 5px; margin-top: 30px;">
        <h4 style="margin: 0 0 10px 0; color: #856404; font-size: 15px;">
            <i class="fas fa-info-circle"></i> About Reports
        </h4>
        <p style="margin: 0; color: #856404; font-size: 13px; line-height: 1.6;">
            All reports are generated in HTML format and can be printed or saved as PDF using your browser's print function.
            Reports include comprehensive data analysis and visualizations for better decision making.
        </p>
    </div>

</div>

<script>
document.getElementById('reportType').addEventListener('change', function() {
    const monthSelection = document.getElementById('monthSelection');
    if (this.value === 'monthly') {
        monthSelection.style.display = 'block';
    } else {
        monthSelection.style.display = 'none';
    }
});
</script>

</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>