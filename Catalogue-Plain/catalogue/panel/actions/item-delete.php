<?php
/**
 * Delete Collection Item Action
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

// Get collection type and item ID
$collection_type = $_POST['collection_type'] ?? '';
$item_id = $_POST['item_id'] ?? '';

if (empty($collection_type) || empty($item_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Collection type and item ID required']);
    exit;
}

// Sanitize
$collection_type = preg_replace('/[^a-z0-9_-]/i', '', $collection_type);
$item_id = sanitizeFilename($item_id);

// Ensure .json extension
if (getFileExtension($item_id) !== 'json') {
    $item_id .= '.json';
}

// Security: Validate path to prevent directory traversal
$filepath = COLLECTIONS_DIR . '/' . $collection_type . '/' . $item_id;
$real_filepath = realpath($filepath);
$real_collections_dir = realpath(COLLECTIONS_DIR);

// Validate that file is within collections directory
if ($real_filepath === false || $real_collections_dir === false || 
    strpos($real_filepath, $real_collections_dir) !== 0) {
    error_log("CMS Security: Path traversal attempt blocked in delete: $filepath");
    http_response_code(403);
    echo json_encode(['error' => 'Invalid path']);
    exit;
}

if (!file_exists($filepath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Item not found']);
    exit;
}

// Delete the file
if (deleteJson($filepath)) {
    // Regenerate data file for frontend
    $content_dir = COLLECTIONS_DIR . '/' . $collection_type;
    $json_files = listJsonFiles($content_dir);
    
    if (count($json_files) === 0) {
        // No items left, delete data file
        $data_file = DATA_DIR . '/' . $collection_type . '.json';
        if (file_exists($data_file)) {
            unlink($data_file);
        }
    } else {
        // Regenerate collection data file
        $items = [];
        foreach ($json_files as $json_file) {
            $item = readJson($json_file);
            if ($item && isset($item['_meta'])) {
                unset($item['_meta']);
            }
            if ($item) {
                $items[] = $item;
            }
        }
        $data_file = DATA_DIR . '/' . $collection_type . '.json';
        writeJson($data_file, $items);
    }
    
    // Regenerate HTML files - suppress ALL output to avoid corrupting JSON response
    $htmlGenerationError = null;
    
    // Start output buffering at the highest level
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Ensure we have a clean buffer
    ob_start();
    
    try {
        require_once CMS_ROOT . '/lib/generator.php';
        @generateHtmlFile($collection_type, 'collection');
    } catch (Exception $e) {
        error_log("CMS Delete: HTML generation error: " . $e->getMessage());
        $htmlGenerationError = $e->getMessage();
    } catch (Throwable $e) {
        error_log("CMS Delete: HTML generation fatal error: " . $e->getMessage());
        $htmlGenerationError = $e->getMessage();
    }
    
    // Discard ALL output from HTML generation
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Delete item HTML file if it exists
    $slug = preg_replace('/\.json$/', '', $item_id);
    $slug = preg_replace('/-\d+$/', '', $slug);
    $itemHtmlFile = CMS_ROOT . '/../' . $collection_type . '/' . $slug . '.html';
    if (file_exists($itemHtmlFile)) {
        unlink($itemHtmlFile);
        error_log("CMS Delete: Removed HTML file: $itemHtmlFile");
    }
    
    // Ensure we're outputting JSON
    header('Content-Type: application/json');
    
    $response = ['success' => true];
    if ($htmlGenerationError) {
        $response['html_generation_warning'] = $htmlGenerationError;
    }
    
    echo json_encode($response);
    exit; // Ensure nothing else outputs
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete item']);
}
