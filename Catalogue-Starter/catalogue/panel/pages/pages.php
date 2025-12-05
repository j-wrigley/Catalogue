<?php
/**
 * Pages Page
 * List and manage content pages
 */
$page = 'pages';
$page_title = 'Pages';

// Get all blueprints and filter for pages (those in content/pages/)
$all_blueprints = getAllBlueprints();
$blueprints = [];
foreach ($all_blueprints as $name => $blueprint) {
    // Skip settings blueprint - it has its own dedicated page in the sidebar
    // Skip 404 page - it's managed separately and auto-generated
    // Skip media blueprint - it has its own dedicated page in the sidebar
    if ($name === 'settings' || $name === '404' || $name === 'media') {
        continue;
    }
    
    $page_dir = PAGES_DIR . '/' . $name;
    $collection_dir = COLLECTIONS_DIR . '/' . $name;
    
    // If it exists in pages dir, it's a page
    // If it exists in collections dir, skip it (it's a collection)
    if (is_dir($page_dir)) {
        $blueprints[$name] = $blueprint;
    } elseif (!is_dir($collection_dir)) {
        // Neither exists - treat as potential page (will be created in pages/)
        $blueprints[$name] = $blueprint;
    }
}

$action = $_GET['action'] ?? 'list';
$content_type = $_GET['type'] ?? '';

// Handle edit action - validate BEFORE outputting headers
if ($action === 'edit' && !empty($content_type)) {
    $blueprint = getBlueprint($content_type);
    if (!$blueprint) {
        header('Location: ' . CMS_URL . '/index.php?page=pages');
        exit;
    }
    
    // Load existing content from pages directory
    $content_file = PAGES_DIR . '/' . $content_type . '/' . $content_type . '.json';
    $content = null;
    $content_meta = null;
    if (file_exists($content_file)) {
        $content_full = readJson($content_file);
        $content_meta = $content_full['_meta'] ?? null;
        $content = $content_full;
        
        // Remove metadata from display
        if (isset($content['_meta'])) {
            unset($content['_meta']);
        }
    }
}

// Now include header after all redirects are handled
require_once PANEL_DIR . '/partials/header.php';
?>
<div class="cms-content">
    <?php if ($action === 'edit' && !empty($content_type)): ?>
        <!-- Core Fields Cards (Featured and Status) -->
        <?php echo generateCoreFieldsCards($content, 'page', $content_type, $action, $content_meta); ?>
        
        <!-- Edit Form -->
        <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
            <div class="cms-card-body">
                <?php echo generateFormFromBlueprint($blueprint, $content); ?>
            </div>
        </div>
        
        <script>
        console.log('Script loaded! Waiting for DOM...');
        document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded fired!');
        const form = document.getElementById('content-form');
        
        if (!form) {
            console.error('Form not found! Form ID: content-form');
            console.error('Available forms:', document.querySelectorAll('form'));
            return;
        }
        
        console.log('Form found:', form);
        console.log('Form ID:', form.id);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        console.log('Attaching handlers...');
        
        // Debug: Check for submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        console.log('Submit button found:', submitBtn);
        
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                console.log('Submit button clicked!');
                console.log('Event:', e);
                console.log('Form:', form);
                // Don't prevent default - let form submit naturally
            });
        } else {
            console.error('Submit button not found!');
        }
        
        // Also try direct click handler on form as fallback
        form.addEventListener('click', function(e) {
            if (e.target.type === 'submit' || e.target.closest('button[type="submit"]')) {
                console.log('Click detected on submit button or form');
            }
        });
        
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
        
        // Attach submit handler with capture phase and higher priority
        console.log('Attaching submit handler...');
        form.addEventListener('submit', async function(e) {
            console.log('Form submit handler attached and triggered!');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('Form submit event triggered!');
            console.log('Event target:', e.target);
            console.log('Form:', this);
            
            const formData = new FormData(this);
            const contentData = {};
            
            console.log('Processing form elements...');
            
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
                            const uploadResponse = await fetch('<?php echo CMS_URL; ?>/panel/actions/upload.php', {
                                method: 'POST',
                                body: uploadFormData
                            });
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
                
                // Handle structure fields (hidden inputs with JSON arrays)
                if (element.type === 'hidden' && element.name && !element.name.includes('_current') && !element.name.includes('_tags')) {
                    // Check if this is a structure field by checking if wrapper has cms-structure-wrapper class
                    const wrapper = element.closest('.cms-structure-wrapper');
                    if (wrapper) {
                        try {
                            const parsed = JSON.parse(element.value || '[]');
                            if (Array.isArray(parsed)) {
                                contentData[element.name] = parsed;
                                console.log('Structure field processed:', element.name, parsed);
                                continue;
                            }
                        } catch (e) {
                            console.error('Error parsing structure field:', element.name, e);
                            // Not valid JSON, treat as empty array
                            contentData[element.name] = [];
                            continue;
                        }
                    }
                    
                    // Check if this is a multiple file field
                    const fileUpload = element.closest('.cms-file-upload');
                    if (fileUpload && fileUpload.getAttribute('data-multiple') === 'true') {
                        try {
                            const parsed = JSON.parse(element.value || '[]');
                            if (Array.isArray(parsed)) {
                                contentData[element.name] = parsed;
                                console.log('Multiple file field processed:', element.name, parsed);
                                continue;
                            }
                        } catch (e) {
                            console.error('Error parsing multiple file field:', element.name, e);
                            contentData[element.name] = [];
                            continue;
                        }
                    }
                }
                
                // Handle tags selector (hidden input contains JSON)
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
                
                // Handle dropdowns (hidden inputs)
                if (element.type === 'hidden' && element.closest('.cms-dropdown')) {
                    contentData[element.name] = element.value || '';
                    continue;
                }
                
                // Handle switches (hidden inputs)
                if (element.type === 'hidden' && element.closest('.cms-switch-wrapper')) {
                    contentData[element.name] = element.value || '';
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
            
            // Handle core fields (_featured and _status)
            const featuredInput = this.querySelector('input[name="_featured"]');
            if (featuredInput) {
                contentData['_featured'] = featuredInput.value || '0';
            }
            
            const statusInput = this.querySelector('input[name="_status"]');
            if (statusInput) {
                contentData['_status'] = statusInput.value || 'draft';
            }
            
            // Save content
            const saveFormData = new FormData();
            saveFormData.append('csrf_token', formData.get('csrf_token'));
            saveFormData.append('content_type', '<?php echo esc_js($content_type); ?>');
            saveFormData.append('content_kind', 'page');
            saveFormData.append('content_data', JSON.stringify(contentData));
            saveFormData.append('filename', '<?php echo esc_js($content_type); ?>.json');
            
            console.log('Saving content:', contentData);
            
            try {
                const response = await fetch('<?php echo CMS_URL; ?>/panel/actions/save.php', {
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
                
                const result = await response.json();
                
                console.log('Save response:', result);
                
                if (result.success) {
                    toast.success('Content saved successfully!');
                    // Redirect back to pages table after a short delay
                    setTimeout(function() {
                        window.location.href = '<?php echo CMS_URL; ?>/index.php?page=pages';
                    }, 500);
                } else {
                    console.error('Save failed:', result.error);
                    toast.error(result.error || 'Failed to save content');
                }
            } catch (error) {
                console.error('Save error:', error);
                toast.error('An error occurred while saving: ' + error.message);
            }
        });
        
        // Initialize tags functionality
        initTagsInContainer(document);
        }); // End DOMContentLoaded
        
        function removeFile(fieldName) {
            const preview = document.querySelector(`[name="${fieldName}"]`).closest('.cms-file-upload').querySelector('.cms-file-preview');
            const fileInput = document.getElementById(fieldName);
            const currentInput = document.querySelector(`[name="${fieldName}_current"]`);
            
            if (preview) preview.remove();
            if (fileInput) fileInput.value = '';
            if (currentInput) currentInput.value = '';
        }
        </script>
    <?php else: ?>
        <!-- List View -->
        <?php if (empty($blueprints)): ?>
            <div class="cms-card cms-card-size-4" style="grid-column: 1 / -1;">
                <div class="cms-card-body">
                    <div class="cms-empty-state">
                        <?php echo icon('component-placeholder', 'cms-empty-state-icon'); ?>
                        <p class="cms-text-muted">No content types found. Create blueprints in the /blueprints directory.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="cms-table-wrapper">
                <table class="cms-table" id="pages-table">
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
                        // Prepare data for sorting
                        $pages_data = [];
                        foreach ($blueprints as $name => $blueprint): 
                            // Load page content to get featured and status
                            $content_file = PAGES_DIR . '/' . $name . '/' . $name . '.json';
                            $page_content = null;
                            if (file_exists($content_file)) {
                                $page_content = readJson($content_file);
                            }
                            
                            $featured = isset($page_content['_featured']) ? $page_content['_featured'] : (isset($page_content['featured']) ? $page_content['featured'] : false);
                            $status = isset($page_content['_status']) ? $page_content['_status'] : (isset($page_content['status']) ? $page_content['status'] : 'draft');
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
                            
                            $updated_time = 0;
                            if ($page_content && isset($page_content['_meta']['updated'])) {
                                $updated_time = strtotime($page_content['_meta']['updated']);
                            }
                            
                            $created_time = 0;
                            if ($page_content && isset($page_content['_meta']['created'])) {
                                $created_time = strtotime($page_content['_meta']['created']);
                            }
                            
                            $pages_data[] = [
                                'name' => $name,
                                'blueprint' => $blueprint,
                                'isFeatured' => $isFeatured,
                                'status' => $status,
                                'statusClass' => $statusClass,
                                'statusLabel' => $statusLabel,
                                'page_content' => $page_content,
                                'updated_time' => $updated_time,
                                'created_time' => $created_time
                            ];
                        endforeach;
                        ?>
                        <?php foreach ($pages_data as $page_data): 
                            $name = $page_data['name'];
                            $blueprint = $page_data['blueprint'];
                            $isFeatured = $page_data['isFeatured'];
                            $status = $page_data['status'];
                            $statusClass = $page_data['statusClass'];
                            $statusLabel = $page_data['statusLabel'];
                            $page_content = $page_data['page_content'];
                        ?>
                            <tr>
                                <td data-sort-value="<?php echo esc_attr(strtolower($blueprint['title'] ?? ucfirst($name))); ?>">
                                    <?php 
                                    $display_title = $blueprint['title'] ?? ucfirst($name);
                                    $truncated_title = mb_strlen($display_title) > 40 ? mb_substr($display_title, 0, 40) . '...' : $display_title;
                                    ?>
                                    <strong><?php echo esc($truncated_title); ?></strong>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: var(--space-1);">
                                        <?php echo esc($name); ?>
                                    </div>
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
                                <td data-sort-value="<?php echo esc_attr($page_data['updated_time']); ?>">
                                    <?php 
                                    if ($page_content && isset($page_content['_meta']['updated'])) {
                                        echo esc(formatDate($page_content['_meta']['updated']));
                                    } else {
                                        echo '<span class="cms-text-muted">Never</span>';
                                    }
                                    ?>
                                </td>
                                <td data-sort-value="<?php echo esc_attr($page_data['created_time']); ?>">
                                    <?php 
                                    if ($page_content && isset($page_content['_meta']['created'])) {
                                        echo esc(formatDate($page_content['_meta']['created']));
                                    } else {
                                        echo '<span class="cms-text-muted">Never</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="cms-table-actions">
                                        <a href="<?php echo CMS_URL; ?>/index.php?page=pages&action=edit&type=<?php echo esc_attr($name); ?>" class="cms-button cms-button-ghost cms-button-sm" title="Edit">
                                            <?php echo icon('pencil-1', 'cms-icon'); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once PANEL_DIR . '/partials/footer.php'; ?>

