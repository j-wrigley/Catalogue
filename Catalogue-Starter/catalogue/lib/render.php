<?php
/**
 * Render Functions
 * Escaping and sanitization utilities
 */

/**
 * Escape HTML output
 */
function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Escape HTML attribute
 */
function esc_attr($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Escape URL
 */
function esc_url($url) {
    return htmlspecialchars($url ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Escape JavaScript
 */
function esc_js($string) {
    return json_encode($string ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

