<?php
/**
 * HTML Generator
 * Generates static HTML files from JSON content using PHP templates
 */

require_once __DIR__ . '/catalogue.php';
require_once __DIR__ . '/blueprint.php';
require_once __DIR__ . '/storage.php';

/**
 * Generate HTML file from content
 * 
 * @param string $contentType Content type name (e.g., 'about', 'project')
 * @param string $contentKind 'page' or 'collection'
 * @param string|null $itemSlug For collections, specific item slug (null = generate all)
 * @return bool Success
 */
function generateHtmlFile($contentType, $contentKind, $itemSlug = null) {
    // Exclude settings from HTML generation
    if ($contentType === 'settings') {
        return false;
    }
    
    // Allow 404 page generation (it's a special page)
    // Note: 404 is handled specially - it generates 404.html in root
    
    // Get blueprint
    $blueprint = getBlueprint($contentType);
    if (!$blueprint) {
        error_log("CMS Generator: No blueprint found for $contentType");
        return false;
    }
    
    // Set blueprint for catalogue() function
    setCatalogueBlueprint($blueprint);
    
    // Load site settings
    // Note: save.php forces filename to {content_type}.json for pages, so it's settings.json
    $siteSettings = readJson(PAGES_DIR . '/settings/settings.json');
    if ($siteSettings && isset($siteSettings['_meta'])) {
        unset($siteSettings['_meta']);
    }
    setCatalogueSite($siteSettings ?: []);
    
    if ($contentKind === 'page') {
        return generatePageHtml($contentType, $blueprint);
    } else {
        return generateCollectionHtml($contentType, $blueprint, $itemSlug);
    }
}

/**
 * Generate HTML for a single page
 */
function generatePageHtml($contentType, $blueprint) {
    // Load content
    $contentFile = PAGES_DIR . '/' . $contentType . '/' . $contentType . '.json';
    if (!file_exists($contentFile)) {
        error_log("CMS Generator: Content file not found: $contentFile");
        return false;
    }
    
    $content = readJson($contentFile);
    if (!$content) {
        return false;
    }
    
    // Remove metadata
    if (isset($content['_meta'])) {
        unset($content['_meta']);
    }
    
    // Set content for catalogue() function
    setCatalogueContent($content);
    
    // Set page identifier for traffic tracking
    $GLOBALS['cms_page_identifier'] = $contentType;
    
    // Get template file
    $templateFile = getTemplateFile($contentType);
    if (!$templateFile) {
        error_log("CMS Generator: No template found for $contentType");
        return false;
    }
    
    // Generate HTML - use nested output buffering to ensure nothing leaks
    $html = '';
    if (ob_get_level() > 0) {
        $currentBuffer = ob_get_contents();
        ob_end_clean();
    }
    
    ob_start();
    $old_error_reporting = error_reporting(0);
    $old_display_errors = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    try {
        include $templateFile;
        $html = ob_get_clean();
    } catch (Exception $e) {
        ob_end_clean();
        error_log("CMS Generator: Template error: " . $e->getMessage());
        return false;
    }
    
    error_reporting($old_error_reporting);
    ini_set('display_errors', $old_display_errors);
    
    if ($html === false || empty($html)) {
        error_log("CMS Generator: Failed to capture template output");
        return false;
    }
    
    // Save HTML file in root directory
    // Special cases: home page generates index.html, 404 generates 404.html
    if ($contentType === 'home') {
        $htmlFile = CMS_ROOT . '/../index.html';
    } elseif ($contentType === '404') {
        $htmlFile = CMS_ROOT . '/../404.html';
    } else {
    $htmlFile = CMS_ROOT . '/../' . $contentType . '.html';
    }
    
    if (!file_put_contents($htmlFile, $html)) {
        error_log("CMS Generator: Failed to write HTML file: $htmlFile");
        return false;
    }
    
    error_log("CMS Generator: Generated HTML: $htmlFile");
    return true;
}

/**
 * Generate HTML for collection (list + items)
 */
function generateCollectionHtml($contentType, $blueprint, $itemSlug = null) {
    $collectionDir = COLLECTIONS_DIR . '/' . $contentType;
    if (!is_dir($collectionDir)) {
        error_log("CMS Generator: Collection directory not found: $collectionDir");
        return false;
    }
    
    // Get all items
    $jsonFiles = listJsonFiles($collectionDir);
    $items = [];
    
    foreach ($jsonFiles as $jsonFile) {
        $item = readJson($jsonFile);
        if ($item) {
            // Extract slug from filename or _slug field
            $filename = basename($jsonFile, '.json');
            $slug = $item['_slug'] ?? preg_replace('/-\d+$/', '', $filename);
            
            // Ensure slug is clean and URL-safe
            $slug = preg_replace('/[^a-z0-9-]/i', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            
            if (empty($slug)) {
                $slug = 'item';
            }
            
            // Store original item with _meta for individual HTML generation
            // But remove _meta for list view (if needed)
            $item['_slug'] = $slug;
            $item['_json_file'] = $jsonFile; // Store file path to reload with _meta later
            $items[] = $item;
        }
    }
    
    // Generate collection list HTML
    $listContent = [
        '_collection' => $contentType,
        'items' => $items
    ];
    
    setCatalogueContent($listContent);
    
    $templateFile = getTemplateFile($contentType);
    if (!$templateFile) {
        error_log("CMS Generator: No template found for $contentType");
        return false;
    }
    
    // Generate list HTML - use nested output buffering
    $listHtml = '';
    if (ob_get_level() > 0) {
        $currentBuffer = ob_get_contents();
        ob_end_clean();
    }
    
    ob_start();
    $old_error_reporting = error_reporting(0);
    $old_display_errors = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    try {
        include $templateFile;
        $listHtml = ob_get_clean();
    } catch (Exception $e) {
        ob_end_clean();
        error_log("CMS Generator: Template error: " . $e->getMessage());
        return false;
    }
    
    error_reporting($old_error_reporting);
    ini_set('display_errors', $old_display_errors);
    
    // Note: We don't actually use $listHtml - collections don't generate list pages
    // But we need to include the template to ensure it's valid
    
    // Create collection directory in root if needed
    $collectionHtmlDir = CMS_ROOT . '/../' . $contentType;
    if (!is_dir($collectionHtmlDir)) {
        if (!mkdir($collectionHtmlDir, 0755, true)) {
            error_log("CMS Generator: Failed to create collection directory: $collectionHtmlDir");
            return false;
        }
    }
    
    // Generate individual item HTML files
    $generatedCount = 0;
    $failedCount = 0;
    
    if ($itemSlug !== null) {
        // Generate only specific item - sanitize the slug for comparison
        $itemSlugSanitized = preg_replace('/[^a-z0-9-]/i', '-', $itemSlug);
        $itemSlugSanitized = preg_replace('/-+/', '-', $itemSlugSanitized);
        $itemSlugSanitized = trim($itemSlugSanitized, '-');
        
        foreach ($items as $item) {
            // Compare sanitized slugs
            if ($item['_slug'] === $itemSlugSanitized) {
                if (generateCollectionItemHtml($contentType, $item, $blueprint, $templateFile)) {
                    $generatedCount++;
                } else {
                    $failedCount++;
                }
                break;
            }
        }
    } else {
        // Generate all items
        foreach ($items as $item) {
            if (generateCollectionItemHtml($contentType, $item, $blueprint, $templateFile)) {
                $generatedCount++;
            } else {
                $failedCount++;
        }
    }
    }
    
    // Return true if at least one item was generated successfully
    if ($generatedCount > 0) {
        error_log("CMS Generator: Generated $generatedCount item(s) for collection: $contentType");
    return true;
    } else {
        error_log("CMS Generator: Failed to generate any items for collection: $contentType (failed: $failedCount)");
        return false;
    }
}

/**
 * Generate HTML for a single collection item
 */
function generateCollectionItemHtml($contentType, $item, $blueprint, $templateFile) {
    // Reload item from file to ensure we have _meta (it may have been removed for list view)
    $jsonFile = $item['_json_file'] ?? null;
    if ($jsonFile && file_exists($jsonFile)) {
        $fullItem = readJson($jsonFile);
        if ($fullItem) {
            // Merge full item data (including _meta) with any modifications
            $item = array_merge($fullItem, $item);
        }
    }
    
    // Remove the temporary _json_file key
    unset($item['_json_file']);
    
    // Extract slug and add system fields
    // Ensure slug is URL-safe before using it
    $slug = $item['_slug'] ?? 'item';
    $slug = preg_replace('/[^a-z0-9-]/i', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    if (empty($slug)) {
        $slug = 'item';
    }
    
    $status = $item['_status'] ?? 'draft';
    $featured = $item['_featured'] ?? false;
    
    // Determine URL - use BASE_PATH for subfolder support
    $basePath = defined('BASE_PATH') ? BASE_PATH : '';
    $basePath = rtrim($basePath, '/');
    $url = $basePath . '/' . $contentType . '/' . $slug . '.html';
    
    // Add system fields to item
    $item['url'] = $url;
    $item['slug'] = $slug;
    $item['status'] = $status;
    $item['featured'] = $featured;
    $item['collection'] = $contentType;
    
    // Add convenience fields for timestamps (if _meta exists)
    if (isset($item['_meta']) && is_array($item['_meta'])) {
        $item['created_at'] = $item['_meta']['created'] ?? '';
        $item['updated_at'] = $item['_meta']['updated'] ?? '';
    } else {
        // Set empty strings if _meta doesn't exist
        $item['created_at'] = '';
        $item['updated_at'] = '';
    }
    
    // Set content for catalogue() function
    setCatalogueContent($item);
    
    // Set blueprint for catalogue() function (needed for structure fields and field type rendering)
    setCatalogueBlueprint($blueprint);
    
    // Set page identifier for traffic tracking (use slug for collection items)
    // Note: $slug was already extracted and sanitized above, reuse it
    $GLOBALS['cms_page_identifier'] = $contentType . '/' . $slug;
    
    // Generate HTML - use nested output buffering
    $html = '';
    if (ob_get_level() > 0) {
        $currentBuffer = ob_get_contents();
        ob_end_clean();
    }
    
    ob_start();
    $old_error_reporting = error_reporting(0);
    $old_display_errors = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    try {
        include $templateFile;
        $html = ob_get_clean();
    } catch (Exception $e) {
        ob_end_clean();
        error_log("CMS Generator: Template error: " . $e->getMessage());
        return false;
    }
    
    error_reporting($old_error_reporting);
    ini_set('display_errors', $old_display_errors);
    
    if ($html === false || empty(trim($html))) {
        error_log("CMS Generator: Template produced empty output for collection item: $contentType/" . ($item['_slug'] ?? 'item') . " - template file appears to be empty or produces no output");
        return false;
    }
    
    // Save HTML file
    // Note: $slug was already extracted and sanitized above, reuse it
    
    $collectionHtmlDir = CMS_ROOT . '/../' . $contentType;
    if (!is_dir($collectionHtmlDir)) {
        mkdir($collectionHtmlDir, 0755, true);
    }
    
    $itemHtmlFile = $collectionHtmlDir . '/' . $slug . '.html';
    
    if (!file_put_contents($itemHtmlFile, $html)) {
        error_log("CMS Generator: Failed to write item HTML: $itemHtmlFile");
        return false;
    }
    
    error_log("CMS Generator: Generated item HTML: $itemHtmlFile");
    return true;
}

/**
 * Get template file path for content type
 */
function getTemplateFile($contentType) {
    // Exclude settings
    if ($contentType === 'settings') {
        return null;
    }
    
    // Check for specific template
    $templatePath = CMS_ROOT . '/templates/' . $contentType . '.php';
    if (file_exists($templatePath)) {
        return $templatePath;
    }
    
    // Fallback to default template
    $defaultTemplate = CMS_ROOT . '/templates/default.php';
    if (file_exists($defaultTemplate)) {
        return $defaultTemplate;
    }
    
    return null;
}

/**
 * Generate home page (index.html)
 */
function generateHomeHtml() {
    // Check if home blueprint exists
    $homeBlueprint = getBlueprint('home');
    
    // Load home content if exists
    $homeContentFile = PAGES_DIR . '/home/home.json';
    $homeContent = null;
    
    if (file_exists($homeContentFile)) {
        $homeContent = readJson($homeContentFile);
        if ($homeContent && isset($homeContent['_meta'])) {
            unset($homeContent['_meta']);
        }
    }
    
    // If no home content, create default
    if (!$homeContent) {
        $homeContent = [
            'title' => 'Home',
            'content' => 'Welcome to the site'
        ];
    }
    
    // Use home blueprint if available, otherwise use default
    $blueprint = $homeBlueprint ?: ['fields' => []];
    setCatalogueBlueprint($blueprint);
    
    // Load site settings
    // Note: save.php forces filename to {content_type}.json for pages, so it's settings.json
    $siteSettings = readJson(PAGES_DIR . '/settings/settings.json');
    if ($siteSettings && isset($siteSettings['_meta'])) {
        unset($siteSettings['_meta']);
    }
    setCatalogueSite($siteSettings ?: []);
    
    // Set content
    setCatalogueContent($homeContent);
    
    // Set page identifier for traffic tracking
    $GLOBALS['cms_page_identifier'] = 'home';
    
    // Get template
    $templateFile = getTemplateFile('home');
    if (!$templateFile) {
        // Fallback to default
        $templateFile = CMS_ROOT . '/templates/default.php';
        if (!file_exists($templateFile)) {
            error_log("CMS Generator: No template found for home page");
            return false;
        }
    }
    
    // Generate HTML - use nested output buffering
    $html = '';
    if (ob_get_level() > 0) {
        $currentBuffer = ob_get_contents();
        ob_end_clean();
    }
    
    ob_start();
    $old_error_reporting = error_reporting(0);
    $old_display_errors = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    try {
        include $templateFile;
        $html = ob_get_clean();
    } catch (Exception $e) {
        ob_end_clean();
        error_log("CMS Generator: Template error: " . $e->getMessage());
        return false;
    }
    
    error_reporting($old_error_reporting);
    ini_set('display_errors', $old_display_errors);
    
    if ($html === false || empty($html)) {
        error_log("CMS Generator: Failed to capture template output for home");
        return false;
    }
    
    // Save as index.html in root
    $indexFile = CMS_ROOT . '/../index.html';
    if (!file_put_contents($indexFile, $html)) {
        error_log("CMS Generator: Failed to write index.html");
        return false;
    }
    
    error_log("CMS Generator: Generated index.html");
    return true;
}

/**
 * Generate all HTML files (useful for initial generation)
 */
function generateAllHtml() {
    // Generate home page
    generateHomeHtml();
    
    // Generate all pages
    $pagesDir = PAGES_DIR;
    if (is_dir($pagesDir)) {
        $pageDirs = glob($pagesDir . '/*', GLOB_ONLYDIR);
        foreach ($pageDirs as $pageDir) {
            $contentType = basename($pageDir);
            if ($contentType !== 'settings' && $contentType !== 'users' && $contentType !== '404') {
                generateHtmlFile($contentType, 'page');
            }
        }
        
        // Generate 404 page separately (it's a special page)
        generateHtmlFile('404', 'page');
    }
    
    // Generate all collections
    $collectionsDir = COLLECTIONS_DIR;
    if (is_dir($collectionsDir)) {
        $collectionDirs = glob($collectionsDir . '/*', GLOB_ONLYDIR);
        foreach ($collectionDirs as $collectionDir) {
            $contentType = basename($collectionDir);
            generateHtmlFile($contentType, 'collection');
        }
    }
    
    return true;
}

