<?php
/**
 * Media Action Handler
 * Handles file uploads, folder creation, and deletions
 */
define('CMS_ROOT', dirname(__FILE__) . '/../..');
require_once CMS_ROOT . '/config.php';

requireLogin();

// Increase upload limits programmatically (fallback if .htaccess doesn't work)
@ini_set('upload_max_filesize', '50M');
@ini_set('post_max_size', '50M');
@ini_set('max_execution_time', '300');
@ini_set('max_input_time', '300');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if POST data was truncated due to size limits
if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $max_size = ini_get('post_max_size');
    http_response_code(413);
    echo json_encode(['error' => 'File size exceeds server limit (' . $max_size . '). Please upload smaller files.']);
    exit;
}

// Validate CSRF token (only if POST data exists)
if (empty($_POST['csrf_token'])) {
    // If we have files but no POST data, it might be a size limit issue
    if (!empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $max_size = ini_get('post_max_size');
        http_response_code(413);
        echo json_encode(['error' => 'File size exceeds server limit (' . $max_size . '). Please upload smaller files.']);
        exit;
    }
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$csrf_token = $_POST['csrf_token'];
if (!validateCsrfToken($csrf_token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        handleList();
        break;
    
    case 'upload':
        handleUpload();
        break;
    
    case 'create_folder':
        handleCreateFolder();
        break;
    
    case 'delete_file':
        handleDeleteFile();
        break;
    
    case 'delete_folder':
        handleDeleteFolder();
        break;
    
    case 'list_folders':
        handleListFolders();
        break;
    
    case 'move':
        handleMove();
        break;
    
    case 'get_metadata':
        handleGetMetadata();
        break;
    
    case 'save_metadata':
        handleSaveMetadata();
        break;
    
    case 'get_metadata_form':
        handleGetMetadataForm();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
}

/**
 * Recursively get all files from directory and subdirectories
 */
function getAllFilesRecursiveAction($dir, $base_dir = null) {
    if ($base_dir === null) {
        $base_dir = $dir;
    }
    
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item[0] === '.') continue;
        if ($item === 'index.php' || $item === '.htaccess' || $item === '.gitkeep') continue;
        
        $item_path = $dir . '/' . $item;
        $relative_path = ltrim(str_replace($base_dir, '', $item_path), '/');
        
        if (is_dir($item_path)) {
            // Recursively get files from subdirectory
            $subfiles = getAllFilesRecursiveAction($item_path, $base_dir);
            $files = array_merge($files, $subfiles);
        } elseif (is_file($item_path)) {
            $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            $is_image = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            
            $url = CMS_URL . '/uploads/' . $relative_path;
            
            $item_data = [
                'name' => $item,
                'type' => $is_image ? 'image' : 'file',
                'path' => '/' . $relative_path,
                'url' => $url,
                'size' => filesize($item_path),
                'modified' => filemtime($item_path),
                'folder' => dirname($relative_path) !== '.' ? dirname($relative_path) : ''
            ];
            
            // Add image dimensions if it's an image
            if ($is_image) {
                $image_info = @getimagesize($item_path);
                if ($image_info) {
                    $item_data['width'] = $image_info[0];
                    $item_data['height'] = $image_info[1];
                }
            }
            
            $files[] = $item_data;
        }
    }
    
    return $files;
}

/**
 * Handle listing media files
 */
function handleList() {
    $folder = $_POST['folder'] ?? '/';
    $folder = preg_replace('/[^a-z0-9_\/-]/i', '', $folder); // Sanitize
    $folder = trim($folder, '/');
    
    // Build directory path
    $directory = UPLOADS_DIR;
    if (!empty($folder)) {
        $directory .= '/' . $folder;
    }
    
    // Security: Ensure directory path is within uploads directory
    $real_directory = realpath($directory);
    $real_uploads = realpath(UPLOADS_DIR);
    if (!$real_directory || strpos($real_directory, $real_uploads) !== 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid folder path']);
        exit;
    }
    
    if (!is_dir($directory)) {
        http_response_code(404);
        echo json_encode(['error' => 'Directory not found']);
        exit;
    }
    
    $items = [];
    
    // If "All Media" (empty folder), get all files recursively
    if (empty($folder)) {
        $items = getAllFilesRecursiveAction(UPLOADS_DIR);
    } else {
        // Get files from specific folder only
        $files = scandir($directory);
        
        // Performance: Limit file processing to prevent memory issues
        $max_files = 1000;
        $file_count = 0;
        
        foreach ($files as $file) {
            // Skip hidden files and system files
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === '.gitkeep' || $file === 'index.php') {
                continue;
            }
            
            // Performance: Limit processing to prevent memory issues
            if ($file_count >= $max_files) {
                break;
            }
            
            $file_path = $directory . '/' . $file;
            $relative_path = (!empty($folder) ? $folder . '/' : '') . $file;
            
            if (is_dir($file_path)) {
                $items[] = [
                    'name' => $file,
                    'type' => 'folder',
                    'path' => '/' . $relative_path,
                    'modified' => filemtime($file_path)
                ];
                $file_count++;
            } else {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $is_image = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                
                $url = CMS_URL . '/uploads/' . $relative_path;
                
                $item = [
                    'name' => $file,
                    'type' => $is_image ? 'image' : 'file',
                    'path' => '/' . $relative_path,
                    'url' => $url,
                    'size' => filesize($file_path),
                    'modified' => filemtime($file_path)
                ];
                
                // Add image dimensions if it's an image (only for performance optimization)
                if ($is_image) {
                    // Performance: Use @ to suppress errors for corrupted images
                    $image_info = @getimagesize($file_path);
                    if ($image_info) {
                        $item['width'] = $image_info[0];
                        $item['height'] = $image_info[1];
                    }
                }
                
                $items[] = $item;
                $file_count++;
            }
        }
    }
    
    // Sort: folders first, then files
    usort($items, function($a, $b) {
        if ($a['type'] === 'folder' && $b['type'] !== 'folder') {
            return -1;
        }
        if ($a['type'] !== 'folder' && $b['type'] === 'folder') {
            return 1;
        }
        return strcmp($a['name'], $b['name']);
    });
    
    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}

/**
 * Handle file upload
 */
function handleUpload() {
    if (!isset($_FILES['files'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No files uploaded']);
        exit;
    }
    
    $files = $_FILES['files'];
    $folder = $_POST['folder'] ?? '';
    $folder = preg_replace('/[^a-z0-9_\/-]/i', '', $folder); // Sanitize
    $folder = trim($folder, '/');
    
    // Build destination path
    $destination_dir = UPLOADS_DIR;
    if (!empty($folder)) {
        $destination_dir .= '/' . $folder;
        // Security: Ensure folder path is within uploads directory
        $real_destination = realpath($destination_dir);
        $real_uploads = realpath(UPLOADS_DIR);
        if (!$real_destination || strpos($real_destination, $real_uploads) !== 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid folder path']);
            exit;
        }
    }
    
    // Ensure directory exists
    if (!is_dir($destination_dir)) {
        if (!mkdir($destination_dir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create directory']);
            exit;
        }
    }
    
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'zip'];
    $allowed_mime_types = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'application/zip'
    ];
    $max_size = 10 * 1024 * 1024; // 10MB
    
    $uploaded_files = [];
    $errors = [];
    
    // Handle multiple files
    $file_count = is_array($files['name']) ? count($files['name']) : 1;
    
    for ($i = 0; $i < $file_count; $i++) {
        $file = [
            'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
            'type' => is_array($files['type']) ? $files['type'][$i] : $files['type'],
            'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
            'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
            'size' => is_array($files['size']) ? $files['size'][$i] : $files['size']
        ];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $file['name'] . ': Upload error ' . $file['error'];
            continue;
        }
        
        if ($file['size'] > $max_size) {
            $errors[] = $file['name'] . ': File too large (max 10MB)';
            continue;
        }
        
        // Security: Validate file extension
        $extension = getFileExtension($file['name']);
        if (!in_array(strtolower($extension), array_map('strtolower', $allowed_extensions))) {
            $errors[] = $file['name'] . ': Invalid file type';
            continue;
        }
        
        // Security: Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_mime_types)) {
            $errors[] = $file['name'] . ': Invalid file type';
            continue;
        }
        
        // Security: Additional validation for image files
        if (strpos($mime_type, 'image/') === 0 && $mime_type !== 'image/svg+xml') {
            $image_info = @getimagesize($file['tmp_name']);
            if ($image_info === false) {
                $errors[] = $file['name'] . ': Invalid image file';
                continue;
            }
        }
        
        // Generate unique filename
        $filename = sanitizeFilename($file['name']);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $unique_filename = $name . '_' . time() . '_' . $i . '.' . $ext;
        
        $destination = $destination_dir . '/' . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $relative_path = (!empty($folder) ? $folder . '/' : '') . $unique_filename;
            $url = CMS_URL . '/uploads/' . $relative_path;
            
            $uploaded_files[] = [
                'url' => $url,
                'filename' => $unique_filename
            ];
        } else {
            $errors[] = $file['name'] . ': Failed to save file';
        }
    }
    
    if (!empty($uploaded_files)) {
        echo json_encode([
            'success' => true,
            'files' => $uploaded_files,
            'errors' => $errors
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Upload failed',
            'errors' => $errors
        ]);
    }
}

/**
 * Handle folder creation
 */
function handleCreateFolder() {
    $folder_name = $_POST['folder_name'] ?? '';
    $parent_folder = $_POST['folder'] ?? '';
    
    // Sanitize folder name
    $folder_name = preg_replace('/[^a-z0-9_-]/i', '', $folder_name);
    $folder_name = trim($folder_name);
    
    if (empty($folder_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid folder name']);
        exit;
    }
    
    // Sanitize parent folder
    $parent_folder = preg_replace('/[^a-z0-9_\/-]/i', '', $parent_folder);
    $parent_folder = trim($parent_folder, '/');
    
    // Build full path
    $full_path = UPLOADS_DIR;
    if (!empty($parent_folder)) {
        $full_path .= '/' . $parent_folder;
        // Security: Ensure parent folder is within uploads directory
        $real_parent = realpath($full_path);
        $real_uploads = realpath(UPLOADS_DIR);
        if (!$real_parent || strpos($real_parent, $real_uploads) !== 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid parent folder']);
            exit;
        }
    }
    
    $new_folder_path = $full_path . '/' . $folder_name;
    
    // Check if folder already exists
    if (is_dir($new_folder_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'Folder already exists']);
        exit;
    }
    
    // Create folder
    if (mkdir($new_folder_path, 0755, true)) {
        // Create index.php to prevent directory listing
        file_put_contents($new_folder_path . '/index.php', '<?php // Directory listing disabled');
        echo json_encode(['success' => true, 'folder' => $folder_name]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create folder']);
    }
}

/**
 * Handle file deletion
 */
function handleDeleteFile() {
    // Suppress any output that might interfere with JSON
    ob_start();
    $old_error_reporting = error_reporting(0);
    $old_display_errors = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    try {
        $filename = $_POST['filename'] ?? '';
        $folder = $_POST['folder'] ?? '';
        
        // Validate filename
        if (empty($filename)) {
            ob_end_clean();
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            http_response_code(400);
            echo json_encode(['error' => 'Filename is required']);
            exit;
        }
        
        // Sanitize inputs
        $filename = basename($filename); // Prevent directory traversal
        $folder = preg_replace('/[^a-z0-9_\/-]/i', '', $folder);
        $folder = trim($folder, '/');
        
        // Build file path - handle empty folder case
        $file_path = rtrim(UPLOADS_DIR, '/');
        if (!empty($folder)) {
            $file_path .= '/' . $folder;
        }
        $file_path .= '/' . $filename;
        
        // Security: Ensure file is within uploads directory
        $real_file = realpath($file_path);
        $real_uploads = realpath(UPLOADS_DIR);
        
        if (!$real_uploads) {
            ob_end_clean();
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            http_response_code(500);
            echo json_encode(['error' => 'Uploads directory not found']);
            exit;
        }
        
        if (!$real_file) {
            // Try to find the file by searching subdirectories if folder was empty
            if (empty($folder)) {
                $found = false;
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(UPLOADS_DIR, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getFilename() === $filename) {
                        $found_path = $file->getPathname();
                        $real_file = realpath($found_path);
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    ob_end_clean();
                    error_reporting($old_error_reporting);
                    ini_set('display_errors', $old_display_errors);
                    http_response_code(404);
                    echo json_encode(['error' => 'File not found: ' . $filename]);
                    exit;
                }
            } else {
                ob_end_clean();
                error_reporting($old_error_reporting);
                ini_set('display_errors', $old_display_errors);
                http_response_code(404);
                echo json_encode(['error' => 'File not found: ' . $file_path]);
                exit;
            }
        }
        
        if (strpos($real_file, $real_uploads) !== 0) {
            ob_end_clean();
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file path: file is outside uploads directory']);
            exit;
        }
        
        // Check if file exists
        if (!file_exists($file_path) || !is_file($file_path)) {
            ob_end_clean();
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            http_response_code(404);
            echo json_encode(['error' => 'File not found']);
            exit;
        }
        
        // Delete file
        if (@unlink($file_path)) {
            // Also delete associated metadata JSON file
            @deleteMediaMetadata($real_file, $real_uploads);
            
            ob_end_clean();
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            echo json_encode(['success' => true]);
            exit;
        } else {
            ob_end_clean();
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete file']);
            exit;
        }
    } catch (Exception $e) {
        ob_end_clean();
        error_reporting($old_error_reporting);
        ini_set('display_errors', $old_display_errors);
        error_log("CMS Media Delete Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred while deleting the file']);
        exit;
    }
}

/**
 * Delete media metadata file associated with a media file
 */
function deleteMediaMetadata($filePath, $uploadsDir) {
    // Suppress errors during metadata deletion
    $old_error_reporting = error_reporting(0);
    
    // Ensure catalogue functions are available
    if (!function_exists('getMediaMetadata')) {
        @require_once CMS_ROOT . '/lib/catalogue.php';
    }
    
    if (!defined('MEDIA_METADATA_DIR')) {
        error_reporting($old_error_reporting);
        return false;
    }
    
    // Calculate relative path from uploads directory
    $relativePath = str_replace($uploadsDir . '/', '', $filePath);
    $relativePath = str_replace('\\', '/', $relativePath);
    $relativePath = ltrim($relativePath, '/');
    $fileName = basename($relativePath);
    
    // Method 1: Try to find metadata by MD5 hash of full path (same as saveMediaMetadata)
    $metadataKey = md5($relativePath);
    $metadataFile = MEDIA_METADATA_DIR . '/' . $metadataKey . '.json';
    
    if (file_exists($metadataFile)) {
        @unlink($metadataFile);
        error_reporting($old_error_reporting);
        return true;
    }
    
    // Method 2: Try MD5 hash of just filename (in case metadata was saved with just filename)
    $metadataKeyFilename = md5($fileName);
    $metadataFileFilename = MEDIA_METADATA_DIR . '/' . $metadataKeyFilename . '.json';
    
    if (file_exists($metadataFileFilename)) {
        // Verify it matches before deleting
        $metadata = @json_decode(@file_get_contents($metadataFileFilename), true);
        if ($metadata && isset($metadata['file_path'])) {
            $storedPath = $metadata['file_path'];
            $storedFileName = basename($storedPath);
            if ($storedFileName === $fileName || $storedPath === $relativePath) {
                @unlink($metadataFileFilename);
                error_reporting($old_error_reporting);
                return true;
            }
        }
    }
    
    // Method 3: Search through all metadata files to find matching file_path
    if (is_dir(MEDIA_METADATA_DIR)) {
        $metadataFiles = @glob(MEDIA_METADATA_DIR . '/*.json');
        if ($metadataFiles) {
            foreach ($metadataFiles as $metaFile) {
                $metadata = @json_decode(@file_get_contents($metaFile), true);
                if ($metadata && isset($metadata['file_path'])) {
                    $storedPath = $metadata['file_path'];
                    $storedFileName = basename($storedPath);
                    
                    // Match by full path or just filename
                    if ($storedPath === $relativePath || $storedFileName === $fileName) {
                        @unlink($metaFile);
                        error_reporting($old_error_reporting);
                        return true;
                    }
                }
            }
        }
    }
    
    error_reporting($old_error_reporting);
    return false;
}

/**
 * Handle folder deletion
 */
function handleDeleteFolder() {
    $folder_path = $_POST['folder_path'] ?? '';
    
    // Sanitize folder path
    $folder_path = preg_replace('/[^a-z0-9_\/-]/i', '', $folder_path);
    $folder_path = trim($folder_path, '/');
    
    if (empty($folder_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid folder path']);
        exit;
    }
    
    // Build full path
    $full_path = UPLOADS_DIR . '/' . $folder_path;
    
    // Security: Ensure folder is within uploads directory
    $real_folder = realpath($full_path);
    $real_uploads = realpath(UPLOADS_DIR);
    if (!$real_folder || strpos($real_folder, $real_uploads) !== 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid folder path']);
        exit;
    }
    
    // Check if folder exists
    if (!is_dir($full_path)) {
        http_response_code(404);
        echo json_encode(['error' => 'Folder not found']);
        exit;
    }
    
    // Recursively delete folder and contents
    if (deleteDirectory($full_path)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete folder']);
    }
}

/**
 * Recursively delete directory and all contents
 */
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

/**
 * Handle listing all folders recursively
 */
function handleListFolders() {
    $exclude_current = $_POST['exclude_current'] ?? '';
    $exclude_item = $_POST['exclude_item'] ?? '';
    
    // Sanitize exclude paths
    $exclude_current = preg_replace('/[^a-z0-9_\/-]/i', '', $exclude_current);
    $exclude_current = trim($exclude_current, '/');
    $exclude_item = preg_replace('/[^a-z0-9_\/-]/i', '', $exclude_item);
    $exclude_item = trim($exclude_item, '/');
    
    $folders = [];
    
    function getFoldersRecursive($dir, $base_dir, $prefix = '', $exclude_current = '', $exclude_item = '') {
        $folders = [];
        if (is_dir($dir)) {
            $items = scandir($dir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..' || $item[0] === '.') continue;
                $item_path = $dir . '/' . $item;
                if (is_dir($item_path)) {
                    $relative_path = ltrim(str_replace($base_dir, '', $item_path), '/');
                    
                    // Skip if this is the current folder
                    if (!empty($exclude_current) && $relative_path === $exclude_current) {
                        continue;
                    }
                    
                    // Skip if this is the item being moved (for folders)
                    if (!empty($exclude_item) && $relative_path === $exclude_item) {
                        continue;
                    }
                    
                    // Skip if exclude_item is a parent of this folder (can't move into itself)
                    if (!empty($exclude_item) && strpos($relative_path, $exclude_item . '/') === 0) {
                        continue;
                    }
                    
                    $folders[] = [
                        'path' => $relative_path,
                        'name' => $item,
                        'display' => $prefix . $item
                    ];
                    // Recursively get subfolders
                    $subfolders = getFoldersRecursive($item_path, $base_dir, $prefix . $item . ' / ', $exclude_current, $exclude_item);
                    $folders = array_merge($folders, $subfolders);
                }
            }
        }
        return $folders;
    }
    
    $all_folders = getFoldersRecursive(UPLOADS_DIR, UPLOADS_DIR, '', $exclude_current, $exclude_item);
    
    echo json_encode([
        'success' => true,
        'folders' => $all_folders
    ]);
}

/**
 * Handle moving files/folders
 */
function handleMove() {
    $type = $_POST['type'] ?? '';
    $name = $_POST['name'] ?? '';
    $current_path = $_POST['current_path'] ?? '';
    $destination = $_POST['destination'] ?? '';
    
    if (empty($type) || empty($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }
    
    // Sanitize paths
    $current_path = preg_replace('/[^a-z0-9_\/-]/i', '', $current_path);
    $destination = preg_replace('/[^a-z0-9_\/-]/i', '', $destination);
    $name = basename($name); // Sanitize filename
    
    // Build source path
    $source_path = UPLOADS_DIR;
    if (!empty($current_path)) {
        $source_path .= '/' . trim($current_path, '/');
    }
    $source_path .= '/' . $name;
    
    // Build destination path
    $dest_path = UPLOADS_DIR;
    if (!empty($destination)) {
        $dest_path .= '/' . trim($destination, '/');
    }
    $dest_path .= '/' . $name;
    
    // Security: Ensure paths are within uploads directory
    $real_source = realpath($source_path);
    $real_uploads = realpath(UPLOADS_DIR);
    
    if (!$real_source || strpos($real_source, $real_uploads) !== 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid source path']);
        exit;
    }
    
    // Check if destination directory exists
    $dest_dir = dirname($dest_path);
    if (!is_dir($dest_dir)) {
        http_response_code(400);
        echo json_encode(['error' => 'Destination directory does not exist']);
        exit;
    }
    
    // Check if destination already exists
    if (file_exists($dest_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'A file or folder with this name already exists in the destination']);
        exit;
    }
    
    // Move the file/folder
    if (rename($real_source, $dest_path)) {
        echo json_encode([
            'success' => true,
            'message' => 'Item moved successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move item']);
    }
}

/**
 * Handle get metadata request
 */
function handleGetMetadata() {
    // Suppress any output that might interfere with JSON
    ob_start();
    
    if (!function_exists('getMediaMetadata')) {
        require_once CMS_ROOT . '/lib/catalogue.php';
    }
    
    // Clear any output buffer
    ob_end_clean();
    
    $file_path = $_POST['file_path'] ?? '';
    $file_path = ltrim($file_path, '/');
    
    if (empty($file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'File path required']);
        exit;
    }
    
    $metadata = getMediaMetadata($file_path);
    
    echo json_encode([
        'success' => true,
        'metadata' => $metadata ?: []
    ]);
    exit;
}

/**
 * Handle get metadata form request
 */
function handleGetMetadataForm() {
    // Suppress any output that might interfere with JSON
    ob_start();
    
    if (!function_exists('getBlueprint')) {
        require_once CMS_ROOT . '/lib/blueprint.php';
    }
    if (!function_exists('renderFormField')) {
        require_once CMS_ROOT . '/lib/form.php';
    }
    
    // Clear any output buffer
    ob_end_clean();
    
    $file_path = $_POST['file_path'] ?? '';
    $file_path = ltrim($file_path, '/');
    
    if (empty($file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'File path required']);
        exit;
    }
    
    // Get blueprint for media
    $blueprint = getBlueprint('media');
    
    if (!$blueprint || !isset($blueprint['fields'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Media blueprint not found']);
        exit;
    }
    
    // Generate form fields using the same system as structure modals
    $html = '<div class="cms-structure-form-fields">';
    
    foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
        $html .= renderFormField($fieldName, $fieldDef, null);
    }
    
    $html .= '</div>';
    
    echo json_encode([
        'success' => true,
        'form' => $html
    ]);
    exit;
}

/**
 * Handle save metadata request
 */
function handleSaveMetadata() {
    // Suppress any output that might interfere with JSON
    ob_start();
    
    if (!function_exists('saveMediaMetadata')) {
        require_once CMS_ROOT . '/lib/catalogue.php';
    }
    
    // Clear any output buffer
    ob_end_clean();
    
    $file_path = $_POST['file_path'] ?? '';
    $file_path = ltrim($file_path, '/');
    
    if (empty($file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'File path required']);
        exit;
    }
    
    // Get blueprint for media
    require_once CMS_ROOT . '/lib/blueprint.php';
    $blueprint = getBlueprint('media');
    
    if (!$blueprint) {
        http_response_code(400);
        echo json_encode(['error' => 'Media blueprint not found']);
        exit;
    }
    
    // Get existing metadata
    $existing_metadata = getMediaMetadata($file_path);
    $metadata = $existing_metadata ?: [];
    
    // Process form fields from blueprint
    foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
        $value = $_POST[$fieldName] ?? null;
        
        // Handle different field types
        if ($fieldDef['type'] === 'tags' && is_string($value)) {
            // Tags might come as JSON string
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }
        
        $metadata[$fieldName] = $value;
    }
    
    // Save metadata
    if (saveMediaMetadata($file_path, $metadata)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save metadata']);
    }
    exit;
}

