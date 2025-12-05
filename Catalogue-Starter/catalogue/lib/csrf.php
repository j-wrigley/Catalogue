<?php
/**
 * CSRF Protection
 * Generate and validate CSRF tokens
 */

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiry'] = time() + CSRF_TOKEN_EXPIRY;
    }
    
    // Regenerate if expired
    if (time() > $_SESSION['csrf_token_expiry']) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiry'] = time() + CSRF_TOKEN_EXPIRY;
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        return false;
    }
    
    // Check expiry
    if (time() > $_SESSION['csrf_token_expiry']) {
        return false;
    }
    
    // Compare tokens
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token HTML input
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . esc_attr($token) . '">';
}

