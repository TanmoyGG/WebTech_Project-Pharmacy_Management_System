/**
 * Real-time Medicine Search (Client-side logic)
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const productsContainer = document.getElementById('productsContainer');
    const searchStatus = document.getElementById('searchStatus');
    let searchTimeout;

    if (!searchInput) return; // Exit if search not on page

    // Load all products on page load
    loadProducts('');

    // Search as user types
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        // Debounce the search (wait 300ms after user stops typing)
        searchTimeout = setTimeout(() => {
            const query = this.value.trim();
            loadProducts(query);
        }, 300);
    });
});

// Function to load and display products
function loadProducts(searchQuery) {
    const productsContainer = document.getElementById('productsContainer');
    const searchStatus = document.getElementById('searchStatus');
    
    // Show loading status
    productsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;">Loading...</p>';
    
    // Get BASE_URL from window variable set in view
    const baseUrl = window.BASE_URL || '';
    
    // Make AJAX request
    fetch(baseUrl + 'customer/searchMedicines?q=' + encodeURIComponent(searchQuery))
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            
            if (data.success && data.products && data.products.length > 0) {
                displayProducts(data.products);
                updateSearchStatus(data.count, searchQuery);
            } else if (data.success && data.products && data.products.length === 0) {
                productsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;">No medicines found.</p>';
                searchStatus.textContent = '';
            } else if (!data.success) {
                productsContainer.innerHTML = '<p class="text-danger" style="text-align: center; padding: 20px;">Error: ' + (data.error || 'Unknown error') + '</p>';
            } else {
                productsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;">No medicines found.</p>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            productsContainer.innerHTML = '<p class="text-danger" style="text-align: center; padding: 20px;">Error loading products: ' + error.message + '</p>';
        });
}

// Display products in grid
function displayProducts(products) {
    const productsContainer = document.getElementById('productsContainer');
    const baseUrl = window.BASE_URL || '';
    
    if (products.length === 0) {
        productsContainer.innerHTML = '<p class="text-muted" style="text-align: center; padding: 20px;">No medicines available.</p>';
        return;
    }
    
    let html = '';
    products.forEach(product => {
        const desc = (product.description || '').substring(0, 80) + (product.description && product.description.length > 80 ? '...' : '');
        html += `
            <div class="product-card">
                <h3>${escapeHtml(product.name)}</h3>
                <p class="text-muted">Generic: ${escapeHtml(product.generic_name || 'N/A')}</p>
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">${escapeHtml(desc)}</p>
                <p class="price">৳ ${parseFloat(product.price).toFixed(2)}</p>
                <p class="stock">Stock: ${parseInt(product.quantity)}</p>
                <form action="${baseUrl}customer/addToCart" method="POST">
                    <input type="hidden" name="product_id" value="${parseInt(product.id)}">
                    <div style="margin-bottom: 10px;">
                        <label for="qty_${parseInt(product.id)}">Quantity:</label>
                        <input type="number" id="qty_${parseInt(product.id)}" name="quantity" value="1" min="1" max="${parseInt(product.quantity)}" style="width: 60px; padding: 5px;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Add to Cart</button>
                </form>
            </div>
        `;
    });
    
    productsContainer.innerHTML = html;
}

// Update search status text
function updateSearchStatus(count, query) {
    const searchStatus = document.getElementById('searchStatus');
    
    if (query) {
        searchStatus.textContent = `Found ${count} result${count !== 1 ? 's' : ''} for "${escapeHtml(query)}"`;
    } else {
        searchStatus.textContent = `Showing all ${count} available medicines`;
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
