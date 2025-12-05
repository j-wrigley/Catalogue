<?php
/**
 * Panel Footer
 * Common footer for admin panel
 */
?>
            </div>
        </main>
    </div>
    
    <!-- Toast Container -->
    <div id="cms-toast-container" class="cms-toast-container"></div>
    
    <!-- Dialog (Alert/Confirm) -->
    <div id="cms-dialog" class="cms-dialog">
        <div class="cms-dialog-content">
            <div class="cms-dialog-header">
                <h3 class="cms-dialog-title" id="cms-dialog-title">Alert</h3>
            </div>
            <div class="cms-dialog-body">
                <p id="cms-dialog-message"></p>
            </div>
            <div class="cms-dialog-footer">
                <button type="button" class="cms-button cms-button-ghost" id="cms-dialog-cancel" style="display: none;">Cancel</button>
                <button type="button" class="cms-button cms-button-primary" id="cms-dialog-confirm">OK</button>
            </div>
        </div>
    </div>
    
    <!-- Link Popover -->
    <div id="cms-link-popover" class="cms-popover" role="dialog" data-state="closed" style="display: none;">
        <div class="cms-popover-content">
            <div class="cms-popover-header">
                <h3 class="cms-popover-title">Add Link</h3>
            </div>
            <div class="cms-popover-body">
                <div class="cms-form-group">
                    <label for="cms-link-text" class="cms-label">Link Text</label>
                    <input type="text" id="cms-link-text" class="cms-input" placeholder="Link text" />
                </div>
                <div class="cms-form-group">
                    <label for="cms-link-url" class="cms-label">URL</label>
                    <input type="url" id="cms-link-url" class="cms-input" placeholder="https://example.com" />
                </div>
            </div>
            <div class="cms-popover-footer">
                <button type="button" class="cms-button cms-button-ghost" id="cms-link-cancel">Cancel</button>
                <button type="button" class="cms-button cms-button-primary" id="cms-link-submit">Add Link</button>
            </div>
        </div>
    </div>
    
    <!-- Media Picker Modal -->
    <div id="cms-media-picker" class="cms-modal" style="display: none;">
        <div class="cms-modal-backdrop" onclick="closeMediaPicker()"></div>
        <div class="cms-modal-content cms-media-picker-content">
            <div class="cms-modal-header">
                <h3 class="cms-modal-title" id="media-picker-title">Select Media</h3>
                <button type="button" class="cms-modal-close" onclick="closeMediaPicker()" aria-label="Close">×</button>
            </div>
            <div class="cms-media-picker-body">
                <div class="cms-media-picker-page">
                    <!-- Sidebar: Folders -->
                    <aside class="cms-media-picker-sidebar">
                        <div class="cms-media-picker-sidebar-header">
                            <h2 class="cms-media-picker-sidebar-title">Folders</h2>
                            <button type="button" class="cms-button cms-button-ghost cms-button-sm" onclick="showMediaPickerFolder()" title="New Folder">
                                <?php echo icon('plus', 'cms-icon'); ?>
                            </button>
                        </div>
                        <nav class="cms-media-picker-sidebar-nav" id="media-picker-sidebar-nav">
                            <div class="cms-media-picker-loading">Loading folders...</div>
                        </nav>
                    </aside>
                    
                    <!-- Main Content: Files -->
                    <main class="cms-media-picker-main">
                        <!-- Toolbar -->
                        <div class="cms-media-picker-toolbar">
                            <nav class="cms-media-breadcrumb" id="media-picker-breadcrumb" aria-label="Breadcrumb">
                                <!-- Breadcrumb will be dynamically generated -->
                            </nav>
                            <div class="cms-media-picker-actions">
                                <div class="cms-media-picker-view-toggle">
                                    <button type="button" class="cms-view-toggle-btn active" data-view="grid" title="Grid View">
                                        <?php echo icon('view-grid', 'cms-icon'); ?>
                                    </button>
                                    <button type="button" class="cms-view-toggle-btn" data-view="list" title="List View">
                                        <?php echo icon('view-vertical', 'cms-icon'); ?>
                                    </button>
                                </div>
                                <button type="button" class="cms-button cms-button-outline" onclick="showMediaUpload()">
                                    <?php echo icon('upload', 'cms-icon'); ?>
                                    <span>Upload</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Files Grid -->
                        <div class="cms-media-picker-grid-wrapper" id="media-picker-grid">
                            <div class="cms-media-picker-loading">Loading media...</div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="cms-modal-footer">
                <button type="button" class="cms-button cms-button-ghost" onclick="closeMediaPicker()">Cancel</button>
                <button type="button" class="cms-button cms-button-primary" id="media-picker-select" onclick="selectMediaFile()" disabled>Select</button>
            </div>
        </div>
    </div>
    
    <!-- Media Picker Folder Modal -->
    <div id="cms-media-picker-folder" class="cms-modal" style="display: none;">
        <div class="cms-modal-backdrop" onclick="hideMediaPickerFolder()"></div>
        <div class="cms-modal-content">
            <div class="cms-modal-header">
                <h3 class="cms-modal-title">Create Folder</h3>
                <button type="button" class="cms-modal-close" onclick="hideMediaPickerFolder()" aria-label="Close">×</button>
            </div>
            <form id="media-picker-folder-form">
                <div class="cms-modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>" />
                    <input type="hidden" name="folder" id="media-picker-folder-path" value="/" />
                    <div class="cms-form-group">
                        <label for="media-picker-folder-name" class="cms-label">Folder Name</label>
                        <input type="text" id="media-picker-folder-name" name="folder_name" class="cms-input" pattern="[a-z0-9_-]+" placeholder="my-folder" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '').replace(/\s+/g, '-')" required />
                        <p class="cms-text-muted" style="margin-top: var(--space-2); font-size: var(--font-size-sm);">Use lowercase letters, numbers, hyphens, and underscores only.</p>
                    </div>
                </div>
                <div class="cms-modal-footer">
                    <button type="button" class="cms-button cms-button-ghost" onclick="hideMediaPickerFolder()">Cancel</button>
                    <button type="submit" class="cms-button cms-button-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Media Upload Modal (within picker) -->
    <div id="cms-media-picker-upload" class="cms-modal" style="display: none;">
        <div class="cms-modal-backdrop" onclick="hideMediaUpload()"></div>
        <div class="cms-modal-content">
            <div class="cms-modal-header">
                <h3 class="cms-modal-title">Upload Files</h3>
                <button type="button" class="cms-modal-close" onclick="hideMediaUpload()" aria-label="Close">×</button>
            </div>
            <form id="media-picker-upload-form" enctype="multipart/form-data">
                <div class="cms-modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>" />
                    <input type="hidden" name="folder" id="media-picker-upload-folder" value="/" />
                    <div class="cms-form-group">
                        <label for="media-picker-files" class="cms-label">Select Files</label>
                        <input type="file" id="media-picker-files" name="files[]" multiple accept="image/*,application/pdf,application/zip" class="cms-input" required />
                        <p class="cms-text-muted" style="margin-top: var(--space-2); font-size: var(--font-size-sm);">You can select multiple files. Supported: images, PDFs, and ZIP files.</p>
                    </div>
                    <div id="media-picker-upload-progress" style="display: none; margin-top: var(--space-4);">
                        <div class="cms-progress-bar">
                            <div class="cms-progress-fill" id="media-picker-upload-progress-fill" style="width: 0%;"></div>
                        </div>
                        <p class="cms-text-muted" id="media-picker-upload-status" style="margin-top: var(--space-2); font-size: var(--font-size-sm);"></p>
                    </div>
                </div>
                <div class="cms-modal-footer">
                    <button type="button" class="cms-button cms-button-ghost" onclick="hideMediaUpload()">Cancel</button>
                    <button type="submit" class="cms-button cms-button-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo htmlspecialchars(CMS_URL, ENT_QUOTES, 'UTF-8'); ?>/panel/assets/js/panel.js"></script>
    <script>
    // Custom Dialog System
    const dialog = {
        show(message, title = 'Alert', type = 'alert') {
            const dialogEl = document.getElementById('cms-dialog');
            const titleEl = document.getElementById('cms-dialog-title');
            const messageEl = document.getElementById('cms-dialog-message');
            const confirmBtn = document.getElementById('cms-dialog-confirm');
            const cancelBtn = document.getElementById('cms-dialog-cancel');
            
            titleEl.textContent = title;
            messageEl.textContent = message;
            
            // Set dialog type
            dialogEl.className = 'cms-dialog';
            if (type === 'error') {
                dialogEl.classList.add('cms-dialog-error');
            }
            
            // Show/hide cancel button
            if (type === 'confirm') {
                cancelBtn.style.display = 'inline-flex';
            } else {
                cancelBtn.style.display = 'none';
            }
            
            // Show dialog
            dialogEl.classList.add('cms-dialog-open');
            
            // Return promise for confirm dialogs
            return new Promise((resolve) => {
                const handleConfirm = () => {
                    dialogEl.classList.remove('cms-dialog-open');
                    confirmBtn.removeEventListener('click', handleConfirm);
                    cancelBtn.removeEventListener('click', handleCancel);
                    resolve(true);
                };
                
                const handleCancel = () => {
                    dialogEl.classList.remove('cms-dialog-open');
                    confirmBtn.removeEventListener('click', handleConfirm);
                    cancelBtn.removeEventListener('click', handleCancel);
                    resolve(false);
                };
                
                confirmBtn.addEventListener('click', handleConfirm);
                if (type === 'confirm') {
                    cancelBtn.addEventListener('click', handleCancel);
                }
            });
        },
        
        alert(message, title = 'Alert') {
            return this.show(message, title, 'alert');
        },
        
        error(message, title = 'Error') {
            return this.show(message, title, 'error');
        },
        
        confirm(message, title = 'Confirm') {
            return this.show(message, title, 'confirm');
        }
    };
    
    // Close dialog when clicking outside
    document.getElementById('cms-dialog').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('cms-dialog-open');
        }
    });
    
    // Toast Notification System
    const toast = {
        show(message, type = 'success', duration = 3000) {
            const container = document.getElementById('cms-toast-container');
            if (!container) return;
            
            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = 'cms-toast cms-toast-' + type;
            toastEl.setAttribute('role', 'alert');
            
            // Toast content
            toastEl.innerHTML = `
                <div class="cms-toast-content">
                    <span class="cms-toast-message">${message}</span>
                </div>
            `;
            
            // Add to container
            container.appendChild(toastEl);
            
            // Trigger animation
            requestAnimationFrame(() => {
                toastEl.classList.add('cms-toast-show');
            });
            
            // Auto remove after duration
            setTimeout(() => {
                toastEl.classList.remove('cms-toast-show');
                setTimeout(() => {
                    if (toastEl.parentNode) {
                        toastEl.parentNode.removeChild(toastEl);
                    }
                }, 300); // Wait for fade out animation
            }, duration);
            
            // Allow manual dismiss on click
            toastEl.addEventListener('click', () => {
                toastEl.classList.remove('cms-toast-show');
                setTimeout(() => {
                    if (toastEl.parentNode) {
                        toastEl.parentNode.removeChild(toastEl);
                    }
                }, 300);
            });
        },
        
        success(message, duration = 3000) {
            return this.show(message, 'success', duration);
        },
        
        error(message, duration = 5000) {
            return this.show(message, 'error', duration);
        },
        
        info(message, duration = 3000) {
            return this.show(message, 'info', duration);
        }
    };
    
    // Visual Markdown Editor
    (function() {
        // Markdown to HTML converter
        function markdownToHtml(markdown) {
            if (!markdown) return '';
            
            let html = markdown;
            
            // Code blocks (must be first to avoid conflicts)
            html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
            
            // Inline code
            html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
            
            // Headings
            html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
            html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
            html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');
            
            // Bold
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>'); // Double pass for nested
            
            // Italic
            html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
            
            // Strikethrough
            html = html.replace(/~~(.+?)~~/g, '<s>$1</s>');
            
            // Underline (HTML)
            html = html.replace(/<u>(.+?)<\/u>/g, '<u>$1</u>');
            
            // Links
            html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');
            
            // Blockquotes
            html = html.replace(/^> (.+$)/gim, '<blockquote>$1</blockquote>');
            
            // Process lists line by line to preserve order (ordered vs unordered)
            const lines = html.split('\n');
            const processedLines = [];
            let inUnorderedList = false;
            let inOrderedList = false;
            
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                
                // Check for unordered list item
                const unorderedMatch = line.match(/^[\*\-\+] (.+)$/);
                if (unorderedMatch) {
                    if (!inUnorderedList) {
                        if (inOrderedList) {
                            processedLines.push('</ol>');
                            inOrderedList = false;
                        }
                        processedLines.push('<ul>');
                        inUnorderedList = true;
                    }
                    processedLines.push('<li>' + unorderedMatch[1] + '</li>');
                }
                // Check for ordered list item
                else {
                    const orderedMatch = line.match(/^\d+\. (.+)$/);
                    if (orderedMatch) {
                        if (!inOrderedList) {
                            if (inUnorderedList) {
                                processedLines.push('</ul>');
                                inUnorderedList = false;
                            }
                            processedLines.push('<ol>');
                            inOrderedList = true;
                        }
                        processedLines.push('<li>' + orderedMatch[1] + '</li>');
                    }
                    // Regular line
                    else {
                        if (inUnorderedList) {
                            processedLines.push('</ul>');
                            inUnorderedList = false;
                        }
                        if (inOrderedList) {
                            processedLines.push('</ol>');
                            inOrderedList = false;
                        }
                        processedLines.push(line);
                    }
                }
            }
            
            // Close any open lists
            if (inUnorderedList) {
                processedLines.push('</ul>');
                }
            if (inOrderedList) {
                processedLines.push('</ol>');
            }
            
            html = processedLines.join('\n');
            
            // Paragraphs (wrap consecutive non-block elements)
            html = html.split('\n\n').map(para => {
                para = para.trim();
                if (para && !para.match(/^<(h[1-6]|ul|ol|blockquote|pre)/)) {
                    return '<p>' + para + '</p>';
                }
                return para;
            }).join('\n\n');
            
            // Clean up empty paragraphs
            html = html.replace(/<p><\/p>/g, '');
            html = html.replace(/\n\n+/g, '\n\n');
            
            return html;
        }
        
        // HTML to Markdown converter
        function htmlToMarkdown(html) {
            if (!html) return '';
            
            // Create a temporary div to parse HTML
            const temp = document.createElement('div');
            temp.innerHTML = html;
            
            function processNode(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    return node.textContent;
                }
                
                if (node.nodeType !== Node.ELEMENT_NODE) {
                    return '';
                }
                
                const tag = node.tagName.toLowerCase();
                const children = Array.from(node.childNodes).map(processNode).join('');
                
                switch (tag) {
                    case 'h1':
                        return '# ' + children + '\n\n';
                    case 'h2':
                        return '## ' + children + '\n\n';
                    case 'h3':
                        return '### ' + children + '\n\n';
                    case 'p':
                        return children + '\n\n';
                    case 'br':
                        return '\n';
                    case 'strong':
                    case 'b':
                        return '**' + children + '**';
                    case 'em':
                    case 'i':
                        return '*' + children + '*';
                    case 's':
                    case 'strike':
                        return '~~' + children + '~~';
                    case 'u':
                        return '<u>' + children + '</u>';
                    case 'code':
                        // Check if parent is pre
                        if (node.parentElement && node.parentElement.tagName.toLowerCase() === 'pre') {
                            return children;
                        }
                        return '`' + children + '`';
                    case 'pre':
                        return '```\n' + children + '\n```\n\n';
                    case 'blockquote':
                        return '> ' + children.replace(/\n/g, '\n> ') + '\n\n';
                    case 'ul':
                        return Array.from(node.querySelectorAll('li'))
                            .map(li => '- ' + processNode(li).trim())
                            .join('\n') + '\n\n';
                    case 'ol':
                        return Array.from(node.querySelectorAll('li'))
                            .map((li, i) => (i + 1) + '. ' + processNode(li).trim())
                            .join('\n') + '\n\n';
                    case 'li':
                        return children;
                    case 'a':
                        const href = node.getAttribute('href') || '';
                        return '[' + children + '](' + href + ')';
                    case 'div':
                        return children + '\n';
                    default:
                        return children;
                }
            }
            
            let markdown = Array.from(temp.childNodes).map(processNode).join('');
            
            // Clean up
            markdown = markdown.replace(/\n{3,}/g, '\n\n');
            markdown = markdown.trim();
            
            return markdown;
        }
        
        // Initialize visual markdown editors
        document.querySelectorAll('.cms-markdown-editor').forEach(editor => {
            const fieldName = editor.getAttribute('data-field-name');
            const textarea = editor.querySelector('.cms-markdown-textarea-hidden');
            const visualEditor = editor.querySelector('.cms-markdown-visual-editor');
            
            if (!textarea || !visualEditor) return;
            
            // Set initial content
            const markdown = textarea.value || '';
            visualEditor.innerHTML = markdownToHtml(markdown);
            
            // Update markdown when visual editor changes
            visualEditor.addEventListener('input', function() {
                const html = this.innerHTML;
                const markdown = htmlToMarkdown(html);
                textarea.value = markdown;
            });
            
            // Prevent paste formatting issues
            visualEditor.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text/plain');
                document.execCommand('insertText', false, text);
            });
            
            // Handle toolbar buttons
            editor.querySelectorAll('.cms-markdown-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    visualEditor.focus();
                    
                    const command = this.getAttribute('data-command');
                    const value = this.getAttribute('data-value');
                    const action = this.getAttribute('data-action');
                    
                    if (command) {
                        if (command === 'formatBlock' && value) {
                            document.execCommand(command, false, value);
                        } else {
                            document.execCommand(command, false, null);
                        }
                        visualEditor.focus();
                        // Update markdown
                        const html = visualEditor.innerHTML;
                        textarea.value = htmlToMarkdown(html);
                    } else if (action === 'link') {
                        // Make sure visual editor has focus first
                        visualEditor.focus();
                        
                        // Save selection before opening popover
                        const selection = window.getSelection();
                        let savedRange = null;
                        let selectedText = '';
                        
                        if (selection.rangeCount > 0) {
                            const range = selection.getRangeAt(0);
                            // Check if selection is within visual editor
                            if (visualEditor.contains(range.commonAncestorContainer) || visualEditor === range.commonAncestorContainer) {
                                savedRange = range.cloneRange();
                                selectedText = selection.toString();
                            }
                        }
                        
                        // If no valid selection, create range at cursor or end
                        if (!savedRange) {
                            savedRange = document.createRange();
                            if (visualEditor.childNodes.length > 0) {
                                // Set at end of editor
                                savedRange.selectNodeContents(visualEditor);
                                savedRange.collapse(false);
                            } else {
                                // Editor is empty
                                savedRange.setStart(visualEditor, 0);
                                savedRange.collapse(true);
                            }
                        }
                        
                        // Show popover
                        const popover = document.getElementById('cms-link-popover');
                        const linkUrlInput = document.getElementById('cms-link-url');
                        const linkTextInput = document.getElementById('cms-link-text');
                        const linkSubmitBtn = document.getElementById('cms-link-submit');
                        const linkCancelBtn = document.getElementById('cms-link-cancel');
                        
                        if (popover) {
                            // Set initial text if selected
                            if (linkTextInput) {
                                linkTextInput.value = selectedText || '';
                            }
                            if (linkUrlInput) {
                                linkUrlInput.value = '';
                            }
                            
                            // Position popover near button
                            const buttonRect = this.getBoundingClientRect();
                            popover.style.display = 'block';
                            popover.style.position = 'fixed';
                            popover.style.top = (buttonRect.bottom + 8) + 'px';
                            // Center popover below button
                            const popoverWidth = 300; // min-width from CSS
                            popover.style.left = (buttonRect.left + (buttonRect.width / 2) - (popoverWidth / 2)) + 'px';
                            popover.setAttribute('data-state', 'open');
                            
                            // Prevent popover clicks from closing it
                            popover.addEventListener('click', (e) => {
                                e.stopPropagation();
                            });
                            
                            // Focus URL input
                            setTimeout(() => {
                                if (linkUrlInput) {
                                    linkUrlInput.focus();
                                }
                            }, 10);
                            
                            // Define handlers (must be defined before use)
                            let handleClickOutsideRef = null;
                            
                            // Handle submit
                            const handleSubmit = (e) => {
                                e.preventDefault();
                                const url = linkUrlInput.value.trim();
                                const text = linkTextInput.value.trim() || url;
                                
                                if (url && savedRange) {
                                    // Restore selection
                                    const newSelection = window.getSelection();
                                    newSelection.removeAllRanges();
                                    newSelection.addRange(savedRange);
                                    
                                    // Create link element
                                    const link = document.createElement('a');
                                    link.href = url;
                                    link.textContent = text;
                                    
                                    if (selectedText) {
                                        // Replace selected text with link
                                        savedRange.deleteContents();
                                        savedRange.insertNode(link);
                                    } else {
                                        // Insert link at cursor
                                        savedRange.insertNode(link);
                                    }
                                    
                                    // Move cursor after link
                                    newSelection.removeAllRanges();
                                    const newRange = document.createRange();
                                    newRange.setStartAfter(link);
                                    newRange.collapse(true);
                                    newSelection.addRange(newRange);
                                    
                                    visualEditor.focus();
                                    const html = visualEditor.innerHTML;
                                    textarea.value = htmlToMarkdown(html);
                                }
                                
                                // Hide popover
                                popover.style.display = 'none';
                                popover.setAttribute('data-state', 'closed');
                                linkSubmitBtn.removeEventListener('click', handleSubmit);
                                linkCancelBtn.removeEventListener('click', handleCancel);
                                linkUrlInput.removeEventListener('keydown', handleKeydown);
                                if (handleClickOutsideRef) {
                                    document.removeEventListener('click', handleClickOutsideRef);
                                }
                            };
                            
                            // Handle cancel
                            const handleCancel = () => {
                                popover.style.display = 'none';
                                popover.setAttribute('data-state', 'closed');
                                linkSubmitBtn.removeEventListener('click', handleSubmit);
                                linkCancelBtn.removeEventListener('click', handleCancel);
                                linkUrlInput.removeEventListener('keydown', handleKeydown);
                                if (handleClickOutsideRef) {
                                    document.removeEventListener('click', handleClickOutsideRef);
                                }
                            };
                            
                            // Handle Enter key
                            const handleKeydown = (e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    handleSubmit(e);
                                } else if (e.key === 'Escape') {
                                    handleCancel();
                                }
                            };
                            
                            // Close popover when clicking outside
                            const handleClickOutside = (e) => {
                                if (!popover.contains(e.target) && !this.contains(e.target)) {
                                    handleCancel();
                                }
                            };
                            
                            // Store reference for cleanup
                            handleClickOutsideRef = handleClickOutside;
                            
                            linkSubmitBtn.addEventListener('click', handleSubmit);
                            linkCancelBtn.addEventListener('click', handleCancel);
                            linkUrlInput.addEventListener('keydown', handleKeydown);
                            
                            // Add click outside listener after a small delay to prevent immediate close
                            setTimeout(() => {
                                document.addEventListener('click', handleClickOutside);
                            }, 10);
                        }
                    } else if (action === 'code') {
                        const selection = window.getSelection();
                        if (selection.rangeCount > 0) {
                            const range = selection.getRangeAt(0);
                            const selectedText = range.toString();
                            if (selectedText) {
                                const code = document.createElement('code');
                                code.textContent = selectedText;
                                range.deleteContents();
                                range.insertNode(code);
                                selection.removeAllRanges();
                                selection.addRange(range);
                            } else {
                                const code = document.createElement('code');
                                code.textContent = 'code';
                                range.insertNode(code);
                                code.focus();
                            }
                            visualEditor.focus();
                            const html = visualEditor.innerHTML;
                            textarea.value = htmlToMarkdown(html);
                        }
                    } else if (action === 'quote') {
                        const selection = window.getSelection();
                        if (selection.rangeCount > 0) {
                            const range = selection.getRangeAt(0);
                            const blockquote = document.createElement('blockquote');
                            if (range.toString()) {
                                blockquote.textContent = range.toString();
                                range.deleteContents();
                            } else {
                                blockquote.textContent = 'Quote';
                            }
                            range.insertNode(blockquote);
                            visualEditor.focus();
                            const html = visualEditor.innerHTML;
                            textarea.value = htmlToMarkdown(html);
                        }
                    }
                });
            });
        });
    })();
    
    // File Upload Functionality
    // File upload handlers using data attributes
    (function() {
        // Handle media picker buttons
        document.addEventListener('click', function(e) {
            const button = e.target.closest('[data-action="open-media-picker"]');
            if (button) {
                const fieldName = button.getAttribute('data-field');
                if (fieldName) {
                    openMediaPicker(fieldName);
                }
            }
            
            const removeButton = e.target.closest('[data-action="remove-file"]');
            if (removeButton) {
                const fieldName = removeButton.getAttribute('data-field');
                if (fieldName) {
                    removeFile(fieldName);
                }
            }
            
            // Handle remove file item (for multiple files)
            const removeItemButton = e.target.closest('[data-action="remove-file-item"]');
            if (removeItemButton) {
                const fieldName = removeItemButton.getAttribute('data-field');
                const index = parseInt(removeItemButton.getAttribute('data-index'), 10);
                if (fieldName && !isNaN(index)) {
                    removeFileItem(fieldName, index);
                }
            }
        });
        
        // Remove single file function
        window.removeFile = function(fieldName) {
            const fieldInput = document.getElementById(fieldName);
            const fileUpload = fieldInput?.closest('.cms-file-upload');
            const preview = fileUpload?.querySelector('.cms-file-preview');
            const previewGrid = fileUpload?.querySelector('.cms-file-preview-grid');
            const trigger = fileUpload?.querySelector('.cms-file-trigger');
            
            if (fieldInput) {
                fieldInput.value = '';
            }
            
            if (preview) {
                preview.remove();
            }
            
            if (previewGrid) {
                previewGrid.remove();
            }
            
            if (trigger) {
                const triggerText = trigger.querySelector('.cms-file-trigger-text');
                if (triggerText) {
                    triggerText.textContent = 'Select from media';
                }
                trigger.disabled = false;
            }
            
            // Clear hidden current value
            const currentInput = fileUpload?.querySelector('input[name="' + fieldName + '_current"]');
            if (currentInput) {
                currentInput.value = '';
            }
        };
        
        // Remove file item from multiple files
        window.removeFileItem = function(fieldName, index) {
            const fieldInput = document.getElementById(fieldName);
            if (!fieldInput) return;
            
            try {
                const files = JSON.parse(fieldInput.value || '[]');
                if (Array.isArray(files) && files[index] !== undefined) {
                    files.splice(index, 1);
                    fieldInput.value = JSON.stringify(files);
                    
                    // Update preview grid
                    const fileUpload = fieldInput.closest('.cms-file-upload');
                    if (fileUpload) {
                        const previewGrid = fileUpload.querySelector('.cms-file-preview-grid');
                        if (previewGrid) {
                            previewGrid.remove();
                        }
                        
                        // Rebuild preview with remaining files
                        const fileObjects = files.map(url => ({ url: url, type: 'image' }));
                        if (typeof window.updateMultipleFilePreview === 'function') {
                            window.updateMultipleFilePreview(fieldName, fileObjects);
                        } else {
                            // Fallback: manually rebuild
                            const grid = document.createElement('div');
                            grid.className = 'cms-file-preview-grid';
                            
                            fileObjects.forEach((file, idx) => {
                                const item = document.createElement('div');
                                item.className = 'cms-file-preview-item';
                                
                                const img = document.createElement('img');
                                img.src = file.url;
                                img.className = 'cms-file-preview-image';
                                img.alt = 'Preview';
                                
                                const overlay = document.createElement('div');
                                overlay.className = 'cms-file-preview-overlay';
                                
                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'cms-button cms-button-ghost cms-button-sm';
                                removeBtn.setAttribute('data-action', 'remove-file-item');
                                removeBtn.setAttribute('data-field', fieldName);
                                removeBtn.setAttribute('data-index', idx);
                                
                                const icon = document.createElement('span');
                                icon.className = 'cms-icon';
                                icon.innerHTML = '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 3C11.7761 3 12 3.22386 12 3.5C12 3.77614 11.7761 4 11.5 4H11V12L10.9951 12.1025C10.9472 12.573 10.573 12.9472 10.1025 12.9951L10 13H5L4.89746 12.9951C4.42703 12.9472 4.05278 12.573 4.00488 12.1025L4 12V4H3.5C3.22386 4 3 3.77614 3 3.5C3 3.22386 3.22386 3 3.5 3H11.5ZM5 12H10V4H5V12ZM9.5 1C9.77614 1 10 1.22386 10 1.5C10 1.77614 9.77614 2 9.5 2H5.5C5.22386 2 5 1.77614 5 1.5C5 1.22386 5.22386 1 5.5 1H9.5Z" fill="currentColor"/></svg>';
                                
                                const span = document.createElement('span');
                                span.textContent = 'Remove';
                                
                                removeBtn.appendChild(icon);
                                removeBtn.appendChild(span);
                                overlay.appendChild(removeBtn);
                                item.appendChild(img);
                                item.appendChild(overlay);
                                grid.appendChild(item);
                            });
                            
                            fileUpload.insertBefore(grid, fileUpload.querySelector('.cms-file-trigger-wrapper'));
                            
                            // Update button text
                            const trigger = fileUpload.querySelector('.cms-file-trigger');
                            const triggerText = trigger?.querySelector('.cms-file-trigger-text');
                            if (triggerText) {
                                const maxFilesAttr = fileUpload.getAttribute('data-max-files');
                                const maxFiles = maxFilesAttr ? parseInt(maxFilesAttr, 10) : null;
                                const remaining = maxFiles ? maxFiles - fileObjects.length : null;
                                
                                if (fileObjects.length > 0) {
                                    if (remaining !== null && remaining > 0) {
                                        triggerText.textContent = `Add more files (${remaining} remaining)`;
                                        if (trigger) trigger.disabled = false;
                                    } else if (remaining === 0) {
                                        triggerText.textContent = 'Maximum files reached';
                                        if (trigger) trigger.disabled = true;
                                    } else {
                                        triggerText.textContent = 'Add more files';
                                        if (trigger) trigger.disabled = false;
                                    }
                                } else {
                                    triggerText.textContent = 'Select files';
                                    if (trigger) trigger.disabled = false;
                                }
                            }
                        }
                    }
                }
            } catch (e) {
                console.error('Error removing file item:', e);
            }
        };
        
        // Handle file input changes for preview
        document.querySelectorAll('.cms-file-input-hidden').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const fileUpload = input.closest('.cms-file-upload');
                        const preview = fileUpload.querySelector('.cms-file-preview');
                        const trigger = fileUpload.querySelector('.cms-file-trigger');
                        
                        if (preview) {
                            // Update existing preview
                            const img = preview.querySelector('.cms-file-preview-image');
                            if (img) {
                                img.src = e.target.result;
                            }
                        } else {
                            // Create new preview
                            const fieldName = input.id;
                            const newPreview = document.createElement('div');
                            newPreview.className = 'cms-file-preview';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'cms-file-preview-image';
                            img.alt = 'Preview';
                            
                            const overlay = document.createElement('div');
                            overlay.className = 'cms-file-preview-overlay';
                            
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'cms-button cms-button-ghost cms-button-sm';
                            removeBtn.onclick = function() { removeFile(fieldName); };
                            
                            // Add trash icon
                            const icon = document.createElement('span');
                            icon.className = 'cms-icon';
                            icon.innerHTML = '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 3C11.7761 3 12 3.22386 12 3.5C12 3.77614 11.7761 4 11.5 4H11V12L10.9951 12.1025C10.9472 12.573 10.573 12.9472 10.1025 12.9951L10 13H5L4.89746 12.9951C4.42703 12.9472 4.05278 12.573 4.00488 12.1025L4 12V4H3.5C3.22386 4 3 3.77614 3 3.5C3 3.22386 3.22386 3 3.5 3H11.5ZM5 12H10V4H5V12ZM9.5 1C9.77614 1 10 1.22386 10 1.5C10 1.77614 9.77614 2 9.5 2H5.5C5.22386 2 5 1.77614 5 1.5C5 1.22386 5.22386 1 5.5 1H9.5Z" fill="currentColor"/></svg>';
                            
                            const span = document.createElement('span');
                            span.textContent = 'Remove';
                            
                            removeBtn.appendChild(icon);
                            removeBtn.appendChild(span);
                            overlay.appendChild(removeBtn);
                            newPreview.appendChild(img);
                            newPreview.appendChild(overlay);
                            
                            fileUpload.insertBefore(newPreview, input.closest('.cms-file-trigger-wrapper'));
                        }
                        
                        if (trigger) {
                            const triggerText = trigger.querySelector('.cms-file-trigger-text');
                            if (triggerText) {
                                triggerText.textContent = 'Replace file';
                            }
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    })();
    
    // Media Picker
    (function() {
        let currentMediaField = null;
        let currentMediaPath = '/';
        let selectedMediaFiles = []; // Changed to array for multiple selection
        let isMultipleMode = false;
        let maxFiles = null;
        
        window.openMediaPicker = function(fieldName) {
            currentMediaField = fieldName;
            currentMediaPath = '/';
            selectedMediaFiles = [];
            
            // Get field configuration
            const fieldElement = document.querySelector(`[data-field="${fieldName}"]`);
            if (fieldElement) {
                isMultipleMode = fieldElement.getAttribute('data-multiple') === 'true';
                const maxFilesAttr = fieldElement.getAttribute('data-max-files');
                maxFiles = maxFilesAttr ? parseInt(maxFilesAttr, 10) : null;
                
                // Load existing files if in multiple mode
                if (isMultipleMode) {
                    const hiddenInput = document.getElementById(fieldName);
                    if (hiddenInput && hiddenInput.value) {
                        try {
                            const existingFiles = JSON.parse(hiddenInput.value);
                            if (Array.isArray(existingFiles)) {
                                selectedMediaFiles = existingFiles.map(file => {
                                    if (typeof file === 'string') {
                                        return { url: file, type: 'image' };
                                    }
                                    return file;
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing existing files:', e);
                        }
                    }
                }
            } else {
                isMultipleMode = false;
                maxFiles = null;
            }
            
            const modal = document.getElementById('cms-media-picker');
            if (modal) {
                modal.style.display = 'flex';
                viewToggleInitialized = false; // Reset view toggle initialization
                loadMediaPickerFolders();
                loadMediaPicker(currentMediaPath);
                updateBreadcrumb(currentMediaPath); // Initialize breadcrumb
                updateSelectButton();
                updateModalTitle();
                initMediaPickerViewToggle();
            }
        };
        
        // View Toggle Handler for Media Picker
        let viewToggleInitialized = false;
        function initMediaPickerViewToggle() {
            const gridWrapper = document.getElementById('media-picker-grid');
            const viewToggleBtns = document.querySelectorAll('.cms-media-picker-view-toggle .cms-view-toggle-btn');
            const STORAGE_KEY = 'cms_media_picker_view';
            
            if (!gridWrapper || viewToggleBtns.length === 0) return;
            
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
            
            // Only add event listeners once
            if (!viewToggleInitialized) {
                viewToggleInitialized = true;
                // Handle toggle button clicks
                viewToggleBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const view = this.getAttribute('data-view');
                        setView(view);
                    });
                });
            }
        }
        
        function loadMediaPickerFolders() {
            const sidebarNav = document.getElementById('media-picker-sidebar-nav');
            if (!sidebarNav) return;
            
            sidebarNav.innerHTML = '<div class="cms-media-picker-loading">Loading folders...</div>';
            
            const formData = new FormData();
            formData.append('action', 'list_folders');
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');
            
            fetch(<?php echo json_encode(CMS_URL . '/panel/actions/media.php'); ?>, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.folders) {
                    renderMediaPickerSidebar(data.folders);
                } else {
                    sidebarNav.innerHTML = '<div class="cms-media-picker-empty">No folders found.</div>';
                }
            })
            .catch(error => {
                console.error('Error loading folders:', error);
                sidebarNav.innerHTML = '<div class="cms-media-picker-error">Error loading folders.</div>';
            });
        }
        
        function renderMediaPickerSidebar(folders) {
            const sidebarNav = document.getElementById('media-picker-sidebar-nav');
            if (!sidebarNav) return;
            
            sidebarNav.innerHTML = '';
            
            // Add "All Media" link
            const allMediaLink = document.createElement('a');
            allMediaLink.href = '#';
            allMediaLink.className = 'cms-media-picker-sidebar-item' + (currentMediaPath === '/' ? ' active' : '');
            allMediaLink.setAttribute('data-path', '/');
            allMediaLink.onclick = (e) => {
                e.preventDefault();
                navigateMediaPicker('/');
            };
            
            const allMediaIcon = document.createElement('span');
            allMediaIcon.className = 'cms-media-picker-sidebar-icon';
            allMediaIcon.innerHTML = getArchiveIcon();
            
            const allMediaLabel = document.createElement('span');
            allMediaLabel.className = 'cms-media-picker-sidebar-label';
            allMediaLabel.textContent = 'All Media';
            
            allMediaLink.appendChild(allMediaIcon);
            allMediaLink.appendChild(allMediaLabel);
            sidebarNav.appendChild(allMediaLink);
            
            // Add folders with nested indentation
            folders.forEach(folder => {
                const folderLink = document.createElement('a');
                folderLink.href = '#';
                const folderPath = '/' + folder.path;
                folderLink.className = 'cms-media-picker-sidebar-item' + (currentMediaPath === folderPath ? ' active' : '');
                folderLink.setAttribute('data-path', folderPath);
                
                // Calculate indentation level based on folder path depth
                const level = (folder.path.match(/\//g) || []).length;
                const paddingLeft = `calc(var(--space-4) + ${level} * var(--space-5))`;
                folderLink.style.paddingLeft = paddingLeft;
                
                folderLink.onclick = (e) => {
                    e.preventDefault();
                    navigateMediaPicker(folderPath);
                };
                
                const folderIcon = document.createElement('span');
                folderIcon.className = 'cms-media-picker-sidebar-icon';
                folderIcon.innerHTML = getArchiveIcon();
                
                const folderLabel = document.createElement('span');
                folderLabel.className = 'cms-media-picker-sidebar-label';
                folderLabel.textContent = folder.name;
                
                folderLink.appendChild(folderIcon);
                folderLink.appendChild(folderLabel);
                sidebarNav.appendChild(folderLink);
            });
        }
        
        function getArchiveIcon() {
            return '<?php echo str_replace("'", "\\'", icon('archive', 'cms-icon')); ?>';
        }
        
        function getFolderIcon() {
            return '<?php echo str_replace("'", "\\'", icon('archive', 'cms-icon')); ?>';
        }
        
        function getFileIcon() {
            return '<?php echo str_replace("'", "\\'", icon('file', 'cms-icon')); ?>';
        }
        
        function updateModalTitle() {
            const title = document.querySelector('#cms-media-picker .cms-modal-title');
            if (title) {
                if (isMultipleMode) {
                    const count = selectedMediaFiles.length;
                    const maxText = maxFiles ? ` (${count}/${maxFiles})` : ` (${count} selected)`;
                    title.textContent = 'Select Media' + maxText;
                } else {
                    title.textContent = 'Select Media';
                }
            }
        }
        
        window.closeMediaPicker = function() {
            const modal = document.getElementById('cms-media-picker');
            if (modal) {
                modal.style.display = 'none';
            }
            currentMediaField = null;
            currentMediaPath = '/';
            selectedMediaFiles = [];
            isMultipleMode = false;
            maxFiles = null;
        };
        
        window.navigateMediaPicker = function(path) {
            console.log('Media Picker - Navigating to:', path);
            currentMediaPath = path;
            loadMediaPicker(path);
            updateBreadcrumb(path);
            updateSidebarActiveState(path);
            // Don't re-initialize view toggle here - it will be applied after content loads
        };
        
        function updateSidebarActiveState(path) {
            const sidebarItems = document.querySelectorAll('.cms-media-picker-sidebar-item');
            sidebarItems.forEach(item => {
                item.classList.remove('active');
                const itemPath = item.getAttribute('data-path');
                if (itemPath === path) {
                    item.classList.add('active');
                }
            });
        }
        
        function updateBreadcrumb(path) {
            const breadcrumb = document.getElementById('media-picker-breadcrumb');
            if (!breadcrumb) return;
            
            console.log('Media Picker - Updating breadcrumb for path:', path);
            
            breadcrumb.innerHTML = '';
            
            // Normalize path - remove leading slash and handle root
            const normalizedPath = path === '/' ? '' : path.replace(/^\//, '');
            const parts = normalizedPath ? normalizedPath.split('/').filter(p => p) : [];
            
            // Root
            const rootBtn = document.createElement('button');
            rootBtn.type = 'button';
            rootBtn.className = 'cms-media-breadcrumb-link';
            rootBtn.textContent = 'Media';
            rootBtn.onclick = (e) => {
                e.preventDefault();
                console.log('Media Picker - Breadcrumb clicked: root (/)');
                navigateMediaPicker('/');
            };
            breadcrumb.appendChild(rootBtn);
            
            // Path parts - build incrementally like the main media page
            let breadcrumbPath = '';
            parts.forEach(part => {
                breadcrumbPath += (breadcrumbPath ? '/' : '') + part;
                const separator = document.createElement('span');
                separator.className = 'cms-media-breadcrumb-separator';
                separator.textContent = '/';
                breadcrumb.appendChild(separator);
                
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cms-media-breadcrumb-link';
                btn.textContent = part;
                // Navigate to path with leading slash for consistency
                const targetPath = '/' + breadcrumbPath;
                btn.onclick = (e) => {
                    e.preventDefault();
                    console.log('Media Picker - Breadcrumb clicked:', targetPath);
                    navigateMediaPicker(targetPath);
                };
                breadcrumb.appendChild(btn);
            });
        }
        
        function loadMediaPicker(path) {
            const grid = document.getElementById('media-picker-grid');
            if (!grid) return;
            
            grid.innerHTML = '<div class="cms-media-picker-loading">Loading media...</div>';
            
            // Normalize path - remove leading slash for backend (backend expects no leading slash)
            // Also handle empty string or just '/' as root
            let normalizedPath = path;
            if (path === '/' || path === '') {
                normalizedPath = '';
            } else {
                normalizedPath = path.replace(/^\//, '').replace(/\/$/, ''); // Remove leading and trailing slashes
            }
            
            console.log('Media Picker - Loading path:', { original: path, normalized: normalizedPath });
            
            const formData = new FormData();
            formData.append('action', 'list');
            formData.append('folder', normalizedPath);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');
            
            fetch(<?php echo json_encode(CMS_URL . '/panel/actions/media.php'); ?>, {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                // Check if response is ok
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Media Picker - HTTP error:', response.status, text);
                    throw new Error('Server error: ' + response.status);
                }
                
                // Check content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Media Picker - Invalid content type:', contentType, text.substring(0, 200));
                    throw new Error('Server returned invalid response (not JSON)');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Media Picker - Response:', { success: data.success, itemsCount: data.items ? data.items.length : 0, error: data.error, items: data.items });
                if (data.success && data.items && data.items.length > 0) {
                    console.log('Media Picker - Calling renderMediaGrid with', data.items.length, 'items');
                    renderMediaGrid(data.items, path);
                    // Re-apply view toggle after content is rendered
                    setTimeout(() => {
                        const savedView = localStorage.getItem('cms_media_picker_view') || 'grid';
                        const gridWrapper = document.getElementById('media-picker-grid');
                        if (gridWrapper) {
                            if (savedView === 'list') {
                                gridWrapper.classList.add('list-view');
                            } else {
                                gridWrapper.classList.remove('list-view');
                            }
                        }
                    }, 0);
                } else if (data.success && data.items && data.items.length === 0) {
                    console.log('Media Picker - Empty folder, showing empty state');
                    grid.innerHTML = '<div class="cms-media-picker-empty">No media files found.</div>';
                } else {
                    console.error('Media Picker - No items or error:', data.error || 'Unknown error');
                    grid.innerHTML = '<div class="cms-media-picker-empty">No media files found.</div>';
                }
            })
            .catch(error => {
                console.error('Media Picker - Error loading media:', error);
                grid.innerHTML = '<div class="cms-media-picker-error">Error loading media files: ' + error.message + '</div>';
            });
        }
        
        function formatFileSize(bytes) {
            if (bytes >= 1048576) {
                return (bytes / 1048576).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(2) + ' KB';
            }
            return bytes + ' bytes';
        }
        
        function formatMediaDate(timestamp) {
            const date = new Date(timestamp * 1000);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 0) {
                return 'Today at ' + date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            } else if (diffDays === 1) {
                return 'Yesterday at ' + date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            } else if (diffDays < 7) {
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                return days[date.getDay()] + ' at ' + date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
        }
        
        function renderMediaGrid(items, currentPath) {
            const grid = document.getElementById('media-picker-grid');
            if (!grid) return;
            
            console.log('Media Picker - Rendering grid:', { itemsCount: items.length, currentPath });
            
            grid.innerHTML = '';
            
            // Filter out folders - only show files in the grid
            const files = items.filter(item => item.type !== 'folder');
            
            console.log('Media Picker - Filtered files:', { totalItems: items.length, filesCount: files.length, foldersCount: items.length - files.length });
            
            if (files.length === 0) {
                grid.innerHTML = '<div class="cms-media-picker-empty">No media files found.</div>';
                return;
            }
            
            // Performance: Use DocumentFragment for batch DOM operations
            const fragment = document.createDocumentFragment();
            
            files.forEach(item => {
                const cardDiv = document.createElement('div');
                cardDiv.className = 'cms-media-picker-card cms-media-picker-file-card';
                cardDiv.dataset.path = item.path;
                cardDiv.dataset.type = item.type;
                
                const isImage = item.type === 'image';
                
                const preview = document.createElement('div');
                preview.className = 'cms-media-picker-card-preview';
                
                if (isImage) {
                    const img = document.createElement('img');
                    img.src = item.url;
                    img.alt = item.name;
                    img.className = 'cms-media-picker-card-image';
                    img.loading = 'lazy';
                    img.decoding = 'async';
                    // Add width/height to prevent layout shift
                    if (item.width && item.height) {
                        img.width = item.width;
                        img.height = item.height;
                    }
                    preview.appendChild(img);
                } else {
                    const iconDiv = document.createElement('div');
                    iconDiv.className = 'cms-media-picker-file-icon';
                    iconDiv.innerHTML = getFileIcon();
                    preview.appendChild(iconDiv);
                }
                
                const info = document.createElement('div');
                info.className = 'cms-media-picker-card-info';
                
                const nameDiv = document.createElement('div');
                nameDiv.className = 'cms-media-picker-card-name';
                nameDiv.textContent = item.name;
                nameDiv.title = item.name;
                
                const metaDiv = document.createElement('div');
                metaDiv.className = 'cms-media-picker-card-meta';
                
                if (item.size) {
                    const sizeSpan = document.createElement('span');
                    sizeSpan.className = 'cms-media-picker-card-size';
                    sizeSpan.textContent = formatFileSize(item.size);
                    metaDiv.appendChild(sizeSpan);
                }
                
                if (item.width && item.height) {
                    const dimSpan = document.createElement('span');
                    dimSpan.className = 'cms-media-picker-card-dimensions';
                    dimSpan.textContent = item.width + ' × ' + item.height;
                    metaDiv.appendChild(dimSpan);
                }
                
                const dateDiv = document.createElement('div');
                dateDiv.className = 'cms-media-picker-card-date';
                if (item.modified) {
                    dateDiv.textContent = formatMediaDate(item.modified);
                }
                
                info.appendChild(nameDiv);
                if (metaDiv.children.length > 0) {
                    info.appendChild(metaDiv);
                }
                if (dateDiv.textContent) {
                    info.appendChild(dateDiv);
                }
                
                // Check if already selected (for multiple mode)
                if (isMultipleMode) {
                    const isSelected = selectedMediaFiles.some(f => f.path === item.path);
                    if (isSelected) {
                        cardDiv.classList.add('selected');
                    }
                }
                
                cardDiv.appendChild(preview);
                cardDiv.appendChild(info);
                cardDiv.onclick = () => selectMediaItem(item);
                
                fragment.appendChild(cardDiv);
            });
            
            // Append all cards at once for better performance
            grid.appendChild(fragment);
            
            console.log('Media Picker - Finished rendering', files.length, 'files. Grid now has', grid.children.length, 'children');
        }
        
        function selectMediaItem(item) {
            if (isMultipleMode) {
                // Check if already selected
                const isSelected = selectedMediaFiles.some(f => f.url === item.url);
                
                if (isSelected) {
                    // Deselect
                    selectedMediaFiles = selectedMediaFiles.filter(f => f.url !== item.url);
                } else {
                    // Check max files limit
                    if (maxFiles && selectedMediaFiles.length >= maxFiles) {
                        toast.error(`Maximum ${maxFiles} files allowed`);
                        return;
                    }
                    // Select
                    selectedMediaFiles.push(item);
                }
                
                // Update visual selection
                document.querySelectorAll('.cms-media-picker-card').forEach(el => {
                    const itemPath = el.dataset.path;
                    const isSelected = selectedMediaFiles.some(f => f.path === itemPath);
                    el.classList.toggle('selected', isSelected);
                });
                
                updateSelectButton();
                updateModalTitle();
            } else {
                // Single selection mode
                // Remove previous selection
                document.querySelectorAll('.cms-media-picker-card').forEach(el => {
                    el.classList.remove('selected');
                });
                
                // Select this item
                const itemEl = document.querySelector(`[data-path="${item.path}"]`);
                if (itemEl) {
                    itemEl.classList.add('selected');
                }
                
                selectedMediaFiles = [item];
                updateSelectButton();
            }
        }
        
        function updateSelectButton() {
            const selectBtn = document.getElementById('media-picker-select');
            if (selectBtn) {
                if (isMultipleMode) {
                    selectBtn.disabled = selectedMediaFiles.length === 0;
                    selectBtn.textContent = selectedMediaFiles.length > 0 
                        ? `Select ${selectedMediaFiles.length} file${selectedMediaFiles.length !== 1 ? 's' : ''}` 
                        : 'Select';
                } else {
                    selectBtn.disabled = selectedMediaFiles.length === 0;
                    selectBtn.textContent = 'Select';
                }
            }
        }
        
        window.selectMediaFile = function() {
            if (selectedMediaFiles.length === 0 || !currentMediaField) return;
            
            const fieldElement = document.querySelector(`[data-field="${currentMediaField}"]`);
            if (!fieldElement) return;
            
            const isMultiple = fieldElement.getAttribute('data-multiple') === 'true';
            
            if (isMultiple) {
                // Multiple file mode - store as JSON array
                const fileUrls = selectedMediaFiles.map(file => file.url);
                const fieldInput = document.getElementById(currentMediaField);
                if (fieldInput) {
                    fieldInput.value = JSON.stringify(fileUrls);
                }
                
                // Update preview grid
                updateMultipleFilePreview(currentMediaField, selectedMediaFiles);
            } else {
                // Single file mode
                const selectedFile = selectedMediaFiles[0];
                const fieldInput = document.getElementById(currentMediaField);
                if (fieldInput) {
                    fieldInput.value = selectedFile.url;
                }
                
                // Update preview if exists
                updateSingleFilePreview(currentMediaField, selectedFile);
            }
            
            closeMediaPicker();
        };
        
        function updateMultipleFilePreview(fieldName, files) {
            const fileUpload = document.querySelector(`[data-field="${fieldName}"]`);
            if (!fileUpload) return;
            
            // Remove existing preview grid
            const existingGrid = fileUpload.querySelector('.cms-file-preview-grid');
            if (existingGrid) {
                existingGrid.remove();
            }
            
            // Create new preview grid
            if (files.length > 0) {
                const grid = document.createElement('div');
                grid.className = 'cms-file-preview-grid';
                
                files.forEach((file, index) => {
                    if (file.type === 'image') {
                        const item = document.createElement('div');
                        item.className = 'cms-file-preview-item';
                        
                        const img = document.createElement('img');
                        img.src = file.url;
                        img.className = 'cms-file-preview-image';
                        img.alt = 'Preview';
                        
                        const overlay = document.createElement('div');
                        overlay.className = 'cms-file-preview-overlay';
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'cms-button cms-button-ghost cms-button-sm';
                        removeBtn.setAttribute('data-action', 'remove-file-item');
                        removeBtn.setAttribute('data-field', fieldName);
                        removeBtn.setAttribute('data-index', index);
                        
                        const icon = document.createElement('span');
                        icon.className = 'cms-icon';
                        icon.innerHTML = '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 3C11.7761 3 12 3.22386 12 3.5C12 3.77614 11.7761 4 11.5 4H11V12L10.9951 12.1025C10.9472 12.573 10.573 12.9472 10.1025 12.9951L10 13H5L4.89746 12.9951C4.42703 12.9472 4.05278 12.573 4.00488 12.1025L4 12V4H3.5C3.22386 4 3 3.77614 3 3.5C3 3.22386 3.22386 3 3.5 3H11.5ZM5 12H10V4H5V12ZM9.5 1C9.77614 1 10 1.22386 10 1.5C10 1.77614 9.77614 2 9.5 2H5.5C5.22386 2 5 1.77614 5 1.5C5 1.22386 5.22386 1 5.5 1H9.5Z" fill="currentColor"/></svg>';
                        
                        const span = document.createElement('span');
                        span.textContent = 'Remove';
                        
                        removeBtn.appendChild(icon);
                        removeBtn.appendChild(span);
                        overlay.appendChild(removeBtn);
                        item.appendChild(img);
                        item.appendChild(overlay);
                        grid.appendChild(item);
                    }
                });
                
                fileUpload.insertBefore(grid, fileUpload.querySelector('.cms-file-trigger-wrapper'));
            }
            
            // Update button text
            const trigger = fileUpload.querySelector('.cms-file-trigger');
            const triggerText = trigger?.querySelector('.cms-file-trigger-text');
            if (triggerText) {
                const maxFilesAttr = fileUpload.getAttribute('data-max-files');
                const maxFiles = maxFilesAttr ? parseInt(maxFilesAttr, 10) : null;
                const remaining = maxFiles ? maxFiles - files.length : null;
                
                if (files.length > 0) {
                    if (remaining !== null && remaining > 0) {
                        triggerText.textContent = `Add more files (${remaining} remaining)`;
                        if (trigger) trigger.disabled = false;
                    } else if (remaining === 0) {
                        triggerText.textContent = 'Maximum files reached';
                        if (trigger) trigger.disabled = true;
                    } else {
                        triggerText.textContent = 'Add more files';
                        if (trigger) trigger.disabled = false;
                    }
                } else {
                    triggerText.textContent = 'Select files';
                    if (trigger) trigger.disabled = false;
                }
            }
        }
        
        function updateSingleFilePreview(fieldName, file) {
            const fileUpload = document.querySelector(`[data-field="${fieldName}"]`);
            if (!fileUpload) return;
            
            const preview = fileUpload.querySelector('.cms-file-preview');
            const trigger = fileUpload.querySelector('.cms-file-trigger');
            
            if (file.type === 'image') {
                if (preview) {
                    const img = preview.querySelector('.cms-file-preview-image');
                    if (img) {
                        img.src = file.url;
                    }
                } else {
                    // Create preview
                    const newPreview = document.createElement('div');
                    newPreview.className = 'cms-file-preview';
                    
                    const img = document.createElement('img');
                    img.src = file.url;
                    img.className = 'cms-file-preview-image';
                    img.alt = 'Preview';
                    
                    const overlay = document.createElement('div');
                    overlay.className = 'cms-file-preview-overlay';
                    
                    const changeBtn = document.createElement('button');
                    changeBtn.type = 'button';
                    changeBtn.className = 'cms-button cms-button-ghost cms-button-sm';
                    changeBtn.setAttribute('data-action', 'open-media-picker');
                    changeBtn.setAttribute('data-field', fieldName);
                    changeBtn.innerHTML = '<span class="cms-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 1C2.22386 1 2 1.22386 2 1.5V13.5C2 13.7761 2.22386 14 2.5 14H12.5C12.7761 14 13 13.7761 13 13.5V1.5C13 1.22386 12.7761 1 12.5 1H2.5ZM3 2H12V13H3V2ZM4.5 3C4.22386 3 4 3.22386 4 3.5V10.5C4 10.7761 4.22386 11 4.5 11H10.5C10.7761 11 11 10.7761 11 10.5V3.5C11 3.22386 10.7761 3 10.5 3H4.5ZM5 4H10V10H5V4Z" fill="currentColor"/></svg></span><span>Change</span>';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'cms-button cms-button-ghost cms-button-sm';
                    removeBtn.setAttribute('data-action', 'remove-file');
                    removeBtn.setAttribute('data-field', fieldName);
                    removeBtn.innerHTML = '<span class="cms-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5 3C11.7761 3 12 3.22386 12 3.5C12 3.77614 11.7761 4 11.5 4H11V12L10.9951 12.1025C10.9472 12.573 10.573 12.9472 10.1025 12.9951L10 13H5L4.89746 12.9951C4.42703 12.9472 4.05278 12.573 4.00488 12.1025L4 12V4H3.5C3.22386 4 3 3.77614 3 3.5C3 3.22386 3.22386 3 3.5 3H11.5ZM5 12H10V4H5V12ZM9.5 1C9.77614 1 10 1.22386 10 1.5C10 1.77614 9.77614 2 9.5 2H5.5C5.22386 2 5 1.77614 5 1.5C5 1.22386 5.22386 1 5.5 1H9.5Z" fill="currentColor"/></svg></span><span>Remove</span>';
                    
                    overlay.appendChild(changeBtn);
                    overlay.appendChild(removeBtn);
                    newPreview.appendChild(img);
                    newPreview.appendChild(overlay);
                    
                    fileUpload.insertBefore(newPreview, fileUpload.querySelector('.cms-file-trigger-wrapper'));
                }
                
                if (trigger) {
                    const triggerText = trigger.querySelector('.cms-file-trigger-text');
                    if (triggerText) {
                        triggerText.textContent = 'Change file';
                    }
                }
            }
        }
        
        // Make functions available globally for removeFileItem
        window.updateMultipleFilePreview = updateMultipleFilePreview;
        
        window.showMediaUpload = function() {
            const uploadModal = document.getElementById('cms-media-picker-upload');
            const folderInput = document.getElementById('media-picker-upload-folder');
            if (uploadModal && folderInput) {
                folderInput.value = currentMediaPath;
                uploadModal.style.display = 'flex';
            }
        };
        
        window.showMediaUpload = function() {
            const uploadModal = document.getElementById('cms-media-picker-upload');
            const folderInput = document.getElementById('media-picker-upload-folder');
            if (uploadModal && folderInput) {
                folderInput.value = currentMediaPath;
                uploadModal.style.display = 'flex';
            }
        };
        
        window.showMediaUpload = function() {
            const uploadModal = document.getElementById('cms-media-picker-upload');
            const folderInput = document.getElementById('media-picker-upload-folder');
            if (uploadModal && folderInput) {
                folderInput.value = currentMediaPath;
                uploadModal.style.display = 'flex';
            }
        };
        
        window.hideMediaUpload = function() {
            const uploadModal = document.getElementById('cms-media-picker-upload');
            if (uploadModal) {
                uploadModal.style.display = 'none';
            }
        };
        
        // Handle upload form submission
        const uploadForm = document.getElementById('media-picker-upload-form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'upload');
                
                const progressDiv = document.getElementById('media-picker-upload-progress');
                const progressFill = document.getElementById('media-picker-upload-progress-fill');
                const statusText = document.getElementById('media-picker-upload-status');
                
                if (progressDiv) progressDiv.style.display = 'block';
                if (statusText) statusText.textContent = 'Uploading...';
                
                fetch(<?php echo json_encode(CMS_URL . '/panel/actions/media.php'); ?>, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (statusText) statusText.textContent = 'Upload complete!';
                        setTimeout(() => {
                            hideMediaUpload();
                            loadMediaPicker(currentMediaPath);
                            loadMediaPickerFolders(); // Reload sidebar in case folder structure changed
                        }, 500);
                    } else {
                        if (statusText) statusText.textContent = data.message || 'Upload failed';
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    if (statusText) statusText.textContent = 'Upload failed';
                });
            });
        }
        
        function getFolderIcon() {
            return '<svg width="64" height="64" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.6914 1C12.0699 1.00016 12.4156 1.21422 12.585 1.55273L13.9473 4.27637L13.9863 4.38477C13.9952 4.42241 14 4.46105 14 4.5V13C14 13.5523 13.5523 14 13 14H2C1.44772 14 1 13.5523 1 13V4.5C1 4.42238 1.01802 4.34579 1.05273 4.27637L2.41504 1.55273L2.48633 1.43164C2.6712 1.16394 2.97741 1.00014 3.30859 1H11.6914ZM2 13H13V5H2V13ZM9.5 7C9.77614 7 10 7.22386 10 7.5C10 7.77614 9.77614 8 9.5 8H5.5C5.22386 8 5 7.77614 5 7.5C5 7.22386 5.22386 7 5.5 7H9.5ZM2.30859 4H7V2H3.30859L2.30859 4ZM8 4H12.6914L11.6914 2H8V4Z" fill="currentColor"/></svg>';
        }
        
        function getFileIcon() {
            return '<svg width="48" height="48" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.5 1C3.22386 1 3 1.22386 3 1.5V13.5C3 13.7761 3.22386 14 3.5 14H11.5C11.7761 14 12 13.7761 12 13.5V4.70711L8.29289 1H3.5ZM4 2H8V4.5C8 4.77614 8.22386 5 8.5 5H11V13H4V2ZM8.29289 4L10.2929 2H9V3.5C9 3.77614 8.77614 4 8.5 4H8.29289Z" fill="currentColor"/></svg>';
        }
        
        window.showMediaPickerFolder = function() {
            const modal = document.getElementById('cms-media-picker-folder');
            const folderPathInput = document.getElementById('media-picker-folder-path');
            if (modal && folderPathInput) {
                folderPathInput.value = currentMediaPath || '/';
                modal.style.display = 'flex';
            }
        };
        
        window.hideMediaPickerFolder = function() {
            const modal = document.getElementById('cms-media-picker-folder');
            if (modal) {
                modal.style.display = 'none';
                document.getElementById('media-picker-folder-form').reset();
            }
        };
        
        // Handle folder form submission
        document.getElementById('media-picker-folder-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'create_folder');
            
            try {
                const response = await fetch(<?php echo json_encode(CMS_URL . '/panel/actions/media.php'); ?>, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    toast.success('Folder created successfully');
                    hideMediaPickerFolder();
                    loadMediaPickerFolders(); // Reload sidebar
                    loadMediaPicker(currentMediaPath); // Reload current folder
                } else {
                    toast.error(result.error || 'Failed to create folder');
                }
            } catch (error) {
                console.error('Create folder error:', error);
                toast.error('An error occurred while creating the folder');
            }
        });
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    })();
    </script>
</body>
</html>

