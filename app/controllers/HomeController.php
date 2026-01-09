<?php
// Home Controller - Display home page
// All functions follow procedural pattern: home_[action]()

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

// Display contact page
function home_contact() {
    render('home/contact');
}

// Process contact form
function home_sendContact() {
    if (!isPost()) {
        redirectTo('home/contact');
    }
    
    $name = sanitize(getPost('name', ''));
    $email = sanitizeEmail(getPost('email', ''));
    $message = sanitize(getPost('message', ''));
    
    if (isEmpty($name) || isEmpty($email) || isEmpty($message)) {
        setFlash('All fields are required', 'error');
        redirectTo('home/contact');
    }
    
    if (!validateEmail($email)) {
        setFlash('Invalid email address', 'error');
        redirectTo('home/contact');
    }
    
    // TODO: Send email to admin
    
    setFlash('Message sent successfully', 'success');
    redirectTo('home/contact');
}
?>
