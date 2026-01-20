<?php 
$pageTitle = 'Add New Product';
include_once __DIR__ . '/../layouts/header.php';
$categories = $categories ?? [];
?>

<div class="container" style="margin-top: 20px;">
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <h2 style="margin: 0;">Add New Medicine</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">
                <strong>Low Stock Alert:</strong> Triggers a warning when inventory drops below this quantity
            </p>
        </div>
        <div class="card-body">
            <!-- Display errors if any -->
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 10px 0 0 20px; padding: 0;">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form method="POST" action="<?php echo BASE_URL; ?>inventory_manager/addProductProcess" onsubmit="return validateProductForm()">
                <!-- Product Name & Generic Name -->
                <div class="grid-2">
                    <div class="form-group">
                        <label for="name">Product Name <span style="color: red;">*</span> <span style="font-size: 12px; color: #666;">(3-150 chars)</span></label>
                        <input type="text" id="name" name="name" required minlength="3" maxlength="150"
                               value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>"
                               placeholder="e.g. Napa Extra">
                    </div>

                    <div class="form-group">
                        <label for="generic_name">Generic Name</label>
                        <input type="text" id="generic_name" name="generic_name" 
                               value="<?php echo isset($_SESSION['form_data']['generic_name']) ? htmlspecialchars($_SESSION['form_data']['generic_name']) : ''; ?>"
                               placeholder="e.g. Paracetamol 500mg">
                    </div>
                </div>

                <!-- Category & Status -->
                <div class="grid-2">
                    <div class="form-group">
                        <label for="category_id">Category <span style="color: red;">*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo (isset($_SESSION['form_data']['category_id']) && $_SESSION['form_data']['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span style="color: red;">*</span></label>
                        <select id="status" name="status" required>
                            <option value="available" <?php echo (!isset($_SESSION['form_data']['status']) || $_SESSION['form_data']['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                            <option value="out_of_stock" <?php echo (isset($_SESSION['form_data']['status']) && $_SESSION['form_data']['status'] === 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                            <option value="discontinued" <?php echo (isset($_SESSION['form_data']['status']) && $_SESSION['form_data']['status'] === 'discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Enter product description"><?php echo isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : ''; ?></textarea>
                </div>

                <!-- Price, Quantity & Low Stock Threshold -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="price">Price (৳) <span style="color: red;">*</span> <span style="font-size: 12px; color: #666;"> &gt; 0</span></label>
                        <input type="number" id="price" name="price" step="0.01" min="0.01" required 
                               value="<?php echo isset($_SESSION['form_data']['price']) ? $_SESSION['form_data']['price'] : ''; ?>"
                               placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity <span style="color: red;">*</span></label>
                        <input type="number" id="quantity" name="quantity" min="0" required 
                               value="<?php echo isset($_SESSION['form_data']['quantity']) ? $_SESSION['form_data']['quantity'] : '0'; ?>"
                               placeholder="0">
                    </div>

                    <div class="form-group">
                        <label for="low_stock_threshold">Low Stock Alert <span style="font-size: 12px; color: #666;">( ≥ 0)</span></label>
                        <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" 
                               value="<?php echo isset($_SESSION['form_data']['low_stock_threshold']) ? $_SESSION['form_data']['low_stock_threshold'] : '10'; ?>"
                               placeholder="10">
                    </div>
                </div>

                <!-- Manufacture Date & Expiry Date -->
                <div class="grid-2">
                    <div class="form-group">
                        <label for="manufacture_date">Manufacture Date</label>
                        <input type="date" id="manufacture_date" name="manufacture_date" 
                               value="<?php echo isset($_SESSION['form_data']['manufacture_date']) ? $_SESSION['form_data']['manufacture_date'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="expiry_date">Expiry Date <span style="color: red;">*</span> <span style="font-size: 12px; color: #666;">(future date)</span></label>
                        <input type="date" id="expiry_date" name="expiry_date" required 
                               value="<?php echo isset($_SESSION['form_data']['expiry_date']) ? $_SESSION['form_data']['expiry_date'] : ''; ?>">
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success">Add Product</button>
                    <a href="<?php echo BASE_URL; ?>inventory_manager/products" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Clear form data
unset($_SESSION['form_data']);
include_once __DIR__ . '/../layouts/footer.php'; 
?>

<script src="<?php echo BASE_URL; ?>/assets/js/product-validation.js"></script>
