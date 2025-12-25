<?php
/**
 * Authentication Helper Functions
 * Online Notes Sharing System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && isset($_SESSION['user_email']);
}

/**
 * Require login - redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user name
 */
function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

/**
 * Get current user email
 */
function getCurrentUserEmail() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
}

/**
 * Set user session
 */
function setUserSession($user_id, $user_name, $user_email) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_email'] = $user_email;
}

/**
 * Destroy user session
 */
function destroyUserSession() {
    session_unset();
    session_destroy();
}

/**
 * Sanitize input to prevent XSS
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate mobile number (basic validation)
 */
function validateMobile($mobile) {
    return preg_match('/^[0-9]{10,15}$/', $mobile);
}

