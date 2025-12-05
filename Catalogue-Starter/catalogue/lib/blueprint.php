<?php
/**
 * Blueprint Functions
 * Parse YAML blueprints to PHP arrays
 */

/**
 * Parse YAML blueprint file
 * Simple YAML parser for basic blueprints (supports arrays, strings, booleans)
 */
function parseBlueprint($filepath) {
    if (!file_exists($filepath)) {
        return null;
    }
    
    $content = file_get_contents($filepath);
    if ($content === false) {
        return null;
    }
    
    // Simple YAML parser (for basic needs)
    // For production, consider using Symfony YAML component
    return parseSimpleYaml($content);
}

/**
 * Simple YAML parser for basic blueprints
 */
function parseSimpleYaml($content) {
    $lines = explode("\n", $content);
    $result = [];
    $stack = [];
    $current = &$result;
    $indent = 0;
    
    foreach ($lines as $line) {
        $line = rtrim($line);
        
        // Skip empty lines and comments
        if (empty($line) || preg_match('/^\s*#/', $line)) {
            continue;
        }
        
        // Calculate indentation
        preg_match('/^(\s*)/', $line, $matches);
        $lineIndent = strlen($matches[1]);
        
        // Adjust stack based on indent
        while (count($stack) > 0 && $stack[count($stack) - 1]['indent'] >= $lineIndent) {
            array_pop($stack);
        }
        
        // Set current context
        if (count($stack) > 0) {
            $current = &$stack[count($stack) - 1]['ref'];
        } else {
            $current = &$result;
        }
        
        // Parse key-value pair
        if (preg_match('/^(\s*)([^:]+):\s*(.+)$/', $line, $matches)) {
            $key = trim($matches[2]);
            $value = trim($matches[3]);
            
            // Remove quotes if present
            if (preg_match('/^["\'](.+)["\']$/', $value, $vMatches)) {
                $value = $vMatches[1];
            }
            
            // Boolean values
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }
            
            $current[$key] = $value;
            $stack[] = ['indent' => $lineIndent, 'ref' => &$current[$key]];
        } elseif (preg_match('/^(\s*)([^:]+):\s*$/', $line, $matches)) {
            // Key with no value (array)
            $key = trim($matches[2]);
            $current[$key] = [];
            $stack[] = ['indent' => $lineIndent, 'ref' => &$current[$key]];
        }
    }
    
    return $result;
}

/**
 * Get all blueprints
 */
function getAllBlueprints() {
    $blueprints = [];
    if (!is_dir(BLUEPRINTS_DIR)) {
        return $blueprints;
    }
    
    $files = glob(BLUEPRINTS_DIR . '/*.blueprint.yml');
    foreach ($files as $file) {
        $name = basename($file, '.blueprint.yml');
        $blueprints[$name] = parseBlueprint($file);
    }
    
    return $blueprints;
}

/**
 * Get blueprint by name
 */
function getBlueprint($name) {
    $filepath = BLUEPRINTS_DIR . '/' . $name . '.blueprint.yml';
    return parseBlueprint($filepath);
}

