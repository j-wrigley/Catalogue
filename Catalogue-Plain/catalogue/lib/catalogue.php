<?php
/**
 * Catalogue - Minimal Templating Function
 * Auto-detects field types from blueprints and renders appropriately
 */

// Prevent multiple includes from causing issues
if (defined('CATALOGUE_LOADED')) {
    return;
}
define('CATALOGUE_LOADED', true);

// Global variables for template context
// Initialize only if not already set to avoid warnings
if (!isset($GLOBALS['catalogue_content'])) {
    $GLOBALS['catalogue_content'] = null;
}
if (!isset($GLOBALS['catalogue_blueprint'])) {
    $GLOBALS['catalogue_blueprint'] = null;
}
if (!isset($GLOBALS['catalogue_site'])) {
    $GLOBALS['catalogue_site'] = null;
}
if (!isset($GLOBALS['catalogue_file'])) {
    $GLOBALS['catalogue_file'] = null;
}
if (!isset($GLOBALS['catalogue_collection_item'])) {
    $GLOBALS['catalogue_collection_item'] = null;
}
if (!isset($GLOBALS['catalogue_structure_item'])) {
    $GLOBALS['catalogue_structure_item'] = null;
}
if (!isset($GLOBALS['catalogue_nav_item'])) {
    $GLOBALS['catalogue_nav_item'] = null;
}

/**
 * Set content for catalogue() function
 */
function setCatalogueContent($content) {
    global $catalogue_content;
    $catalogue_content = $content;
}

/**
 * Set blueprint for catalogue() function
 */
function setCatalogueBlueprint($blueprint) {
    global $catalogue_blueprint;
    $catalogue_blueprint = $blueprint;
}

/**
 * Set site settings for catalogue() function
 */
function setCatalogueSite($site) {
    global $catalogue_site;
    $catalogue_site = $site;
}

/**
 * Catalogue - Main templating function
 * Auto-detects field types and renders appropriately
 * 
 * @param string $key Field name (supports dot notation: 'image.src')
 * @param mixed $default Default value if not found
 * @param string $source 'content' (default), 'site' (for settings), or 'file' (for file metadata)
 * @param string|null $dateFormat Optional PHP date format string (e.g., 'D, M Y', 'Y-m-d') or separator string (e.g., ', ') for arrays
 * @param string|null $separator Optional separator string for array values (e.g., ', ', ' | '). If null and dateFormat looks like a separator, uses dateFormat as separator.
 * @return string Rendered HTML
 */
function catalogue($key, $default = '', $source = 'content', $dateFormat = null, $separator = null) {
    global $catalogue_content, $catalogue_blueprint, $catalogue_site, $catalogue_file, $catalogue_collection_item, $catalogue_structure_item, $catalogue_nav_item;
    
    // Helper function to check and apply separator (returns array with [result, modifiedDateFormat])
    $checkSeparator = function($value, $dateFormat, $separator) {
        $actualSeparator = $separator;
        $modifiedDateFormat = $dateFormat;
        
        if ($actualSeparator === null && $dateFormat !== null && is_string($dateFormat)) {
            // Check if dateFormat looks like a separator (contains common separators but not date format chars)
            if (preg_match('/^[^a-zA-Z]*[,|;:\s]+[^a-zA-Z]*$/', $dateFormat) && !preg_match('/[dDjlNSwzWFmMntLoYyaABgGhHisuvZ]/', $dateFormat)) {
                $actualSeparator = $dateFormat;
                $modifiedDateFormat = null; // Don't use as date format
            }
        }
        
        // If separator is requested and value is an array, join it
        if ($actualSeparator !== null && is_array($value) && !empty($value)) {
            $escaped = array_map(function($item) {
                return htmlspecialchars((string)$item, ENT_QUOTES, 'UTF-8');
            }, $value);
            return [implode($actualSeparator, $escaped), $modifiedDateFormat];
        }
        return [null, $modifiedDateFormat];
    };
    
    // Check if we're in a navigation item context (for foreach loops)
    if ($source === 'content' && $catalogue_nav_item !== null && is_array($catalogue_nav_item)) {
        // Use navigation item as data source
        $value = getNestedValue($catalogue_nav_item, $key);
        
        if ($value !== null) {
            // Special handling for 'url' field (should not be escaped as HTML)
            if ($key === 'url') {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            // Return raw value (navigation items are simple data, not blueprint fields)
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Check if we're in a structure item context (for foreach loops)
    if ($source === 'content' && $catalogue_structure_item !== null && is_array($catalogue_structure_item)) {
        // Use structure item as data source
        $value = getNestedValue($catalogue_structure_item, $key);
        
        if ($value !== null) {
            // Check for separator first
            list($separated, $dateFormat) = $checkSeparator($value, $dateFormat, $separator);
            if ($separated !== null) {
                return $separated;
            }
            
            // Apply date formatting if requested
            if ($dateFormat !== null && is_string($value) && !empty($value)) {
                $formatted = formatCatalogueDate($value, $dateFormat);
                if ($formatted !== false) {
                    return htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8');
                }
            }
            // Get field type from blueprint if available
            $fieldType = getFieldType($key, $catalogue_blueprint);
            
            // Render based on type
            return renderField($value, $fieldType, $catalogue_blueprint, $key);
        }
    }
    
    // Check if we're in a collection item context (for foreach loops)
    if ($source === 'content' && $catalogue_collection_item !== null && is_array($catalogue_collection_item)) {
        // Use collection item as data source
        $value = getNestedValue($catalogue_collection_item, $key);
        
        if ($value !== null) {
            // Special handling for 'url' field (should not be escaped as HTML)
            if ($key === 'url') {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            
            // Check for separator first
            list($separated, $dateFormat) = $checkSeparator($value, $dateFormat, $separator);
            if ($separated !== null) {
                return $separated;
            }
            
            // Apply date formatting if requested
            if ($dateFormat !== null && is_string($value) && !empty($value)) {
                $formatted = formatCatalogueDate($value, $dateFormat);
                if ($formatted !== false) {
                    return htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8');
                }
            }
            
            // Get field type from blueprint if available
            $fieldType = getFieldType($key, $catalogue_blueprint);
            
            // Render based on type
            return renderField($value, $fieldType, $catalogue_blueprint, $key);
        }
    }
    
    // Check if we're in a file context and source is 'content' (default)
    // Only check file context if it's explicitly set (not null) and is an array
    if ($source === 'content' && $catalogue_file !== null && is_array($catalogue_file) && isset($catalogue_file[$key])) {
        // Use file metadata as data source
        $value = $catalogue_file[$key];
        
        // Special handling for 'image' key - return pre-rendered HTML
        if ($key === 'image') {
            return $catalogue_file['image'] ?? $default;
        }
        
        // For tags, return formatted HTML string
        if ($key === 'tags' && is_array($value)) {
            if (empty($value)) {
                return $default;
            }
            $tagsHtml = [];
            foreach ($value as $tag) {
                $tagsHtml[] = '<span class="tag">' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . '</span>';
            }
            return implode('', $tagsHtml);
        }
        
        // Apply date formatting if requested
        if ($dateFormat !== null && is_string($value) && !empty($value)) {
            $formatted = formatCatalogueDate($value, $dateFormat);
            if ($formatted !== false) {
                return htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8');
            }
        }
        
        // For other fields, return escaped string
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    // Get data source
    if ($source === 'site') {
        $data = $catalogue_site;
    } else {
        $data = $catalogue_content;
    }
    
    if (!$data) {
        return $default;
    }
    
    // Get value using dot notation
    $value = getNestedValue($data, $key);
    
    if ($value === null) {
        return $default;
    }
    
    // Special handling for 'items' key in collection context
    if ($key === 'items' && is_array($value)) {
        return $value; // Return array directly for foreach loops
    }
    
    // Check for separator first
    list($separated, $dateFormat) = $checkSeparator($value, $dateFormat, $separator);
    if ($separated !== null) {
        return $separated;
    }
    
    // Apply date formatting if requested and value is a date
    if ($dateFormat !== null && is_string($value) && !empty($value)) {
        $formatted = formatCatalogueDate($value, $dateFormat);
        if ($formatted !== false) {
            return htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Get field type from blueprint
    $fieldType = getFieldType($key, $catalogue_blueprint);
    
    // Render based on type
    return renderField($value, $fieldType, $catalogue_blueprint, $key);
}

/**
 * Format date value using PHP date format
 * Detects ISO 8601 dates and formats them
 * 
 * @param string $dateString Date string (ISO 8601 format)
 * @param string $format PHP date format string (e.g., 'D, M Y', 'Y-m-d', 'F j, Y')
 * @return string|false Formatted date string or false if not a valid date
 */
function formatCatalogueDate($dateString, $format) {
    if (empty($dateString)) {
        return false;
    }
    
    try {
        // Try to parse as ISO 8601 date
        $date = new DateTime($dateString);
        return $date->format($format);
    } catch (Exception $e) {
        // Not a valid date, return false
        return false;
    }
}

/**
 * Catalogue Date - Format date field
 * Convenience function for formatting dates
 * 
 * @param string $key Field name
 * @param string $format PHP date format string
 * @param mixed $default Default value if not found
 * @param string $source 'content' (default), 'site', or 'file'
 * @return string Formatted date or default value
 */
function catalogueDate($key, $format, $default = '', $source = 'content') {
    return catalogue($key, $default, $source, $format);
}

/**
 * Get nested value from array using dot notation
 */
function getNestedValue($array, $key) {
    if (!is_array($array) || empty($key)) {
        return null;
    }
    
    if (strpos($key, '.') === false) {
        return $array[$key] ?? null;
    }
    
    $keys = explode('.', $key);
    $value = $array;
    
    foreach ($keys as $k) {
        if (!is_array($value) || !isset($value[$k])) {
            return null;
        }
        $value = $value[$k];
    }
    
    return $value;
}

/**
 * Get field type from blueprint
 */
function getFieldType($key, $blueprint) {
    if (!$blueprint || !isset($blueprint['fields'])) {
        return 'text'; // Default
    }
    
    // Handle dot notation (image.src → check 'image' field)
    $mainKey = explode('.', $key)[0];
    
    if (isset($blueprint['fields'][$mainKey])) {
        return $blueprint['fields'][$mainKey]['type'] ?? 'text';
    }
    
    return 'text';
}

/**
 * Render field based on type
 */
function renderField($value, $fieldType, $blueprint = null, $key = '') {
    switch ($fieldType) {
        case 'markdown':
            // Ensure value is a string before parsing
            $markdownValue = is_string($value) ? $value : (is_scalar($value) ? (string)$value : '');
            return parseMarkdown($markdownValue);
            
        case 'file':
            // Check if this is a multiple file field (array of files)
            if (is_array($value) && !empty($value)) {
                // Check if first element is a string (array of URLs) or array (array of objects)
                $firstElement = $value[0];
                if (is_string($firstElement) || (is_array($firstElement) && isset($firstElement['src']))) {
                    // Multiple files - render all
                    $html = [];
                    foreach ($value as $file) {
                        $rendered = renderImage($file);
                        if (!empty($rendered)) {
                            $html[] = $rendered;
                        }
                    }
                    return implode('', $html);
                }
            }
            // Single file
            return renderImage($value);
            
        case 'select':
            // Return label if options available in blueprint
            if ($blueprint && isset($blueprint['fields'][$key]['options'])) {
                $options = $blueprint['fields'][$key]['options'];
                return htmlspecialchars($options[$value] ?? $value, ENT_QUOTES, 'UTF-8');
            }
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
            
        case 'radio':
            // Return label if options available in blueprint
            if ($blueprint && isset($blueprint['fields'][$key]['options'])) {
                $options = $blueprint['fields'][$key]['options'];
                return htmlspecialchars($options[$value] ?? $value, ENT_QUOTES, 'UTF-8');
            }
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
            
        case 'checkbox':
            // Auto-format like tags for arrays
            if (is_array($value) && !empty($value)) {
                $html = [];
                foreach ($value as $item) {
                    $html[] = '<span class="checkbox-item">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
                }
                return implode(' ', $html);
            }
            // Single checkbox value
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
            
        case 'tags':
            // Return formatted HTML string for direct display
            if (!is_array($value) || empty($value)) {
                return '';
            }
            $tagHtml = [];
            foreach ($value as $tag) {
                $tagHtml[] = '<span class="tag">' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . '</span>';
            }
            return implode(' ', $tagHtml);
            
        case 'structure':
            // Return array for foreach loops
            return is_array($value) ? $value : [];
            
        case 'collection':
            // Special case: return array as-is for collection items
            return is_array($value) ? $value : [];
            
        case 'slider':
        case 'range':
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
            
        case 'switch':
        case 'toggle':
            $checked = ($value === true || $value === '1' || $value === 'true' || $value === 'on');
            // Return boolean-like value (works in conditionals)
            // Empty string is falsy, non-empty is truthy
            // For display, use conditionals: if (catalogue('featured')): ... endif;
            return $checked ? '1' : '';
            
        case 'text':
        case 'textarea':
        default:
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Parse markdown to HTML
 */
function parseMarkdown($markdown) {
    // Ensure we have a string
    if (!is_string($markdown)) {
        if (is_scalar($markdown)) {
            $markdown = (string)$markdown;
        } else {
            return '';
        }
    }
    
    if (empty($markdown)) {
        return '';
    }
    
    $html = $markdown;
    
    // Code blocks (must be first to avoid conflicts)
    $html = preg_replace('/```([\s\S]*?)```/s', '<pre><code>$1</code></pre>', $html);
    
    // Inline code
    $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
    
    // Headings
    $html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $html);
    
    // Bold
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
    
    // Italic
    $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);
    
    // Strikethrough
    $html = preg_replace('/~~(.+?)~~/s', '<s>$1</s>', $html);
    
    // Links
    $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $html);
    
    // Blockquotes
    $html = preg_replace('/^> (.+)$/m', '<blockquote>$1</blockquote>', $html);
    
    // Process lists - convert markdown list syntax to HTML
    // Process unordered lists first
    $lines = explode("\n", $html);
    $processedLines = [];
    $inUnorderedList = false;
    $inOrderedList = false;
    
    foreach ($lines as $line) {
        // Check for unordered list item
        if (preg_match('/^[\*\-\+] (.+)$/', $line, $matches)) {
            if (!$inUnorderedList) {
                if ($inOrderedList) {
                    $processedLines[] = '</ol>';
                    $inOrderedList = false;
                }
                $processedLines[] = '<ul>';
                $inUnorderedList = true;
            }
            $processedLines[] = '<li>' . $matches[1] . '</li>';
        }
        // Check for ordered list item
        elseif (preg_match('/^\d+\. (.+)$/', $line, $matches)) {
            if (!$inOrderedList) {
                if ($inUnorderedList) {
                    $processedLines[] = '</ul>';
                    $inUnorderedList = false;
                }
                $processedLines[] = '<ol>';
                $inOrderedList = true;
            }
            $processedLines[] = '<li>' . $matches[1] . '</li>';
        }
        // Regular line
        else {
            if ($inUnorderedList) {
                $processedLines[] = '</ul>';
                $inUnorderedList = false;
            }
            if ($inOrderedList) {
                $processedLines[] = '</ol>';
                $inOrderedList = false;
            }
            $processedLines[] = $line;
        }
    }
    
    // Close any open lists
    if ($inUnorderedList) {
        $processedLines[] = '</ul>';
    }
    if ($inOrderedList) {
        $processedLines[] = '</ol>';
    }
    
    $html = implode("\n", $processedLines);
    
    // Paragraphs (wrap consecutive non-block elements)
    // Split by double newlines first to handle paragraph breaks (markdown standard)
    $paragraphBlocks = preg_split('/\n\s*\n/', $html);
    $paragraphs = [];
    
    foreach ($paragraphBlocks as $block) {
        $block = trim($block);
        if (empty($block)) {
            continue;
        }
        
        // Split block into lines for processing
        $lines = explode("\n", $block);
        $processedBlock = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
                continue;
            }
            
            // If it's already a block element, add it as-is
            if (preg_match('/^<(h[1-6]|ul|ol|blockquote|pre|code)/', $line)) {
                if (!empty($processedBlock)) {
                    $paragraphs[] = '<p>' . implode(' ', $processedBlock) . '</p>';
                    $processedBlock = [];
            }
            $paragraphs[] = $line;
        } else {
                // Regular text line - add to current paragraph
                $processedBlock[] = $line;
            }
        }
        
        // Wrap any remaining content in a paragraph
        if (!empty($processedBlock)) {
            $paragraphs[] = '<p>' . implode(' ', $processedBlock) . '</p>';
        }
    }
    
    $html = implode("\n", $paragraphs);
    
    // Clean up empty paragraphs
    $html = preg_replace('/<p><\/p>/', '', $html);
    $html = preg_replace('/\n\n+/', "\n", $html);
    
    return $html;
}

/**
 * Get media metadata for a file
 * 
 * @param string $filePath Relative path to file (e.g., 'image.jpg' or 'folder/image.jpg')
 * @return array|null Metadata array or null if not found
 */
function getMediaMetadata($filePath) {
    if (!function_exists('readJson')) {
        $storageFile = __DIR__ . '/storage.php';
        if (file_exists($storageFile)) {
            require_once $storageFile;
        }
    }
    
    if (!defined('MEDIA_METADATA_DIR')) {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }
    }
    
    if (!defined('MEDIA_METADATA_DIR')) {
        return null;
    }
    
    // Normalize file path (remove leading slash, handle folder paths)
    $filePath = ltrim($filePath, '/');
    $filePath = str_replace('\\', '/', $filePath);
    
    // Extract just the filename for matching
    $fileName = basename($filePath);
    
    // Try to find metadata by MD5 hash of full path first
    $metadataKey = md5($filePath);
    $metadataFile = MEDIA_METADATA_DIR . '/' . $metadataKey . '.json';
    
    if (file_exists($metadataFile)) {
        $metadata = readJson($metadataFile);
        if ($metadata && isset($metadata['file_path'])) {
            // Match if file_path matches exactly, or if it's just the filename and matches
            $storedPath = $metadata['file_path'];
            $storedFileName = basename($storedPath);
            if ($storedPath === $filePath || $storedFileName === $fileName) {
                return $metadata;
            }
        }
    }
    
    // If not found by full path hash, try searching all metadata files for filename match
    if (is_dir(MEDIA_METADATA_DIR)) {
        $metadataFiles = glob(MEDIA_METADATA_DIR . '/*.json');
        foreach ($metadataFiles as $metaFile) {
            $metadata = readJson($metaFile);
            if ($metadata && isset($metadata['file_path'])) {
                $storedPath = $metadata['file_path'];
                $storedFileName = basename($storedPath);
                // Match by filename (handles cases where path differs)
                if ($storedFileName === $fileName || $storedPath === $filePath) {
                    return $metadata;
                }
            }
        }
    }
    
    return null;
}

/**
 * Save media metadata for a file
 * 
 * @param string $filePath Relative path to file
 * @param array $metadata Metadata to save
 * @return bool Success status
 */
function saveMediaMetadata($filePath, $metadata) {
    if (!function_exists('writeJson')) {
        $storageFile = __DIR__ . '/storage.php';
        if (file_exists($storageFile)) {
            require_once $storageFile;
        }
    }
    
    if (!defined('MEDIA_METADATA_DIR')) {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }
    }
    
    if (!defined('MEDIA_METADATA_DIR')) {
        return false;
    }
    
    // Ensure directory exists
    if (!is_dir(MEDIA_METADATA_DIR)) {
        mkdir(MEDIA_METADATA_DIR, 0755, true);
    }
    
    // Normalize file path
    $filePath = ltrim($filePath, '/');
    $filePath = str_replace('\\', '/', $filePath);
    
    // Create a safe filename for metadata JSON
    $metadataKey = md5($filePath);
    $metadataFile = MEDIA_METADATA_DIR . '/' . $metadataKey . '.json';
    
    // Add file path to metadata for verification
    $metadata['file_path'] = $filePath;
    
    return writeJson($metadataFile, $metadata);
}

/**
 * Get media file path from URL
 * Extracts relative path from full URL
 */
function getMediaPathFromUrl($url) {
    if (empty($url)) {
        return '';
    }
    
    // If it's already a relative path, return as-is
    if (strpos($url, '/') !== 0 && strpos($url, 'http') !== 0) {
        return $url;
    }
    
    // Extract path from URL - check various upload directory patterns
    if (strpos($url, '/catalogue/uploads/') !== false) {
        $parts = explode('/catalogue/uploads/', $url);
        return isset($parts[1]) ? $parts[1] : '';
    }
    
    if (strpos($url, '/cms/uploads/') !== false) {
        $parts = explode('/cms/uploads/', $url);
        return isset($parts[1]) ? $parts[1] : '';
    }
    
    if (strpos($url, '/uploads/') !== false) {
        $parts = explode('/uploads/', $url);
        return isset($parts[1]) ? $parts[1] : '';
    }
    
    return '';
}

/**
 * Render image tag with metadata support
 */
function renderImage($image, $class = '') {
    if (empty($image)) {
        return '';
    }
    
    $src = '';
    $alt = '';
    $caption = '';
    
    // Handle string (just URL)
    if (is_string($image)) {
        $src = $image;
        // Try to get metadata from URL
        $filePath = getMediaPathFromUrl($src);
        if ($filePath) {
            $metadata = getMediaMetadata($filePath);
            if ($metadata) {
                $alt = $metadata['alt_text'] ?? '';
                $caption = $metadata['caption'] ?? '';
            }
        }
    } 
    // Handle array (image object)
    elseif (is_array($image)) {
        $src = $image['src'] ?? $image['url'] ?? '';
        $alt = $image['alt'] ?? '';
        
        // If alt is empty, try to get from metadata
        if (empty($alt)) {
            $filePath = getMediaPathFromUrl($src);
            if ($filePath) {
                $metadata = getMediaMetadata($filePath);
                if ($metadata) {
                    $alt = $metadata['alt_text'] ?? '';
                    $caption = $metadata['caption'] ?? '';
                }
            }
        }
    } 
    else {
        return '';
    }
    
    if (empty($src)) {
        return '';
    }
    
    // Convert relative paths to absolute
    if (strpos($src, '/') === 0 || strpos($src, 'http') === 0) {
        $imageUrl = $src;
    } else {
        // Relative to uploads
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $imageUrl = $basePath . '/cms/uploads/' . ltrim($src, '/');
    }
    
    $classAttr = $class ? ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' : '';
    $altAttr = ' alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"';
    
    $html = '<img src="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '"' . $classAttr . $altAttr . '>';
    
    // Add caption if available
    if (!empty($caption)) {
        $html .= '<figcaption>' . htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') . '</figcaption>';
    }
    
    return $html;
}

/**
 * Catalogue Raw - Get raw field value without rendering
 * Useful for getting file arrays to loop through
 * 
 * @param string $key Field name
 * @param mixed $default Default value
 * @param string $source 'content' (default) or 'site'
 * @return mixed Raw field value
 */
function catalogueRaw($key, $default = null, $source = 'content') {
    global $catalogue_content, $catalogue_site;
    
    // Get data source
    if ($source === 'site') {
        $data = $catalogue_site;
    } else {
        $data = $catalogue_content;
    }
    
    if (!$data) {
        return $default;
    }
    
    // Get value using dot notation
    $value = getNestedValue($data, $key);
    
    return $value !== null ? $value : $default;
}

/**
 * Set file context for catalogue() function
 * Used internally by catalogueFiles() iterator
 */
function setCatalogueFile($file) {
    global $catalogue_file;
    $catalogue_file = $file;
}

/**
 * Clear file context
 */
function clearCatalogueFile() {
    global $catalogue_file;
    $catalogue_file = null;
}

/**
 * Set collection item context for catalogue() function
 * Used internally by catalogueCollection() iterator
 */
function setCatalogueCollectionItem($item) {
    global $catalogue_collection_item;
    $catalogue_collection_item = $item;
}

/**
 * Clear collection item context
 */
function clearCatalogueCollectionItem() {
    global $catalogue_collection_item;
    $catalogue_collection_item = null;
}

/**
 * Set structure item context for catalogue() function
 * Used internally by catalogueStructure() iterator
 */
function setCatalogueStructureItem($item) {
    global $catalogue_structure_item;
    $catalogue_structure_item = $item;
}

/**
 * Clear structure item context
 */
function clearCatalogueStructureItem() {
    global $catalogue_structure_item;
    $catalogue_structure_item = null;
}

/**
 * Structure Iterator Class
 * Automatically sets structure item context during foreach iteration
 */
class CatalogueStructureIterator implements Iterator {
    private $items = [];
    private $position = 0;
    
    public function __construct($items) {
        $this->items = $items;
        $this->position = 0;
        // Clear context initially
        clearCatalogueStructureItem();
    }
    
    public function rewind() {
        $this->position = 0;
        if (!empty($this->items) && isset($this->items[$this->position])) {
            setCatalogueStructureItem($this->items[$this->position]);
        } else {
            clearCatalogueStructureItem();
        }
    }
    
    public function current() {
        if (isset($this->items[$this->position])) {
            setCatalogueStructureItem($this->items[$this->position]);
            return $this->items[$this->position];
        }
        clearCatalogueStructureItem();
        return null;
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        $this->position++;
        if ($this->valid()) {
            setCatalogueStructureItem($this->items[$this->position]);
        } else {
            clearCatalogueStructureItem();
        }
    }
    
    public function valid() {
        $valid = isset($this->items[$this->position]);
        if (!$valid) {
            clearCatalogueStructureItem();
        }
        return $valid;
    }
    
    public function __destruct() {
        // Ensure context is cleared when iterator is destroyed
        clearCatalogueStructureItem();
    }
}

/**
 * Set navigation item context
 */
function setCatalogueNavItem($item) {
    global $catalogue_nav_item;
    $catalogue_nav_item = $item;
}

/**
 * Clear navigation item context
 */
function clearCatalogueNavItem() {
    global $catalogue_nav_item;
    $catalogue_nav_item = null;
}

/**
 * Navigation Iterator Class
 * Automatically sets navigation item context during foreach iteration
 */
class CatalogueNavIterator implements Iterator {
    private $items = [];
    private $position = 0;
    
    public function __construct($items) {
        $this->items = $items;
        $this->position = 0;
        // Clear context initially
        clearCatalogueNavItem();
    }
    
    public function rewind() {
        $this->position = 0;
        if (!empty($this->items) && isset($this->items[$this->position])) {
            setCatalogueNavItem($this->items[$this->position]);
        } else {
            clearCatalogueNavItem();
        }
    }
    
    public function current() {
        if (isset($this->items[$this->position])) {
            setCatalogueNavItem($this->items[$this->position]);
            return $this->items[$this->position];
        }
        clearCatalogueNavItem();
        return null;
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        $this->position++;
        if ($this->valid()) {
            setCatalogueNavItem($this->items[$this->position]);
        } else {
            clearCatalogueNavItem();
        }
    }
    
    public function valid() {
        $valid = isset($this->items[$this->position]);
        if (!$valid) {
            clearCatalogueNavItem();
        }
        return $valid;
    }
    
    public function __destruct() {
        // Ensure context is cleared when iterator is destroyed
        clearCatalogueNavItem();
    }
}

/**
 * Collection Iterator Class
 * Automatically sets collection item context during foreach iteration
 */
class CatalogueCollectionIterator implements Iterator {
    private $items = [];
    private $position = 0;
    
    public function __construct($items) {
        $this->items = $items;
        $this->position = 0;
        // Clear context initially
        clearCatalogueCollectionItem();
    }
    
    public function rewind() {
        $this->position = 0;
        if (!empty($this->items) && isset($this->items[$this->position])) {
            setCatalogueCollectionItem($this->items[$this->position]);
        } else {
            clearCatalogueCollectionItem();
        }
    }
    
    public function current() {
        if (isset($this->items[$this->position])) {
            setCatalogueCollectionItem($this->items[$this->position]);
            return $this->items[$this->position];
        }
        clearCatalogueCollectionItem();
        return null;
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        $this->position++;
        if ($this->valid()) {
            setCatalogueCollectionItem($this->items[$this->position]);
        } else {
            clearCatalogueCollectionItem();
        }
    }
    
    public function valid() {
        $valid = isset($this->items[$this->position]);
        if (!$valid) {
            clearCatalogueCollectionItem();
        }
        return $valid;
    }
    
    public function __destruct() {
        // Ensure context is cleared when iterator is destroyed
        clearCatalogueCollectionItem();
    }
}

/**
 * File Iterator Class
 * Automatically sets file context during foreach iteration
 */
class CatalogueFilesIterator implements Iterator {
    private $files = [];
    private $position = 0;
    
    public function __construct($files) {
        $this->files = $files;
        $this->position = 0;
        // Clear context initially
        clearCatalogueFile();
    }
    
    public function rewind() {
        $this->position = 0;
        if (!empty($this->files) && isset($this->files[$this->position])) {
            setCatalogueFile($this->files[$this->position]);
        } else {
            clearCatalogueFile();
        }
    }
    
    public function current() {
        if (isset($this->files[$this->position])) {
            setCatalogueFile($this->files[$this->position]);
            return $this->files[$this->position];
        }
        clearCatalogueFile();
        return null;
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        $this->position++;
        if ($this->valid()) {
            setCatalogueFile($this->files[$this->position]);
        } else {
            clearCatalogueFile();
        }
    }
    
    public function valid() {
        $valid = isset($this->files[$this->position]);
        if (!$valid) {
            clearCatalogueFile();
        }
        return $valid;
    }
    
    public function __destruct() {
        // Ensure context is cleared when iterator is destroyed
        clearCatalogueFile();
    }
}

/**
 * Catalogue Files - Get files with metadata attached
 * Simple function to retrieve files from a field with all metadata
 * Sets context so catalogue() can access file metadata directly
 * 
 * Usage:
 *   foreach (catalogueFiles('files') as $file):
 *       <?= catalogue('image') ?> - Rendered image HTML
 *       <?= catalogue('alt_text') ?> - Alt text
 *       <?= catalogue('caption') ?> - Caption
 *       <?= catalogue('description') ?> - Description
 *       <?= catalogue('credit') ?> - Credit
 *       <?= catalogue('tags') ?> - Tags (formatted HTML)
 *   endforeach;
 * 
 * @param string $field Field name containing files (e.g., 'files', 'images')
 * @param string $source 'content' (default) or 'site'
 * @return CatalogueFilesIterator Iterator that sets context during iteration
 */
function catalogueFiles($field, $source = 'content') {
    // Clear any existing file context first
    clearCatalogueFile();
    
    $filesRaw = catalogueRaw($field, [], $source);
    
    if (empty($filesRaw) || !is_array($filesRaw)) {
        return new CatalogueFilesIterator([]);
    }
    
    $files = [];
    foreach ($filesRaw as $file) {
        // Get file URL/path
        $fileUrl = is_string($file) ? $file : ($file['src'] ?? $file['url'] ?? '');
        if (empty($fileUrl)) {
            continue;
        }
        
        // Get metadata for this file
        $metadata = catalogueMedia($fileUrl);
        if (!is_array($metadata)) {
            $metadata = [];
        }
        
        // Build file object with all metadata
        $fileObj = [
            'url' => $fileUrl,
            'image' => renderImage($file), // Pre-rendered image HTML
            'alt_text' => $metadata['alt_text'] ?? '',
            'caption' => $metadata['caption'] ?? '',
            'description' => $metadata['description'] ?? '',
            'credit' => $metadata['credit'] ?? '',
            'tags' => $metadata['tags'] ?? []
        ];
        
        $files[] = $fileObj;
    }
    
    return new CatalogueFilesIterator($files);
}

/**
 * Catalogue Structure - Get structure field items
 * Simple function to retrieve structure items with automatic context
 * Sets context so catalogue() can access structure item fields directly
 * 
 * Usage:
 *   foreach (catalogueStructure('settings') as $item):
 *       <?= catalogue('title') ?> - Title field
 *       <?= catalogue('value') ?> - Value field
 *       <?= catalogue('description') ?> - Description field
 *   endforeach;
 * 
 * @param string $field Field name containing structure items (e.g., 'settings', 'features')
 * @param string $source 'content' (default) or 'site'
 * @return CatalogueStructureIterator Iterator that sets context during iteration
 */
function catalogueStructure($field, $source = 'content') {
    // Clear any existing structure context first
    clearCatalogueStructureItem();
    
    $itemsRaw = catalogueRaw($field, [], $source);
    
    if (empty($itemsRaw) || !is_array($itemsRaw)) {
        return new CatalogueStructureIterator([]);
    }
    
    // Return iterator with items
    return new CatalogueStructureIterator($itemsRaw);
}

/**
 * Catalogue Media - Get media metadata
 * Helper function for templates to access media metadata
 * 
 * Usage:
 *   catalogueMedia('image.jpg', 'alt_text') - Get alt text for image.jpg
 *   catalogueMedia('image.jpg') - Get all metadata for image.jpg
 * 
 * @param string $filePath Relative path to media file (can be URL or relative path)
 * @param string|null $field Field name to return or null for all metadata
 * @return string|array|null Field value, full metadata array, or null
 */
function catalogueMedia($filePath, $field = null) {
    // Extract relative path from URL if needed
    $relativePath = getMediaPathFromUrl($filePath);
    if (empty($relativePath)) {
        $relativePath = $filePath;
    }
    
    $metadata = getMediaMetadata($relativePath);
    
    if (!$metadata) {
        return $field ? '' : null;
    }
    
    if ($field !== null) {
        return $metadata[$field] ?? '';
    }
    
    return $metadata;
}

/**
 * Include a snippet file
 * 
 * @param string $name Snippet name (without .php extension)
 * @param array $vars Optional variables to pass to snippet
 * @return string Rendered snippet HTML
 */
function snippet($name, $vars = []) {
    // Security: sanitize snippet name to prevent directory traversal
    $name = basename($name);
    $name = preg_replace('/[^a-z0-9_-]/i', '', $name);
    
    if (empty($name)) {
        error_log("CMS Snippet: Invalid snippet name");
        return '';
    }
    
    // Determine snippets directory
    $snippetsDir = CMS_ROOT . '/templates/snippets';
    $snippetFile = $snippetsDir . '/' . $name . '.php';
    
    if (!file_exists($snippetFile)) {
        error_log("CMS Snippet: Snippet not found: $snippetFile");
        return '';
    }
    
    // Security: ensure file is within snippets directory (prevent directory traversal)
    $realSnippetsDir = realpath($snippetsDir);
    $realSnippetFile = realpath($snippetFile);
    
    if (!$realSnippetFile || strpos($realSnippetFile, $realSnippetsDir) !== 0) {
        error_log("CMS Snippet: Security violation - snippet path outside snippets directory");
        return '';
    }
    
    // Extract variables to local scope
    extract($vars, EXTR_SKIP);
    
    // Capture output
    ob_start();
    try {
        include $snippetFile;
        $output = ob_get_clean();
    } catch (Exception $e) {
        ob_end_clean();
        error_log("CMS Snippet: Error including snippet '$name': " . $e->getMessage());
        return '';
    }
    
    return $output;
}

/**
 * Traffic tracking function
 * Logs page views and returns analytics data
 * 
 * @param string $action 'log' to log a page view, 'get' to retrieve stats (default: 'log')
 * @param string|null $page Page identifier (auto-detected if null)
 * @return string Returns JavaScript code when logging, or array when getting stats
 */
function traffic($action = 'log', $page = null) {
    if ($action === 'log') {
        // Determine page identifier
        if ($page === null) {
            // Use global page identifier if set (during HTML generation)
            $page = $GLOBALS['cms_page_identifier'] ?? null;
            
            if ($page === null) {
                // Auto-detect page from current request
                $page = $_SERVER['REQUEST_URI'] ?? '/';
                // Remove query strings and normalize
                $page = parse_url($page, PHP_URL_PATH);
                if (empty($page) || $page === '/') {
                    $page = 'home';
                } else {
                    // Remove leading slash and .html extension
                    $page = ltrim($page, '/');
                    $page = preg_replace('/\.html$/', '', $page);
                    if (empty($page)) {
                        $page = 'home';
                    }
                }
            }
        }
        
        // Sanitize page identifier
        $page = preg_replace('/[^a-z0-9_\/-]/i', '', $page);
        if (empty($page)) {
            $page = 'home';
        }
        
        // Output JavaScript that will log the page view when the HTML is served
        $pageJson = json_encode($page);
        // Use CMS_URL directly (it already includes /catalogue)
        // If CMS_URL is not defined, fall back to BASE_PATH + /catalogue
        if (defined('CMS_URL') && !empty(CMS_URL)) {
            $trafficLogPath = CMS_URL . '/data/traffic-log.php';
        } else {
            $basePath = defined('BASE_PATH') ? BASE_PATH : '';
            $trafficLogPath = ($basePath ?: '') . '/catalogue/data/traffic-log.php';
        }
        return <<<JS
<script>
(function() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{$trafficLogPath}', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('page=' + encodeURIComponent({$pageJson}));
})();
</script>
JS;
        
    } elseif ($action === 'get') {
        // Return traffic data for dashboard
        $trafficDir = CMS_ROOT . '/data/traffic';
        $date = date('Y-m-d');
        $trafficFile = $trafficDir . '/' . $date . '.json';
        
        $trafficData = [];
        if (file_exists($trafficFile)) {
            $trafficData = readJson($trafficFile);
            if (!is_array($trafficData)) {
                $trafficData = [];
            }
        }
        
        return $trafficData;
    }
    
    return '';
}

/**
 * Catalogue Navigation - Get page links and data
 * Simple function to retrieve pages for navigation
 * 
 * Usage:
 *   catalogueNav() - Returns all pages as array
 *   catalogueNav('home') - Returns home page data as array
 *   catalogueNav('home', 'title') - Returns home page title string
 *   catalogueNav('home', 'url') - Returns home page URL string
 *   catalogueNav(['status' => 'published']) - Returns filtered pages
 * 
 * @param string|array|null $page Page slug (e.g., 'home', 'about') or filter array
 * @param string|null $field Field name to return ('title', 'url', etc.) or null for full data
 * @return array|string Array of pages, single page data, or field value
 */
function catalogueNav($page = null, $field = null) {
    // Ensure storage functions are available
    if (!function_exists('readJson')) {
        // Load storage if not already loaded
        $storageFile = __DIR__ . '/storage.php';
        if (file_exists($storageFile)) {
            require_once $storageFile;
        }
    }
    
    // Ensure constants are defined
    if (!defined('PAGES_DIR')) {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }
    }
    
    // Ensure BASE_PATH is defined (for subfolder support)
    if (!defined('BASE_PATH')) {
        // Use the same logic as config.php
        $document_root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
        $cms_root_real = realpath(__DIR__ . '/..');
        if ($cms_root_real && $document_root && strpos($cms_root_real, $document_root) === 0) {
            $cms_path = substr($cms_root_real, strlen($document_root));
            $cms_path = str_replace('\\', '/', $cms_path);
            $cms_path = rtrim($cms_path, '/');
            $path_parts = explode('/', trim($cms_path, '/'));
            if (count($path_parts) > 1) {
                array_pop($path_parts); // Remove 'catalogue'
                $basePath = empty($path_parts) ? '' : '/' . implode('/', $path_parts);
            } else {
                $basePath = '';
            }
        } else {
            $basePath = '';
        }
        define('BASE_PATH', $basePath);
    }
    
    if (!defined('PAGES_DIR') || !is_dir(PAGES_DIR)) {
        return $field ? '' : [];
    }
    
    $pages = [];
    $pageDirs = glob(PAGES_DIR . '/*', GLOB_ONLYDIR);
    
    foreach ($pageDirs as $pageDir) {
        $pageType = basename($pageDir);
        
        // Skip system folders
        if (in_array($pageType, ['settings', 'users'])) {
            continue;
        }
        
        $contentFile = $pageDir . '/' . $pageType . '.json';
        if (!file_exists($contentFile)) {
            continue;
        }
        
        $content = readJson($contentFile);
        if (!$content) {
            continue;
        }
        
        // Get page data
        $title = $content['title'] ?? $content['name'] ?? ucfirst($pageType);
        $status = $content['_status'] ?? 'draft';
        // Normalize featured value (handle string "1" or boolean true)
        $featuredRaw = $content['_featured'] ?? false;
        $featured = ($featuredRaw === true || $featuredRaw === '1' || $featuredRaw === 1 || $featuredRaw === 'true');
        
        // Determine URL - use BASE_PATH for subfolder support
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        // Ensure BASE_PATH doesn't have trailing slash
        $basePath = rtrim($basePath, '/');
        
        if ($pageType === 'home') {
            $url = $basePath . '/';
        } else {
            $url = $basePath . '/' . $pageType . '.html';
        }
        
        $pageData = [
            'slug' => $pageType,
            'title' => $title,
            'url' => $url,
            'status' => $status,
            'featured' => $featured
        ];
        
        // Add all content fields for flexibility
        foreach ($content as $key => $value) {
            if (!isset($pageData[$key]) && $key !== '_meta') {
                $pageData[$key] = $value;
            }
        }
        
        $pages[] = $pageData;
    }
    
    // Handle filters (if first param is array)
    if (is_array($page)) {
        $filters = $page;
        $filteredPages = [];
        
        foreach ($pages as $pageData) {
            $include = true;
            
            // Status filter
            if (isset($filters['status'])) {
                if ($pageData['status'] !== $filters['status']) {
                    $include = false;
                }
            }
            
            // Featured filter
            if (isset($filters['featured']) && $filters['featured'] === true) {
                // Normalize featured value for comparison (handle string "1" or boolean true)
                $isFeatured = ($pageData['featured'] === true || $pageData['featured'] === '1' || $pageData['featured'] === 1 || $pageData['featured'] === 'true');
                if (!$isFeatured) {
                    $include = false;
                }
            }
            
            if ($include) {
                $filteredPages[] = $pageData;
            }
        }
        
        // Return as iterator for clean templating
        return new CatalogueNavIterator($filteredPages);
    }
    
    // Handle single page request
    if (is_string($page)) {
        foreach ($pages as $pageData) {
            if ($pageData['slug'] === $page) {
                // Return specific field if requested
                if ($field !== null) {
                    if ($field === 'url') {
                        return $pageData['url'];
                    }
                    return $pageData[$field] ?? '';
                }
                // Return full page data
                return $pageData;
            }
        }
        // Page not found
        return $field ? '' : [];
    }
    
    // Return all pages as iterator for clean templating
    return new CatalogueNavIterator($pages);
}

/**
 * Catalogue Collection - Get collection items
 * Simple function to retrieve collection items with filtering, sorting, and pagination
 * 
 * Usage:
 *   catalogueCollection('posts') - Returns all items (sorted by updated_at desc)
 *   catalogueCollection('posts', ['status' => 'published']) - Returns filtered items
 *   catalogueCollection('posts', ['status' => 'published', 'sort' => 'date', 'order' => 'desc']) - Sorted items
 *   catalogueCollection('posts', ['limit' => 10, 'offset' => 0]) - Paginated items
 *   catalogueCollection('posts', 'item-slug') - Returns single item data
 *   catalogueCollection('posts', 'item-slug', 'title') - Returns single field value
 * 
 * Filter options:
 *   - 'status' => 'published'|'draft' - Filter by status
 *   - 'featured' => true - Filter featured items
 *   - 'sort' => 'date'|'created_at'|'updated_at'|'title'|field_name - Sort field
 *   - 'order' => 'asc'|'desc' - Sort order (default: 'desc')
 *   - 'limit' => int - Maximum items to return
 *   - 'offset' => int - Number of items to skip
 * 
 * @param string $collection Collection name (e.g., 'posts')
 * @param string|array|null $filter Item slug, filter array, or null for all items
 * @param string|null $field Field name to return or null for full data
 * @return CatalogueCollectionIterator|array|string Iterator for foreach loops, array, or field value
 */
function catalogueCollection($collection, $filter = null, $field = null) {
    // Ensure storage functions are available
    if (!function_exists('readJson')) {
        $storageFile = __DIR__ . '/storage.php';
        if (file_exists($storageFile)) {
            require_once $storageFile;
        }
    }
    
    // Ensure constants are defined
    if (!defined('COLLECTIONS_DIR')) {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }
    }
    
    // Ensure BASE_PATH is defined
    if (!defined('BASE_PATH')) {
        // Use the same logic as config.php
        $document_root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
        $cms_root_real = realpath(__DIR__ . '/..');
        if ($cms_root_real && $document_root && strpos($cms_root_real, $document_root) === 0) {
            $cms_path = substr($cms_root_real, strlen($document_root));
            $cms_path = str_replace('\\', '/', $cms_path);
            $cms_path = rtrim($cms_path, '/');
            $path_parts = explode('/', trim($cms_path, '/'));
            if (count($path_parts) > 1) {
                array_pop($path_parts); // Remove 'catalogue'
                $basePath = empty($path_parts) ? '' : '/' . implode('/', $path_parts);
            } else {
                $basePath = '';
            }
        } else {
            $basePath = '';
        }
        define('BASE_PATH', $basePath);
    }
    
    if (!defined('COLLECTIONS_DIR') || !is_dir(COLLECTIONS_DIR)) {
        return $field ? '' : [];
    }
    
    $collectionDir = COLLECTIONS_DIR . '/' . $collection;
    if (!is_dir($collectionDir)) {
        return $field ? '' : [];
    }
    
    // Get all JSON files in collection directory
    $jsonFiles = glob($collectionDir . '/*.json');
    $items = [];
    
    foreach ($jsonFiles as $jsonFile) {
        $item = readJson($jsonFile);
        if (!$item) {
            continue;
        }
        
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
        
        // Get item data
        $title = $item['title'] ?? $item['name'] ?? 'Untitled';
        $status = $item['_status'] ?? 'draft';
        $featured = $item['_featured'] ?? false;
        
        // Extract metadata timestamps
        $created_at = null;
        $updated_at = null;
        if (isset($item['_meta'])) {
            $created_at = $item['_meta']['created'] ?? null;
            $updated_at = $item['_meta']['updated'] ?? null;
        }
        
        // Determine URL - use BASE_PATH for subfolder support
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $basePath = rtrim($basePath, '/');
        
        $url = $basePath . '/' . $collection . '/' . $slug . '.html';
        
        $itemData = [
            'slug' => $slug,
            'title' => $title,
            'url' => $url,
            'status' => $status,
            'featured' => $featured,
            'collection' => $collection,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
        
        // Add all content fields for flexibility
        foreach ($item as $key => $value) {
            if (!isset($itemData[$key]) && $key !== '_meta') {
                $itemData[$key] = $value;
            }
        }
        
        $items[] = $itemData;
    }
    
    // Handle filters (if second param is array) - return iterator for foreach loops
    if (is_array($filter)) {
        $filters = $filter;
        $filteredItems = [];
        
        foreach ($items as $itemData) {
            $include = true;
            
            // Status filter
            if (isset($filters['status'])) {
                if ($itemData['status'] !== $filters['status']) {
                    $include = false;
                }
            }
            
            // Featured filter
            if (isset($filters['featured']) && $filters['featured'] === true) {
                if (!$itemData['featured']) {
                    $include = false;
                }
            }
            
            if ($include) {
                $filteredItems[] = $itemData;
            }
        }
        
        // Apply sorting
        if (!empty($filteredItems) && isset($filters['sort'])) {
            $sortField = $filters['sort'];
            $sortOrder = isset($filters['order']) && strtolower($filters['order']) === 'asc' ? 'asc' : 'desc';
            
            usort($filteredItems, function($a, $b) use ($sortField, $sortOrder) {
                $aValue = null;
                $bValue = null;
                
                // Handle special sort fields
                if ($sortField === 'date' || $sortField === 'updated_at') {
                    $aValue = isset($a['updated_at']) ? strtotime($a['updated_at']) : 0;
                    $bValue = isset($b['updated_at']) ? strtotime($b['updated_at']) : 0;
                } elseif ($sortField === 'created_at') {
                    $aValue = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                    $bValue = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                } elseif ($sortField === 'title') {
                    $aValue = isset($a['title']) ? strtolower($a['title']) : '';
                    $bValue = isset($b['title']) ? strtolower($b['title']) : '';
                } elseif (isset($a[$sortField]) && isset($b[$sortField])) {
                    // Generic field sort
                    $aValue = $a[$sortField];
                    $bValue = $b[$sortField];
                    
                    // Try to detect numeric or date values
                    if (is_numeric($aValue) && is_numeric($bValue)) {
                        $aValue = (float)$aValue;
                        $bValue = (float)$bValue;
                    } elseif (is_string($aValue) && is_string($bValue)) {
                        // Try parsing as date
                        $aTime = strtotime($aValue);
                        $bTime = strtotime($bValue);
                        if ($aTime !== false && $bTime !== false) {
                            $aValue = $aTime;
                            $bValue = $bTime;
                        } else {
                            $aValue = strtolower($aValue);
                            $bValue = strtolower($bValue);
                        }
                    }
                } else {
                    return 0;
                }
                
                // Compare values
                if ($aValue < $bValue) {
                    return $sortOrder === 'asc' ? -1 : 1;
                } elseif ($aValue > $bValue) {
                    return $sortOrder === 'asc' ? 1 : -1;
                }
                return 0;
            });
        }
        
        // Apply pagination
        // Note: During static HTML generation, we ignore limit/offset so all items are included
        // JavaScript will handle pagination client-side. During PHP template execution, apply limits.
        $isGeneratingHtml = ob_get_level() > 0 && defined('CMS_ROOT');
        
        if (isset($filters['limit']) || isset($filters['offset'])) {
            $limit = isset($filters['limit']) ? (int)$filters['limit'] : null;
            
            // Skip pagination during HTML generation (let JavaScript handle it)
            if (!$isGeneratingHtml) {
                // Automatically calculate offset from page parameter if limit is set but offset isn't
                if ($limit !== null && !isset($filters['offset'])) {
                    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                    $offset = ($currentPage - 1) * $limit;
                } else {
                    $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
                }
                
                if ($limit !== null) {
                    $filteredItems = array_slice($filteredItems, $offset, $limit);
                } elseif ($offset > 0) {
                    $filteredItems = array_slice($filteredItems, $offset);
                }
            }
            // During HTML generation, limit/offset are ignored - all items included for JavaScript pagination
        }
        
        // Set blueprint for collection items so catalogue() can render fields correctly
        if (!function_exists('getBlueprint')) {
            $blueprintFile = __DIR__ . '/blueprint.php';
            if (file_exists($blueprintFile)) {
                require_once $blueprintFile;
            }
        }
        $blueprint = getBlueprint($collection);
        if ($blueprint) {
            setCatalogueBlueprint($blueprint);
        }
        
        // Return iterator for foreach loops (sets context so catalogue() works)
        return new CatalogueCollectionIterator($filteredItems);
    }
    
    // Handle single item request (if second param is string)
    if (is_string($filter)) {
        foreach ($items as $itemData) {
            if ($itemData['slug'] === $filter) {
                // Return specific field if requested
                if ($field !== null) {
                    if ($field === 'url') {
                        return $itemData['url'];
                    }
                    return $itemData[$field] ?? '';
                }
                // Return full item data
                return $itemData;
            }
        }
        // Item not found
        return $field ? '' : [];
    }
    
    // Return all items as iterator (for foreach loops)
    // Apply default sorting by updated_at (newest first) if no filter specified
    usort($items, function($a, $b) {
        $aTime = isset($a['updated_at']) ? strtotime($a['updated_at']) : 0;
        $bTime = isset($b['updated_at']) ? strtotime($b['updated_at']) : 0;
        return $bTime - $aTime; // Newest first
    });
    
    // Set blueprint for collection items
    if (!function_exists('getBlueprint')) {
        $blueprintFile = __DIR__ . '/blueprint.php';
        if (file_exists($blueprintFile)) {
            require_once $blueprintFile;
        }
    }
    $blueprint = getBlueprint($collection);
    if ($blueprint) {
        setCatalogueBlueprint($blueprint);
    }
    
    return new CatalogueCollectionIterator($items);
}

/**
 * Get total count of collection items (for pagination)
 * Uses the same filtering logic as catalogueCollection but excludes limit/offset
 * 
 * @param string $collection Collection name
 * @param array|null $filter Filter options (same as catalogueCollection, limit/offset ignored)
 * @return int Total count of matching items
 */
function catalogueCollectionCount($collection, $filter = null) {
    // Ensure storage functions are available
    if (!function_exists('readJson')) {
        $storageFile = __DIR__ . '/storage.php';
        if (file_exists($storageFile)) {
            require_once $storageFile;
        }
    }
    
    // Ensure constants are defined
    if (!defined('COLLECTIONS_DIR')) {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }
    }
    
    if (!defined('COLLECTIONS_DIR') || !is_dir(COLLECTIONS_DIR)) {
        return 0;
    }
    
    $collectionDir = COLLECTIONS_DIR . '/' . $collection;
    if (!is_dir($collectionDir)) {
        return 0;
    }
    
    // Get all JSON files in collection directory
    $jsonFiles = glob($collectionDir . '/*.json');
    $items = [];
    
    foreach ($jsonFiles as $jsonFile) {
        $item = readJson($jsonFile);
        if (!$item) {
            continue;
        }
        
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
        
        // Get item data
        $title = $item['title'] ?? $item['name'] ?? 'Untitled';
        $status = $item['_status'] ?? 'draft';
        $featured = $item['_featured'] ?? false;
        
        // Extract metadata timestamps
        $created_at = null;
        $updated_at = null;
        if (isset($item['_meta'])) {
            $created_at = $item['_meta']['created'] ?? null;
            $updated_at = $item['_meta']['updated'] ?? null;
        }
        
        $itemData = [
            'slug' => $slug,
            'title' => $title,
            'status' => $status,
            'featured' => $featured,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
        
        // Add all content fields for flexibility
        foreach ($item as $key => $value) {
            if (!isset($itemData[$key]) && $key !== '_meta') {
                $itemData[$key] = $value;
            }
        }
        
        $items[] = $itemData;
    }
    
    // Apply filters if provided (excluding limit/offset for count)
    if (is_array($filter)) {
        $filters = $filter;
        $filteredItems = [];
        
        foreach ($items as $itemData) {
            $include = true;
            
            // Status filter
            if (isset($filters['status'])) {
                if ($itemData['status'] !== $filters['status']) {
                    $include = false;
                }
            }
            
            // Featured filter
            if (isset($filters['featured']) && $filters['featured'] === true) {
                if (!$itemData['featured']) {
                    $include = false;
                }
            }
            
            if ($include) {
                $filteredItems[] = $itemData;
            }
        }
        
        // Apply sorting (same logic as catalogueCollection)
        if (!empty($filteredItems) && isset($filters['sort'])) {
            $sortField = $filters['sort'];
            $sortOrder = isset($filters['order']) && strtolower($filters['order']) === 'asc' ? 'asc' : 'desc';
            
            usort($filteredItems, function($a, $b) use ($sortField, $sortOrder) {
                $aValue = null;
                $bValue = null;
                
                // Handle special sort fields
                if ($sortField === 'date' || $sortField === 'updated_at') {
                    $aValue = isset($a['updated_at']) ? strtotime($a['updated_at']) : 0;
                    $bValue = isset($b['updated_at']) ? strtotime($b['updated_at']) : 0;
                } elseif ($sortField === 'created_at') {
                    $aValue = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
                    $bValue = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
                } elseif ($sortField === 'title') {
                    $aValue = isset($a['title']) ? strtolower($a['title']) : '';
                    $bValue = isset($b['title']) ? strtolower($b['title']) : '';
                } elseif (isset($a[$sortField]) && isset($b[$sortField])) {
                    // Generic field sort
                    $aValue = $a[$sortField];
                    $bValue = $b[$sortField];
                    
                    // Try to detect numeric or date values
                    if (is_numeric($aValue) && is_numeric($bValue)) {
                        $aValue = (float)$aValue;
                        $bValue = (float)$bValue;
                    } elseif (is_string($aValue) && is_string($bValue)) {
                        // Try parsing as date
                        $aTime = strtotime($aValue);
                        $bTime = strtotime($bValue);
                        if ($aTime !== false && $bTime !== false) {
                            $aValue = $aTime;
                            $bValue = $bTime;
                        } else {
                            $aValue = strtolower($aValue);
                            $bValue = strtolower($bValue);
                        }
                    }
                } else {
                    return 0;
                }
                
                // Compare values
                if ($aValue < $bValue) {
                    return $sortOrder === 'asc' ? -1 : 1;
                } elseif ($aValue > $bValue) {
                    return $sortOrder === 'asc' ? 1 : -1;
                }
                return 0;
            });
        }
        
        return count($filteredItems);
    }
    
    return count($items);
}

/**
 * Generate pagination controls
 * Automatically handles page calculation from URL and generates prev/next buttons
 * 
 * Usage:
 *   cataloguePagination('posts', ['status' => 'published', 'limit' => 10])
 * 
 * @param string $collection Collection name
 * @param array $filters Filter options (must include 'limit' for pagination)
 * @param array $options Additional options (class, prevText, nextText, etc.)
 * @return string HTML for pagination controls (empty if no limit or single page)
 */
function cataloguePagination($collection, $filters = [], $options = []) {
    // Get limit from filters
    $limit = isset($filters['limit']) ? (int)$filters['limit'] : 0;
    if ($limit <= 0) {
        return ''; // No pagination if no limit
    }
    
    // Get current page from URL (works for both PHP and static HTML via JavaScript)
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    
    // Add data attributes for JavaScript pagination (for static HTML)
    $dataLimit = $limit;
    $dataCollection = htmlspecialchars($collection, ENT_QUOTES, 'UTF-8');
    
    // Get total count (remove limit/offset from filters for count)
    $countFilters = $filters;
    unset($countFilters['limit']);
    unset($countFilters['offset']);
    $totalItems = catalogueCollectionCount($collection, $countFilters);
    
    // Calculate total pages
    $totalPages = (int)ceil($totalItems / $limit);
    
    // Don't show pagination if only one page or no items
    if ($totalPages <= 1 || $totalItems === 0) {
        return '';
    }
    
    // Get base URL (current page URL without page parameter)
    // For frontend templates, use relative URLs to avoid admin panel URL issues
    $baseUrl = '';
    
    // Try to get the current page path
    if (isset($_SERVER['PHP_SELF'])) {
        $scriptPath = $_SERVER['PHP_SELF'];
        // Only use if it's not an admin panel path
        if (strpos($scriptPath, '/catalogue/panel/') === false && strpos($scriptPath, '/catalogue/index.php') === false) {
            $baseUrl = $scriptPath;
        }
    }
    
    // If we don't have a valid frontend path, use REQUEST_URI but filter out admin paths
    if (empty($baseUrl) && isset($_SERVER['REQUEST_URI'])) {
        $requestUri = $_SERVER['REQUEST_URI'];
        // Skip if it's an admin panel or action URL
        if (strpos($requestUri, '/catalogue/panel/') === false && 
            strpos($requestUri, '/catalogue/index.php') === false &&
            strpos($requestUri, '/catalogue/panel/actions/') === false) {
            // Parse to get path only (remove query string)
            $parsed = parse_url($requestUri, PHP_URL_PATH);
            $baseUrl = $parsed ?: '';
        }
    }
    
    // If still empty or it's an admin path, use relative URL (works for static HTML too)
    if (empty($baseUrl) || strpos($baseUrl, '/catalogue/') !== false) {
        $baseUrl = ''; // Empty means relative to current page
    }
    
    // Remove existing page parameter from query string if present
    $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    if ($queryString) {
        $queryParams = [];
        parse_str($queryString, $queryParams);
        unset($queryParams['page']);
        if (!empty($queryParams)) {
            $baseUrl .= ($baseUrl ? '?' : '') . http_build_query($queryParams);
        }
    }
    
    $separator = ($baseUrl && strpos($baseUrl, '?') !== false) ? '&' : '?';
    
    // Options with defaults
    $class = isset($options['class']) ? htmlspecialchars($options['class'], ENT_QUOTES, 'UTF-8') : 'catalogue-pagination';
    $prevText = isset($options['prevText']) ? htmlspecialchars($options['prevText'], ENT_QUOTES, 'UTF-8') : 'Previous';
    $nextText = isset($options['nextText']) ? htmlspecialchars($options['nextText'], ENT_QUOTES, 'UTF-8') : 'Next';
    $showPageNumbers = isset($options['showPageNumbers']) ? (bool)$options['showPageNumbers'] : true;
    $maxPageNumbers = isset($options['maxPageNumbers']) ? (int)$options['maxPageNumbers'] : 7;
    
    // Generate unique ID for this pagination instance
    $paginationId = 'pagination-' . uniqid();
    
    // Build pagination HTML
    $html = '<nav id="' . $paginationId . '" class="' . $class . '" aria-label="Pagination" data-limit="' . $limit . '">';
    $html .= '<div class="' . $class . '__controls">';
    
    // Previous button
    $prevDisabled = $currentPage <= 1;
    $prevPage = $currentPage - 1;
    if ($prevDisabled) {
        $html .= '<span class="' . $class . '__prev ' . $class . '__prev--disabled">' . $prevText . '</span>';
    } else {
        if ($prevPage === 1) {
            $prevUrl = $baseUrl ?: './';
        } else {
            $prevUrl = ($baseUrl ?: './') . $separator . 'page=' . $prevPage;
        }
        $html .= '<a href="' . htmlspecialchars($prevUrl, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '__prev">' . $prevText . '</a>';
    }
    
    // Page numbers
    if ($showPageNumbers) {
        $html .= '<div class="' . $class . '__pages">';
        
        if ($totalPages <= $maxPageNumbers) {
            // Show all pages
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i === $currentPage) {
                    $html .= '<span class="' . $class . '__page ' . $class . '__page--active">' . $i . '</span>';
                } else {
                    if ($i === 1) {
                        $pageUrl = $baseUrl ?: './';
                    } else {
                        $pageUrl = ($baseUrl ?: './') . $separator . 'page=' . $i;
                    }
                    $html .= '<a href="' . htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '__page">' . $i . '</a>';
                }
            }
        } else {
            // Show first, last, current, and ellipsis
            // First page
            if ($currentPage === 1) {
                $html .= '<span class="' . $class . '__page ' . $class . '__page--active">1</span>';
            } else {
                $firstPageUrl = $baseUrl ?: './';
                $html .= '<a href="' . htmlspecialchars($firstPageUrl, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '__page">1</a>';
            }
            
            // Ellipsis before current if needed
            if ($currentPage > 3) {
                $html .= '<span class="' . $class . '__ellipsis">…</span>';
            }
            
            // Pages around current
            $start = max(2, $currentPage - 1);
            $end = min($totalPages - 1, $currentPage + 1);
            
            for ($i = $start; $i <= $end; $i++) {
                if ($i === $currentPage) {
                    $html .= '<span class="' . $class . '__page ' . $class . '__page--active">' . $i . '</span>';
                } else {
                    if ($i === 1) {
                        $pageUrl = $baseUrl ?: './';
                    } else {
                        $pageUrl = ($baseUrl ?: './') . $separator . 'page=' . $i;
                    }
                    $html .= '<a href="' . htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '__page">' . $i . '</a>';
                }
            }
            
            // Ellipsis after current if needed
            if ($currentPage < $totalPages - 2) {
                $html .= '<span class="' . $class . '__ellipsis">…</span>';
            }
            
            // Last page
            if ($currentPage === $totalPages) {
                $html .= '<span class="' . $class . '__page ' . $class . '__page--active">' . $totalPages . '</span>';
            } else {
                $pageUrl = ($baseUrl ?: './') . $separator . 'page=' . $totalPages;
                $html .= '<a href="' . htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '__page">' . $totalPages . '</a>';
            }
        }
        
        $html .= '</div>';
    }
    
    // Next button
    $nextDisabled = $currentPage >= $totalPages;
    $nextPage = $currentPage + 1;
    if ($nextDisabled) {
        $html .= '<span class="' . $class . '__next ' . $class . '__next--disabled">' . $nextText . '</span>';
    } else {
        $nextUrl = ($baseUrl ?: './') . $separator . 'page=' . $nextPage;
        $html .= '<a href="' . htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') . '" class="' . $class . '__next">' . $nextText . '</a>';
    }
    
    $html .= '</div>';
    $html .= '</nav>';
    
    // Add JavaScript for static HTML pagination
    $html .= '<script>
(function() {
    var nav = document.getElementById("' . $paginationId . '");
    if (!nav) {
        console.error("Catalogue Pagination: Nav element #' . $paginationId . ' not found");
        return;
    }
    
    var limit = parseInt(nav.getAttribute("data-limit")) || 10;
    
    // Find the container - look for postsfeed-container in the same section or parent
    var container = null;
    var section = nav.closest("section");
    if (section) {
        container = section.querySelector(".postsfeed-container");
    }
    
    // If not found, look backwards from nav
    if (!container) {
        var prev = nav.previousElementSibling;
        while (prev) {
            if (prev.classList && prev.classList.contains("postsfeed-container")) {
                container = prev;
                break;
            }
            prev = prev.previousElementSibling;
        }
    }
    
    // Last resort: look in parent
    if (!container && nav.parentElement) {
        container = nav.parentElement.querySelector(".postsfeed-container");
    }
    
    if (!container) {
        console.error("Catalogue Pagination: Container .postsfeed-container not found");
        return;
    }
    
    // Find all items
    var items = container.querySelectorAll("article.feed-post, article");
    if (items.length === 0) {
        console.warn("Catalogue Pagination: No items found");
        return;
    }
    
    // Calculate total pages
    var totalPages = Math.ceil(items.length / limit);
    
    // Handle pagination link clicks
    function handlePaginationClick(e) {
        e.preventDefault();
        var href = this.getAttribute("href");
        if (href) {
            if (history.pushState) {
                history.pushState(null, "", href);
                var newParams = new URLSearchParams(window.location.search);
                showPage(parseInt(newParams.get("page")) || 1);
            } else {
                window.location.href = href;
            }
        }
    }
    
    // Attach click handlers to all pagination links
    function attachHandlers() {
        var links = nav.querySelectorAll("a");
        for (var k = 0; k < links.length; k++) {
            // Remove old handler if exists
            links[k].removeEventListener("click", handlePaginationClick);
            links[k].addEventListener("click", handlePaginationClick);
        }
    }
    
    function showPage(page) {
        var startIndex = (page - 1) * limit;
        var endIndex = startIndex + limit;
        
        // Show/hide items
        for (var i = 0; i < items.length; i++) {
            if (i >= startIndex && i < endIndex) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
        
        // Update active page indicator and ensure page number links have correct hrefs
        var pages = nav.querySelectorAll(".' . $class . '__page");
        var baseUrl = window.location.pathname;
        var search = window.location.search.replace(/[?&]page=\\d+/, "");
        
        for (var j = 0; j < pages.length; j++) {
            var pageNum = parseInt(pages[j].textContent.trim());
            pages[j].classList.remove("' . $class . '__page--active");
            
            if (pageNum === page) {
                // Current page - convert to span if it\'s a link
                if (pages[j].tagName === "A") {
                    var span = document.createElement("span");
                    span.className = "' . $class . '__page ' . $class . '__page--active";
                    span.textContent = pageNum;
                    pages[j].parentNode.replaceChild(span, pages[j]);
                } else {
                    pages[j].classList.add("' . $class . '__page--active");
                }
            } else {
                // Other pages - ensure it\'s a link with correct href
                var pageUrl = pageNum === 1 ? baseUrl + (search ? search : "") : baseUrl + (search ? search + "&" : "?") + "page=" + pageNum;
                
                if (pages[j].tagName === "SPAN") {
                    // Convert span to link
                    var link = document.createElement("a");
                    link.href = pageUrl;
                    link.className = "' . $class . '__page";
                    link.textContent = pageNum;
                    pages[j].parentNode.replaceChild(link, pages[j]);
                } else if (pages[j].tagName === "A") {
                    // Update href
                    pages[j].href = pageUrl;
                }
            }
        }
        
        // Update previous button
        var prevEl = nav.querySelector(".' . $class . '__prev");
        if (prevEl) {
            if (page <= 1) {
                // Disable previous - convert link to span if needed
                if (prevEl.tagName === "A") {
                    var prevSpan = document.createElement("span");
                    prevSpan.className = "' . $class . '__prev ' . $class . '__prev--disabled";
                    prevSpan.textContent = prevEl.textContent;
                    prevEl.parentNode.replaceChild(prevSpan, prevEl);
                } else {
                    prevEl.classList.add("' . $class . '__prev--disabled");
                }
            } else {
                // Enable previous - convert span to link if needed
                var prevPage = page - 1;
                var baseUrl = window.location.pathname;
                var search = window.location.search.replace(/[?&]page=\d+/, "");
                var prevUrl = prevPage === 1 ? baseUrl + (search ? search : "") : baseUrl + (search ? search + "&" : "?") + "page=" + prevPage;
                
                if (prevEl.tagName === "SPAN") {
                    var prevA = document.createElement("a");
                    prevA.href = prevUrl;
                    prevA.className = "' . $class . '__prev";
                    prevA.textContent = prevEl.textContent;
                    prevEl.parentNode.replaceChild(prevA, prevEl);
                    // Re-attach click handler
                    prevA.addEventListener("click", handlePaginationClick);
                } else if (prevEl.tagName === "A") {
                    prevEl.href = prevUrl;
                    prevEl.classList.remove("' . $class . '__prev--disabled");
                }
            }
        }
        
        // Update next button
        var nextEl = nav.querySelector(".' . $class . '__next");
        if (nextEl) {
            if (page >= totalPages) {
                // Disable next - convert link to span if needed
                if (nextEl.tagName === "A") {
                    var nextSpan = document.createElement("span");
                    nextSpan.className = "' . $class . '__next ' . $class . '__next--disabled";
                    nextSpan.textContent = nextEl.textContent;
                    nextEl.parentNode.replaceChild(nextSpan, nextEl);
                } else {
                    nextEl.classList.add("' . $class . '__next--disabled");
                }
            } else {
                // Enable next - convert span to link if needed
                var nextPage = page + 1;
                var baseUrl = window.location.pathname;
                var search = window.location.search.replace(/[?&]page=\d+/, "");
                var nextUrl = baseUrl + (search ? search + "&" : "?") + "page=" + nextPage;
                
                if (nextEl.tagName === "SPAN") {
                    var nextA = document.createElement("a");
                    nextA.href = nextUrl;
                    nextA.className = "' . $class . '__next";
                    nextA.textContent = nextEl.textContent;
                    nextEl.parentNode.replaceChild(nextA, nextEl);
                    // Re-attach click handler
                    nextA.addEventListener("click", handlePaginationClick);
                } else if (nextEl.tagName === "A") {
                    nextEl.href = nextUrl;
                    nextEl.classList.remove("' . $class . '__next--disabled");
                }
            }
        }
        
        // Re-attach handlers after updating buttons
        attachHandlers();
    }
    
    // Get current page from URL
    var urlParams = new URLSearchParams(window.location.search);
    var currentPage = parseInt(urlParams.get("page")) || 1;
    showPage(currentPage);
    attachHandlers();
    
    // Handle browser back/forward
    window.addEventListener("popstate", function() {
        var urlParams = new URLSearchParams(window.location.search);
        showPage(parseInt(urlParams.get("page")) || 1);
    });
})();
</script>';
    
    return $html;
}

/**
 * Render navigation link HTML
 * Clean helper for generating navigation links
 * Uses navigation context if available, otherwise accepts page data
 * 
 * @param array|null $page Page data from catalogueNav() (optional if in nav context)
 * @param string|null $text Link text (optional, uses catalogue() if in nav context)
 * @param array $attributes Additional HTML attributes
 * @return string HTML anchor tag
 */
function navLink($page = null, $text = null, $attributes = []) {
    global $catalogue_nav_item;
    
    // If no page provided and we're in nav context, use context
    if ($page === null && $catalogue_nav_item !== null) {
        $page = $catalogue_nav_item;
    }
    
    if (!is_array($page) || !isset($page['url'])) {
        return '';
    }
    
    $url = htmlspecialchars($page['url'], ENT_QUOTES, 'UTF-8');
    
    // Determine link text
    if ($text !== null) {
        $linkText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    } elseif ($catalogue_nav_item !== null) {
        // Use catalogue() if in nav context - try description first, then title
        $desc = catalogue('description');
        $linkText = (!empty($desc)) ? $desc : catalogue('title');
    } else {
        $linkText = htmlspecialchars($page['title'] ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    // Build attributes string
    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
    }
    
    return '<a href="' . $url . '"' . $attrString . '>' . $linkText . '</a>';
}

/**
 * Render navigation list HTML
 * Clean helper for generating navigation lists
 * 
 * @param array $pages Array of pages from catalogueNav()
 * @param string|null $listClass CSS class for <nav> or <ul> element
 * @param string|null $itemClass CSS class for each <li> element
 * @param string|null $linkClass CSS class for each <a> element
 * @return string HTML navigation list
 */
function navList($pages, $listClass = null, $itemClass = null, $linkClass = null) {
    if (empty($pages) || !is_array($pages)) {
        return '';
    }
    
    $listClassAttr = $listClass ? ' class="' . htmlspecialchars($listClass, ENT_QUOTES, 'UTF-8') . '"' : '';
    $itemClassAttr = $itemClass ? ' class="' . htmlspecialchars($itemClass, ENT_QUOTES, 'UTF-8') . '"' : '';
    $linkAttrs = $linkClass ? ['class' => $linkClass] : [];
    
    $html = '<nav' . $listClassAttr . '><ul>';
    foreach ($pages as $page) {
        $html .= '<li' . $itemClassAttr . '>' . navLink($page, null, $linkAttrs) . '</li>';
    }
    $html .= '</ul></nav>';
    
    return $html;
}

