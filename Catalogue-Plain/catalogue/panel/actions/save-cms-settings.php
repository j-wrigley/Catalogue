<?php
/**
 * Save CMS Settings
 * Handles saving CMS system settings
 */

// Start output buffering to catch any unwanted output
ob_start();

require_once __DIR__ . '/../../config.php';

// Clean any output that might have been generated
ob_end_clean();

// Set JSON header
header('Content-Type: application/json');

// Check authentication
requireLogin();

// Verify CSRF token
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Get settings data
$settings_json = $_POST['settings_data'] ?? '{}';
$settings = json_decode($settings_json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}

// Validate settings
$validated_settings = [
    'site_name' => trim($settings['site_name'] ?? 'JSON Catalogue'),
    'accent_color' => trim($settings['accent_color'] ?? '#e11d48'),
    'accent_hover' => trim($settings['accent_hover'] ?? '#be123c'),
    'accent_text' => trim($settings['accent_text'] ?? '#ffffff'),
    'traffic_enabled' => isset($settings['traffic_enabled']) ? (bool)$settings['traffic_enabled'] : true,
];

// Validate color format and sanitize
if (!preg_match('/^#[0-9A-F]{6}$/i', $validated_settings['accent_color'])) {
    $validated_settings['accent_color'] = '#e11d48';
} else {
    $validated_settings['accent_color'] = strtoupper($validated_settings['accent_color']);
}

if (!preg_match('/^#[0-9A-F]{6}$/i', $validated_settings['accent_hover'])) {
    $validated_settings['accent_hover'] = '#be123c';
} else {
    $validated_settings['accent_hover'] = strtoupper($validated_settings['accent_hover']);
}

if (!preg_match('/^#[0-9A-F]{6}$/i', $validated_settings['accent_text'])) {
    $validated_settings['accent_text'] = '#ffffff';
} else {
    $validated_settings['accent_text'] = strtoupper($validated_settings['accent_text']);
}

// Sanitize site name (alphanumeric, spaces, hyphens, underscores)
$validated_settings['site_name'] = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $validated_settings['site_name']);

// Ensure settings directory exists
$settings_dir = CMS_ROOT . '/content';
if (!is_dir($settings_dir)) {
    if (!@mkdir($settings_dir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create settings directory']);
        exit;
    }
}

// Save settings
$settings_file = $settings_dir . '/cms-settings.json';
$result = @writeJson($settings_file, $validated_settings);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    $error_msg = 'Failed to save settings';
    if (function_exists('error_get_last')) {
        $last_error = error_get_last();
        if ($last_error && strpos($last_error['message'], 'writeJson') === false) {
            $error_msg .= ': ' . $last_error['message'];
        }
    }
    echo json_encode(['success' => false, 'error' => $error_msg]);
}
exit;

