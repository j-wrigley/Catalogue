<?php
/**
 * CMS Settings Page
 * Settings for the CMS system itself (not site settings)
 */
$page = 'cms-settings';
$page_title = 'Settings';
require_once PANEL_DIR . '/partials/header.php';

// Load CMS settings from config or create default
$cms_settings_file = CMS_ROOT . '/content/cms-settings.json';
$cms_settings = [
    'site_name' => getCmsName(),
    'accent_color' => '#e11d48',
    'accent_hover' => '#be123c',
    'accent_text' => '#ffffff',
    'traffic_enabled' => true,
];

if (file_exists($cms_settings_file)) {
    $saved_settings = readJson($cms_settings_file);
    $cms_settings = array_merge($cms_settings, $saved_settings);
}
?>
<div class="cms-content">
    <form id="cms-settings-form" class="cms-settings-form">
        <input type="hidden" name="csrf_token" value="<?php echo esc_attr(generateCsrfToken()); ?>">
        
        <!-- General Settings Accordion -->
        <div class="cms-accordion" data-component="accordion">
            <div class="cms-accordion-item">
                <button type="button" class="cms-accordion-trigger" aria-expanded="false" aria-controls="general-content">
                    <span class="cms-accordion-title">General</span>
                    <span class="cms-accordion-icon" aria-hidden="true">
                        <?php echo icon('chevron-down', 'cms-icon'); ?>
                    </span>
                </button>
                <div class="cms-accordion-content" id="general-content" aria-hidden="true">
                    <div class="cms-accordion-content-inner">
                        <div class="cms-form-group">
                            <label for="site_name" class="cms-label">CMS Name</label>
                            <input type="text" id="site_name" name="site_name" class="cms-input" value="<?php echo esc_attr($cms_settings['site_name']); ?>" placeholder="JSON Catalogue">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Theme Settings Accordion -->
        <div class="cms-accordion" data-component="accordion">
            <div class="cms-accordion-item">
                <button type="button" class="cms-accordion-trigger" aria-expanded="false" aria-controls="theme-content">
                    <span class="cms-accordion-title">Theme</span>
                    <span class="cms-accordion-icon" aria-hidden="true">
                        <?php echo icon('chevron-down', 'cms-icon'); ?>
                    </span>
                </button>
                <div class="cms-accordion-content" id="theme-content" aria-hidden="true">
                    <div class="cms-accordion-content-inner">
                        <!-- Theme Presets -->
                        <div class="cms-form-group">
                            <label class="cms-label">Theme Presets</label>
                            <div class="cms-theme-presets">
                                <?php
                                $presets = [
                                    ['name' => 'Red', 'accent' => '#e11d48', 'hover' => '#be123c', 'text' => '#ffffff'],
                                    ['name' => 'Blue', 'accent' => '#2563eb', 'hover' => '#1d4ed8', 'text' => '#ffffff'],
                                    ['name' => 'Green', 'accent' => '#16a34a', 'hover' => '#15803d', 'text' => '#ffffff'],
                                    ['name' => 'Purple', 'accent' => '#9333ea', 'hover' => '#7e22ce', 'text' => '#ffffff'],
                                    ['name' => 'Orange', 'accent' => '#ea580c', 'hover' => '#c2410c', 'text' => '#ffffff'],
                                    ['name' => 'Pink', 'accent' => '#db2777', 'hover' => '#be185d', 'text' => '#ffffff'],
                                    ['name' => 'Teal', 'accent' => '#14b8a6', 'hover' => '#0d9488', 'text' => '#ffffff'],
                                    ['name' => 'Indigo', 'accent' => '#6366f1', 'hover' => '#4f46e5', 'text' => '#ffffff'],
                                ];
                                
                                $current_accent = strtoupper($cms_settings['accent_color'] ?? '#e11d48');
                                
                                foreach ($presets as $preset):
                                    $is_active = (strtoupper($preset['accent']) === $current_accent);
                                ?>
                                    <button type="button" class="cms-theme-preset-card <?php echo $is_active ? 'active' : ''; ?>" 
                                            data-accent="<?php echo esc_attr($preset['accent']); ?>"
                                            data-hover="<?php echo esc_attr($preset['hover']); ?>"
                                            data-text="<?php echo esc_attr($preset['text']); ?>"
                                            title="<?php echo esc_attr($preset['name']); ?>">
                                        <div class="cms-theme-preset-color" style="background-color: <?php echo esc_attr($preset['accent']); ?>;"></div>
                                        <span class="cms-theme-preset-name"><?php echo esc($preset['name']); ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Custom Colors -->
                        <div class="cms-theme-custom">
                            <div class="cms-form-group">
                                <label for="accent_color" class="cms-label">Accent Color</label>
                                <div class="cms-color-input-group">
                                    <input type="color" id="accent_color" name="accent_color" class="cms-color-input" value="<?php echo esc_attr($cms_settings['accent_color'] ?? '#e11d48'); ?>">
                                    <input type="text" id="accent_color_text" class="cms-color-text-input" value="<?php echo esc_attr($cms_settings['accent_color'] ?? '#e11d48'); ?>" placeholder="#e11d48">
                                </div>
                            </div>
                            <div class="cms-form-group">
                                <label for="accent_hover" class="cms-label">Hover Color</label>
                                <div class="cms-color-input-group">
                                    <input type="color" id="accent_hover" name="accent_hover" class="cms-color-input" value="<?php echo esc_attr($cms_settings['accent_hover'] ?? '#be123c'); ?>">
                                    <input type="text" id="accent_hover_text" class="cms-color-text-input" value="<?php echo esc_attr($cms_settings['accent_hover'] ?? '#be123c'); ?>" placeholder="#be123c">
                                </div>
                            </div>
                            <div class="cms-form-group">
                                <label for="accent_text" class="cms-label">Text Color</label>
                                <div class="cms-color-input-group">
                                    <input type="color" id="accent_text" name="accent_text" class="cms-color-input" value="<?php echo esc_attr($cms_settings['accent_text'] ?? '#ffffff'); ?>">
                                    <input type="text" id="accent_text_text" class="cms-color-text-input" value="<?php echo esc_attr($cms_settings['accent_text'] ?? '#ffffff'); ?>" placeholder="#ffffff">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Site Regeneration Accordion -->
        <div class="cms-accordion" data-component="accordion">
            <div class="cms-accordion-item">
                <button type="button" class="cms-accordion-trigger" aria-expanded="false" aria-controls="regenerate-content">
                    <span class="cms-accordion-title">Site Regeneration</span>
                    <span class="cms-accordion-icon" aria-hidden="true">
                        <?php echo icon('chevron-down', 'cms-icon'); ?>
                    </span>
                </button>
                <div class="cms-accordion-content" id="regenerate-content" aria-hidden="true">
                    <div class="cms-accordion-content-inner">
                        <div class="cms-form-group">
                            <p class="cms-text-muted" style="margin-bottom: var(--space-4);">
                                Regenerate all HTML files from templates. Use this after making template changes to update all pages and collections at once.
                            </p>
                            <button type="button" id="regenerate-all-btn" class="cms-button cms-button-primary">
                                <?php echo icon('refresh', 'cms-icon'); ?>
                                <span>Regenerate All Pages & Collections</span>
                            </button>
                            <div id="regenerate-status" style="display: none;"></div>
                            <p class="cms-text-muted" style="margin-top: var(--space-4); font-size: var(--font-size-xs);">
                                <strong>Note:</strong> To edit the 404 error page, go to <a href="<?php echo CMS_URL; ?>/index.php?page=pages&action=edit&type=404" style="color: var(--color-accent);">Pages → Edit 404 Page</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Traffic Overview Accordion -->
        <div class="cms-accordion" data-component="accordion">
            <div class="cms-accordion-item">
                <button type="button" class="cms-accordion-trigger" aria-expanded="false" aria-controls="traffic-content">
                    <span class="cms-accordion-title">Traffic Overview</span>
                    <span class="cms-accordion-icon" aria-hidden="true">
                        <?php echo icon('chevron-down', 'cms-icon'); ?>
                    </span>
                </button>
                <div class="cms-accordion-content" id="traffic-content" aria-hidden="true">
                    <div class="cms-accordion-content-inner">
                        <div class="cms-form-group">
                            <label class="cms-label">Enable Traffic Tracking</label>
                            <?php 
                            $trafficEnabled = !isset($cms_settings['traffic_enabled']) || $cms_settings['traffic_enabled'];
                            $checked = $trafficEnabled ? 'checked' : '';
                            ?>
                            <div class="cms-switch-wrapper" data-field="traffic_enabled">
                                <input type="hidden" id="traffic_enabled" name="traffic_enabled" value="<?php echo $trafficEnabled ? '1' : '0'; ?>" />
                                <label class="cms-switch-label">
                                    <button type="button" role="switch" aria-checked="<?php echo $trafficEnabled ? 'true' : 'false'; ?>" data-state="<?php echo $trafficEnabled ? 'checked' : 'unchecked'; ?>" aria-label="Enable Traffic Tracking" class="cms-switch" data-switch="traffic_enabled" tabindex="0">
                                        <span class="cms-switch-thumb"></span>
                                    </button>
                                    <span class="cms-switch-label-text">When disabled, the traffic overview card will be hidden from the dashboard.</span>
                                </label>
                            </div>
                        </div>
                        <div class="cms-form-group">
                            <label class="cms-label">Reset Traffic Data</label>
                            <button type="button" class="cms-button cms-button-outline" id="reset-traffic-btn">
                                <?php echo icon('trash', 'cms-icon'); ?>
                                <span>Reset All Traffic Data</span>
                            </button>
                            <p class="cms-form-help">This will permanently delete all traffic data and cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="cms-form-actions">
            <button type="submit" class="cms-button cms-button-primary">Save Settings</button>
            <button type="button" class="cms-button cms-button-ghost" onclick="window.location.reload()">Cancel</button>
        </div>
    </form>
</div>

<script>
// Accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const accordions = document.querySelectorAll('[data-component="accordion"]');
    
    accordions.forEach(accordion => {
        const trigger = accordion.querySelector('.cms-accordion-trigger');
        const content = accordion.querySelector('.cms-accordion-content');
        const icon = accordion.querySelector('.cms-accordion-icon');
        
        if (!trigger || !content) return;
        
        trigger.addEventListener('click', function() {
            const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
            
            // Toggle state
            trigger.setAttribute('aria-expanded', !isExpanded);
            content.setAttribute('aria-hidden', isExpanded);
            
            // Update icon rotation
            if (icon) {
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            }
            
            // Set max-height for smooth animation
            if (!isExpanded) {
                // Expand
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                // Collapse
                content.style.maxHeight = '0px';
            }
        });
    });
    
    // Form submission
    const form = document.getElementById('cms-settings-form');
    if (form) {
        // Theme preset selection
        const presetCards = document.querySelectorAll('.cms-theme-preset-card');
        presetCards.forEach(card => {
            card.addEventListener('click', function() {
                const accent = this.getAttribute('data-accent');
                const hover = this.getAttribute('data-hover');
                const text = this.getAttribute('data-text');
                
                // Update color inputs
                document.getElementById('accent_color').value = accent;
                document.getElementById('accent_color_text').value = accent;
                document.getElementById('accent_hover').value = hover;
                document.getElementById('accent_hover_text').value = hover;
                document.getElementById('accent_text').value = text;
                document.getElementById('accent_text_text').value = text;
                
                // Update active state
                presetCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                
                // Update preview
                updateThemePreview();
            });
        });
        
        // Sync color inputs with text inputs
        const colorInputs = {
            'accent_color': 'accent_color_text',
            'accent_hover': 'accent_hover_text',
            'accent_text': 'accent_text_text'
        };
        
        Object.keys(colorInputs).forEach(colorId => {
            const colorInput = document.getElementById(colorId);
            const textInput = document.getElementById(colorInputs[colorId]);
            
            if (colorInput && textInput) {
                // Color input -> text input
                colorInput.addEventListener('input', function() {
                    textInput.value = this.value;
                    updateThemePreview();
                });
                
                // Text input -> color input
                textInput.addEventListener('input', function() {
                    const value = this.value.trim();
                    if (/^#[0-9A-F]{6}$/i.test(value)) {
                        colorInput.value = value;
                        updateThemePreview();
                    }
                });
            }
        });
        
        // Update theme preview in real-time
        function updateThemePreview() {
            const accentColor = document.getElementById('accent_color')?.value || '<?php echo esc_js($cms_settings['accent_color'] ?? '#e11d48'); ?>';
            const accentHover = document.getElementById('accent_hover')?.value || '<?php echo esc_js($cms_settings['accent_hover'] ?? '#be123c'); ?>';
            const accentText = document.getElementById('accent_text')?.value || '<?php echo esc_js($cms_settings['accent_text'] ?? '#ffffff'); ?>';
            
            // Convert hex to rgba for subtle color
            function hexToRgba(hex, alpha = 0.08) {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }
            
            const accentSubtle = hexToRgba(accentColor);
            
            // Apply theme colors to CSS variables
            document.documentElement.style.setProperty('--color-accent', accentColor);
            document.documentElement.style.setProperty('--color-accent-hover', accentHover);
            document.documentElement.style.setProperty('--color-accent-text', accentText);
            document.documentElement.style.setProperty('--color-accent-subtle', accentSubtle);
        }
        
        // Initialize preview
        updateThemePreview();
        
        // Reset traffic button handler
        const resetTrafficBtn = document.getElementById('reset-traffic-btn');
        if (resetTrafficBtn) {
            resetTrafficBtn.addEventListener('click', async function() {
                if (!confirm('Are you sure you want to reset all traffic data? This action cannot be undone.')) {
                    return;
                }
                
                // Get CSRF token from form
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
                if (!csrfToken) {
                    toast.error('CSRF token not found. Please refresh the page and try again.');
                    return;
                }
                
                try {
                    const response = await fetch('<?php echo CMS_URL; ?>/panel/actions/reset-traffic.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'csrf_token=' + encodeURIComponent(csrfToken)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        toast.success('Traffic data has been reset successfully.');
                    } else {
                        toast.error(result.error || 'Failed to reset traffic data.');
                    }
                } catch (error) {
                    toast.error('An error occurred while resetting traffic data.');
                    console.error('Reset traffic error:', error);
                }
            });
        }
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const settingsData = {};
            
            // Extract form fields
            for (let element of this.elements) {
                if (!element.name || element.name === 'csrf_token') {
                    continue;
                }
                
                // Handle switches (hidden inputs in switch wrappers)
                if (element.type === 'hidden' && element.closest('.cms-switch-wrapper')) {
                    settingsData[element.name] = element.value === '1' ? true : false;
                    continue;
                }
                
                if (element.type === 'checkbox') {
                    settingsData[element.name] = element.checked ? true : false;
                } else {
                    settingsData[element.name] = element.value || '';
                }
            }
            
            // Save settings
            const saveFormData = new FormData();
            saveFormData.append('csrf_token', formData.get('csrf_token'));
            saveFormData.append('settings_data', JSON.stringify(settingsData));
            
            try {
                const response = await fetch('<?php echo CMS_URL; ?>/panel/actions/save-cms-settings.php', {
                    method: 'POST',
                    body: saveFormData
                });
                
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Save failed - HTTP error:', response.status, text);
                    toast.error('Server error: ' + response.status);
                    return;
                }
                
                const result = await response.json();
                
                if (result.success) {
                    toast.success('Settings saved successfully!');
                    // Reload page to apply theme changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    console.error('Save failed:', result.error);
                    toast.error(result.error || 'Failed to save settings');
                }
            } catch (error) {
                console.error('Save error:', error);
                toast.error('An error occurred while saving: ' + error.message);
            }
        });
    }
    
    // Regenerate All button handler
    const regenerateBtn = document.getElementById('regenerate-all-btn');
    const regenerateStatus = document.getElementById('regenerate-status');
    const regenerateAccordion = document.querySelector('#regenerate-content');
    const regenerateTrigger = document.querySelector('[aria-controls="regenerate-content"]');
    
    // Helper function to expand accordion and update its height
    function expandRegenerateAccordion() {
        if (regenerateTrigger && regenerateAccordion) {
            const isExpanded = regenerateTrigger.getAttribute('aria-expanded') === 'true';
            if (!isExpanded) {
                // Expand the accordion
                regenerateTrigger.setAttribute('aria-expanded', 'true');
                regenerateAccordion.setAttribute('aria-hidden', 'false');
                const icon = regenerateTrigger.querySelector('.cms-accordion-icon');
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
                // Set initial max-height for animation
                regenerateAccordion.style.maxHeight = regenerateAccordion.scrollHeight + 'px';
            }
        }
    }
    
    // Helper function to update accordion height when content changes
    function updateRegenerateAccordionHeight() {
        if (regenerateAccordion && regenerateAccordion.getAttribute('aria-hidden') === 'false') {
            // Temporarily remove max-height constraint to measure actual content height
            const tempMaxHeight = regenerateAccordion.style.maxHeight;
            regenerateAccordion.style.maxHeight = 'none';
            regenerateAccordion.style.transition = 'none'; // Disable transition for instant measurement
            // Force reflow to get accurate scrollHeight
            void regenerateAccordion.offsetHeight;
            // Calculate new height (ensure minimum height for scrollable area visibility)
            const contentHeight = regenerateAccordion.scrollHeight;
            const newMaxHeight = Math.max(contentHeight, 500); // At least 500px to show scrollable area
            // Restore transition and set new max-height
            regenerateAccordion.style.transition = '';
            regenerateAccordion.style.maxHeight = newMaxHeight + 'px';
        }
    }
    
    if (regenerateBtn && regenerateStatus) {
        regenerateBtn.addEventListener('click', async function() {
            // Expand accordion first
            expandRegenerateAccordion();
            
            // Disable button and show loading state
            this.disabled = true;
            const originalHTML = this.innerHTML;
            this.innerHTML = '<span class="cms-loading-spinner"></span><span>Regenerating...</span>';
            
            regenerateStatus.style.display = 'block';
            regenerateStatus.innerHTML = '<p class="cms-text-muted">Regenerating all HTML files...</p>';
            
            // Update accordion height after showing status
            setTimeout(() => {
                updateRegenerateAccordionHeight();
            }, 50);
            
            // Get CSRF token from form
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
            
            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch('<?php echo CMS_URL; ?>/panel/actions/regenerate-all.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('HTTP error: ' + response.status);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    let statusHTML = '<div style="color: var(--color-success); margin-bottom: var(--space-2);"><strong>✓ Successfully regenerated ' + result.count + ' file(s)</strong></div>';
                    
                    if (result.generated && result.generated.length > 0) {
                        statusHTML += '<ul style="margin: var(--space-2) 0; padding-left: var(--space-5); color: var(--color-text-muted); font-size: var(--font-size-sm);">';
                        result.generated.forEach(item => {
                            statusHTML += '<li>' + item + '</li>';
                        });
                        statusHTML += '</ul>';
                    }
                    
                    if (result.skipped && result.skipped.length > 0) {
                        statusHTML += '<div style="color: var(--color-text-muted); margin-top: var(--space-3);"><strong>Skipped:</strong></div>';
                        statusHTML += '<ul style="margin: var(--space-2) 0; padding-left: var(--space-5); color: var(--color-text-muted); font-size: var(--font-size-sm);">';
                        result.skipped.forEach(item => {
                            statusHTML += '<li>' + item + '</li>';
                        });
                        statusHTML += '</ul>';
                    }
                    
                    if (result.errors && result.errors.length > 0) {
                        statusHTML += '<div style="color: var(--color-danger); margin-top: var(--space-3);"><strong>Errors:</strong></div>';
                        statusHTML += '<ul style="margin: var(--space-2) 0; padding-left: var(--space-5); color: var(--color-danger); font-size: var(--font-size-sm);">';
                        result.errors.forEach(error => {
                            statusHTML += '<li>' + error + '</li>';
                        });
                        statusHTML += '</ul>';
                    }
                    
                    regenerateStatus.innerHTML = statusHTML;
                    // Update accordion height after content is added
                    setTimeout(() => {
                        updateRegenerateAccordionHeight();
                    }, 100);
                    toast.success('All HTML files regenerated successfully!');
                } else {
                    let errorMsg = 'Failed to regenerate files.';
                    if (result.errors && result.errors.length > 0) {
                        errorMsg += ' Errors: ' + result.errors.join(', ');
                    }
                    regenerateStatus.innerHTML = '<p style="color: var(--color-danger);">' + errorMsg + '</p>';
                    // Update accordion height
                    setTimeout(() => {
                        updateRegenerateAccordionHeight();
                    }, 100);
                    toast.error(errorMsg);
                }
            } catch (error) {
                console.error('Regenerate error:', error);
                regenerateStatus.innerHTML = '<p style="color: var(--color-danger);">An error occurred: ' + error.message + '</p>';
                // Update accordion height
                setTimeout(() => {
                    updateRegenerateAccordionHeight();
                }, 100);
                toast.error('An error occurred while regenerating files: ' + error.message);
            } finally {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = originalHTML;
            }
        });
    }
});
</script>

<?php require_once PANEL_DIR . '/partials/footer.php'; ?>

