<?php
/**
 * Media Page
 * Manage uploaded media files and folders
 */
$page = 'media';
$page_title = 'Media';
require_once PANEL_DIR . '/partials/header.php';

// Get current folder from query string
$current_folder = $_GET['folder'] ?? '';
$current_folder = preg_replace('/[^a-z0-9_\/-]/i', '', $current_folder); // Sanitize
$current_folder = trim($current_folder, '/');

// Build full path to current folder
$media_path = UPLOADS_DIR;
if (!empty($current_folder)) {
    $media_path .= '/' . $current_folder;
    // Security: Ensure path is within uploads directory
    $real_media_path = realpath($media_path);
    $real_uploads_dir = realpath(UPLOADS_DIR);
    if (!$real_media_path || strpos($real_media_path, $real_uploads_dir) !== 0) {
        $current_folder = '';
        $media_path = UPLOADS_DIR;
    }
}

// Function to get all folders recursively for sidebar
function getAllFolders($dir, $base_dir, $prefix = '') {
    $folders = [];
    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item[0] === '.') continue;
            if ($item === 'index.php' || $item === '.htaccess' || $item === '.gitkeep') continue;
            
            $item_path = $dir . '/' . $item;
            if (is_dir($item_path)) {
                $relative_path = ltrim(str_replace($base_dir, '', $item_path), '/');
                $folders[] = [
                    'name' => $item,
                    'path' => $relative_path,
                    'display' => $prefix . $item,
                    'level' => substr_count($prefix, ' / ')
                ];
                // Recursively get subfolders
                $subfolders = getAllFolders($item_path, $base_dir, $prefix . $item . ' / ');
                $folders = array_merge($folders, $subfolders);
            }
        }
    }
    return $folders;
}

// Function to recursively get all files from directory and subdirectories
function getAllFilesRecursive($dir, $base_dir = null) {
    if ($base_dir === null) {
        $base_dir = $dir;
    }
    
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item[0] === '.') continue;
        if ($item === 'index.php' || $item === '.htaccess' || $item === '.gitkeep') continue;
        
        $item_path = $dir . '/' . $item;
        $relative_path = ltrim(str_replace($base_dir, '', $item_path), '/');
        
        if (is_dir($item_path)) {
            // Recursively get files from subdirectory
            $subfiles = getAllFilesRecursive($item_path, $base_dir);
            $files = array_merge($files, $subfiles);
        } elseif (is_file($item_path)) {
            $file_info = [
                'name' => $item,
                'size' => filesize($item_path),
                'modified' => filemtime($item_path),
                'url' => CMS_URL . '/uploads/' . $relative_path,
                'type' => mime_content_type($item_path),
                'folder' => dirname($relative_path) !== '.' ? dirname($relative_path) : ''
            ];
            
            // Check if it's an image
            if (strpos($file_info['type'], 'image/') === 0) {
                $file_info['is_image'] = true;
                $image_info = @getimagesize($item_path);
                if ($image_info) {
                    $file_info['width'] = $image_info[0];
                    $file_info['height'] = $image_info[1];
                }
            }
            
            $files[] = $file_info;
        }
    }
    
    return $files;
}

// Get all folders for sidebar
$all_folders = getAllFolders(UPLOADS_DIR, UPLOADS_DIR, '');

// Get all files and folders in current directory
$items = [];
$folders = [];
$files = [];

// If "All Media" (empty folder), get all files recursively
if (empty($current_folder)) {
    $files = getAllFilesRecursive(UPLOADS_DIR);
} else {
    // Get files from specific folder only
    if (is_dir($media_path)) {
        $dir_items = scandir($media_path);
        foreach ($dir_items as $item) {
            // Skip hidden/system files
            if ($item === '.' || $item === '..' || $item[0] === '.') {
                continue;
            }
            
            // Skip system files
            if ($item === 'index.php' || $item === '.htaccess' || $item === '.gitkeep') {
                continue;
            }
            
            $item_path = $media_path . '/' . $item;
            if (is_dir($item_path)) {
                $folders[] = [
                    'name' => $item,
                    'path' => $current_folder ? $current_folder . '/' . $item : $item,
                    'modified' => filemtime($item_path)
                ];
            } elseif (is_file($item_path)) {
                $file_info = [
                    'name' => $item,
                    'size' => filesize($item_path),
                    'modified' => filemtime($item_path),
                    'url' => CMS_URL . '/uploads/' . ($current_folder ? $current_folder . '/' : '') . $item,
                    'type' => mime_content_type($item_path)
                ];
                
                // Check if it's an image
                if (strpos($file_info['type'], 'image/') === 0) {
                    $file_info['is_image'] = true;
                    $image_info = @getimagesize($item_path);
                    if ($image_info) {
                        $file_info['width'] = $image_info[0];
                        $file_info['height'] = $image_info[1];
                    }
                }
                
                $files[] = $file_info;
            }
        }
    }
}

// Sort folders alphabetically
usort($folders, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Sort files by modified date (newest first)
usort($files, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

$items = array_merge($folders, $files);

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}

// Format date
function formatMediaDate($timestamp) {
    $date = new DateTime('@' . $timestamp);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days === 0) {
        return 'Today at ' . $date->format('g:i A');
    } elseif ($diff->days === 1) {
        return 'Yesterday at ' . $date->format('g:i A');
    } elseif ($diff->days < 7) {
        return $date->format('l') . ' at ' . $date->format('g:i A');
    } else {
        return $date->format('M j, Y');
    }
}
?>
<div class="cms-media-page">
    <!-- Sidebar: Folders -->
    <aside class="cms-media-sidebar">
        <div class="cms-media-sidebar-header">
            <h2 class="cms-media-sidebar-title">Folders</h2>
            <button type="button" class="cms-button cms-button-ghost cms-button-sm" onclick="openFolderModal()" title="New Folder">
                <?php echo icon('plus', 'cms-icon'); ?>
            </button>
        </div>
        <nav class="cms-media-sidebar-nav">
            <a href="<?php echo CMS_URL; ?>/index.php?page=media" class="cms-media-sidebar-item <?php echo empty($current_folder) ? 'active' : ''; ?>">
                <span class="cms-media-sidebar-icon"><?php echo icon('archive', 'cms-icon'); ?></span>
                <span class="cms-media-sidebar-label">All Media</span>
            </a>
            <?php foreach ($all_folders as $folder): ?>
                <a href="<?php echo CMS_URL; ?>/index.php?page=media&folder=<?php echo urlencode($folder['path']); ?>" class="cms-media-sidebar-item <?php echo $current_folder === $folder['path'] ? 'active' : ''; ?>" style="padding-left: calc(var(--space-4) + <?php echo $folder['level']; ?> * var(--space-5));">
                    <span class="cms-media-sidebar-icon"><?php echo icon('archive', 'cms-icon'); ?></span>
                    <span class="cms-media-sidebar-label"><?php echo esc($folder['name']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    
    <!-- Main Content: Files -->
    <main class="cms-media-main">
        <!-- Hidden CSRF token for AJAX requests -->
        <input type="hidden" name="csrf_token" id="media-csrf-token" value="<?php echo generateCsrfToken(); ?>" />
        
        <!-- Toolbar -->
        <div class="cms-media-toolbar">
            <nav class="cms-media-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo CMS_URL; ?>/index.php?page=media" class="cms-media-breadcrumb-link">Media</a>
                <?php if (!empty($current_folder)): ?>
                    <?php
                    $folder_parts = explode('/', $current_folder);
                    $breadcrumb_path = '';
                    foreach ($folder_parts as $part) {
                        $breadcrumb_path .= ($breadcrumb_path ? '/' : '') . $part;
                    ?>
                        <span class="cms-media-breadcrumb-separator">/</span>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=media&folder=<?php echo urlencode($breadcrumb_path); ?>" class="cms-media-breadcrumb-link"><?php echo esc($part); ?></a>
                    <?php } ?>
                <?php endif; ?>
            </nav>
            
            <div class="cms-media-toolbar-actions">
                <div class="cms-media-view-toggle">
                    <button type="button" class="cms-view-toggle-btn active" data-view="grid" title="Grid View">
                        <?php echo icon('view-grid', 'cms-icon'); ?>
                    </button>
                    <button type="button" class="cms-view-toggle-btn" data-view="list" title="List View">
                        <?php echo icon('view-vertical', 'cms-icon'); ?>
                    </button>
                </div>
                <button type="button" class="cms-button cms-button-outline" onclick="openUploadModal()">
                    <?php echo icon('upload', 'cms-icon'); ?>
                    <span>Upload</span>
                </button>
            </div>
        </div>
        
        <!-- Files Grid -->
        <?php if (empty($files)): ?>
            <div class="cms-media-empty">
                <div class="cms-empty-state">
                    <?php echo icon('image', 'cms-empty-state-icon'); ?>
                    <h3 class="cms-empty-state-title">No files</h3>
                    <p class="cms-empty-state-description">Upload files to get started.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="cms-media-grid-wrapper" id="media-grid-wrapper">
                <?php foreach ($files as $item): ?>
                    <?php 
                    // Determine the actual folder for this file
                    // Use the file's folder property if available (from getAllFilesRecursive), otherwise use current_folder
                    $file_folder = isset($item['folder']) ? $item['folder'] : $current_folder;
                    ?>
                    <div class="cms-media-card cms-media-file-card" data-context-menu="file" data-name="<?php echo esc_attr($item['name']); ?>" data-folder="<?php echo esc_attr($file_folder); ?>" data-url="<?php echo esc_attr($item['url']); ?>">
                        <div class="cms-media-card-preview">
                            <?php if (isset($item['is_image']) && $item['is_image']): ?>
                                <img src="<?php echo esc_url($item['url']); ?>" 
                                     alt="<?php echo esc_attr($item['name']); ?>" 
                                     loading="lazy" 
                                     decoding="async"
                                     <?php if (isset($item['width']) && isset($item['height'])): ?>
                                     width="<?php echo esc_attr($item['width']); ?>"
                                     height="<?php echo esc_attr($item['height']); ?>"
                                     <?php endif; ?>
                                     class="cms-media-card-image" />
                            <?php else: ?>
                                <div class="cms-media-file-icon">
                                    <?php echo icon('file', 'cms-icon'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="cms-media-card-info">
                            <div class="cms-media-card-name" title="<?php echo esc_attr($item['name']); ?>"><?php echo esc($item['name']); ?></div>
                            <div class="cms-media-card-meta">
                                <span class="cms-media-card-size"><?php echo formatFileSize($item['size']); ?></span>
                                <?php if (isset($item['width']) && isset($item['height'])): ?>
                                    <span class="cms-media-card-dimensions"><?php echo esc($item['width']); ?> × <?php echo esc($item['height']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="cms-media-card-date"><?php echo formatMediaDate($item['modified']); ?></div>
                        </div>
                        <div class="cms-media-card-actions">
                            <button type="button" class="cms-button cms-button-ghost cms-button-sm" data-action="edit-metadata" data-name="<?php echo esc_attr($item['name']); ?>" data-folder="<?php echo esc_attr($file_folder); ?>" data-url="<?php echo esc_attr($item['url']); ?>" title="Edit Metadata">
                                <?php echo icon('pencil-1', 'cms-icon'); ?>
                            </button>
                            <button type="button" class="cms-button cms-button-ghost cms-button-sm" data-action="copy-url" data-url="<?php echo esc_attr($item['url']); ?>" title="Copy URL">
                                <?php echo icon('copy', 'cms-icon'); ?>
                            </button>
                            <button type="button" class="cms-button cms-button-ghost cms-button-sm" data-action="delete-file" data-name="<?php echo esc_attr($item['name']); ?>" data-folder="<?php echo esc_attr($file_folder); ?>" title="Delete file">
                                <?php echo icon('trash', 'cms-icon'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Context Menu -->
<div id="cms-context-menu" class="cms-context-menu" style="display: none;" role="menu" aria-orientation="vertical">
    <div class="cms-context-menu-content" id="cms-context-menu-content">
        <!-- Menu items will be populated dynamically -->
    </div>
</div>

<!-- Move to Folder Modal -->
<div id="move-modal" class="cms-modal" style="display: none;">
    <div class="cms-modal-backdrop" onclick="closeMoveModal()"></div>
    <div class="cms-modal-content">
        <div class="cms-modal-header">
            <h3 class="cms-modal-title">Move to Folder</h3>
            <button type="button" class="cms-modal-close" onclick="closeMoveModal()" aria-label="Close">×</button>
        </div>
        <div class="cms-modal-body">
            <div class="cms-form-group">
                <label class="cms-label">Select Destination</label>
                <div class="cms-folder-list" id="move-folder-list">
                    <!-- Folder list will be populated dynamically -->
                </div>
            </div>
        </div>
        <div class="cms-modal-footer">
            <button type="button" class="cms-button cms-button-ghost" onclick="closeMoveModal()">Cancel</button>
            <button type="button" class="cms-button cms-button-primary" id="move-confirm-btn" disabled>Move</button>
        </div>
    </div>
</div>

<!-- Metadata Edit Modal -->
<div id="metadata-modal" class="cms-modal cms-structure-modal" style="display: none;">
    <div class="cms-modal-backdrop" onclick="closeMetadataModal()"></div>
    <div class="cms-modal-content cms-structure-modal-content">
        <div class="cms-modal-header">
            <h3 class="cms-modal-title" id="metadata-modal-title">Edit Metadata</h3>
            <button type="button" class="cms-modal-close" onclick="closeMetadataModal()" aria-label="Close">×</button>
        </div>
        <form id="metadata-form" class="cms-structure-item-form">
            <div class="cms-modal-body">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>" />
                <input type="hidden" name="action" value="save_metadata" />
                <input type="hidden" name="file_path" id="metadata-file-path" />
                <div id="metadata-form-content">
                    <!-- Form fields will be populated dynamically -->
                </div>
            </div>
            <div class="cms-modal-footer">
                <button type="button" class="cms-button cms-button-ghost" onclick="closeMetadataModal()">Cancel</button>
                <button type="submit" class="cms-button cms-button-primary">Save Metadata</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload Modal -->
<div class="cms-modal" id="upload-modal" style="display: none;">
    <div class="cms-modal-backdrop" onclick="closeUploadModal()"></div>
    <div class="cms-modal-content">
        <div class="cms-modal-header">
            <h3 class="cms-modal-title">Upload Files</h3>
            <button type="button" class="cms-modal-close" onclick="closeUploadModal()" aria-label="Close">×</button>
        </div>
        <form id="upload-form" enctype="multipart/form-data">
            <div class="cms-modal-body">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>" />
                <input type="hidden" name="folder" value="<?php echo esc_attr($current_folder); ?>" />
                <div class="cms-form-group">
                    <label for="upload-files" class="cms-label">Select Files</label>
                    <input type="file" id="upload-files" name="files[]" multiple accept="image/*,application/pdf,application/zip" class="cms-input" required />
                    <p class="cms-text-muted" style="margin-top: var(--space-2); font-size: var(--font-size-sm);">You can select multiple files. Supported: images, PDFs, and ZIP files.</p>
                </div>
                <div id="upload-progress" style="display: none; margin-top: var(--space-4);">
                    <div class="cms-progress-bar">
                        <div class="cms-progress-fill" id="upload-progress-fill" style="width: 0%;"></div>
                    </div>
                    <p class="cms-text-muted" id="upload-status" style="margin-top: var(--space-2); font-size: var(--font-size-sm);"></p>
                </div>
            </div>
            <div class="cms-modal-footer">
                <button type="button" class="cms-button cms-button-ghost" onclick="closeUploadModal()">Cancel</button>
                <button type="submit" class="cms-button cms-button-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Folder Modal -->
<div class="cms-modal" id="folder-modal" style="display: none;">
    <div class="cms-modal-backdrop" onclick="closeFolderModal()"></div>
    <div class="cms-modal-content">
        <div class="cms-modal-header">
            <h3 class="cms-modal-title">Create Folder</h3>
            <button type="button" class="cms-modal-close" onclick="closeFolderModal()" aria-label="Close">×</button>
        </div>
        <form id="folder-form">
            <div class="cms-modal-body">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>" />
                <input type="hidden" name="folder" value="<?php echo esc_attr($current_folder); ?>" />
                <div class="cms-form-group">
                    <label for="folder-name" class="cms-label">Folder Name</label>
                    <input type="text" id="folder-name" name="folder_name" class="cms-input" pattern="[a-z0-9_-]+" placeholder="my-folder" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '').replace(/\s+/g, '-')" required />
                    <p class="cms-text-muted" style="margin-top: var(--space-2); font-size: var(--font-size-sm);">Use lowercase letters, numbers, hyphens, and underscores only.</p>
                </div>
            </div>
            <div class="cms-modal-footer">
                <button type="button" class="cms-button cms-button-ghost" onclick="closeFolderModal()">Cancel</button>
                <button type="submit" class="cms-button cms-button-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
// Base URL for API calls
const CMS_BASE_URL = <?php echo json_encode(CMS_URL); ?>;
const CURRENT_FOLDER = <?php echo json_encode($current_folder); ?>;

// Context Menu State
let contextMenuTarget = null;
let contextMenuType = null;

// Event delegation for media card actions
document.addEventListener('DOMContentLoaded', function() {
    // Right-click context menu
    document.addEventListener('contextmenu', function(e) {
        const card = e.target.closest('[data-context-menu]');
        if (card) {
            e.preventDefault();
            contextMenuTarget = card;
            contextMenuType = card.getAttribute('data-context-menu');
            showContextMenu(e.clientX, e.clientY, contextMenuType, card);
        }
    });
    
    // Close context menu on click outside
    document.addEventListener('click', function(e) {
        const contextMenu = document.getElementById('cms-context-menu');
        if (contextMenu && contextMenu.style.display !== 'none' && !contextMenu.contains(e.target)) {
            hideContextMenu();
        }
    });
    
    // Close context menu on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideContextMenu();
        }
    });
    
    // Handle context menu actions
    document.addEventListener('click', function(e) {
        const item = e.target.closest('[data-context-action]');
        if (item) {
            e.preventDefault();
            e.stopPropagation();
            const action = item.getAttribute('data-context-action');
            handleContextAction(action);
        }
    });
    
    // Handle delete folder
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="delete-folder"]');
        if (btn) {
            e.preventDefault();
            const name = btn.getAttribute('data-name');
            const path = btn.getAttribute('data-path');
            deleteFolder(name, path);
        }
    });
    
    // Handle edit metadata
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="edit-metadata"]');
        if (btn) {
            e.preventDefault();
            const fileName = btn.getAttribute('data-name');
            const folder = btn.getAttribute('data-folder') || '';
            const filePath = folder ? folder + '/' + fileName : fileName;
            openMetadataModal(filePath, fileName);
        }
    });
    
    // Handle delete file
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="delete-file"]');
        if (btn) {
            e.preventDefault();
            const name = btn.getAttribute('data-name');
            const folder = btn.getAttribute('data-folder');
            deleteFile(name, folder);
        }
    });
    
    // Handle copy URL
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="copy-url"]');
        if (btn) {
            e.preventDefault();
            const url = btn.getAttribute('data-url');
            copyUrl(url);
        }
    });
    
    // Handle move confirmation
    document.getElementById('move-confirm-btn')?.addEventListener('click', function() {
        const selectedFolder = document.querySelector('.cms-folder-list-item.selected');
        if (selectedFolder) {
            const destination = selectedFolder.getAttribute('data-folder-path') || '';
            moveItem(destination);
        }
    });
});

function showContextMenu(x, y, type, card) {
    const menu = document.getElementById('cms-context-menu');
    const content = document.getElementById('cms-context-menu-content');
    if (!menu || !content) return;
    
    // Build menu items based on type
    let menuItems = [];
    
    if (type === 'file') {
        menuItems = [
            { action: 'edit-metadata', label: 'Edit Metadata', icon: 'pencil-1' },
            { separator: true },
            { action: 'copy-url', label: 'Copy URL', icon: 'copy' },
            { separator: true },
            { action: 'move', label: 'Move to folder', icon: 'move' },
            { separator: true },
            { action: 'delete', label: 'Delete', icon: 'trash', danger: true }
        ];
    } else if (type === 'folder') {
        menuItems = [
            { action: 'move', label: 'Move to folder', icon: 'move' },
            { separator: true },
            { action: 'delete', label: 'Delete', icon: 'trash', danger: true }
        ];
    }
    
    // Render menu items
    content.innerHTML = '';
    menuItems.forEach(item => {
        if (item.separator) {
            const separator = document.createElement('div');
            separator.className = 'cms-context-menu-separator';
            content.appendChild(separator);
        } else {
            const menuItem = document.createElement('div');
            menuItem.className = 'cms-context-menu-item' + (item.danger ? ' cms-context-menu-item-danger' : '');
            menuItem.setAttribute('role', 'menuitem');
            menuItem.setAttribute('tabindex', '0');
            menuItem.setAttribute('data-context-action', item.action);
            
            const icon = document.createElement('span');
            icon.className = 'cms-context-menu-icon';
            icon.innerHTML = getContextMenuIcon(item.icon);
            
            const label = document.createElement('span');
            label.className = 'cms-context-menu-label';
            label.textContent = item.label;
            
            menuItem.appendChild(icon);
            menuItem.appendChild(label);
            content.appendChild(menuItem);
        }
    });
    
    // Position menu
    menu.style.display = 'block';
    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
    
    // Adjust if menu goes off screen
    setTimeout(() => {
        const rect = menu.getBoundingClientRect();
        if (rect.right > window.innerWidth) {
            menu.style.left = (x - rect.width) + 'px';
        }
        if (rect.bottom > window.innerHeight) {
            menu.style.top = (y - rect.height) + 'px';
        }
    }, 0);
}

function hideContextMenu() {
    const menu = document.getElementById('cms-context-menu');
    if (menu) {
        menu.style.display = 'none';
    }
}

function getContextMenuIcon(iconName) {
    const icons = {
        'pencil-1': <?php echo json_encode(file_get_contents(PANEL_DIR . '/assets/icons/pencil-1.svg')); ?>,
        'edit': <?php echo json_encode(file_get_contents(PANEL_DIR . '/assets/icons/pencil-1.svg')); ?>, // Alias for backward compatibility
        'copy': '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 9.50006C1 10.3285 1.67157 11.0001 2.5 11.0001H4L4 10.0001H2.5C2.22386 10.0001 2 9.7762 2 9.50006L2 2.50006C2 2.22392 2.22386 2.00006 2.5 2.00006L9.5 2.00006C9.77614 2.00006 10 2.22392 10 2.50006V4.00002H11V2.50006C11 1.67163 10.3284 1.00006 9.5 1.00006H2.5C1.67157 1.00006 1 1.67163 1 2.50006L1 9.50006ZM5 4.50006C5 3.67163 5.67157 3.00006 6.5 3.00006H12.5C13.3284 3.00006 14 3.67163 14 4.50006V12.5C14 13.3285 13.3284 14 12.5 14H6.5C5.67157 14 5 13.3285 5 12.5V4.50006ZM6.5 4.00006C6.22386 4.00006 6 4.22392 6 4.50006V12.5C6 12.7762 6.22386 13 6.5 13H12.5C12.7761 13 13 12.7762 13 12.5V4.50006C13 4.22392 12.7761 4.00006 12.5 4.00006H6.5Z" fill="currentColor"/></svg>',
        'move': '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.85355 2.14645C7.65829 1.95118 7.34171 1.95118 7.14645 2.14645L4.14645 5.14645C3.95118 5.34171 3.95118 5.65829 4.14645 5.85355C4.34171 6.04882 4.65829 6.04882 4.85355 5.85355L7 3.70711V9.5C7 9.77614 7.22386 10 7.5 10C7.77614 10 8 9.77614 8 9.5V3.70711L10.1464 5.85355C10.3417 6.04882 10.6583 6.04882 10.8536 5.85355C11.0488 5.65829 11.0488 5.34171 10.8536 5.14645L7.85355 2.14645ZM2.5 9C2.77614 9 3 9.22386 3 9.5V12.5C3 12.7761 3.22386 13 3.5 13H11.5C11.7761 13 12 12.7761 12 12.5V9.5C12 9.22386 12.2239 9 12.5 9C12.7761 9 13 9.22386 13 9.5V12.5C13 13.3284 12.3284 14 11.5 14H3.5C2.67157 14 2 13.3284 2 12.5V9.5C2 9.22386 2.22386 9 2.5 9Z" fill="currentColor"/></svg>',
        'trash': '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 3C11.7761 3 12 3.22386 12 3.5C12 3.77614 11.7761 4 11.5 4H11V12L10.9951 12.1025C10.9472 12.573 10.573 12.9472 10.1025 12.9951L10 13H5L4.89746 12.9951C4.42703 12.9472 4.05278 12.573 4.00488 12.1025L4 12V4H3.5C3.22386 4 3 3.77614 3 3.5C3 3.22386 3.22386 3 3.5 3H11.5ZM5 12H10V4H5V12ZM9.5 1C9.77614 1 10 1.22386 10 1.5C10 1.77614 9.77614 2 9.5 2H5.5C5.22386 2 5 1.77614 5 1.5C5 1.22386 5.22386 1 5.5 1H9.5Z" fill="currentColor"/></svg>'
    };
    return icons[iconName] || '';
}

function handleContextAction(action) {
    if (!contextMenuTarget) return;
    
    const targetType = contextMenuTarget.getAttribute('data-context-menu');
    const targetName = contextMenuTarget.getAttribute('data-name');
    const targetPath = contextMenuTarget.getAttribute('data-folder') || contextMenuTarget.getAttribute('data-path') || '';
    const targetUrl = contextMenuTarget.getAttribute('data-url') || '';
    
    hideContextMenu();
    
    if (action === 'copy-url') {
        if (targetUrl) {
            copyUrl(targetUrl);
        }
        contextMenuTarget = null;
        contextMenuType = null;
    } else if (action === 'edit-metadata') {
        const fileName = targetName;
        const folder = targetPath || '';
        const filePath = folder ? folder + '/' + fileName : fileName;
        openMetadataModal(filePath, fileName);
        contextMenuTarget = null;
        contextMenuType = null;
    } else if (action === 'move') {
        // Store target info in modal
        const modal = document.getElementById('move-modal');
        if (modal) {
            modal.setAttribute('data-move-type', targetType);
            modal.setAttribute('data-move-name', targetName);
            modal.setAttribute('data-move-path', targetPath);
            modal.setAttribute('data-current-folder', CURRENT_FOLDER);
        }
        contextMenuTarget = null;
        contextMenuType = null;
        openMoveModal();
    } else if (action === 'delete') {
        if (contextMenuType === 'file') {
            deleteFile(targetName, targetPath);
        } else if (contextMenuType === 'folder') {
            deleteFolder(targetName, targetPath);
        }
        contextMenuTarget = null;
        contextMenuType = null;
    }
}

function openMoveModal() {
    const modal = document.getElementById('move-modal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    
    // Load folder list
    loadFolderList();
}

function closeMoveModal() {
    const modal = document.getElementById('move-modal');
    if (modal) {
        modal.style.display = 'none';
        const list = document.getElementById('move-folder-list');
        if (list) list.innerHTML = '';
        const confirmBtn = document.getElementById('move-confirm-btn');
        if (confirmBtn) confirmBtn.disabled = true;
    }
}

// Metadata Modal Functions
function openMetadataModal(filePath, fileName) {
    const modal = document.getElementById('metadata-modal');
    const formContent = document.getElementById('metadata-form-content');
    const filePathInput = document.getElementById('metadata-file-path');
    const title = document.getElementById('metadata-modal-title');
    
    if (!modal || !formContent || !filePathInput) return;
    
    filePathInput.value = filePath;
    
    if (title) {
        title.textContent = 'Edit Metadata: ' + fileName;
    }
    
    formContent.innerHTML = '<p class="cms-text-muted">Loading...</p>';
    modal.style.display = 'flex';
    
    // Load metadata first, then form
    fetch('<?php echo CMS_URL; ?>/panel/actions/media.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'get_metadata',
            file_path: filePath,
            csrf_token: '<?php echo generateCsrfToken(); ?>'
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            loadMediaBlueprintForm(data.metadata || {});
        } else {
            formContent.innerHTML = '<p class="cms-text-error">' + (data.error || 'Failed to load metadata') + '</p>';
        }
    })
    .catch(error => {
        console.error('Error loading metadata:', error);
        formContent.innerHTML = '<p class="cms-text-error">Error loading metadata: ' + error.message + '</p>';
    });
}

function loadMediaBlueprintForm(metadata) {
    const formContent = document.getElementById('metadata-form-content');
    if (!formContent) return;
    
    // Load form HTML from server (uses proper form generation)
    fetch('<?php echo CMS_URL; ?>/panel/actions/media.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'get_metadata_form',
            file_path: document.getElementById('metadata-file-path').value,
            csrf_token: '<?php echo generateCsrfToken(); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.form) {
            // Clear hidden inputs that are already in the form
            const existingHidden = formContent.querySelectorAll('input[type="hidden"]');
            existingHidden.forEach(input => {
                if (input.name !== 'csrf_token' && input.name !== 'action' && input.name !== 'file_path') {
                    input.remove();
                }
            });
            
            formContent.innerHTML = data.form;
            
            // Populate fields with existing metadata
            Object.keys(metadata).forEach(key => {
                if (key === 'file_path') return; // Skip file_path
                
                // Check if this is a tags field (has a tags selector)
                const tagsSelector = formContent.querySelector(`.cms-tags-selector[data-field="${key}"]`);
                if (tagsSelector) {
                    // Handle tags field - set hidden input value
                    const hiddenInput = tagsSelector.querySelector('input[id$="_tags"]') || tagsSelector.querySelector('input[type="hidden"][name="' + key + '"]');
                    if (hiddenInput) {
                        const tagsArray = Array.isArray(metadata[key]) ? metadata[key] : (metadata[key] ? [metadata[key]] : []);
                        hiddenInput.value = JSON.stringify(tagsArray);
                    }
                } else {
                    // Regular field
                    const input = formContent.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = metadata[key] === true || metadata[key] === '1';
                        } else if (input.tagName === 'TEXTAREA') {
                            input.value = metadata[key] || '';
                        } else if (input.type === 'hidden' && input.closest('.cms-dropdown')) {
                            // Dropdowns are handled by their hidden input
                            input.value = metadata[key] || '';
                        } else {
                            input.value = metadata[key] || '';
                        }
                    }
                }
            });
            
            // Initialize custom components AFTER setting values
            if (typeof initDropdowns !== 'undefined') {
                initDropdowns();
            }
            if (typeof initSwitches !== 'undefined') {
                initSwitches();
            }
            // Initialize tags using the reusable function (this will read from hidden input and render tags)
            if (typeof initTagsInContainer !== 'undefined') {
                initTagsInContainer(formContent);
            }
        } else {
            formContent.innerHTML = '<p class="cms-text-error">Failed to load form</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        formContent.innerHTML = '<p class="cms-text-error">Error loading form</p>';
    });
}

function closeMetadataModal() {
    const modal = document.getElementById('metadata-modal');
    if (modal) {
        modal.style.display = 'none';
        const formContent = document.getElementById('metadata-form-content');
        if (formContent) {
            formContent.innerHTML = '';
        }
    }
}

function loadFolderList() {
    const list = document.getElementById('move-folder-list');
    if (!list) return;
    
    const modal = document.getElementById('move-modal');
    if (!modal) return;
    
    const currentFolder = modal.getAttribute('data-current-folder') || '';
    let movePath = modal.getAttribute('data-move-path') || '';
    const moveType = modal.getAttribute('data-move-type') || '';
    
    // Remove leading slash from movePath if present (for folders)
    if (movePath.startsWith('/')) {
        movePath = movePath.substring(1);
    }
    
    list.innerHTML = '<div class="cms-folder-list-loading">Loading folders...</div>';
    
    // Fetch all folders
    const formData = new FormData();
    const csrfToken = document.getElementById('media-csrf-token')?.value || document.querySelector('input[name="csrf_token"]')?.value || '';
    if (!csrfToken) {
        toast.error('CSRF token not found. Please refresh the page.');
        return;
    }
    formData.append('action', 'list_folders');
    formData.append('csrf_token', csrfToken);
    formData.append('exclude_current', currentFolder);
    formData.append('exclude_item', moveType === 'folder' ? movePath : '');
    
    fetch(CMS_BASE_URL + '/panel/actions/media.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.folders) {
            renderFolderList(data.folders);
        } else {
            list.innerHTML = '<div class="cms-folder-list-empty">No folders available</div>';
        }
    })
    .catch(error => {
        console.error('Error loading folders:', error);
        list.innerHTML = '<div class="cms-folder-list-error">Error loading folders</div>';
    });
}

function renderFolderList(folders) {
    const list = document.getElementById('move-folder-list');
    if (!list) return;
    
    const modal = document.getElementById('move-modal');
    if (!modal) return;
    
    const currentFolder = modal.getAttribute('data-current-folder') || '';
    
    list.innerHTML = '';
    
    // Only add root option if not already in root
    if (currentFolder !== '') {
        const rootItem = document.createElement('div');
        rootItem.className = 'cms-folder-list-item';
        rootItem.setAttribute('data-folder-path', '');
        rootItem.innerHTML = '<span class="cms-folder-list-icon">' + getFolderIcon() + '</span><span class="cms-folder-list-name">Root (Media)</span>';
        rootItem.addEventListener('click', function() {
            selectFolderItem('');
        });
        list.appendChild(rootItem);
    }
    
    // Add folders
    folders.forEach(folder => {
        const item = document.createElement('div');
        item.className = 'cms-folder-list-item';
        item.setAttribute('data-folder-path', folder.path);
        item.innerHTML = '<span class="cms-folder-list-icon">' + getFolderIcon() + '</span><span class="cms-folder-list-name">' + escapeHtml(folder.display) + '</span>';
        item.addEventListener('click', function() {
            selectFolderItem(folder.path);
        });
        list.appendChild(item);
    });
}

function selectFolderItem(path) {
    // Remove previous selection
    document.querySelectorAll('.cms-folder-list-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Select clicked item
    const item = document.querySelector(`[data-folder-path="${path}"]`);
    if (item) {
        item.classList.add('selected');
    }
    
    // Enable move button
    const confirmBtn = document.getElementById('move-confirm-btn');
    if (confirmBtn) {
        confirmBtn.disabled = false;
    }
}

function getFolderIcon() {
    return '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.6914 1C12.0699 1.00016 12.4156 1.21422 12.585 1.55273L13.9473 4.27637L13.9863 4.38477C13.9952 4.42241 14 4.46105 14 4.5V13C14 13.5523 13.5523 14 13 14H2C1.44772 14 1 13.5523 1 13V4.5C1 4.42238 1.01802 4.34579 1.05273 4.27637L2.41504 1.55273L2.48633 1.43164C2.6712 1.16394 2.97741 1.00014 3.30859 1H11.6914ZM2 13H13V5H2V13ZM9.5 7C9.77614 7 10 7.22386 10 7.5C10 7.77614 9.77614 8 9.5 8H5.5C5.22386 8 5 7.77614 5 7.5C5 7.22386 5.22386 7 5.5 7H9.5ZM2.30859 4H7V2H3.30859L2.30859 4ZM8 4H12.6914L11.6914 2H8V4Z" fill="currentColor"/></svg>';
}

function moveItem(destination) {
    const modal = document.getElementById('move-modal');
    if (!modal) return;
    
    const type = modal.getAttribute('data-move-type');
    const name = modal.getAttribute('data-move-name');
    const currentPath = modal.getAttribute('data-move-path') || '';
    
    if (!type || !name) {
        toast.error('Unable to determine item to move');
        return;
    }
    
    const formData = new FormData();
    const csrfToken = document.getElementById('media-csrf-token')?.value || document.querySelector('input[name="csrf_token"]')?.value || '';
    if (!csrfToken) {
        toast.error('CSRF token not found. Please refresh the page.');
        return;
    }
    formData.append('csrf_token', csrfToken);
    formData.append('action', 'move');
    formData.append('type', type);
    formData.append('name', name);
    formData.append('current_path', currentPath);
    formData.append('destination', destination);
    
    fetch(CMS_BASE_URL + '/panel/actions/media.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            toast.success('Item moved successfully');
            closeMoveModal();
            setTimeout(() => window.location.reload(), 500);
        } else {
            toast.error(result.error || 'Failed to move item');
        }
    })
    .catch(error => {
        console.error('Move error:', error);
        toast.error('An error occurred while moving the item');
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function openUploadModal() {
    document.getElementById('upload-modal').style.display = 'flex';
}

function closeUploadModal() {
    document.getElementById('upload-modal').style.display = 'none';
    document.getElementById('upload-form').reset();
    document.getElementById('upload-progress').style.display = 'none';
}

function openFolderModal() {
    document.getElementById('folder-modal').style.display = 'flex';
}

function closeFolderModal() {
    document.getElementById('folder-modal').style.display = 'none';
    document.getElementById('folder-form').reset();
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        toast.success('URL copied to clipboard');
    }).catch(() => {
        toast.error('Failed to copy URL');
    });
}

function deleteFile(filename, folder) {
    dialog.confirm('Are you sure you want to delete this file?', 'Delete File').then((confirmed) => {
        if (!confirmed) return;
        
        const formData = new FormData();
        const csrfToken = document.getElementById('media-csrf-token')?.value || document.querySelector('input[name="csrf_token"]')?.value || '';
        if (!csrfToken) {
            toast.error('CSRF token not found. Please refresh the page.');
            return;
        }
        
        // Ensure folder is a string (could be undefined/null)
        const folderValue = folder || '';
        
        formData.append('csrf_token', csrfToken);
        formData.append('filename', filename || '');
        formData.append('folder', folderValue);
        formData.append('action', 'delete_file');
        
        fetch(CMS_BASE_URL + '/panel/actions/media.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    let errorData;
                    try {
                        errorData = JSON.parse(text);
                    } catch (e) {
                        errorData = { error: text || 'Server error: ' + response.status };
                    }
                    throw new Error(errorData.error || 'Failed to delete file');
                });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                toast.success('File deleted successfully');
                setTimeout(() => window.location.reload(), 500);
            } else {
                toast.error(result.error || 'Failed to delete file');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            console.error('Filename:', filename, 'Folder:', folder);
            toast.error(error.message || 'An error occurred while deleting the file');
        });
    });
}

function deleteFolder(foldername, folderpath) {
    dialog.confirm('Are you sure you want to delete this folder and all its contents?', 'Delete Folder').then((confirmed) => {
        if (!confirmed) return;
        
        const formData = new FormData();
        const csrfToken = document.getElementById('media-csrf-token')?.value || document.querySelector('input[name="csrf_token"]')?.value || '';
        if (!csrfToken) {
            toast.error('CSRF token not found. Please refresh the page.');
            return;
        }
        formData.append('csrf_token', csrfToken);
        formData.append('folder_path', folderpath);
        formData.append('action', 'delete_folder');
        
        fetch(CMS_BASE_URL + '/panel/actions/media.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                toast.success('Folder deleted successfully');
                setTimeout(() => window.location.reload(), 500);
            } else {
                toast.error(result.error || 'Failed to delete folder');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            toast.error('An error occurred while deleting the folder');
        });
    });
}

// Upload form handler
document.getElementById('upload-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const files = document.getElementById('upload-files').files;
    
    if (files.length === 0) {
        toast.error('Please select at least one file');
        return;
    }
    
    const progressBar = document.getElementById('upload-progress');
    const progressFill = document.getElementById('upload-progress-fill');
    const statusText = document.getElementById('upload-status');
    
    progressBar.style.display = 'block';
    statusText.textContent = 'Uploading...';
    
    // Create new FormData with all form fields (including CSRF token)
    const uploadData = new FormData();
    
    // Copy all form fields (including CSRF token and folder)
    for (let [key, value] of formData.entries()) {
        if (key !== 'files[]') {
            uploadData.append(key, value);
        }
    }
    
    // Add all files
    for (let i = 0; i < files.length; i++) {
        uploadData.append('files[]', files[i]);
    }
    
    // Add action
    uploadData.append('action', 'upload');
    
    try {
        const response = await fetch(CMS_BASE_URL + '/panel/actions/media.php', {
            method: 'POST',
            body: uploadData
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Upload failed:', response.status, errorText);
            
            // Check for file size error (413 Payload Too Large)
            if (response.status === 413 || errorText.includes('Content-Length') || errorText.includes('exceeds the limit')) {
                let errorMessage = 'File size exceeds server limit.';
                try {
                    const errorJson = JSON.parse(errorText);
                    if (errorJson.error) {
                        errorMessage = errorJson.error;
                    }
                } catch (e) {
                    // Not JSON, use default message
                }
                toast.error(errorMessage);
                statusText.textContent = errorMessage;
            } else if (response.status === 403) {
                toast.error('Invalid CSRF token. Please refresh the page and try again.');
                statusText.textContent = 'CSRF token error';
            } else {
                toast.error('Upload failed. Check console for details.');
                statusText.textContent = 'Upload failed';
            }
            return;
        }
        
        const result = await response.json();
        
        if (result.success) {
            const uploadedCount = result.files ? result.files.length : files.length;
            const errorCount = result.errors ? result.errors.length : 0;
            
            if (errorCount === 0) {
                statusText.textContent = `Successfully uploaded ${uploadedCount} file(s)`;
                setTimeout(() => {
                    closeUploadModal();
                    window.location.reload();
                }, 1000);
            } else {
                statusText.textContent = `Uploaded ${uploadedCount} file(s), ${errorCount} failed`;
                if (result.errors && result.errors.length > 0) {
                    toast.error(result.errors.join(', '));
                }
            }
        } else {
            toast.error(result.message || 'Upload failed');
            statusText.textContent = result.message || 'Upload failed';
        }
    } catch (error) {
        console.error('Upload error:', error);
        toast.error('An error occurred while uploading files');
        statusText.textContent = 'Upload failed';
    }
});

// Folder form handler
document.getElementById('folder-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create_folder');
    
    try {
        const response = await fetch(CMS_BASE_URL + '/panel/actions/media.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            toast.success('Folder created successfully');
            closeFolderModal();
            setTimeout(() => window.location.reload(), 500);
        } else {
            toast.error(result.error || 'Failed to create folder');
        }
    } catch (error) {
        console.error('Create folder error:', error);
        toast.error('An error occurred while creating the folder');
    }
});

// View Toggle Handler
(function() {
    const gridWrapper = document.getElementById('media-grid-wrapper');
    const viewToggleBtns = document.querySelectorAll('.cms-view-toggle-btn');
    const STORAGE_KEY = 'cms_media_view';
    
    // Get saved view preference or default to grid
    const savedView = localStorage.getItem(STORAGE_KEY) || 'grid';
    
    // Set initial view
    function setView(view) {
        if (!gridWrapper) return;
        
        // Update buttons
        viewToggleBtns.forEach(btn => {
            if (btn.getAttribute('data-view') === view) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Update grid wrapper
        if (view === 'list') {
            gridWrapper.classList.add('list-view');
        } else {
            gridWrapper.classList.remove('list-view');
        }
        
        // Save preference
        localStorage.setItem(STORAGE_KEY, view);
    }
    
    // Initialize view
    setView(savedView);
    
    // Handle toggle button clicks
    viewToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            setView(view);
        });
    });
    
    // Handle metadata form submission
    const metadataForm = document.getElementById('metadata-form');
    if (metadataForm) {
        // Prevent Enter key from submitting form (except on submit button and in allowed fields)
        metadataForm.addEventListener('keydown', function(e) {
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
        
        metadataForm.addEventListener('submit', function(e) {
            // Check if submit was triggered by Enter in tags input - if so, prevent submission
            const activeElement = document.activeElement;
            if (activeElement && activeElement.classList && activeElement.classList.contains('cms-tags-input')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            e.preventDefault();
            const formData = new FormData(metadataForm);
            formData.append('action', 'save_metadata');
            
            fetch('<?php echo CMS_URL; ?>/panel/actions/media.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof toast !== 'undefined') {
                        toast.success('Metadata saved successfully');
                    }
                    closeMetadataModal();
                } else {
                    if (typeof toast !== 'undefined') {
                        toast.error(data.error || 'Failed to save metadata');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof toast !== 'undefined') {
                    toast.error('Error saving metadata');
                }
            });
        });
    }
})();
</script>

<?php require_once PANEL_DIR . '/partials/footer.php'; ?>

