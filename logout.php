<?php
/**
 * Logout Handler
 * Online Notes Sharing System
 */

require_once 'includes/auth.php';

// Destroy session
destroyUserSession();

// Redirect to login page
header('Location: login.php');
exit();

