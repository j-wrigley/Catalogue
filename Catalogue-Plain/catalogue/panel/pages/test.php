<?php
/**
 * Test Page - Showcase all blueprint field types
 */
$page = 'test';
$page_title = 'Blueprint Test';
require_once PANEL_DIR . '/partials/header.php';

// Load test blueprint
$blueprint = getBlueprint('test');

// Load existing content (if any)
$content_file = PAGES_DIR . '/test/content.json';
$content = null;
if (file_exists($content_file)) {
    $content = readJson($content_file);
    // Remove metadata if present
    if (isset($content['_meta'])) {
        unset($content['_meta']);
    }
}
?>
<div class="cms-content">
    <?php if ($blueprint): ?>
        <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
            <div class="cms-card-body">
                <?php echo generateFormFromBlueprint($blueprint, $content); ?>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('content-form');
        
        // Completely prevent Enter key from submitting form (except on submit button)
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                // Allow Enter in textareas
                if (target.tagName === 'TEXTAREA') {
                    return true;
                }
                // Allow Enter in contenteditable elements (markdown editor)
                if (target.isContentEditable || target.getAttribute('contenteditable') === 'true') {
                    return true;
                }
                // Allow Enter in tags input - it will be handled by tags handler
                if (target.classList && target.classList.contains('cms-tags-input')) {
                    return true; // Let tags handler deal with it
                }
                // Allow Enter on submit button
                if (target.type === 'submit' || (target.tagName === 'BUTTON' && target.type === 'submit')) {
                    return true;
                }
                // Prevent all other Enter key presses
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }, true); // Use capture phase
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const contentData = {};

            // Extract form fields
            for (let element of this.elements) {
                if (!element.name || element.name === 'csrf_token' || element.name.includes('_current')) {
                    continue;
                }

                // Handle file fields
                if (element.type === 'file') {
                    if (element.files.length > 0) {
                        // File upload will be handled separately
                        continue;
                    } else {
                        // Use current value if no new file
                        const currentInput = this.querySelector('input[name="' + element.name + '_current"]');
                        if (currentInput) {
                            contentData[element.name] = currentInput.value ? { src: currentInput.value } : null;
                        }
                        continue;
                    }
                }

                // Handle checkbox arrays
                if (element.type === 'checkbox' && element.name.endsWith('[]')) {
                    const fieldName = element.name.replace('[]', '');
                    if (!contentData[fieldName]) {
                        contentData[fieldName] = [];
                    }
                    if (element.checked) {
                        contentData[fieldName].push(element.value);
                    }
                    continue;
                }
                
                // Handle radio buttons - only save the checked one
                if (element.type === 'radio') {
                    // Only process if this radio is checked
                    if (element.checked) {
                        contentData[element.name] = element.value || '';
                    }
                    // If unchecked, skip it (we'll handle the field name later if needed)
                    continue;
                }

                // Handle tags selector (hidden input contains JSON)
                // Handle structure fields (hidden inputs with JSON arrays)
                if (element.type === 'hidden' && element.name && !element.name.includes('_current') && !element.name.includes('_tags')) {
                    // Check if this is a structure field by checking if wrapper has cms-structure-wrapper class
                    const wrapper = element.closest('.cms-structure-wrapper');
                    if (wrapper) {
                        try {
                            const parsed = JSON.parse(element.value || '[]');
                            if (Array.isArray(parsed)) {
                                contentData[element.name] = parsed;
                                continue;
                            }
                        } catch (e) {
                            // Not valid JSON, treat as regular hidden field
                        }
                    }
                }
                
                const isTagsInput = element.type === 'hidden' && element.id && element.id.endsWith('_tags');
                if (isTagsInput) {
                    try {
                        contentData[element.name] = JSON.parse(element.value || '[]');
                    } catch (e) {
                        console.error('Error parsing tags:', e);
                        contentData[element.name] = [];
                    }
                    continue;
                }

                // Handle regular fields (but skip radio buttons - already handled)
                if (element.name && !element.name.endsWith('[]') && element.type !== 'radio') {
                    // Always include the field, even if empty
                    contentData[element.name] = element.value || '';
                }
            }
            
            // Ensure all radio button groups are included (even if none selected)
            for (let element of this.elements) {
                if (element.type === 'radio' && element.name && !(element.name in contentData)) {
                    contentData[element.name] = '';
                }
            }

            // Handle file uploads first
            const fileFields = [];
            for (let element of this.elements) {
                if (element.type === 'file' && element.files.length > 0) {
                    fileFields.push({
                        name: element.name,
                        file: element.files[0]
                    });
                }
            }

            // Upload files first
            for (let fileField of fileFields) {
                const uploadFormData = new FormData();
                uploadFormData.append('file', fileField.file);
                uploadFormData.append('csrf_token', formData.get('csrf_token'));

                try {
                    const uploadResponse = await fetch('<?php echo CMS_URL; ?>/panel/actions/upload.php', {
                        method: 'POST',
                        body: uploadFormData
                    });

                    if (uploadResponse.ok) {
                        const uploadResult = await uploadResponse.json();
                        if (uploadResult.success) {
                            contentData[fileField.name] = { src: uploadResult.url };
                        }
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                }
            }

            // Save content
            const saveFormData = new FormData();
            saveFormData.append('csrf_token', formData.get('csrf_token'));
            saveFormData.append('content_type', 'test');
            saveFormData.append('content_kind', 'page');
            saveFormData.append('content_data', JSON.stringify(contentData));
            saveFormData.append('filename', 'content.json');

            try {
                const response = await fetch('<?php echo CMS_URL; ?>/panel/actions/save.php', {
                    method: 'POST',
                    body: saveFormData
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Save failed - HTTP error:', response.status, text);
                    dialog.error('Server error: ' + response.status + ' - ' + text, 'Error');
                    return;
                }

                const result = await response.json();

                if (result.success) {
                    dialog.alert('Content saved successfully!', 'Success').then(() => {
                        window.location.reload();
                    });
                } else {
                    console.error('Save failed:', result.error);
                    dialog.error(result.error || 'Failed to save content', 'Error');
                }
            } catch (error) {
                console.error('Save error:', error);
                dialog.error('An error occurred while saving: ' + error.message, 'Error');
            }
        });

        // Tags selector functionality
        document.querySelectorAll('.cms-tags-selector').forEach(selector => {
            const fieldName = selector.getAttribute('data-field');
            const hiddenInput = document.getElementById(fieldName + '_tags');
            const input = selector.querySelector('.cms-tags-input');
            const selectedContainer = selector.querySelector('.cms-tags-selected');
            
            if (!hiddenInput) {
                console.warn('No hidden input found for tags field:', fieldName);
                return;
            }
            
            if (!input) {
                console.warn('No input field found for tags selector:', fieldName);
            }
            
            if (!selectedContainer) {
                console.warn('No selected container found for tags selector:', fieldName);
            }
            
            // Function to get selected values
            const getSelectedValues = () => {
                try {
                    let values = JSON.parse(hiddenInput.value || '[]');
                    // Handle double-encoded values (cleanup)
                    if (Array.isArray(values) && values.length > 0 && typeof values[0] === 'string') {
                        // Check if first item looks like JSON string
                        try {
                            const parsed = JSON.parse(values[0]);
                            if (Array.isArray(parsed)) {
                                // It was double-encoded, use the parsed version
                                values = parsed;
                            }
                        } catch (e) {
                            // Not double-encoded, use as-is
                        }
                    }
                    // Ensure we always return an array
                    return Array.isArray(values) ? values : [];
                } catch (e) {
                    console.error('Error parsing tags value:', e, hiddenInput.value);
                    return [];
                }
            };
            
            // Function to update hidden input
            const updateHiddenInput = (values) => {
                hiddenInput.value = JSON.stringify(values);
            };
            
            // Function to render selected tags
            const renderSelectedTags = () => {
                const values = getSelectedValues();
                if (!selectedContainer) return;
                
                // Get options from buttons
                const options = {};
                selector.querySelectorAll('.cms-tag-button').forEach(btn => {
                    options[btn.getAttribute('data-value')] = btn.getAttribute('data-label');
                });
                
                selectedContainer.innerHTML = '';
                values.forEach(tagValue => {
                    const tagLabel = options[tagValue] || tagValue;
                    const isCustom = !options[tagValue];
                    
                    const tag = document.createElement('span');
                    tag.className = 'cms-tag' + (isCustom ? ' cms-tag-custom' : '');
                    tag.setAttribute('data-value', tagValue);
                    
                    const tagText = document.createElement('span');
                    tagText.className = 'cms-tag-text';
                    tagText.textContent = tagLabel;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'cms-tag-remove';
                    removeBtn.setAttribute('aria-label', 'Remove tag');
                    removeBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.8536 1.85355C12.0488 1.65829 12.0488 1.34171 11.8536 1.14645C11.6583 0.951184 11.3417 0.951184 11.1464 1.14645L7.5 4.79289L3.85355 1.14645C3.65829 0.951184 3.34171 0.951184 3.14645 1.14645C2.95118 1.34171 2.95118 1.65829 3.14645 1.85355L6.79289 5.5L3.14645 9.14645C2.95118 9.34171 2.95118 9.65829 3.14645 9.85355C3.34171 10.0488 3.65829 10.0488 3.85355 9.85355L7.5 6.20711L11.1464 9.85355C11.3417 10.0488 11.6583 10.0488 11.8536 9.85355C12.0488 9.65829 12.0488 9.34171 11.8536 9.14645L7.70711 5L11.8536 1.85355Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>';
                    
                    removeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const values = getSelectedValues();
                        const newValues = values.filter(v => v !== tagValue);
                        updateHiddenInput(newValues);
                        renderSelectedTags();
                        updateTagButtons();
                    });
                    
                    tag.appendChild(tagText);
                    tag.appendChild(removeBtn);
                    selectedContainer.appendChild(tag);
                });
            };
            
            // Function to update tag button states
            const updateTagButtons = () => {
                const values = getSelectedValues();
                selector.querySelectorAll('.cms-tag-button').forEach(button => {
                    const value = button.getAttribute('data-value');
                    if (values.includes(value)) {
                        button.classList.add('selected');
                    } else {
                        button.classList.remove('selected');
                    }
                });
            };
            
            // Add tag function
            const addTag = (tagValue, tagLabel = null) => {
                tagValue = String(tagValue).trim();
                if (!tagValue) {
                    console.warn('Empty tag value, skipping');
                    return;
                }
                
                console.log('addTag called with:', tagValue);
                const values = getSelectedValues();
                console.log('Current values:', values);
                
                if (values.includes(tagValue)) {
                    console.log('Tag already exists:', tagValue);
                    return; // Already added
                }
                
                values.push(tagValue);
                console.log('Updated values:', values);
                updateHiddenInput(values);
                renderSelectedTags();
                updateTagButtons();
            };
            
            // Remove tag function
            const removeTag = (tagValue) => {
                console.log('removeTag called with:', tagValue);
                const values = getSelectedValues();
                console.log('Current values before remove:', values);
                const newValues = values.filter(v => v !== tagValue);
                console.log('New values after remove:', newValues);
                updateHiddenInput(newValues);
                renderSelectedTags();
                updateTagButtons();
            };
            
            // Input field - add tag on Enter or comma
            if (input) {
                // Add tag on Enter
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        const value = this.value.trim();
                        if (value) {
                            console.log('Adding tag:', value);
                            addTag(value);
                            this.value = '';
                        }
                        return false;
                    }
                    if (e.key === ',') {
                        e.preventDefault();
                        e.stopPropagation();
                        const value = this.value.trim();
                        if (value) {
                            console.log('Adding tag via comma:', value);
                            addTag(value);
                            this.value = '';
                        }
                        return false;
                    }
                }, true); // Capture phase - run before form handler
                
                // Also prevent default form submission
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        return false;
                    }
                }, true);
                
                // Add tag on blur
                input.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value) {
                        console.log('Adding tag on blur:', value);
                        addTag(value);
                        this.value = '';
                    }
                });
            }
            
            // Predefined tag buttons
            selector.querySelectorAll('.cms-tag-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const value = this.getAttribute('data-value');
                    const values = getSelectedValues();
                    
                    if (values.includes(value)) {
                        removeTag(value);
                    } else {
                        addTag(value);
                    }
                    return false;
                });
            });
            
            // Remove buttons in selected tags
            selector.addEventListener('click', function(e) {
                if (e.target.closest('.cms-tag-remove')) {
                    const tag = e.target.closest('.cms-tag');
                    if (tag) {
                        const value = tag.getAttribute('data-value');
                        removeTag(value);
                    }
                }
            });
            
            // Initialize - clean up any double-encoded values
            const initialValues = getSelectedValues();
            updateHiddenInput(initialValues); // Re-save clean values
            
            renderSelectedTags();
            updateTagButtons();
        });
        }); // End DOMContentLoaded
        </script>
    <?php else: ?>
        <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
            <div class="cms-card-body">
                <div class="cms-empty-state">
                    <?php echo icon('component-placeholder', 'cms-empty-state-icon'); ?>
                    <p class="cms-text-muted">Test blueprint not found. Create test.blueprint.yml in the /blueprints directory.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require_once PANEL_DIR . '/partials/footer.php'; ?>

