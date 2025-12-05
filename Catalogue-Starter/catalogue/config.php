<?php
/**
 * CMS Configuration
 * Core settings for the flat-file CMS
 */

// Define CMS_ROOT if not already defined
if (!defined('CMS_ROOT')) {
    define('CMS_ROOT', __DIR__);
}

// Base path detection (works in root or subfolder)
// Calculate CMS_URL relative to document root, not current script
$document_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
$cms_root_real = realpath(CMS_ROOT);
if ($cms_root_real && strpos($cms_root_real, $document_root) === 0) {
    // Calculate relative path from document root
    $cms_path = substr($cms_root_real, strlen($document_root));
    $cms_path = str_replace('\\', '/', $cms_path); // Normalize Windows paths
    $cms_path = rtrim($cms_path, '/');
    define('CMS_URL', $cms_path);
    
    // BASE_PATH is the parent directory if CMS is in a subdirectory
    // Remove 'catalogue' from the path to get the base path
    $path_parts = explode('/', trim($cms_path, '/'));
    if (count($path_parts) > 1) {
        // Remove the last part (should be 'catalogue' or 'cms')
        array_pop($path_parts);
        define('BASE_PATH', empty($path_parts) ? '' : '/' . implode('/', $path_parts));
    } else {
        // CMS is at root level, BASE_PATH is empty
        define('BASE_PATH', '');
    }
} else {
    // Fallback: use script name calculation
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_path = dirname($script_name);
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '';
    } else {
        $base_path = rtrim($base_path, '/');
        // Remove /panel/actions if present
        $base_path = preg_replace('#/panel/actions?$#', '', $base_path);
        $base_path = preg_replace('#/panel$#', '', $base_path);
        // Remove /catalogue if present (CMS folder name)
        $base_path = preg_replace('#/catalogue$#', '', $base_path);
    }
    define('BASE_PATH', $base_path);
    define('CMS_URL', $base_path . '/catalogue');
}

// Assets URL (same as BASE_PATH for root-level assets)
define('ASSETS_URL', defined('BASE_PATH') ? BASE_PATH : '');

// Directory paths
define('BLUEPRINTS_DIR', CMS_ROOT . '/blueprints');
define('CONTENT_DIR', CMS_ROOT . '/content');
define('PAGES_DIR', CONTENT_DIR . '/pages');
define('COLLECTIONS_DIR', CONTENT_DIR . '/collections');
define('MEDIA_METADATA_DIR', CONTENT_DIR . '/media');
define('DATA_DIR', CMS_ROOT . '/data');
define('UPLOADS_DIR', CMS_ROOT . '/uploads');
define('LIB_DIR', CMS_ROOT . '/lib');
define('PANEL_DIR', CMS_ROOT . '/panel');
define('LOGS_DIR', CMS_ROOT . '/logs');

// Site configuration
define('SITE_NAME', 'JSON Catalogue');
define('SESSION_NAME', 'cms_session');

// Security
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour

// Debug mode (set to false in production)
define('DEBUG_MODE', false);

// Security headers
if (!headers_sent()) {
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Enable XSS filter
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (adjust as needed)
    // Note: 'unsafe-inline' is needed for inline scripts/styles in this CMS
    // Consider removing in future versions
    $csp = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: http: https:; font-src 'self' data:; connect-src 'self';";
    header("Content-Security-Policy: $csp");
}

// Ensure directories exist
$required_dirs = [
    BLUEPRINTS_DIR,
    CONTENT_DIR,
    PAGES_DIR,
    COLLECTIONS_DIR,
    MEDIA_METADATA_DIR,
    DATA_DIR,
    UPLOADS_DIR,
    LIB_DIR,
    PANEL_DIR,
    LOGS_DIR
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Start session with security settings
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    
    // Security: Set secure session cookie parameters
    $cookie_params = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookie_params['lifetime'],
        'path' => $cookie_params['path'],
        'domain' => $cookie_params['domain'],
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Only send over HTTPS if available
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // CSRF protection
    ]);
    
    if (!session_start()) {
        error_log('CMS Error: Failed to start session');
    }
}

// Load library files
require_once LIB_DIR . '/util.php';
require_once LIB_DIR . '/storage.php';
require_once LIB_DIR . '/render.php';
require_once LIB_DIR . '/csrf.php';
require_once LIB_DIR . '/blueprint.php';
require_once LIB_DIR . '/auth.php';
require_once LIB_DIR . '/form.php';
require_once LIB_DIR . '/icons.php';

