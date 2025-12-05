<?php
/**
 * Utility Functions
 * Helper functions for common operations
 */

/**
 * Generate URL-safe slug from string
 */
function slugify($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Get current timestamp in ISO format
 */
function getTimestamp() {
    return date('c');
}

/**
 * Sanitize filename for safe storage
 * Preserves file extensions
 */
function sanitizeFilename($filename) {
    $filename = basename($filename);
    
    // Extract extension before sanitizing
    $extension = '';
    $name_without_ext = $filename;
    if (($pos = strrpos($filename, '.')) !== false) {
        $extension = substr($filename, $pos);
        $name_without_ext = substr($filename, 0, $pos);
    }
    
    // Sanitize the name part (preserve alphanumeric, dots, hyphens, underscores)
    $name_without_ext = preg_replace('/[^a-z0-9._-]/i', '-', $name_without_ext);
    $name_without_ext = preg_replace('/-+/', '-', $name_without_ext);
    $name_without_ext = trim($name_without_ext, '-');
    
    // Recombine
    return $name_without_ext . $extension;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if string is valid JSON
 */
function isValidJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Get relative path from CMS root
 */
function getRelativePath($path) {
    $real_path = realpath($path);
    $cms_root = realpath(CMS_ROOT);
    if ($real_path && $cms_root && strpos($real_path, $cms_root) === 0) {
        return substr($real_path, strlen($cms_root) + 1);
    }
    return $path;
}

/**
 * Format date for display
 * Converts ISO 8601 dates to readable format
 */
function formatDate($date_string, $format = 'short') {
    if (empty($date_string) || $date_string === 'Never' || $date_string === 'Unknown') {
        return $date_string;
    }
    
    try {
        $date = new DateTime($date_string);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        // For short format, show relative time if recent, otherwise formatted date
        if ($format === 'short') {
            // Less than 1 minute ago
            if ($diff->days === 0 && $diff->h === 0 && $diff->i === 0) {
                return 'Just now';
            }
            // Less than 1 hour ago
            if ($diff->days === 0 && $diff->h === 0) {
                return $diff->i . ' minute' . ($diff->i !== 1 ? 's' : '') . ' ago';
            }
            // Today
            if ($diff->days === 0) {
                return 'Today at ' . $date->format('g:i A');
            }
            // Yesterday
            if ($diff->days === 1) {
                return 'Yesterday at ' . $date->format('g:i A');
            }
            // Less than a week
            if ($diff->days < 7) {
                return $diff->days . ' day' . ($diff->days !== 1 ? 's' : '') . ' ago';
            }
            // Current year
            if ($date->format('Y') === $now->format('Y')) {
                return $date->format('M j, g:i A');
            }
            // Different year
            return $date->format('M j, Y');
        }
        
        // Long format
        if ($format === 'long') {
            return $date->format('F j, Y \a\t g:i A');
        }
        
        // Default: short format
        return $date->format('M j, Y');
    } catch (Exception $e) {
        return $date_string;
    }
}

/**
 * Get CMS name from settings or fallback to constant
 */
function getCmsName() {
    $cms_settings_file = CMS_ROOT . '/content/cms-settings.json';
    if (file_exists($cms_settings_file)) {
        $cms_settings = readJson($cms_settings_file);
        if (isset($cms_settings['site_name']) && !empty($cms_settings['site_name'])) {
            return $cms_settings['site_name'];
        }
    }
    return defined('SITE_NAME') ? SITE_NAME : 'JSON Catalogue';
}

