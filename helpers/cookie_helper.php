<?php
// Cookie Helper Functions (Procedural)
// Manages persistent cookie data for user preferences, recent views, and remember-me functionality

// Cookie expiration constants
define('COOKIE_EXPIRY_REMEMBER_ME', time() + (30 * 24 * 60 * 60)); // 30 days
define('COOKIE_EXPIRY_PREFERENCE', time() + (365 * 24 * 60 * 60)); // 1 year
define('COOKIE_EXPIRY_RECENTLY_VIEWED', time() + (90 * 24 * 60 * 60)); // 90 days

// Set remember me cookie for auto-login
function setCookieRememberMe($userId, $email, $token) {
    $cookieData = json_encode([
        'user_id' => $userId,
        'email' => $email,
        'token' => $token,
        'created' => time()
    ]);
    
    setcookie('pharmacy_remember_me', $cookieData, COOKIE_EXPIRY_REMEMBER_ME, '/', '', false, true);
}

// Get remember me cookie data
function getCookieRememberMe() {
    if (isset($_COOKIE['pharmacy_remember_me'])) {
        $data = json_decode($_COOKIE['pharmacy_remember_me'], true);
        return $data;
    }
    return null;
}

// Clear remember me cookie (on logout)
function clearCookieRememberMe() {
    setcookie('pharmacy_remember_me', '', time() - 3600, '/', '', false, true);
    unset($_COOKIE['pharmacy_remember_me']);
}

// Add product to recently viewed products cookie
function addRecentlyViewedProduct($productId, $productName) {
    $recently_viewed = [];
    
    // Get existing data
    if (isset($_COOKIE['pharmacy_recently_viewed'])) {
        $recently_viewed = json_decode($_COOKIE['pharmacy_recently_viewed'], true);
        if (!is_array($recently_viewed)) {
            $recently_viewed = [];
        }
    }
    
    // Remove if already exists and add to beginning
    $recently_viewed = array_filter($recently_viewed, function($item) use ($productId) {
        return $item['product_id'] != $productId;
    });
    
    // Add to beginning (most recent)
    array_unshift($recently_viewed, [
        'product_id' => $productId,
        'product_name' => $productName,
        'viewed_at' => time()
    ]);
    
    // Keep only last 10 products
    $recently_viewed = array_slice($recently_viewed, 0, 10);
    
    setcookie('pharmacy_recently_viewed', json_encode($recently_viewed), COOKIE_EXPIRY_RECENTLY_VIEWED, '/', '', false, true);
}

// Get recently viewed products
function getRecentlyViewedProducts() {
    if (isset($_COOKIE['pharmacy_recently_viewed'])) {
        $viewed = json_decode($_COOKIE['pharmacy_recently_viewed'], true);
        return is_array($viewed) ? $viewed : [];
    }
    return [];
}

// Clear recently viewed products cookie
function clearRecentlyViewedProducts() {
    setcookie('pharmacy_recently_viewed', '', time() - 3600, '/', '', false, true);
    unset($_COOKIE['pharmacy_recently_viewed']);
}

// Set user preference cookie (theme, language, etc)
function setCookiePreference($key, $value) {
    $preferences = [];
    
    if (isset($_COOKIE['pharmacy_preferences'])) {
        $preferences = json_decode($_COOKIE['pharmacy_preferences'], true);
        if (!is_array($preferences)) {
            $preferences = [];
        }
    }
    
    $preferences[$key] = $value;
    setcookie('pharmacy_preferences', json_encode($preferences), COOKIE_EXPIRY_PREFERENCE, '/', '', false, true);
}

// Get user preference cookie
function getCookiePreference($key = null) {
    if (!isset($_COOKIE['pharmacy_preferences'])) {
        return null;
    }
    
    $preferences = json_decode($_COOKIE['pharmacy_preferences'], true);
    
    if ($key === null) {
        return $preferences;
    }
    
    return $preferences[$key] ?? null;
}

// Set last browsed category
function setCookieLastCategory($categoryId) {
    setcookie('pharmacy_last_category', $categoryId, COOKIE_EXPIRY_PREFERENCE, '/', '', false, true);
}

// Get last browsed category
function getCookieLastCategory() {
    return $_COOKIE['pharmacy_last_category'] ?? null;
}

// Set search history
function addSearchHistory($query) {
    $search_history = [];
    
    if (isset($_COOKIE['pharmacy_search_history'])) {
        $search_history = json_decode($_COOKIE['pharmacy_search_history'], true);
        if (!is_array($search_history)) {
            $search_history = [];
        }
    }
    
    // Remove duplicate if exists
    $search_history = array_filter($search_history, function($item) use ($query) {
        return $item['query'] !== $query;
    });
    
    // Add to beginning
    array_unshift($search_history, [
        'query' => $query,
        'searched_at' => time()
    ]);
    
    // Keep only last 5 searches
    $search_history = array_slice($search_history, 0, 5);
    
    setcookie('pharmacy_search_history', json_encode($search_history), COOKIE_EXPIRY_PREFERENCE, '/', '', false, true);
}

// Get search history
function getSearchHistory() {
    if (isset($_COOKIE['pharmacy_search_history'])) {
        $history = json_decode($_COOKIE['pharmacy_search_history'], true);
        return is_array($history) ? $history : [];
    }
    return [];
}

// Clear all pharmacy cookies
function clearAllPharmacyCookies() {
    clearCookieRememberMe();
    clearRecentlyViewedProducts();
    
    $cookies = ['pharmacy_preferences', 'pharmacy_last_category', 'pharmacy_search_history'];
    foreach ($cookies as $cookie) {
        if (isset($_COOKIE[$cookie])) {
            setcookie($cookie, '', time() - 3600, '/', '', false, true);
            unset($_COOKIE[$cookie]);
        }
    }
}
?>
