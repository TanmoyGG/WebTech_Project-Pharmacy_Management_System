/**
 * Product Form Validation (shared for add/edit product forms)
 */

function validateProductForm() {
    const name = document.getElementById('name').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const quantity = parseInt(document.getElementById('quantity').value);
    const expiry_date = document.getElementById('expiry_date').value;
    const category_id = document.getElementById('category_id').value;
    const low_stock = parseInt(document.getElementById('low_stock_threshold').value);
    
    if (name.length < 3 || name.length > 150) {
        alert('Product name must be 3-150 characters long');
        return false;
    }
    
    if (!category_id) {
        alert('Please select a category');
        return false;
    }
    
    if (price <= 0) {
        alert('Price must be greater than 0');
        return false;
    }
    
    if (quantity < 0 || !Number.isInteger(quantity)) {
        alert('Quantity must be a non-negative number');
        return false;
    }
    
    if (!expiry_date) {
        alert('Expiry date is required');
        return false;
    }
    
    const today = new Date().toISOString().split('T')[0];
    if (expiry_date <= today) {
        alert('Expiry date must be in the future');
        return false;
    }
    
    if (low_stock < 0 || !Number.isInteger(low_stock)) {
        alert('Low stock threshold must be a non-negative number');
        return false;
    }
    
    return true;
}
