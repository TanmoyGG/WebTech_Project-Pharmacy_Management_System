<?php 
$pageTitle = 'Customer Home';
include_once __DIR__ . '/../layouts/header.php';

// Basic product stats for quick glance
$stats = function_exists('productGetStats') ? productGetStats() : null;
?>

<div class="card">
	<div class="card-header">Welcome back</div>
	<div class="card-body">
		<p class="text-muted">Browse medicines, track orders, and manage your cart from one place.</p>
		<div class="mt-20">
			<a class="btn btn-primary" href="<?php echo BASE_URL; ?>/customer/browseMedicines">Browse Medicines</a>
			<a class="btn btn-secondary" href="<?php echo BASE_URL; ?>/customer/orderHistory">My Orders</a>
			<a class="btn btn-success" href="<?php echo BASE_URL; ?>/customer/cart">View Cart</a>
		</div>
	</div>
</div>

<?php if ($stats): ?>
<div class="stats-grid">
	<div class="stat-box">
		<h3><?php echo (int) $stats['total_products']; ?></h3>
		<p>Total products</p>
	</div>
	<div class="stat-box">
		<h3><?php echo (int) $stats['available_products']; ?></h3>
		<p>Available now</p>
	</div>
	<div class="stat-box">
		<h3><?php echo (int) $stats['total_stock']; ?></h3>
		<p>Units in stock</p>
	</div>
</div>
<?php endif; ?>

<div class="card">
	<div class="card-header">Featured medicines</div>
	<div class="card-body">
		<?php if (!empty($products)): ?>
			<div class="product-grid">
				<?php foreach ($products as $product): ?>
					<div class="product-card">
						<h3><?php echo htmlspecialchars($product['name']); ?></h3>
						<?php if (!empty($product['generic_name'])): ?>
							<p class="text-muted">Generic: <?php echo htmlspecialchars($product['generic_name']); ?></p>
						<?php endif; ?>
						<p class="price">৳ <?php echo number_format($product['price'], 2); ?></p>
						<p class="stock">In stock: <?php echo (int) $product['quantity']; ?></p>
						<div class="mt-20 flex-between">
							<form action="<?php echo BASE_URL; ?>/customer/addToCart" method="POST" style="margin:0; flex:1;">
								<input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
								<input type="hidden" name="quantity" value="1">
								<button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Add to Cart</button>
							</form>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<p class="text-muted">No products available right now. Please check back soon.</p>
		<?php endif; ?>
	</div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
