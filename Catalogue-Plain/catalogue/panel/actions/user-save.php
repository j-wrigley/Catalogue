<?php
/**
 * User Save Action
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

// Get form data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$existing_username = $_POST['existing_username'] ?? null;

if (empty($username)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username is required']);
    exit;
}

// Validate username format
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username can only contain letters, numbers, underscores, and hyphens']);
    exit;
}

// Check if username already exists (if creating new user or changing username)
if ($existing_username !== $username) {
    $existing_user = getUserByUsername($username);
    if ($existing_user) {
        http_response_code(400);
        echo json_encode(['error' => 'Username already exists']);
        exit;
    }
}

// If updating and password is empty, don't change password
$password_to_save = null;
if (!empty($password)) {
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters long']);
        exit;
    }
    $password_to_save = $password;
}

// Save user
if (saveUser($username, $password_to_save, $existing_username ?: null)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save user']);
}

