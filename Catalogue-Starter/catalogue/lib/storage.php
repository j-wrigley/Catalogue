<?php
/**
 * Storage Functions
 * Safe read/write operations for JSON files
 */

/**
 * Read JSON file safely
 */
function readJson($filepath) {
    if (!file_exists($filepath)) {
        return null;
    }
    
    $content = file_get_contents($filepath);
    if ($content === false) {
        return null;
    }
    
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }
    
    return $data;
}

/**
 * Write JSON file safely
 */
function writeJson($filepath, $data) {
    // Ensure directory exists
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("CMS Storage: Failed to create directory $dir");
            return false;
        }
    }
    
    // Encode with pretty printing
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
    if ($json === false) {
        error_log("CMS Storage: JSON encode failed - " . json_last_error_msg());
        return false;
    }
    
    // Write atomically using temp file
    $temp_file = $filepath . '.tmp';
    $written = file_put_contents($temp_file, $json);
    
    if ($written === false) {
        error_log("CMS Storage: Failed to write temp file $temp_file");
        return false;
    }
    
    // Atomic rename
    if (!rename($temp_file, $filepath)) {
        error_log("CMS Storage: Failed to rename $temp_file to $filepath");
        unlink($temp_file);
        return false;
    }
    
    return true;
}

/**
 * List all JSON files in directory
 */
function listJsonFiles($directory) {
    $files = [];
    if (!is_dir($directory)) {
        return $files;
    }
    
    $items = scandir($directory);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $directory . '/' . $item;
        if (is_file($path) && getFileExtension($path) === 'json') {
            $files[] = $path;
        }
    }
    
    return $files;
}

/**
 * Delete JSON file safely
 */
function deleteJson($filepath) {
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

