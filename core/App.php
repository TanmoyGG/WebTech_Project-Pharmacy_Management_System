<?php
// Core Application Functions - Handles routing procedurally

// Load and initialize the application
function initApp() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check for "Remember Me" cookie and auto-login if user is not logged in
    if (!userIsLoggedIn() && isset($_COOKIE['pharmacy_remember_me'])) {
        require_once __DIR__ . '/../helpers/cookie_helper.php';
        require_once __DIR__ . '/../app/models/User.php';
        
        $rememberMe = getCookieRememberMe();
        if ($rememberMe && isset($rememberMe['email'])) {
            $user = userGetByEmail($rememberMe['email']);
            if ($user) {
                setUserSession($user['id'], $user['name'], $user['email'], $user['role']);
            }
        }
    }

    // Get the requested URL
    $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
    
    // If no URL specified (base URL), default to home
    if (empty($url)) {
        $url = 'home';
    }
    
    $urlArray = explode('/', $url);

    return $urlArray;
}

// Route the request to appropriate controller and method
function routeRequest($urlArray) {
    // Default controller is 'home'
    $controller = !empty($urlArray[0]) ? ucfirst($urlArray[0]) : 'Home';
    $method = !empty($urlArray[1]) ? $urlArray[1] : 'index';
    $params = array_slice($urlArray, 2);

    // Build the controller file path
    $controllerFile = __DIR__ . '/../app/controllers/' . $controller . 'Controller.php';

    // Check if controller file exists
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Create function name from controller and method
        $functionName = strtolower($controller) . '_' . $method;
        
        // Check if function exists and call it
        if (function_exists($functionName)) {
            call_user_func_array($functionName, $params);
        } else {
            // Debug: log available functions for this controller
            $availableFunctions = get_defined_functions();
            $controllerFunctions = array_filter($availableFunctions['user'], function($f) use ($controller) {
                return strpos($f, strtolower($controller) . '_') === 0;
            });
            
            showError(404, "Method not found: " . $functionName . ". Available: " . implode(', ', $controllerFunctions));
        }
    } else {
        showError(404, "Controller not found: " . $controller);
    }
}

// Render a view
function render($view, $data = []) {
    // Extract data array into individual variables
    extract($data);

    // Build view file path
    $viewFile = __DIR__ . '/../app/views/' . $view . '.php';

    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        showError(500, "View not found: " . $view);
    }
}

// Show error page
function showError($code, $message) {
    http_response_code($code);
    echo "<h1>Error $code</h1>";
    echo "<p>$message</p>";
}
?>