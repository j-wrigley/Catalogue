<?php
/**
 * Collections Page
 * Manage collection items (like blog posts)
 */
$page = 'collections';
$page_title = 'Collections';

$blueprints = getAllBlueprints();
$action = $_GET['action'] ?? 'list';
$collection_type = $_GET['type'] ?? '';
$item_id = $_GET['item'] ?? '';

// Get collections (blueprints that exist in content/collections/)
// Also include directories in collections/ even if no blueprint exists yet
$all_blueprints = getAllBlueprints();
$collections = [];

// First, add collections that have blueprints
foreach ($all_blueprints as $name => $blueprint) {
    // Skip media blueprint - it has its own dedicated page in the sidebar
    if ($name === 'media') {
        continue;
    }
    
    $collection_dir = COLLECTIONS_DIR . '/' . $name;
    $page_dir = PAGES_DIR . '/' . $name;
    
    // If it exists in collections dir, it's a collection
    // If it exists in pages dir, skip it (it's a page)
    if (is_dir($collection_dir)) {
        $collections[$name] = $blueprint;
    } elseif (!is_dir($page_dir)) {
        // Neither exists - treat as potential collection (will be created in collections/)
        $collections[$name] = $blueprint;
    }
}

// Also check for directories in collections/ that don't have blueprints
if (is_dir(COLLECTIONS_DIR)) {
    $collection_dirs = glob(COLLECTIONS_DIR . '/*', GLOB_ONLYDIR);
    foreach ($collection_dirs as $collection_dir) {
        $name = basename($collection_dir);
        // Skip media directory - it has its own dedicated page
        if ($name === 'media') {
            continue;
        }
        // Only add if not already in collections (from blueprints)
        if (!isset($collections[$name])) {
            // Create a basic blueprint structure for display
            $collections[$name] = [
                'title' => ucfirst($name),
                'fields' => []
            ];
        }
    }
}

// Handle item edit/create - validate BEFORE outputting headers
if (($action === 'edit' || $action === 'create') && !empty($collection_type)) {
    $blueprint = getBlueprint($collection_type);
    if (!$blueprint) {
        // If no blueprint exists, check if directory exists
        $collection_dir = COLLECTIONS_DIR . '/' . $collection_type;
        if (!is_dir($collection_dir)) {
            header('Location: ' . CMS_URL . '/index.php?page=collections');
            exit;
        }
        // Create a basic blueprint for collections without blueprints
        $blueprint = [
            'title' => ucfirst($collection_type),
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'required' => true,
                    'label' => 'Title'
                ],
                'description' => [
                    'type' => 'textarea',
                    'label' => 'Description'
                ]
            ]
        ];
    }
    
    $content = null;
    $content_meta = null;
    if ($action === 'edit' && !empty($item_id)) {
        // Load existing item from collections directory
        $content_file = COLLECTIONS_DIR . '/' . $collection_type . '/' . sanitizeFilename($item_id) . '.json';
        if (file_exists($content_file)) {
            $content_full = readJson($content_file);
            $content_meta = $content_full['_meta'] ?? null;
            $content = $content_full;
            
            // Ensure _slug is set - extract from filename if missing
            if (empty($content['_slug'])) {
                $filename_base = preg_replace('/\.json$/', '', basename($content_file));
                $slug_base = preg_replace('/-\d+$/', '', $filename_base);
                if (!empty($slug_base)) {
                    $content['_slug'] = $slug_base;
                }
            }
            
            // Remove metadata from display
            if (isset($content['_meta'])) {
                unset($content['_meta']);
            }
        }
    }
}

// Handle items list - validate BEFORE outputting headers
if ($action === 'items' && !empty($collection_type)) {
    $blueprint = getBlueprint($collection_type);
    $collection_dir = COLLECTIONS_DIR . '/' . $collection_type;
    
    // If no blueprint exists, check if directory exists
    if (!$blueprint && !is_dir($collection_dir)) {
        header('Location: ' . CMS_URL . '/index.php?page=collections');
        exit;
    }
    
    // Create a basic blueprint if none exists
    if (!$blueprint) {
        $blueprint = [
            'title' => ucfirst($collection_type),
            'fields' => []
        ];
    }
}

// Now include header after all redirects are handled
require_once PANEL_DIR . '/partials/header.php';
?>
<div class="cms-content">
    <?php if ($action === 'edit' || $action === 'create'): ?>
        <!-- Core Fields Cards (Featured and Status) -->
        <?php echo generateCoreFieldsCards($content, 'collection', $collection_type, $action, $content_meta, $item_id ?? ''); ?>
        
        <!-- Edit/Create Form -->
        <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
            <div class="cms-card-body">
                <?php echo generateFormFromBlueprint($blueprint, $content); ?>
            </div>
        </div>
        
        <script>
        // Base URL for API calls (defined once to avoid escaping issues)
        const CMS_BASE_URL = <?php echo json_encode(CMS_URL); ?>;
        
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
            
            // Extract form fields - iterate over all form elements to ensure we get all fields
            for (let element of this.elements) {
                if (!element.name || element.name === 'csrf_token' || element.name.includes('_current')) {
                    continue;
                }
                
                // Handle file fields
                if (element.type === 'file') {
                    const file = element.files[0];
                    if (file) {
                        // Upload file first
                        const uploadFormData = new FormData();
                        uploadFormData.append('csrf_token', formData.get('csrf_token'));
                        uploadFormData.append('file', file);
                        
                        try {
                            const uploadResponse = await fetch(CMS_BASE_URL + '/panel/actions/upload.php', {
                                method: 'POST',
                                body: uploadFormData
                            });
                            
                            // Check content type before parsing JSON
                            const contentType = uploadResponse.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await uploadResponse.text();
                                console.error('Upload failed - Invalid content type:', contentType, text.substring(0, 200));
                                throw new Error('Server returned invalid response (not JSON). Check console for details.');
                            }
                            
                            const uploadResult = await uploadResponse.json();
                            
                            if (uploadResult.success) {
                                contentData[element.name] = {
                                    src: uploadResult.url,
                                    alt: element.name.replace('_', ' ')
                                };
                            } else {
                                dialog.error(uploadResult.error || 'Failed to upload file', 'Upload Error');
                                return;
                            }
                        } catch (error) {
                            console.error('Upload error:', error);
                            dialog.error('Failed to upload file', 'Upload Error');
                            return;
                        }
                    } else {
                        // Use current file if exists
                        const currentInput = this.querySelector(`[name="${element.name}_current"]`);
                        if (currentInput && currentInput.value) {
                            contentData[element.name] = {
                                src: currentInput.value,
                                alt: element.name.replace('_', ' ')
                            };
                        }
                    }
                    continue;
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
                    
                    // Check if this is a multiple file field
                    const fileUpload = element.closest('.cms-file-upload');
                    if (fileUpload && fileUpload.getAttribute('data-multiple') === 'true') {
                        try {
                            const parsed = JSON.parse(element.value || '[]');
                            if (Array.isArray(parsed)) {
                                contentData[element.name] = parsed;
                                continue;
                            }
                        } catch (e) {
                            console.error('Error parsing multiple file field:', element.name, e);
                            contentData[element.name] = [];
                            continue;
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
                
                // Handle markdown editor (hidden textarea)
                if (element.classList && element.classList.contains('cms-markdown-textarea-hidden')) {
                    contentData[element.name] = element.value || '';
                    continue;
                }
                
                // Always include the field, even if empty (but skip radio buttons - already handled)
                if (element.name && !element.name.endsWith('[]') && element.type !== 'radio') {
                    contentData[element.name] = element.value || '';
                }
            }
            
            // Ensure all radio button groups are included (even if none selected)
            for (let element of this.elements) {
                if (element.type === 'radio' && element.name && !(element.name in contentData)) {
                    contentData[element.name] = '';
                }
            }
            
            // Handle core fields (_featured and _status)
            const featuredInput = this.querySelector('input[name="_featured"]');
            if (featuredInput) {
                contentData['_featured'] = featuredInput.value || '0';
            }
            
            const statusInput = this.querySelector('input[name="_status"]');
            if (statusInput) {
                contentData['_status'] = statusInput.value || 'draft';
            }
            
            // Handle slug for collections - slug is always sent, even if empty
            // PHP will preserve existing slug if empty when editing
            // Note: slug input is outside the form (has form="content-form" attribute)
            const slugInput = document.querySelector('input[name="_slug"]');
            const itemId = <?php echo $action === "edit" && !empty($item_id) ? json_encode($item_id) : '""'; ?>;
            const isEditing = !!itemId;
            
            console.log('CMS Debug - itemId check:', {
                action: <?php echo json_encode($action ?? ""); ?>,
                item_id: <?php echo json_encode($item_id ?? ""); ?>,
                itemId: itemId,
                isEditing: isEditing
            });
            
            let slug = '';
            if (slugInput && slugInput.value) {
                slug = slugInput.value.trim();
            }
            
            console.log('CMS Debug - Slug capture:', {
                slugInput: slugInput ? 'found' : 'not found',
                slugInputValue: slugInput ? slugInput.value : 'N/A',
                slug: slug,
                isEditing: isEditing
            });
            
            // Only generate slug from title if it's a NEW item (not editing) and slug is empty
            if (!slug && !isEditing && contentData.title) {
                slug = contentData.title.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
                if (!slug) {
                    slug = 'item';
                }
                // Update the input field with generated slug
                if (slugInput) {
                    slugInput.value = slug;
                }
            }
            
            // Always send slug (even if empty - PHP will handle preserving existing)
            contentData['_slug'] = slug;
            
            // Generate filename for collection items
            let filename;
            let originalFilename = '';
            if (itemId) {
                // Editing existing item - use the item ID (without .json extension)
                filename = itemId.replace(/\.json$/, '');
                originalFilename = filename + '.json';
                console.log('CMS Debug - Editing mode:', {
                    itemId: itemId,
                    filename: filename,
                    originalFilename: originalFilename
                });
            } else {
                // New item - filename will be generated by PHP from slug + timestamp
                filename = '';
                console.log('CMS Debug - Creating new item');
            }
            
            // Always send both, even if empty (so PHP knows it's a new item)
            console.log('CMS Save Debug:', {
                itemId: itemId,
                filename: filename,
                originalFilename: originalFilename,
                slug: slug,
                contentData: contentData
            });
            
            // Save content
            const saveFormData = new FormData();
            saveFormData.append('csrf_token', formData.get('csrf_token'));
            saveFormData.append('content_type', <?php echo json_encode($collection_type); ?>);
            saveFormData.append('content_kind', 'collection');
            saveFormData.append('content_data', JSON.stringify(contentData));
            
            // CRITICAL: Always send filename and original_filename - this tells PHP if we're editing
            saveFormData.append('filename', filename);
            saveFormData.append('original_filename', originalFilename || ''); // Always send, even if empty
            
            // Debug: Log what we're sending
            console.log('CMS Save - Sending FormData:', {
                filename: filename,
                originalFilename: originalFilename,
                hasOriginalFilename: !!originalFilename
            });
            
            // Verify FormData contents
            for (let [key, value] of saveFormData.entries()) {
                console.log('CMS Save - FormData:', key, '=', value);
            }
            
            try {
                const response = await fetch(CMS_BASE_URL + '/panel/actions/save.php', {
                    method: 'POST',
                    body: saveFormData
                });
                
                // Check if response is ok
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Save failed - HTTP error:', response.status, text);
                    toast.error('Server error: ' + response.status);
                    return;
                }
                
                // Check content type before parsing JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Save failed - Invalid content type:', contentType, text.substring(0, 500));
                    toast.error('Server returned invalid response. Check console for details.');
                    return;
                }
                
                const result = await response.json();
                
                console.log('Save response:', result);
                
                if (result.success) {
                    toast.success('Item saved successfully!');
                    // Redirect back to collections table after a short delay
                    setTimeout(function() {
                        window.location.href = CMS_BASE_URL + '/index.php?page=collections&action=items&type=<?php echo esc_js($collection_type); ?>';
                    }, 500);
                } else {
                    console.error('Save failed:', result.error);
                    toast.error(result.error || 'Failed to save item');
                }
            } catch (error) {
                console.error('Save error:', error);
                toast.error('An error occurred while saving: ' + error.message);
            }
        });
        
        function removeFile(fieldName) {
            const preview = document.querySelector(`[name="${fieldName}"]`).closest('.cms-file-upload').querySelector('.cms-file-preview');
            const fileInput = document.getElementById(fieldName);
            const currentInput = document.querySelector(`[name="${fieldName}_current"]`);
            
            if (preview) preview.remove();
            if (fileInput) fileInput.value = '';
            if (currentInput) currentInput.value = '';
        }
        
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
            
            // Function to get selected values
            const getSelectedValues = () => {
                try {
                    let values = JSON.parse(hiddenInput.value || '[]');
                    // Handle double-encoded values (cleanup)
                    if (Array.isArray(values) && values.length > 0 && typeof values[0] === 'string') {
                        try {
                            const parsed = JSON.parse(values[0]);
                            if (Array.isArray(parsed)) {
                                values = parsed;
                            }
                        } catch (e) {
                            // Not double-encoded, use as-is
                        }
                    }
                    return Array.isArray(values) ? values : [];
                } catch (e) {
                    console.error('Error parsing tags value:', e, hiddenInput.value);
                    // Try to fix corrupted data
                    try {
                        hiddenInput.value = '[]';
                        return [];
                    } catch (e2) {
                        return [];
                    }
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
                        e.preventDefault();
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
            const addTag = (tagValue) => {
                tagValue = String(tagValue).trim();
                if (!tagValue) return;
                
                const values = getSelectedValues();
                if (values.includes(tagValue)) return;
                
                values.push(tagValue);
                updateHiddenInput(values);
                renderSelectedTags();
                updateTagButtons();
            };
            
            // Remove tag function
            const removeTag = (tagValue) => {
                const values = getSelectedValues();
                const newValues = values.filter(v => v !== tagValue);
                updateHiddenInput(newValues);
                renderSelectedTags();
                updateTagButtons();
            };
            
            // Input field - add tag on Enter or comma
            if (input) {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        const value = this.value.trim();
                        if (value) {
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
                            addTag(value);
                            this.value = '';
                        }
                        return false;
                    }
                }, true);
                
                input.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value) {
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
                    e.preventDefault();
                    e.stopPropagation();
                    const tag = e.target.closest('.cms-tag');
                    if (tag) {
                        const value = tag.getAttribute('data-value');
                        removeTag(value);
                    }
                }
            });
            
            // Initialize - clean up any corrupted values
            const initialValues = getSelectedValues();
            updateHiddenInput(initialValues);
            renderSelectedTags();
            updateTagButtons();
        });
        }); // End DOMContentLoaded
        </script>
    <?php elseif ($action === 'items' && !empty($collection_type)): ?>
        <!-- Collection Items List with Sidebar -->
        <?php
        // Get all items in this collection from collections directory
        $items_dir = COLLECTIONS_DIR . '/' . $collection_type;
        $items = [];
        if (is_dir($items_dir)) {
            $files = listJsonFiles($items_dir);
            foreach ($files as $file) {
                $item = readJson($file);
                if ($item) {
                    $item['_file'] = basename($file, '.json');
                    $items[] = $item;
                }
            }
        }
        
        // Sort by updated date (newest first)
        usort($items, function($a, $b) {
            $a_time = isset($a['_meta']['updated']) ? strtotime($a['_meta']['updated']) : 0;
            $b_time = isset($b['_meta']['updated']) ? strtotime($b['_meta']['updated']) : 0;
            return $b_time - $a_time;
        });
        ?>
        
        <div class="cms-collections-page">
            <!-- Sidebar: Collections -->
            <aside class="cms-collections-sidebar">
                <div class="cms-collections-sidebar-header">
                    <h2 class="cms-collections-sidebar-title">Collections</h2>
                </div>
                <nav class="cms-collections-sidebar-nav">
                    <?php foreach ($collections as $name => $blueprint): 
                        // Count items in collection
                        $items_dir_check = COLLECTIONS_DIR . '/' . $name;
                        $item_count = 0;
                        if (is_dir($items_dir_check)) {
                            $item_count = count(listJsonFiles($items_dir_check));
                        }
                        $is_active = ($name === $collection_type);
                    ?>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=collections&action=items&type=<?php echo esc_attr($name); ?>" class="cms-collections-sidebar-item <?php echo $is_active ? 'active' : ''; ?>">
                            <span class="cms-collections-sidebar-icon"><?php echo icon('layers', 'cms-icon'); ?></span>
                            <span class="cms-collections-sidebar-label"><?php echo esc($blueprint['title'] ?? ucfirst($name)); ?></span>
                            <span class="cms-collections-sidebar-count"><?php echo $item_count; ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </aside>
            
            <!-- Main Content: Items -->
            <main class="cms-collections-main">
                <!-- Toolbar -->
                <div class="cms-collections-toolbar">
                    <div class="cms-collections-toolbar-actions">
                        <a href="<?php echo CMS_URL; ?>/index.php?page=collections&action=create&type=<?php echo esc_attr($collection_type); ?>" class="cms-button cms-button-outline">
                            <?php echo icon('plus', 'cms-icon'); ?>
                            <span>New Item</span>
                        </a>
                    </div>
                </div>
                
                <!-- Items List -->
        <?php if (empty($items)): ?>
                    <div class="cms-collections-empty">
                    <div class="cms-empty-state">
                        <?php echo icon('component-placeholder', 'cms-empty-state-icon'); ?>
                        <h3 class="cms-empty-state-title">No items yet</h3>
                        <p class="cms-empty-state-description">Create your first item to get started.</p>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=collections&action=create&type=<?php echo esc_attr($collection_type); ?>" class="cms-button cms-button-primary" style="margin-top: var(--space-4);">
                            <?php echo icon('plus', 'cms-icon'); ?>
                            <span>Create First Item</span>
                        </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Hidden form for CSRF token (used by delete function) -->
            <form id="csrf-form" style="display: none;">
                <input type="hidden" name="csrf_token" value="<?php echo esc_attr(generateCsrfToken()); ?>">
            </form>
            
                    <div class="cms-collections-content">
            <div class="cms-table-wrapper">
                <table class="cms-table" id="collections-table">
                    <thead>
                        <tr>
                            <th class="cms-table-sortable" data-sort="title" data-sort-type="text">
                                <span>Title</span>
                                <span class="cms-sort-indicator"></span>
                            </th>
                            <th class="cms-table-sortable" data-sort="featured" data-sort-type="boolean">
                                <span>Featured</span>
                                <span class="cms-sort-indicator"></span>
                            </th>
                            <th class="cms-table-sortable" data-sort="status" data-sort-type="text">
                                <span>Status</span>
                                <span class="cms-sort-indicator"></span>
                            </th>
                            <th class="cms-table-sortable" data-sort="updated" data-sort-type="date">
                                <span>Updated</span>
                                <span class="cms-sort-indicator"></span>
                            </th>
                            <th class="cms-table-sortable" data-sort="created" data-sort-type="date">
                                <span>Created</span>
                                <span class="cms-sort-indicator"></span>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                                    <?php 
                                    $row_index = 0;
                                    foreach ($items as $item): 
                            $featured = isset($item['_featured']) ? $item['_featured'] : (isset($item['featured']) ? $item['featured'] : false);
                            $status = isset($item['_status']) ? $item['_status'] : (isset($item['status']) ? $item['status'] : 'draft');
                            $isFeatured = ($featured === true || $featured === '1' || $featured === 'true' || $featured === 'on');
                            
                            // Status badge colors
                            $statusClass = '';
                            $statusLabel = ucfirst($status);
                            if ($status === 'published') {
                                $statusClass = 'cms-badge-success';
                            } elseif ($status === 'draft') {
                                $statusClass = 'cms-badge-secondary';
                            } elseif ($status === 'unlisted') {
                                $statusClass = 'cms-badge-info';
                            }
                            
                            $updated_time = isset($item['_meta']['updated']) ? strtotime($item['_meta']['updated']) : 0;
                            $created_time = isset($item['_meta']['created']) ? strtotime($item['_meta']['created']) : 0;
                        ?>
                                        <tr class="cms-table-row" data-row-index="<?php echo esc_attr($row_index++); ?>">
                                <td data-sort-value="<?php echo esc_attr(strtolower($item['title'] ?? $item['_file'] ?? 'untitled')); ?>">
                                    <?php 
                                    $display_title = $item['title'] ?? $item['_file'] ?? 'Untitled';
                                    $truncated_title = mb_strlen($display_title) > 40 ? mb_substr($display_title, 0, 40) . '...' : $display_title;
                                    ?>
                                    <strong><?php echo esc($truncated_title); ?></strong>
                                </td>
                                <td data-sort-value="<?php echo $isFeatured ? '1' : '0'; ?>">
                                    <?php if ($isFeatured): ?>
                                        <span class="cms-badge cms-badge-success">Featured</span>
                                    <?php else: ?>
                                        <span class="cms-text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td data-sort-value="<?php echo esc_attr(strtolower($status)); ?>">
                                    <span class="cms-badge <?php echo esc_attr($statusClass); ?>"><?php echo esc($statusLabel); ?></span>
                                </td>
                                <td data-sort-value="<?php echo esc_attr($updated_time); ?>">
                                    <?php echo esc(formatDate($item['_meta']['updated'] ?? null)); ?>
                                </td>
                                <td data-sort-value="<?php echo esc_attr($created_time); ?>">
                                    <?php 
                                    if (isset($item['_meta']['created'])) {
                                        echo esc(formatDate($item['_meta']['created']));
                                    } else {
                                        echo '<span class="cms-text-muted">Never</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="cms-table-actions">
                                        <a href="<?php echo CMS_URL; ?>/index.php?page=collections&action=edit&type=<?php echo esc_attr($collection_type); ?>&item=<?php echo esc_attr($item['_file']); ?>" class="cms-button cms-button-ghost cms-button-sm" title="Edit">
                                            <?php echo icon('pencil-1', 'cms-icon'); ?>
                                        </a>
                                        <button class="cms-button cms-button-ghost cms-button-sm cms-button-danger" data-delete-item data-collection-type="<?php echo esc_attr($collection_type); ?>" data-item-id="<?php echo esc_attr($item['_file']); ?>" title="Delete">
                                            <?php echo icon('trash', 'cms-icon'); ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div class="cms-table-pagination">
                            <div class="cms-pagination-info">
                                <span class="cms-pagination-text">Items per page:</span>
                                <select class="cms-pagination-select" id="items-per-page" aria-label="Items per page">
                                    <option value="1">1</option>
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="cms-pagination-controls">
                                <button type="button" class="cms-button cms-button-ghost cms-button-sm" id="pagination-prev" disabled>
                                    <?php echo icon('chevron-left', 'cms-icon'); ?>
                                    <span>Previous</span>
                                </button>
                                <div class="cms-pagination-pages" id="pagination-pages"></div>
                                <button type="button" class="cms-button cms-button-ghost cms-button-sm" id="pagination-next">
                                    <span>Next</span>
                                    <?php echo icon('chevron-right', 'cms-icon'); ?>
                                </button>
                            </div>
                            <div class="cms-pagination-status" id="pagination-status"></div>
                        </div>
            </div>
        <?php endif; ?>
            </main>
        </div>
        
        <script>
        // Delete button handlers using data attributes (more secure than inline onclick)
        const CMS_BASE_URL = <?php echo json_encode(CMS_URL); ?>;
        
        // Table Pagination
        (function() {
            const table = document.getElementById('collections-table');
            if (!table) return;
            
            const tbody = table.querySelector('tbody');
            
            let currentPage = 1;
            let itemsPerPage = parseInt(localStorage.getItem('cms-collections-items-per-page') || '10', 10);
            
            const itemsPerPageSelect = document.getElementById('items-per-page');
            const prevButton = document.getElementById('pagination-prev');
            const nextButton = document.getElementById('pagination-next');
            const pagesContainer = document.getElementById('pagination-pages');
            const statusContainer = document.getElementById('pagination-status');
            
            // Set initial items per page
            if (itemsPerPageSelect) {
                itemsPerPageSelect.value = itemsPerPage;
            }
            
            function getRows() {
                return Array.from(tbody.querySelectorAll('tr.cms-table-row'));
            }
            
            function updatePagination() {
                // Re-get rows to handle sorting
                const rows = getRows();
                const totalRows = rows.length;
                const totalPages = Math.ceil(totalRows / itemsPerPage);
                
                // Update status
                if (statusContainer) {
                    const start = totalRows === 0 ? 0 : ((currentPage - 1) * itemsPerPage) + 1;
                    const end = Math.min(currentPage * itemsPerPage, totalRows);
                    statusContainer.textContent = `Showing ${start}-${end} of ${totalRows}`;
                }
                
                // Show/hide rows
                rows.forEach((row, index) => {
                    const rowPage = Math.floor(index / itemsPerPage) + 1;
                    if (rowPage === currentPage) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update prev/next buttons
                if (prevButton) {
                    prevButton.disabled = currentPage === 1;
                }
                if (nextButton) {
                    nextButton.disabled = currentPage >= totalPages || totalPages === 0;
                }
                
                // Update page numbers
                if (pagesContainer) {
                    pagesContainer.innerHTML = '';
                    
                    if (totalPages <= 7) {
                        // Show all pages if 7 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            const pageButton = document.createElement('button');
                            pageButton.className = `cms-button cms-button-ghost cms-button-sm cms-pagination-page ${i === currentPage ? 'active' : ''}`;
                            pageButton.textContent = i;
                            pageButton.addEventListener('click', () => {
                                currentPage = i;
                                updatePagination();
                            });
                            pagesContainer.appendChild(pageButton);
                        }
                    } else {
                        // Show first, last, current, and ellipsis
                        const pageButton = (num, active = false) => {
                            const btn = document.createElement('button');
                            btn.className = `cms-button cms-button-ghost cms-button-sm cms-pagination-page ${active ? 'active' : ''}`;
                            btn.textContent = num;
                            btn.addEventListener('click', () => {
                                currentPage = num;
                                updatePagination();
                            });
                            return btn;
                        };
                        
                        // First page
                        pagesContainer.appendChild(pageButton(1, currentPage === 1));
                        
                        if (currentPage > 3) {
                            const ellipsis = document.createElement('span');
                            ellipsis.className = 'cms-pagination-ellipsis';
                            ellipsis.textContent = '…';
                            pagesContainer.appendChild(ellipsis);
                        }
                        
                        // Pages around current
                        const start = Math.max(2, currentPage - 1);
                        const end = Math.min(totalPages - 1, currentPage + 1);
                        
                        for (let i = start; i <= end; i++) {
                            pagesContainer.appendChild(pageButton(i, i === currentPage));
                        }
                        
                        if (currentPage < totalPages - 2) {
                            const ellipsis = document.createElement('span');
                            ellipsis.className = 'cms-pagination-ellipsis';
                            ellipsis.textContent = '…';
                            pagesContainer.appendChild(ellipsis);
                        }
                        
                        // Last page
                        pagesContainer.appendChild(pageButton(totalPages, currentPage === totalPages));
                    }
                }
            }
            
            // Items per page change
            if (itemsPerPageSelect) {
                itemsPerPageSelect.addEventListener('change', function() {
                    itemsPerPage = parseInt(this.value, 10);
                    localStorage.setItem('cms-collections-items-per-page', itemsPerPage);
                    currentPage = 1; // Reset to first page
                    updatePagination();
                });
            }
            
            // Prev/Next buttons
            if (prevButton) {
                prevButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const rows = getRows();
                    const totalPages = Math.ceil(rows.length / itemsPerPage);
                    if (currentPage > 1 && !this.disabled) {
                        currentPage--;
                        updatePagination();
                    }
                });
            }
            
            if (nextButton) {
                nextButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const rows = getRows();
                    const totalPages = Math.ceil(rows.length / itemsPerPage);
                    if (currentPage < totalPages && !this.disabled) {
                        currentPage++;
                        updatePagination();
                    }
                });
            }
            
            // Initial pagination
            updatePagination();
            
            // Re-run pagination after sorting
            // Hook into the existing sort functionality by observing DOM changes
            const observer = new MutationObserver(() => {
                // Debounce to avoid excessive updates
                clearTimeout(observer.timeout);
                observer.timeout = setTimeout(() => {
                    updatePagination();
                }, 100);
            });
            observer.observe(tbody, { childList: true, subtree: false });
            
            // Also listen for clicks on sortable headers to update pagination
            table.querySelectorAll('.cms-table-sortable').forEach(header => {
                header.addEventListener('click', () => {
                    setTimeout(() => {
                        currentPage = 1; // Reset to first page after sort
                        updatePagination();
                    }, 50);
                });
            });
        })();
        
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-delete-item]').forEach(button => {
                button.addEventListener('click', function() {
                    const collectionType = this.getAttribute('data-collection-type');
                    const itemId = this.getAttribute('data-item-id');
                    
                    // Get CSRF token from any form on the page (or generate a new one)
                    let csrfToken = '';
                    const csrfInput = document.querySelector('input[name="csrf_token"]');
                    if (csrfInput) {
                        csrfToken = csrfInput.value;
                    } else {
                        // Fallback: use the token from PHP (generated on page load)
                        csrfToken = '<?php echo esc_js(generateCsrfToken()); ?>';
                    }
                    
                    deleteItem(collectionType, itemId, csrfToken);
                });
            });
        });
        
        function deleteItem(collectionType, itemId, csrfToken) {
            dialog.confirm(`Are you sure you want to delete this item? This action cannot be undone.`, 'Delete Item')
                .then(confirmed => {
                    if (!confirmed) return;
                    
                    const formData = new FormData();
                    formData.append('csrf_token', csrfToken);
                    formData.append('collection_type', collectionType);
                    formData.append('item_id', itemId);
                    
                    fetch(CMS_BASE_URL + '/panel/actions/item-delete.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(async response => {
                        // Check content type before parsing JSON
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await response.text();
                            console.error('Delete failed - Invalid content type:', contentType, text.substring(0, 200));
                            throw new Error('Server returned invalid response (not JSON). Check console for details.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            dialog.error(data.error || 'Failed to delete item', 'Error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        dialog.error('An error occurred while deleting the item: ' + error.message, 'Error');
                    });
                });
        }
        </script>
    <?php else: ?>
        <!-- Collections List View with Sidebar -->
        <div class="cms-collections-page">
            <!-- Sidebar: Collections -->
            <aside class="cms-collections-sidebar">
                <div class="cms-collections-sidebar-header">
                    <h2 class="cms-collections-sidebar-title">Collections</h2>
                </div>
                <nav class="cms-collections-sidebar-nav">
        <?php if (empty($collections)): ?>
                        <div class="cms-collections-sidebar-empty">
                            <p class="cms-text-muted">No collections found.</p>
            </div>
        <?php else: ?>
                <?php foreach ($collections as $name => $blueprint): 
                    // Count items in collection
                            $items_dir_check = COLLECTIONS_DIR . '/' . $name;
                    $item_count = 0;
                            if (is_dir($items_dir_check)) {
                                $item_count = count(listJsonFiles($items_dir_check));
                    }
                ?>
                            <a href="<?php echo CMS_URL; ?>/index.php?page=collections&action=items&type=<?php echo esc_attr($name); ?>" class="cms-collections-sidebar-item">
                                <span class="cms-collections-sidebar-icon"><?php echo icon('layers', 'cms-icon'); ?></span>
                                <span class="cms-collections-sidebar-label"><?php echo esc($blueprint['title'] ?? ucfirst($name)); ?></span>
                                <span class="cms-collections-sidebar-count"><?php echo $item_count; ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </nav>
            </aside>
            
            <!-- Main Content: Empty State or Instructions -->
            <main class="cms-collections-main">
                <div class="cms-collections-empty">
                    <div class="cms-empty-state">
                        <?php echo icon('layers', 'cms-empty-state-icon'); ?>
                        <h3 class="cms-empty-state-title"><?php echo empty($collections) ? 'No collections found' : 'Select a collection'; ?></h3>
                        <p class="cms-empty-state-description">
                            <?php if (empty($collections)): ?>
                                Create a blueprint file and add items to create a collection.
                            <?php else: ?>
                                Choose a collection from the sidebar to view and manage its items.
                            <?php endif; ?>
                            </p>
                        </div>
                        </div>
            </main>
            </div>
    <?php endif; ?>
</div>
<?php require_once PANEL_DIR . '/partials/footer.php'; ?>

