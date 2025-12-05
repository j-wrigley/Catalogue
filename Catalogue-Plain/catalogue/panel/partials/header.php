<?php
/**
 * Panel Header
 * Common header for admin panel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($page_title ?? 'Dashboard'); ?> - <?php echo esc(getCmsName()); ?></title>
    <link rel="stylesheet" href="<?php echo CMS_URL; ?>/panel/assets/css/panel.css">
    <?php
    // Load and apply theme colors from CMS settings
    $cms_settings_file = CMS_ROOT . '/content/cms-settings.json';
    if (file_exists($cms_settings_file)) {
        $cms_settings = readJson($cms_settings_file);
        if (isset($cms_settings['accent_color']) || isset($cms_settings['accent_hover']) || isset($cms_settings['accent_text'])) {
            $accent_color = $cms_settings['accent_color'] ?? '#e11d48';
            $accent_hover = $cms_settings['accent_hover'] ?? '#be123c';
            $accent_text = $cms_settings['accent_text'] ?? '#ffffff';
            
            // Convert hex to rgba for subtle color
            $hex = $accent_color;
            $r = hexdec(substr($hex, 1, 2));
            $g = hexdec(substr($hex, 3, 2));
            $b = hexdec(substr($hex, 5, 2));
            $accent_subtle = "rgba($r, $g, $b, 0.08)";
            
            echo '<style>';
            echo ':root {';
            echo '--color-accent: ' . esc($accent_color) . ';';
            echo '--color-accent-hover: ' . esc($accent_hover) . ';';
            echo '--color-accent-text: ' . esc($accent_text) . ';';
            echo '--color-accent-subtle: ' . esc($accent_subtle) . ';';
            echo '}';
            echo '</style>';
        }
    }
    ?>
    <style>
    /* Critical CSS for tabs - hide inactive tabs immediately */
    /* Applied globally so tabs work on all pages (pages, collections, settings) */
    .cms-tabs-content:not(.active):not([data-state="active"]) {
        display: none !important;
        visibility: hidden !important;
    }
    .cms-tabs-content.active,
    .cms-tabs-content[data-state="active"] {
        display: block !important;
        visibility: visible !important;
    }
    </style>
    <?php if ($page !== 'login'): ?>
    <script>
    // Prevent FOUC by applying collapsed state styles immediately
    // This runs synchronously in head before body renders
    (function() {
        try {
            if (typeof Storage !== 'undefined' && localStorage.getItem('cms-nav-collapsed') === 'true') {
                // Inject critical CSS immediately to prevent flash
                var style = document.createElement('style');
                style.id = 'cms-nav-collapsed-preload';
                style.textContent = '#cms-nav { width: var(--nav-width-collapsed) !important; min-width: var(--nav-width-collapsed) !important; max-width: var(--nav-width-collapsed) !important; }';
                (document.head || document.getElementsByTagName('head')[0]).appendChild(style);
            }
        } catch(e) {
            // Silently fail if localStorage not available
        }
    })();
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="cms-wrapper">
        <?php if ($page !== 'login'): ?>
            <?php require_once PANEL_DIR . '/partials/nav.php'; ?>
        <?php endif; ?>
        
        <main class="cms-main">
            <?php if ($page !== 'login'): ?>
            <?php endif; ?>
            
            <div class="cms-content-wrapper">

