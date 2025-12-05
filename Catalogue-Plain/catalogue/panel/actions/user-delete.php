<?php
/**
 * User Delete Action
 */
define('CMS_ROOT', dirname(__FILE__) . '/../..');
require_once CMS_ROOT . '/config.php';

requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrf_token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Get username
$username = trim($_POST['username'] ?? '');

if (empty($username)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username is required']);
    exit;
}

// Prevent deleting yourself
if ($username === $_SESSION['username']) {
    http_response_code(400);
    echo json_encode(['error' => 'You cannot delete your own account']);
    exit;
}

// Delete user
if (deleteUser($username)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete user']);
}

