<?php
/**
 * CMS Router
 * Main entry point for admin panel
 */

// Enable error reporting for debugging (DISABLE IN PRODUCTION)
// In production, set display_errors to 0 and keep log_errors enabled
error_reporting(E_ALL);
ini_set('display_errors', 0); // SECURITY: Don't display errors in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Prevent output buffering issues
if (ob_get_level()) {
    ob_end_clean();
}

define('CMS_ROOT', dirname(__FILE__));

// Check if config exists
if (!file_exists(CMS_ROOT . '/config.php')) {
    die('Error: config.php not found');
}

require_once CMS_ROOT . '/config.php';

// Check session timeout
checkSessionTimeout();

// Get page from query string and sanitize
$page = $_GET['page'] ?? 'dashboard';
$page = preg_replace('/[^a-z0-9_-]/i', '', $page); // Sanitize to prevent path traversal
if (empty($page)) {
    $page = 'dashboard';
}

// Redirect to login if not authenticated (except login page)
if ($page !== 'login' && !isLoggedIn()) {
    header('Location: ' . CMS_URL . '/index.php?page=login');
    exit;
}

// Redirect to dashboard if already logged in and trying to access login
if ($page === 'login' && isLoggedIn()) {
    header('Location: ' . CMS_URL . '/index.php?page=dashboard');
    exit;
}

// Require login for protected pages
if ($page !== 'login') {
    requireLogin();
}

// Map page to file
$page_map = [
    'dashboard' => 'dashboard.php',
    'login' => 'login.php',
    'pages' => 'pages.php',
    'collections' => 'collections.php',
    'media' => 'media.php',
    'settings' => 'settings.php',
    'cms-settings' => 'cms-settings.php',
    'users' => 'users.php',
    'test' => 'test.php',
];

$page_file = $page_map[$page] ?? 'dashboard.php';
$page_path = PANEL_DIR . '/pages/' . $page_file;

// Include page if exists, otherwise show 404
if (file_exists($page_path)) {
    require_once $page_path;
} else {
    http_response_code(404);
    echo '<h1>Page not found</h1>';
}

