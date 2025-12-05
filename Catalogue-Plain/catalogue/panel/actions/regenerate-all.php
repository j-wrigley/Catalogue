<?php
/**
 * Regenerate All HTML Files
 * Regenerates all pages and collections HTML from templates
 */

// Suppress ALL output and errors at the very start
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
@error_reporting(0);

// Start output buffering immediately to catch ANY output
if (ob_get_level() == 0) {
    ob_start();
}

// Set error handler to catch all errors silently (don't throw exceptions)
set_error_handler(function($severity, $message, $file, $line) {
    // Log to error log but don't output anything
    @error_log("CMS Regenerate Error: $message in $file on line $line");
    return true; // Suppress all error output
}, E_ALL);

require_once __DIR__ . '/../../config.php';
require_once LIB_DIR . '/generator.php';
require_once LIB_DIR . '/csrf.php';
require_once LIB_DIR . '/auth.php';
require_once LIB_DIR . '/blueprint.php';

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Check CSRF token
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Regenerate all HTML files
$results = [
    'success' => true,
    'generated' => [],
    'errors' => [],
    'skipped' => [],
    'count' => 0
];

try {
    // Generate home page
    if (generateHomeHtml()) {
        $results['generated'][] = 'Home page (index.html)';
        $results['count']++;
    } else {
        $results['errors'][] = 'Failed to generate home page';
    }
    
    // Generate 404 page
    if (generateHtmlFile('404', 'page')) {
        $results['generated'][] = '404 error page (404.html)';
        $results['count']++;
    } else {
        $results['skipped'][] = '404 page: No blueprint or template found (optional)';
    }
    
    // Generate all pages
    $pagesDir = PAGES_DIR;
    if (is_dir($pagesDir)) {
        $pageDirs = glob($pagesDir . '/*', GLOB_ONLYDIR);
        foreach ($pageDirs as $pageDir) {
            $contentType = basename($pageDir);
            if ($contentType !== 'settings' && $contentType !== 'users' && $contentType !== '404') {
                // Check if content file exists
                $contentFile = $pageDir . '/' . $contentType . '.json';
                if (!file_exists($contentFile)) {
                    $results['skipped'][] = "Page '$contentType': No content file found (create content/pages/$contentType/$contentType.json)";
                    continue;
                }
                
                // Check if template exists
                $templateFile = getTemplateFile($contentType);
                if (!$templateFile) {
                    $results['skipped'][] = "Page '$contentType': No template found (create templates/$contentType.php)";
                    continue;
                }
                
                // Try to generate
                try {
                    if (generateHtmlFile($contentType, 'page')) {
                        $results['generated'][] = "Page: $contentType";
                        $results['count']++;
                    } else {
                        $results['errors'][] = "Failed to generate page: $contentType (check error logs for details)";
                    }
                } catch (Exception $e) {
                    $results['errors'][] = "Error generating page '$contentType': " . $e->getMessage();
                    error_log("CMS Regenerate Error for page '$contentType': " . $e->getMessage());
                }
            }
        }
    }
    
    // Generate all collections
    $collectionsDir = COLLECTIONS_DIR;
    if (is_dir($collectionsDir)) {
        $collectionDirs = glob($collectionsDir . '/*', GLOB_ONLYDIR);
        foreach ($collectionDirs as $collectionDir) {
            $contentType = basename($collectionDir);
            
            // Check if blueprint exists
            $blueprint = getBlueprint($contentType);
            if (!$blueprint) {
                $results['skipped'][] = "Collection '$contentType': No blueprint found";
                continue;
            }
            
            // Check if collection has items
            $jsonFiles = listJsonFiles($collectionDir);
            if (empty($jsonFiles)) {
                $results['skipped'][] = "Collection '$contentType': No items found";
                continue;
            }
            
            // Check if template exists
            $templateFile = getTemplateFile($contentType);
            if (!$templateFile) {
                $results['skipped'][] = "Collection '$contentType': No template found (create templates/$contentType.php)";
                continue;
            }
            
            // Try to generate
            try {
                $generated = generateHtmlFile($contentType, 'collection');
                if ($generated) {
                    $results['generated'][] = "Collection: $contentType (" . count($jsonFiles) . " items)";
                    $results['count']++;
                } else {
                    // Get more specific error from error log or provide generic message
                    $results['errors'][] = "Failed to generate collection: $contentType (check error logs for details)";
                }
            } catch (Exception $e) {
                $results['errors'][] = "Error generating collection '$contentType': " . $e->getMessage();
                error_log("CMS Regenerate Error for collection '$contentType': " . $e->getMessage());
            }
        }
    }
    
    // Only mark as failed if there are actual errors (not just skipped items)
    if (count($results['errors']) > 0) {
        $results['success'] = false;
    }
    
} catch (Exception $e) {
    $results['success'] = false;
    $results['errors'][] = 'Exception: ' . $e->getMessage();
    $results['errors'][] = 'Stack trace: ' . $e->getTraceAsString();
    error_log("CMS Regenerate All Error: " . $e->getMessage());
    error_log("CMS Regenerate All Stack Trace: " . $e->getTraceAsString());
} catch (Error $e) {
    $results['success'] = false;
    $results['errors'][] = 'Fatal Error: ' . $e->getMessage();
    $results['errors'][] = 'File: ' . $e->getFile() . ' Line: ' . $e->getLine();
    error_log("CMS Regenerate All Fatal Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
} catch (ErrorException $e) {
    $results['success'] = false;
    $results['errors'][] = 'PHP Error: ' . $e->getMessage();
    $results['errors'][] = 'File: ' . $e->getFile() . ' Line: ' . $e->getLine();
    error_log("CMS Regenerate All PHP Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Capture any output that shouldn't be there
$output = '';
while (ob_get_level() > 0) {
    $output .= ob_get_clean();
}
if (!empty($output)) {
    // Only report if output contains actual content (not just whitespace)
    $trimmedOutput = trim($output);
    if (!empty($trimmedOutput) && strlen($trimmedOutput) > 0) {
        $results['success'] = false;
        $results['errors'][] = 'Unexpected output detected: ' . substr($trimmedOutput, 0, 200);
        error_log("CMS Regenerate All: Unexpected output: " . substr($trimmedOutput, 0, 500));
    }
}

// Capture any PHP errors that occurred
$lastError = error_get_last();
if ($lastError && $lastError['type'] === E_ERROR) {
    $results['success'] = false;
    $results['errors'][] = 'PHP Error: ' . $lastError['message'] . ' in ' . $lastError['file'] . ' on line ' . $lastError['line'];
}

// Restore error handler
restore_error_handler();

// Ensure we have clean output
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Output JSON
echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;

