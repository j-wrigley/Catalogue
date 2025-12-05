<?php
/**
 * Upload Action
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

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allowed_mime_types = [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/gif',
    'image/webp'
];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload error']);
    exit;
}

if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large']);
    exit;
}

// Security: Validate file extension
$extension = getFileExtension($file['name']);
if (!in_array($extension, $allowed_extensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Security: Validate MIME type (prevent fake extensions)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_mime_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Security: Additional validation for image files
if (strpos($mime_type, 'image/') === 0) {
    $image_info = @getimagesize($file['tmp_name']);
    if ($image_info === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image file']);
        exit;
    }
}

// Generate unique filename
$filename = sanitizeFilename($file['name']);
$name = pathinfo($filename, PATHINFO_FILENAME);
$ext = pathinfo($filename, PATHINFO_EXTENSION);
$unique_filename = $name . '_' . time() . '.' . $ext;

$destination = UPLOADS_DIR . '/' . $unique_filename;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $url = CMS_URL . '/uploads/' . $unique_filename;
    echo json_encode([
        'success' => true,
        'url' => $url,
        'filename' => $unique_filename
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
}

