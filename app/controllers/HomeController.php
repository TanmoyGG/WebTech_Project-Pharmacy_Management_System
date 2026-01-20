<?php
// Home Controller - Display home page

// Display home page
function home_index() {
    if (isLoggedIn()) {
        $role = getCurrentUserRole();
        if ($role === 'admin') {
            redirectTo('admin/dashboard');
        } elseif ($role === 'inventory_manager') {
            redirectTo('inventory_manager/dashboard');
        } else {
            redirectTo('customer/home');
        }
    }
    
    render('home/index');
}

// Display about page
function home_about() {
    render('home/about');
}

?>
