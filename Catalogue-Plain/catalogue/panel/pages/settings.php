<?php
/**
 * Settings Page
 */
$page = 'settings';
$page_title = 'Settings';
require_once PANEL_DIR . '/partials/header.php';
?>
<style>
/* Immediately hide inactive tabs - critical CSS */
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
<?php

// Load settings blueprint
$blueprint = getBlueprint('settings');

// Load existing settings
// Note: save.php forces filename to {content_type}.json for pages, so it's settings.json
$settings_file = PAGES_DIR . '/settings/settings.json';
$settings = [];
if (file_exists($settings_file)) {
    $loaded_settings = readJson($settings_file);
    if ($loaded_settings && is_array($loaded_settings)) {
        $settings = $loaded_settings;
// Remove metadata if present
if (isset($settings['_meta'])) {
    unset($settings['_meta']);
        }
    }
}
?>
<div class="cms-content">
    <?php if ($blueprint): ?>
        <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
            <div class="cms-card-body">
                <?php echo generateFormFromBlueprintWithTabs($blueprint, $settings, true); ?>
            </div>
        </div>
        
        <script>
        // Immediately show active tab and hide others (runs synchronously)
        (function() {
            const tabContents = document.querySelectorAll('.cms-tabs-content');
            let foundActive = false;
            tabContents.forEach((content, index) => {
                const isActive = content.classList.contains('active') || content.getAttribute('data-state') === 'active' || index === 0;
                if (isActive && !foundActive) {
                    content.style.setProperty('display', 'block', 'important');
                    content.classList.add('active');
                    content.setAttribute('data-state', 'active');
                    foundActive = true;
                } else {
                    content.style.setProperty('display', 'none', 'important');
                    content.classList.remove('active');
                    content.setAttribute('data-state', 'inactive');
                }
            });
        })();
        
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('content-form');
            if (!form) {
                console.error('Settings form not found');
                return;
            }
            
            // Initialize form components (tags, dropdowns, etc.)
            // Use setTimeout to ensure DOM is fully ready, especially for tabs
            setTimeout(function() {
                if (typeof window.initializeFormComponents === 'function') {
                    window.initializeFormComponents(form);
                    
                    // Verify tags inputs exist and have handlers
                    form.querySelectorAll('.cms-tags-input').forEach(function(input) {
                        console.log('Tags input found:', input.id || input.name);
                    });
                } else {
                    console.error('initializeFormComponents not available');
                }
            }, 200);
            
            // Prevent form submission on Enter key when in tags input or markdown editor
            // This must be on the form itself to catch Enter before native submit
            form.addEventListener('keydown', function(e) {
                // Only handle Enter key
                if (e.key !== 'Enter') return;
                
                const target = e.target;
                // Allow Enter in textareas
                if (target.tagName === 'TEXTAREA') {
                    return true;
                }
                // Allow Enter in contenteditable elements (markdown editor)
                if (target.isContentEditable || target.getAttribute('contenteditable') === 'true') {
                    return true;
                }
                // Check if the target is a tags input
                if (target && target.classList && target.classList.contains('cms-tags-input')) {
                    // Prevent form submission
                    e.preventDefault();
                    e.stopPropagation();
                    // Don't stop immediate propagation - let the tags handler run
                    // The tags handler will add the tag
                }
            }, false); // Bubble phase - runs after tags handler (capture phase)
            
            form.addEventListener('submit', async function(e) {
            // Check if submit was triggered by Enter in tags input - if so, prevent submission
            const activeElement = document.activeElement;
            if (activeElement && activeElement.classList && activeElement.classList.contains('cms-tags-input')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            e.preventDefault();
            
            const formData = new FormData(this);
            const contentData = {};
            
                // Extract form fields - handle all field types properly
            for (let element of this.elements) {
                if (!element.name || element.name === 'csrf_token' || element.name.includes('_current')) {
                    continue;
                }
                
                    // Skip buttons
                    if (element.type === 'submit' || element.type === 'button') {
                        continue;
                    }
                    
                    // Handle checkboxes
                    if (element.type === 'checkbox') {
                        contentData[element.name] = element.checked;
                        continue;
                    }
                    
                    // Handle radio buttons
                    if (element.type === 'radio') {
                        if (element.checked) {
                            contentData[element.name] = element.value;
                        }
                        continue;
                    }
                    
                    // Handle file inputs (for media fields)
                    if (element.type === 'file') {
                        // File fields are handled via hidden inputs with JSON values
                        // Skip the actual file input, use the hidden input instead
                        continue;
                    }
                    
                    // Handle hidden inputs (may contain JSON for tags, files, etc.)
                    if (element.type === 'hidden') {
                        const value = element.value || '';
                        // Try to parse as JSON if it looks like JSON
                        if (value.trim().startsWith('[') || value.trim().startsWith('{')) {
                            try {
                                contentData[element.name] = JSON.parse(value);
                            } catch (e) {
                                // Not valid JSON, store as string
                                contentData[element.name] = value;
                            }
                        } else {
                            contentData[element.name] = value;
                        }
                        continue;
                    }
                    
                    // Handle textareas and text inputs
                    if (element.tagName === 'TEXTAREA' || element.type === 'text' || element.type === 'email' || element.type === 'url' || element.type === 'number') {
                        contentData[element.name] = element.value || '';
                        continue;
                    }
                    
                    // Default: use value
                contentData[element.name] = element.value || '';
            }
            
            // Save settings
            const saveFormData = new FormData();
            saveFormData.append('csrf_token', formData.get('csrf_token'));
            saveFormData.append('content_type', 'settings');
            saveFormData.append('content_kind', 'page');
            saveFormData.append('content_data', JSON.stringify(contentData));
                saveFormData.append('filename', 'settings.json');
            
            try {
                const response = await fetch('<?php echo CMS_URL; ?>/panel/actions/save.php', {
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
                } else {
                    console.error('Save failed:', result.error);
                        toast.error(result.error || 'Failed to save settings');
                }
            } catch (error) {
                console.error('Save error:', error);
                    toast.error('An error occurred while saving: ' + error.message);
            }
            });
        });
        </script>
    <?php else: ?>
        <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
            <div class="cms-card-body">
                <div class="cms-empty-state">
                    <?php echo icon('component-placeholder', 'cms-empty-state-icon'); ?>
                    <p class="cms-text-muted">Settings blueprint not found. Create settings.blueprint.yml in the /blueprints directory.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require_once PANEL_DIR . '/partials/footer.php'; ?>