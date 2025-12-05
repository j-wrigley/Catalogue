<?php
/**
 * Icon Helper Function
 * Returns SVG icon markup
 */
function icon($name, $class = 'cms-icon') {
    $icon_path = PANEL_DIR . '/assets/icons/' . $name . '.svg';
    if (file_exists($icon_path)) {
        $svg = file_get_contents($icon_path);
        // Add class to SVG
        $svg = str_replace('<svg', '<svg class="' . esc_attr($class) . '"', $svg);
        return $svg;
    }
    return '';
}

