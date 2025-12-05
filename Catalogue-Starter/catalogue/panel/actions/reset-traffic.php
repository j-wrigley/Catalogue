<?php
/**
 * Reset Traffic Data Action
 * Deletes all traffic tracking data
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

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get traffic directory
$trafficDir = CMS_ROOT . '/data/traffic';

// Check if directory exists
if (!is_dir($trafficDir)) {
    echo json_encode(['success' => true, 'message' => 'No traffic data to reset']);
    exit;
}

// Delete all files in traffic directory
$files = glob($trafficDir . '/*.json');
$deleted = 0;
$errors = [];

foreach ($files as $file) {
    if (is_file($file)) {
        if (@unlink($file)) {
            $deleted++;
        } else {
            $errors[] = basename($file);
        }
    }
}

if (empty($errors)) {
    echo json_encode([
        'success' => true,
        'message' => "Successfully deleted {$deleted} traffic file(s)"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete some files: ' . implode(', ', $errors)
    ]);
}
exit;

