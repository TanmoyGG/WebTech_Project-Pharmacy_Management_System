<?php 
$pageTitle = 'Browse Medicines';
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">Browse Medicines</div>
    <div class="card-body">
        <!-- Real-time Search Bar -->
        <div style="margin-bottom: 20px;">
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search by medicine name, generic name, or description..." 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                   autocomplete="off">
            <p id="searchStatus" class="text-muted" style="margin-top: 5px; font-size: 12px;"></p>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer" class="product-grid">
            <!-- Products will be loaded here via AJAX -->
        </div>
    </div>
</div>

<!-- Setup BASE_URL for JavaScript -->
<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>

<!-- Load external search script -->
<script src="<?php echo BASE_URL; ?>/assets/js/search.js"></script>

<!-- AJAX add-to-cart handler -->
<script src="<?php echo BASE_URL; ?>/assets/js/cart.js"></script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

