<?php
/**
 * Save Content Action
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

// Get content type, kind (page/collection), and data
$content_type = $_POST['content_type'] ?? '';
$content_kind = $_POST['content_kind'] ?? 'page'; // 'page' or 'collection'
$content_data = $_POST['content_data'] ?? '';

if (empty($content_type)) {
    http_response_code(400);
    echo json_encode(['error' => 'Content type required']);
    exit;
}

// Validate content_kind
if (!in_array($content_kind, ['page', 'collection'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid content kind. Must be "page" or "collection"']);
    exit;
}

// Log the incoming data for debugging (only in debug mode)
if (defined('DEBUG_MODE') && DEBUG_MODE) {
error_log("CMS Save: content_type=$content_type");
error_log("CMS Save: content_data length=" . strlen($content_data));
}

// Validate JSON
if (!isValidJson($content_data)) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_log("CMS Save: Invalid JSON detected, attempting to fix");
    }
    $content_data = json_encode(json_decode($content_data, true));
}

$data = json_decode($content_data, true);
if ($data === null) {
    error_log("CMS Save: JSON decode failed - " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

if (defined('DEBUG_MODE') && DEBUG_MODE) {
error_log("CMS Save: Decoded data: " . print_r($data, true));
}

// Sanitize content_type to prevent directory issues (remove quotes, spaces, etc.)
$content_type = preg_replace('/[^a-z0-9_-]/i', '', $content_type);

// Determine base directory based on content kind
$base_dir = ($content_kind === 'collection') ? COLLECTIONS_DIR : PAGES_DIR;

// Security: Validate path to prevent directory traversal
$content_dir = $base_dir . '/' . $content_type;
$real_content_dir = realpath($content_dir);
$real_base_dir = realpath($base_dir);

// If directory doesn't exist yet, create it (but validate it's within base_dir)
if (!is_dir($content_dir)) {
    // Validate that the path we're creating is safe
    $normalized_path = str_replace('\\', '/', $content_dir);
    $normalized_base = str_replace('\\', '/', $base_dir);
    
    if (strpos($normalized_path, $normalized_base) !== 0) {
        error_log("CMS Security: Attempted path traversal detected: $content_dir");
        http_response_code(403);
        echo json_encode(['error' => 'Invalid path']);
        exit;
    }
    
    mkdir($content_dir, 0755, true);
    $real_content_dir = realpath($content_dir);
}

// Final security check: ensure resolved path is within base directory
if ($real_content_dir === false || $real_base_dir === false || 
    strpos($real_content_dir, $real_base_dir) !== 0) {
    error_log("CMS Security: Path traversal attempt blocked: $content_dir");
    http_response_code(403);
    echo json_encode(['error' => 'Invalid path']);
    exit;
}

// Track original filepath FIRST - before any filename generation
$original_filename = $_POST['original_filename'] ?? '';

$original_filepath = null;
// Check if original_filename exists AND is not empty string
if ($content_kind === 'collection' && isset($_POST['original_filename']) && $_POST['original_filename'] !== '') {
    $original_filename = basename($original_filename);
    $original_filepath = $real_content_dir . '/' . $original_filename;
    if (!file_exists($original_filepath)) {
        $original_filepath = null; // Original file doesn't exist
    }
}

// Determine if we're editing
$is_editing = ($original_filepath && file_exists($original_filepath));

$filename = $_POST['filename'] ?? '';

// Generate filename ONLY if not editing and not provided
if (empty($filename)) {
    if ($content_kind === 'page') {
        $filename = $content_type . '.json';
    } elseif (!$is_editing) {
        // NEW collection item - generate from title
        $title = $data['title'] ?? 'item';
        $base = preg_replace('/[^a-z0-9_-]/i', '-', strtolower($title));
        $base = preg_replace('/-+/', '-', $base);
        $base = trim($base, '-');
        if (empty($base)) {
            $base = 'item';
        }
        $filename = $base . '-' . time() . '.json';
    } else {
        // EDITING - use original filename (will be updated by slug logic if slug changes)
        $filename = $original_filename;
    }
}

$filename = basename($filename); // Remove any path

if ($content_kind === 'page') {
    // For pages, ensure filename is exactly {content_type}.json
    $filename = $content_type . '.json';
} elseif (!$is_editing) {
    // For NEW collections only, sanitize the filename
    $base = preg_replace('/\.json$/i', '', $filename);
    if (empty($base) || preg_match('/^[.\-]+$/', $base)) {
        $title = $data['title'] ?? 'item';
        $base = preg_replace('/[^a-z0-9_-]/i', '-', strtolower($title));
        $base = preg_replace('/-+/', '-', $base);
        $base = trim($base, '-');
        if (empty($base)) {
            $base = 'item';
        }
        $base .= '-' . time();
    } else {
        $base = preg_replace('/[^a-z0-9_-]/i', '-', $base);
        $base = preg_replace('/-+/', '-', $base);
        $base = trim($base, '-');
        if (empty($base)) {
            $base = 'item-' . time();
        }
    }
    $filename = $base . '.json';
}
// For editing collections, filename will be updated by slug logic below

// Handle slug for collections - slug is the single source of truth for filenames
if ($content_kind === 'collection') {
    // Load existing slug if editing
    $existing_slug = null;
    if ($original_filepath && file_exists($original_filepath)) {
        $existing_item = readJson($original_filepath);
        if ($existing_item && isset($existing_item['_slug']) && !empty($existing_item['_slug'])) {
            $existing_slug = $existing_item['_slug'];
        } else {
            // Fallback: extract from filename if no slug in data
            $filename_base = preg_replace('/\.json$/', '', basename($original_filepath));
            $existing_slug = preg_replace('/-\d+$/', '', $filename_base);
        }
    }
    
    // Get new slug from data (or preserve existing if empty)
    if (empty($data['_slug']) && $existing_slug) {
        // Preserve existing slug if not provided
        $data['_slug'] = $existing_slug;
    } elseif (empty($data['_slug']) && !$existing_slug) {
        // New item - generate from title or use default
        if (!empty($data['title'])) {
            $data['_slug'] = preg_replace('/[^a-z0-9_-]/i', '-', strtolower($data['title']));
            $data['_slug'] = preg_replace('/-+/', '-', $data['_slug']);
            $data['_slug'] = trim($data['_slug'], '-');
        }
        if (empty($data['_slug'])) {
            $data['_slug'] = 'item';
        }
    }
    
    // Sanitize slug
    $slug = preg_replace('/[^a-z0-9_-]/i', '-', $data['_slug']);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    if (empty($slug)) {
        $slug = 'item';
    }
    $data['_slug'] = $slug;
    
    // Extract timestamp from original filename (preserve on rename)
    $timestamp = '';
    if ($original_filepath && file_exists($original_filepath)) {
        if (preg_match('/-(\d+)\.json$/', basename($original_filepath), $matches)) {
            $timestamp = '-' . $matches[1];
        }
    }
    
    // If no timestamp from original, generate new one for new files
    if (empty($timestamp)) {
        $timestamp = '-' . time();
    }
    
    // Build filename from slug (slug is single source of truth)
    $new_filename = $slug . $timestamp . '.json';
    
    // Determine if slug changed (for rename detection)
    $slug_changed = false;
    if ($existing_slug) {
        // Sanitize existing slug for comparison
        $existing_slug_sanitized = preg_replace('/[^a-z0-9_-]/i', '-', $existing_slug);
        $existing_slug_sanitized = preg_replace('/-+/', '-', $existing_slug_sanitized);
        $existing_slug_sanitized = trim($existing_slug_sanitized, '-');
        $slug_changed = ($existing_slug_sanitized !== $slug);
    }
    
    // Set filepath - rename if slug changed, otherwise keep original
    if ($original_filepath && file_exists($original_filepath)) {
        if ($slug_changed) {
            // Slug changed - rename file
            $filepath = $real_content_dir . '/' . $new_filename;
            $filename = $new_filename;
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("CMS Save: Slug changed - renaming from '$original_filepath' to '$filepath'");
            }
        } else {
            // Slug unchanged - keep original filename
            $filepath = $original_filepath;
            $filename = basename($original_filepath);
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("CMS Save: Slug unchanged - keeping original filepath: $filepath");
            }
        }
    } else {
        // New file - use new filename
        $filepath = $real_content_dir . '/' . $new_filename;
        $filename = $new_filename;
    }
    
    // Store slug_changed for later use in HTML generation
    $GLOBALS['cms_slug_changed'] = $slug_changed;
    $GLOBALS['cms_old_slug'] = $existing_slug ? $existing_slug : null;
} else {
    // For pages or non-collection content, set filepath normally
    $filepath = $real_content_dir . '/' . $filename;
}

// Debug logging removed for security (only log in DEBUG_MODE)

// Security: Final path validation - ensure filename is safe
$filename = basename($filename); // Remove any path components

// Security: Validate final filepath is within content directory
$final_realpath = realpath(dirname($filepath));
if ($final_realpath === false || $final_realpath !== $real_content_dir) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_log("CMS Security: Invalid filepath detected: $filepath");
    }
    http_response_code(403);
    echo json_encode(['error' => 'Invalid file path']);
    exit;
}

// Check if file exists to determine if this is a new file
$is_new_file = !file_exists($filepath);

// Load existing metadata if file exists
$existing_meta = [];
if (!$is_new_file && file_exists($filepath)) {
    $existing_content = readJson($filepath);
    if ($existing_content && isset($existing_content['_meta'])) {
        $existing_meta = $existing_content['_meta'];
    }
} elseif ($original_filepath && file_exists($original_filepath)) {
    // We're renaming - load metadata from original file
    $existing_content = readJson($original_filepath);
    if ($existing_content && isset($existing_content['_meta'])) {
        $existing_meta = $existing_content['_meta'];
    }
    $is_new_file = false; // It's actually a rename, not a new file
}

// Add/update metadata
$data['_meta'] = [
    'updated' => getTimestamp(),
    'updated_by' => $_SESSION['username'] ?? 'unknown'
];

// Preserve created date if editing existing file
if (!$is_new_file && isset($existing_meta['created'])) {
    $data['_meta']['created'] = $existing_meta['created'];
} else {
    // New file - set created timestamp
    $data['_meta']['created'] = getTimestamp();
    $data['_meta']['created_by'] = $_SESSION['username'] ?? 'unknown';
}

// Debug logging removed for security (only log in DEBUG_MODE)

// Security: Final path validation - ensure filename is safe
$filename = basename($filename); // Remove any path components

// Write the updated content
if (writeJson($filepath, $data)) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_log("CMS Save: Successfully wrote to $filepath");
    }
    
    // If slug changed, delete old file after successful write
    if ($original_filepath && file_exists($original_filepath) && $original_filepath !== $filepath) {
        // Delete old file after successful save to new location
        if (unlink($original_filepath)) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("CMS Save: Deleted old file after rename: $original_filepath");
            }
        } else {
            error_log("CMS Save: Warning - Failed to delete old file");
        }
    }
    
    // Generate data file for frontend (skip settings - they're server-side only)
    if ($content_type !== 'settings') {
    generateDataFile($content_type, $filepath, $content_kind);
    }
    
    // Generate HTML file(s) - suppress ALL output to avoid corrupting JSON response
    $htmlGenerationError = null;
    $outputLevel = ob_get_level();
    
    // Start output buffering at the highest level
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Ensure we have a clean buffer
    ob_start();
    
    try {
        require_once CMS_ROOT . '/lib/generator.php';
        
        if ($content_kind === 'collection') {
            // Get slug from saved data (it should be in the JSON file now)
            $saved_data = readJson($filepath);
            $slug = $saved_data['_slug'] ?? null;
            
            if (!$slug) {
                // Fallback: extract from filename
                $base = preg_replace('/\.json$/', '', $filename);
                $slug = preg_replace('/-\d+$/', '', $base);
            }
            
            // Sanitize slug to match HTML generation
            $slug = preg_replace('/[^a-z0-9-]/i', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            if (empty($slug)) {
                $slug = 'item';
            }
            
            // Delete old HTML file if slug changed
            $slug_changed = $GLOBALS['cms_slug_changed'] ?? false;
            $old_slug = $GLOBALS['cms_old_slug'] ?? null;
            
            if ($slug_changed && $old_slug) {
                // Sanitize old slug
                $old_slug_sanitized = preg_replace('/[^a-z0-9-]/i', '-', $old_slug);
                $old_slug_sanitized = preg_replace('/-+/', '-', $old_slug_sanitized);
                $old_slug_sanitized = trim($old_slug_sanitized, '-');
                
                if ($old_slug_sanitized && $old_slug_sanitized !== $slug) {
                    $old_html_file = CMS_ROOT . '/../' . $content_type . '/' . $old_slug_sanitized . '.html';
                    if (file_exists($old_html_file)) {
                        unlink($old_html_file);
                        error_log("CMS Save: Deleted old HTML file (slug changed from '$old_slug_sanitized' to '$slug')");
                    }
                }
            }
            
            // Generate collection HTML - regenerate ALL items to ensure consistency
            // This ensures the new item appears and any archive/list pages are updated
            @generateHtmlFile($content_type, $content_kind, null);
            
            // Also regenerate home page
            @generateHomeHtml();
        } else {
            // Generate page HTML
            @generateHtmlFile($content_type, $content_kind);
            
            // If home page was saved, also regenerate via generateHomeHtml() to ensure index.html is updated
            // (generatePageHtml already handles home -> index.html, but this ensures consistency)
            if ($content_type === 'home') {
                @generateHomeHtml();
            }
        }
        
        // If settings were saved, regenerate all HTML files to pick up new settings
        if ($content_type === 'settings') {
            // Regenerate all pages and collections to pick up new settings
            $all_blueprints = getAllBlueprints();
            foreach ($all_blueprints as $type => $blueprint) {
                // Skip settings itself
                if ($type === 'settings') {
                    continue;
                }
                
                // Check if it's a page or collection
                $page_dir = PAGES_DIR . '/' . $type;
                $collection_dir = COLLECTIONS_DIR . '/' . $type;
                
                if (is_dir($page_dir)) {
                    @generateHtmlFile($type, 'page');
                } elseif (is_dir($collection_dir)) {
                    @generateHtmlFile($type, 'collection');
                }
            }
            
            // Regenerate home page
            @generateHomeHtml();
        }
    } catch (Exception $e) {
        // Log error but don't fail the save
        error_log("CMS Save: HTML generation error: " . $e->getMessage());
        $htmlGenerationError = $e->getMessage();
    } catch (Throwable $e) {
        // Catch any other throwable (including fatal errors)
        error_log("CMS Save: HTML generation fatal error: " . $e->getMessage());
        $htmlGenerationError = $e->getMessage();
    }
    
    // Discard ALL output from HTML generation
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Ensure we're outputting JSON
    header('Content-Type: application/json');
    
    $response = ['success' => true, 'file' => $filename];
    if ($htmlGenerationError) {
        $response['html_generation_warning'] = $htmlGenerationError;
    }
    
    echo json_encode($response);
    exit; // Ensure nothing else outputs
} else {
    error_log("CMS Save: Failed to write file");
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save content. Check file permissions.']);
}

/**
 * Generate data file for frontend
 * For pages: copies content to /data/{type}.json
 * For collections: aggregates all files into /data/{type}.json array
 */
function generateDataFile($content_type, $content_file, $content_kind) {
    if ($content_kind === 'page') {
        // Single page - copy directly
        $content = readJson($content_file);
        if (!$content) {
            return false;
        }
        
        // Remove metadata before saving to data directory
        if (isset($content['_meta'])) {
            unset($content['_meta']);
        }
        
        $data_file = DATA_DIR . '/' . $content_type . '.json';
        return writeJson($data_file, $content);
    } else {
        // Collection - aggregate all files
        $content_dir = dirname($content_file);
        $json_files = listJsonFiles($content_dir);
        
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
        $data_file = DATA_DIR . '/' . $content_type . '.json';
        return writeJson($data_file, $items);
    }
}

