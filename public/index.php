<?php
// Entry point of the application
// Bootstrap file that initializes the application

// Load configuration and core files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../helpers/session_helper.php';
require_once __DIR__ . '/../helpers/url_helper.php';
require_once __DIR__ . '/../helpers/validation_helper.php';

// Initialize the application
$urlArray = initApp();

// Route the request
routeRequest($urlArray);
?>