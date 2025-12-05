/**
 * Panel JavaScript
 * Client-side interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar collapse/expand functionality
    const nav = document.getElementById('cms-nav');
    const navToggle = document.getElementById('cms-nav-toggle');
    const main = document.querySelector('.cms-main');
    
    if (nav && navToggle) {
        // Get computed CSS values
        const getComputedValue = (property) => {
            return getComputedStyle(document.documentElement).getPropertyValue(property).trim();
        };
        
        const navWidth = getComputedValue('--nav-width');
        const navWidthCollapsed = getComputedValue('--nav-width-collapsed');
        const space4 = getComputedValue('--space-4');
        
        // Check if mobile (define once)
        const isMobile = () => window.matchMedia('(max-width: 768px)').matches;
        
        // Load saved state (only applies on desktop)
        const savedState = localStorage.getItem('cms-nav-collapsed');
        
        // Remove preload style if it exists (cleanup)
        const preloadStyle = document.getElementById('cms-nav-collapsed-preload');
        if (preloadStyle) {
            preloadStyle.remove();
        }
        
        // On mobile, always force collapsed state
        if (isMobile()) {
            nav.classList.add('collapsed');
        } else {
            // On desktop, use saved state
            if (savedState === 'true') {
                // Apply collapsed state
                if (!nav.classList.contains('collapsed')) {
                    nav.classList.add('collapsed');
                }
            } else {
                // Ensure expanded state
                if (nav.classList.contains('collapsed')) {
                    nav.classList.remove('collapsed');
                }
            }
        }
        
        // Toggle sidebar - only respond to toggle button clicks directly
        // Disable toggle on mobile (screens <= 768px)
        
        navToggle.addEventListener('click', function(e) {
            // Disable toggle functionality on mobile
            if (isMobile()) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            
            // Only proceed if clicking directly on the toggle button or its icon
            const isToggleButton = e.target === navToggle || 
                                   e.target.closest('.cms-nav-toggle') === navToggle ||
                                   navToggle.contains(e.target);
            
            if (!isToggleButton) {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            nav.classList.toggle('collapsed');
            
            // Save state
            const isCollapsed = nav.classList.contains('collapsed');
            localStorage.setItem('cms-nav-collapsed', isCollapsed);
        });
        
        // Force collapsed state on mobile
        const handleMobileResize = () => {
            if (isMobile()) {
                nav.classList.add('collapsed');
            } else {
                // Restore saved state on desktop
                const savedState = localStorage.getItem('cms-nav-collapsed');
                if (savedState === 'true') {
                    nav.classList.add('collapsed');
                } else {
                    nav.classList.remove('collapsed');
                }
            }
        };
        
        // Check on load and resize
        handleMobileResize();
        window.addEventListener('resize', handleMobileResize);
        
        // Prevent nav links from triggering collapse - add to all links
        const navLinks = nav.querySelectorAll('.cms-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Stop propagation to prevent any toggle
                e.stopPropagation();
                e.stopImmediatePropagation();
                // Allow navigation to proceed normally
            });
        });
        
        // Prevent nav from toggling when clicking links
        nav.addEventListener('click', function(e) {
            if (e.target.closest('.cms-nav-link')) {
                e.stopPropagation();
            }
        });
    }
    
    // Switch/Toggle functionality (includes all switches - blueprint fields and core featured switch)
    document.querySelectorAll('.cms-switch').forEach(switchElement => {
        const wrapper = switchElement.closest('.cms-switch-wrapper');
        if (!wrapper) return;
        
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        if (!hiddenInput) return;
        
        // Check if already initialized
        if (switchElement.hasAttribute('data-switch-initialized')) return;
        switchElement.setAttribute('data-switch-initialized', 'true');
        
        // Toggle switch function
        const toggleSwitch = () => {
            const isChecked = switchElement.getAttribute('aria-checked') === 'true';
            const newState = !isChecked;
            
            switchElement.setAttribute('aria-checked', newState ? 'true' : 'false');
            switchElement.setAttribute('data-state', newState ? 'checked' : 'unchecked');
            hiddenInput.value = newState ? '1' : '0';
            
            // Trigger change event
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        };
        
        // Click handler
        switchElement.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSwitch();
        });
        
        // Keyboard handler
        switchElement.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                e.stopPropagation();
                toggleSwitch();
            }
        });
    });
    
    // Table sorting functionality
    document.querySelectorAll('.cms-table-sortable').forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const sortColumn = this.getAttribute('data-sort');
            const sortType = this.getAttribute('data-sort-type') || 'text';
            const currentDirection = this.getAttribute('data-sort-direction') || 'none';
            
            // Determine new sort direction
            let newDirection = 'asc';
            if (currentDirection === 'asc') {
                newDirection = 'desc';
            } else if (currentDirection === 'desc') {
                newDirection = 'asc';
            }
            
            // Remove sort indicators from all headers
            table.querySelectorAll('.cms-table-sortable').forEach(h => {
                h.removeAttribute('data-sort-direction');
            });
            
            // Set sort direction on clicked header
            if (newDirection !== 'none') {
                this.setAttribute('data-sort-direction', newDirection);
            }
            
            // Get the column index
            const headerIndex = Array.from(this.parentElement.children).indexOf(this);
            
            // Sort rows
            rows.sort((a, b) => {
                const aCells = a.querySelectorAll('td');
                const bCells = b.querySelectorAll('td');
                
                if (!aCells[headerIndex] || !bCells[headerIndex]) return 0;
                
                let aValue = aCells[headerIndex].getAttribute('data-sort-value') || '';
                let bValue = bCells[headerIndex].getAttribute('data-sort-value') || '';
                
                // Convert values based on sort type
                if (sortType === 'number') {
                    aValue = parseFloat(aValue) || 0;
                    bValue = parseFloat(bValue) || 0;
                } else if (sortType === 'date') {
                    // Handle timestamps (numbers) or ISO date strings
                    aValue = aValue ? (isNaN(aValue) ? new Date(aValue).getTime() : parseFloat(aValue)) : 0;
                    bValue = bValue ? (isNaN(bValue) ? new Date(bValue).getTime() : parseFloat(bValue)) : 0;
                } else if (sortType === 'boolean') {
                    aValue = aValue === '1' || aValue === 'true' || aValue === true;
                    bValue = bValue === '1' || bValue === 'true' || bValue === true;
                } else if (sortType === 'text') {
                    // Case-insensitive text comparison
                    aValue = (aValue || '').toString().toLowerCase();
                    bValue = (bValue || '').toString().toLowerCase();
                }
                
                // Compare values
                let comparison = 0;
                if (aValue < bValue) {
                    comparison = -1;
                } else if (aValue > bValue) {
                    comparison = 1;
                }
                
                // Reverse if descending
                return newDirection === 'desc' ? -comparison : comparison;
            });
            
            // Reorder rows in DOM
            rows.forEach(row => tbody.appendChild(row));
        });
    });
    
    // Form submissions
    const forms = document.querySelectorAll('.cms-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Add loading state
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Saving...';
                
                // Reset on error (you might want to handle this differently)
                setTimeout(() => {
                    if (submitButton.disabled) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }
                }, 5000);
            }
        });
    });
    
    // Confirm logout
    const logoutLinks = document.querySelectorAll('.cms-nav-link-logout');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            dialog.confirm('Are you sure you want to logout?', 'Logout')
                .then(confirmed => {
                    if (confirmed) {
                        window.location.href = href;
                    }
                });
        });
    });
    
    // Tab functionality
    const tabTriggers = document.querySelectorAll('.cms-tabs-trigger');
    const tabContents = document.querySelectorAll('.cms-tabs-content');
    
    // Initialize tabs on page load - ensure only active tab is visible
    let hasActiveTab = false;
    tabContents.forEach((content, index) => {
        const isActive = content.classList.contains('active') || content.getAttribute('data-state') === 'active';
        if (isActive) {
            content.style.display = 'block';
            content.classList.add('active');
            content.setAttribute('data-state', 'active');
            hasActiveTab = true;
        } else {
            content.style.display = 'none';
            content.classList.remove('active');
            content.setAttribute('data-state', 'inactive');
        }
    });
    
    // If no tab is active, activate the first one
    if (!hasActiveTab && tabContents.length > 0) {
        const firstContent = tabContents[0];
        const firstTrigger = document.getElementById('cms-tab-trigger-' + firstContent.getAttribute('data-tab'));
        if (firstContent && firstTrigger) {
            firstContent.style.display = 'block';
            firstContent.classList.add('active');
            firstContent.setAttribute('data-state', 'active');
            firstTrigger.classList.add('active');
            firstTrigger.setAttribute('aria-selected', 'true');
            firstTrigger.setAttribute('data-state', 'active');
        }
    }
    
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tabId = this.getAttribute('data-tab');
            const targetContent = document.getElementById('cms-tab-content-' + tabId);
            
            if (!targetContent) return;
            
            // Remove active state from all triggers and contents
            tabTriggers.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
                t.setAttribute('data-state', 'inactive');
                t.setAttribute('tabindex', '-1');
            });
            tabContents.forEach(c => {
                c.classList.remove('active');
                c.setAttribute('data-state', 'inactive');
                c.setAttribute('tabindex', '-1');
                c.style.display = 'none'; // Explicitly hide inactive tabs
            });
            
            // Add active state to clicked trigger and corresponding content
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            this.setAttribute('data-state', 'active');
            this.removeAttribute('tabindex');
            targetContent.classList.add('active');
            targetContent.setAttribute('data-state', 'active');
            targetContent.setAttribute('tabindex', '0');
            targetContent.style.display = 'block'; // Explicitly show active tab
        });
    });
    
    // Custom Dropdown functionality
    document.querySelectorAll('.cms-dropdown').forEach(dropdown => {
        const fieldName = dropdown.getAttribute('data-field');
        if (!fieldName) return; // Skip if no data-field attribute
        
        const trigger = dropdown.querySelector('[data-dropdown-trigger="' + fieldName + '"]');
        const content = dropdown.querySelector('[data-dropdown-content="' + fieldName + '"]');
        const hiddenInput = dropdown.querySelector('input[type="hidden"]');
        
        if (!trigger || !content || !hiddenInput) return;
        
        const valueDisplay = trigger.querySelector('.cms-dropdown-value');
        if (!valueDisplay) return; // Make sure value display exists
        
        // Check if already initialized
        if (trigger.hasAttribute('data-dropdown-initialized')) return;
        trigger.setAttribute('data-dropdown-initialized', 'true');
        
        // Toggle dropdown
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = this.getAttribute('aria-expanded') === 'true';
            
            // Close all other dropdowns
            document.querySelectorAll('.cms-dropdown-trigger').forEach(t => {
                if (t !== trigger) {
                    t.setAttribute('aria-expanded', 'false');
                    const otherContent = t.closest('.cms-dropdown').querySelector('.cms-dropdown-content');
                    if (otherContent) {
                        otherContent.style.display = 'none';
                    }
                }
            });
            
            // Toggle this dropdown
            if (isOpen) {
                this.setAttribute('aria-expanded', 'false');
                content.style.display = 'none';
            } else {
                this.setAttribute('aria-expanded', 'true');
                content.style.display = 'block';
                // Focus first item
                const firstItem = content.querySelector('.cms-dropdown-item');
                if (firstItem) {
                    firstItem.focus();
                }
            }
        });
        
        // Keyboard navigation on trigger
        trigger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (this.getAttribute('aria-expanded') !== 'true') {
                    this.click();
                }
            }
        });
        
        // Handle item selection
        const items = content.querySelectorAll('[data-dropdown-item="' + fieldName + '"]');
        items.forEach((item, index) => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const value = this.getAttribute('data-value');
                const labelSpan = this.querySelector('span');
                const label = labelSpan ? labelSpan.textContent : '';
                
                // Update hidden input
                hiddenInput.value = value || '';
                
                // Update display
                if (value && label && label !== '-- Select --') {
                    valueDisplay.textContent = label;
                    valueDisplay.classList.remove('cms-dropdown-placeholder');
                } else {
                    // Show placeholder for empty selection
                    const dropdown = trigger.closest('.cms-dropdown');
                    const hasPlaceholder = dropdown.querySelector('[data-value=""]');
                    if (hasPlaceholder) {
                        valueDisplay.textContent = '-- Select --';
                        valueDisplay.classList.add('cms-dropdown-placeholder');
                    } else {
                        valueDisplay.textContent = '';
                        valueDisplay.classList.add('cms-dropdown-placeholder');
                    }
                }
                
                // Update selected state
                items.forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                
                // Close dropdown
                trigger.setAttribute('aria-expanded', 'false');
                content.style.display = 'none';
                
                // Trigger change event for form validation
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
            
            // Keyboard navigation
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = (index + 1) % items.length;
                    items[nextIndex].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = (index - 1 + items.length) % items.length;
                    items[prevIndex].focus();
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    trigger.setAttribute('aria-expanded', 'false');
                    content.style.display = 'none';
                    trigger.focus();
                }
            });
            
            // Make items focusable
            item.setAttribute('tabindex', '-1');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cms-dropdown')) {
            document.querySelectorAll('.cms-dropdown-trigger').forEach(trigger => {
                trigger.setAttribute('aria-expanded', 'false');
                const content = trigger.closest('.cms-dropdown').querySelector('.cms-dropdown-content');
                if (content) {
                    content.style.display = 'none';
                }
            });
        }
    });
    
    // Close dropdowns on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cms-dropdown-trigger').forEach(trigger => {
                trigger.setAttribute('aria-expanded', 'false');
                const content = trigger.closest('.cms-dropdown').querySelector('.cms-dropdown-content');
                if (content) {
                    content.style.display = 'none';
                }
            });
        }
    });
    
    // Slider functionality
    document.querySelectorAll('.cms-slider').forEach(slider => {
        const fieldName = slider.getAttribute('data-slider');
        const wrapper = slider.closest('.cms-slider-wrapper');
        const thumb = wrapper.querySelector('[data-slider-thumb="' + fieldName + '"]');
        const track = slider.querySelector('.cms-slider-track');
        const range = slider.querySelector('.cms-slider-range');
        const valueDisplay = wrapper.querySelector('[data-slider-value="' + fieldName + '"]');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        
        if (!thumb || !track || !range || !hiddenInput) return;
        
        const min = parseFloat(slider.getAttribute('aria-valuemin')) || 0;
        const max = parseFloat(slider.getAttribute('aria-valuemax')) || 100;
        const step = parseFloat(slider.getAttribute('data-step')) || 1;
        
        let isDragging = false;
        let currentValue = parseFloat(hiddenInput.value) || min;
        
        // Update slider position and value
        const updateSlider = (value) => {
            currentValue = Math.max(min, Math.min(max, value));
            const percentage = ((currentValue - min) / (max - min)) * 100;
            
            thumb.style.left = percentage + '%';
            range.style.width = percentage + '%';
            hiddenInput.value = currentValue;
            slider.setAttribute('aria-valuenow', currentValue);
            
            if (valueDisplay) {
                valueDisplay.textContent = Math.round(currentValue * 100) / 100; // Round to 2 decimals
            }
        };
        
        // Get value from position
        const getValueFromPosition = (clientX) => {
            const rect = track.getBoundingClientRect();
            const percentage = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
            let value = min + (percentage * (max - min));
            
            // Snap to step
            if (step > 0) {
                value = Math.round(value / step) * step;
            }
            
            return Math.max(min, Math.min(max, value));
        };
        
        // Mouse/Touch drag handlers
        const startDrag = (clientX) => {
            isDragging = true;
            thumb.classList.add('dragging');
            slider.focus();
            const value = getValueFromPosition(clientX);
            updateSlider(value);
        };
        
        const onDrag = (clientX) => {
            if (!isDragging) return;
            const value = getValueFromPosition(clientX);
            updateSlider(value);
        };
        
        const endDrag = () => {
            isDragging = false;
            thumb.classList.remove('dragging');
            
            // Trigger change event
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        };
        
        // Mouse events
        slider.addEventListener('mousedown', function(e) {
            e.preventDefault();
            if (e.target === thumb || e.target === track || e.target === range) {
                startDrag(e.clientX);
            }
        });
        
        document.addEventListener('mousemove', function(e) {
            if (isDragging) {
                onDrag(e.clientX);
            }
        });
        
        document.addEventListener('mouseup', function() {
            if (isDragging) {
                endDrag();
            }
        });
        
        // Touch events
        slider.addEventListener('touchstart', function(e) {
            e.preventDefault();
            const touch = e.touches[0];
            startDrag(touch.clientX);
        });
        
        document.addEventListener('touchmove', function(e) {
            if (isDragging) {
                e.preventDefault();
                const touch = e.touches[0];
                onDrag(touch.clientX);
            }
        });
        
        document.addEventListener('touchend', function() {
            if (isDragging) {
                endDrag();
            }
        });
        
        // Keyboard navigation
        slider.addEventListener('keydown', function(e) {
            let newValue = currentValue;
            
            switch(e.key) {
                case 'ArrowRight':
                case 'ArrowUp':
                    e.preventDefault();
                    newValue = Math.min(max, currentValue + step);
                    break;
                case 'ArrowLeft':
                case 'ArrowDown':
                    e.preventDefault();
                    newValue = Math.max(min, currentValue - step);
                    break;
                case 'Home':
                    e.preventDefault();
                    newValue = min;
                    break;
                case 'End':
                    e.preventDefault();
                    newValue = max;
                    break;
                default:
                    return;
            }
            
            updateSlider(newValue);
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
        
        // Click on track to jump to position
        track.addEventListener('click', function(e) {
            if (e.target !== thumb) {
                const value = getValueFromPosition(e.clientX);
                updateSlider(value);
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
    
    // Switch/Toggle functionality is handled above in the main DOMContentLoaded
    
});

// Structure Component Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Store structure field definitions (from PHP)
    const structureFieldDefs = {};
    
    // Handle opening modal for adding new item
    document.querySelectorAll('[data-structure-add]').forEach(button => {
        button.addEventListener('click', function() {
            const fieldName = this.getAttribute('data-structure-add');
            openStructureModal(fieldName, null);
        });
    });
    
    // Handle opening modal for editing existing item
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('[data-structure-edit]');
        if (editBtn) {
            const fieldName = editBtn.getAttribute('data-structure-edit');
            const index = editBtn.getAttribute('data-index');
            openStructureModal(fieldName, index);
        }
    });
    
    // Handle closing modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-structure-modal-close]')) {
            const closeBtn = e.target.closest('[data-structure-modal-close]');
            const fieldName = closeBtn.getAttribute('data-structure-modal-close');
            closeStructureModal(fieldName);
        }
    });
    
    // Handle form submission in modal (only for structure modals)
    document.addEventListener('submit', function(e) {
        // Only handle structure modal forms
        const form = e.target.closest('.cms-structure-item-form');
        if (form) {
            console.log('Structure modal form submit intercepted');
            e.preventDefault();
            e.stopPropagation();
            const fieldName = form.getAttribute('data-field');
            saveStructureItem(fieldName);
            return false;
        }
        // Don't prevent default for other forms - let them handle their own submission
        // Make sure we're not catching the main content form
        if (e.target.id === 'content-form' || e.target.classList.contains('cms-form')) {
            console.log('Main form submit detected, allowing it to proceed');
            // Let the page-specific handler deal with it
            return true;
        }
    }, false); // Use bubble phase, not capture
    
    // Handle deleting structure items
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('[data-structure-delete]');
        if (deleteBtn) {
            const fieldName = deleteBtn.getAttribute('data-structure-delete');
            const index = deleteBtn.getAttribute('data-index');
            const wrapper = document.querySelector(`[data-field="${fieldName}"]`);
            const hiddenInput = wrapper.querySelector(`input[type="hidden"][name="${fieldName}"]`);
            
            if (!hiddenInput || index === null) return;
            
            dialog.confirm('Are you sure you want to delete this item?', 'Delete Item').then(confirmed => {
                if (confirmed) {
                    // Get current data
                    let allData = [];
                    try {
                        allData = JSON.parse(hiddenInput.value || '[]');
                    } catch (e) {
                        console.error('Error parsing structure data:', e);
                        allData = [];
                    }
                    
                    // Remove item at index
                    const itemIndex = parseInt(index);
                    if (itemIndex >= 0 && itemIndex < allData.length) {
                        allData.splice(itemIndex, 1);
                        
                        // Update hidden input
                        hiddenInput.value = JSON.stringify(allData);
                        
                        // Refresh table
                        refreshStructureTable(fieldName);
                    }
                }
            });
        }
    });
    
    // Function to open structure modal
    function openStructureModal(fieldName, itemIndex) {
        const modal = document.getElementById(`cms-structure-modal-${fieldName}`);
        const formContainer = modal.querySelector('[data-structure-form-container]');
        const title = modal.querySelector('[data-structure-modal-title]');
        const wrapper = document.querySelector(`[data-field="${fieldName}"]`);
        const formTemplate = wrapper.querySelector(`template.cms-structure-form-template[data-field="${fieldName}"]`);
        const tbody = wrapper.querySelector(`tbody[data-field="${fieldName}"]`);
        const hiddenInput = wrapper.querySelector(`input[type="hidden"][name="${fieldName}"]`);
        
        if (!modal || !formContainer || !formTemplate) return;
        
        // Set title
        if (itemIndex === null) {
            title.textContent = 'Add Item';
        } else {
            title.textContent = 'Edit Item';
        }
        
        // Get existing data if editing
        let itemData = {};
        if (itemIndex !== null) {
            try {
                const allData = JSON.parse(hiddenInput.value || '[]');
                const index = parseInt(itemIndex);
                if (allData[index] !== undefined) {
                    itemData = allData[index];
                }
            } catch (e) {
                console.error('Error parsing structure data:', e);
            }
        }
        
        // Clone form template and populate with data
        const formHtml = formTemplate.innerHTML;
        formContainer.innerHTML = formHtml;
        
        // Populate form fields with existing data
        if (Object.keys(itemData).length > 0) {
            formContainer.querySelectorAll('[name]').forEach(input => {
                const fieldNameAttr = input.name;
                if (itemData.hasOwnProperty(fieldNameAttr)) {
                    if (input.type === 'checkbox') {
                        input.checked = Array.isArray(itemData[fieldNameAttr]) && itemData[fieldNameAttr].includes(input.value);
                    } else if (input.type === 'radio') {
                        input.checked = input.value === itemData[fieldNameAttr];
                    } else if (input.type === 'hidden' && input.id && input.id.endsWith('_tags')) {
                        // Tags field
                        const tagsValue = Array.isArray(itemData[fieldNameAttr]) ? itemData[fieldNameAttr] : [];
                        input.value = JSON.stringify(tagsValue);
                    } else if (input.classList && input.classList.contains('cms-markdown-textarea-hidden')) {
                        input.value = itemData[fieldNameAttr] || '';
                    } else {
                        input.value = itemData[fieldNameAttr] || '';
                    }
                }
            });
            
            // Handle markdown editors
            formContainer.querySelectorAll('.cms-markdown-visual-editor').forEach(editor => {
                const textarea = editor.parentElement.querySelector('.cms-markdown-textarea-hidden');
                if (textarea && itemData[textarea.name]) {
                    if (typeof window.markdownToHtml === 'function') {
                        editor.innerHTML = window.markdownToHtml(itemData[textarea.name]);
                    } else {
                        editor.textContent = itemData[textarea.name];
                    }
                }
            });
            
            // Handle tags - initialize them
            formContainer.querySelectorAll('.cms-tags-selector').forEach(selector => {
                const hiddenInput = selector.querySelector('input[type="hidden"]');
                if (hiddenInput && itemData[hiddenInput.name]) {
                    const values = Array.isArray(itemData[hiddenInput.name]) ? itemData[hiddenInput.name] : [];
                    hiddenInput.value = JSON.stringify(values);
                }
            });
            
            // Handle dropdowns
            formContainer.querySelectorAll('.cms-dropdown').forEach(dropdown => {
                const hiddenInput = dropdown.querySelector('input[type="hidden"]');
                if (hiddenInput && itemData[hiddenInput.name]) {
                    const value = itemData[hiddenInput.name];
                    hiddenInput.value = value;
                    
                    // Update trigger display
                    const trigger = dropdown.querySelector('.cms-dropdown-trigger');
                    const valueSpan = trigger.querySelector('.cms-dropdown-value');
                    const options = {};
                    dropdown.querySelectorAll('.cms-dropdown-item').forEach(item => {
                        options[item.getAttribute('data-value')] = item.querySelector('span').textContent;
                    });
                    
                    if (options[value]) {
                        valueSpan.textContent = options[value];
                        valueSpan.classList.remove('cms-dropdown-placeholder');
                    }
                }
            });
            
            // Handle switches
            formContainer.querySelectorAll('.cms-switch').forEach(switchBtn => {
                const wrapper = switchBtn.closest('.cms-switch-wrapper');
                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                if (hiddenInput && itemData.hasOwnProperty(hiddenInput.name)) {
                    const checked = itemData[hiddenInput.name] === true || itemData[hiddenInput.name] === 'true' || itemData[hiddenInput.name] === '1' || itemData[hiddenInput.name] === 'on';
                    switchBtn.setAttribute('aria-checked', checked ? 'true' : 'false');
                    switchBtn.setAttribute('data-state', checked ? 'checked' : 'unchecked');
                    hiddenInput.value = checked ? '1' : '0';
                }
            });
        }
        
        // Store item index for later use
        formContainer.setAttribute('data-editing-index', itemIndex !== null ? itemIndex : '');
        
        // Initialize all form components (use existing initialization functions)
        initializeFormComponents(formContainer);
        
        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Function to close structure modal
    function closeStructureModal(fieldName) {
        const modal = document.getElementById(`cms-structure-modal-${fieldName}`);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Function to save structure item
    function saveStructureItem(fieldName) {
        const modal = document.getElementById(`cms-structure-modal-${fieldName}`);
        const formContainer = modal.querySelector('[data-structure-form-container]');
        const form = modal.querySelector('.cms-structure-item-form');
        const itemIndex = formContainer.getAttribute('data-editing-index');
        const wrapper = document.querySelector(`[data-field="${fieldName}"]`);
        const hiddenInput = wrapper.querySelector(`input[type="hidden"][name="${fieldName}"]`);
        
        // Collect form data using the same logic as main form
        const itemData = {};
        formContainer.querySelectorAll('[name]').forEach(input => {
            const name = input.name;
            
            // Skip CSRF and system fields
            if (name === 'csrf_token' || name.includes('_current') || name.includes('_tags')) {
                return;
            }
            
            // Handle different input types
            if (input.type === 'checkbox') {
                if (!itemData[name]) itemData[name] = [];
                if (input.checked) itemData[name].push(input.value);
            } else if (input.type === 'radio') {
                if (input.checked) {
                    itemData[name] = input.value;
                }
            } else if (input.classList && input.classList.contains('cms-switch')) {
                itemData[name] = input.getAttribute('aria-checked') === 'true' ? '1' : '0';
            } else if (input.classList && input.classList.contains('cms-markdown-textarea-hidden')) {
                itemData[name] = input.value || '';
            } else if (input.type === 'file') {
                // Skip file inputs for now
                return;
            } else if (input.type === 'hidden' && input.closest('.cms-dropdown')) {
                itemData[name] = input.value || '';
            } else if (input.type === 'hidden' && input.closest('.cms-tags-selector')) {
                try {
                    itemData[name] = JSON.parse(input.value || '[]');
                } catch (e) {
                    itemData[name] = [];
                }
            } else {
                itemData[name] = input.value || '';
            }
        });
        
        // Handle tags separately
        formContainer.querySelectorAll('input[id$="_tags"]').forEach(hiddenInput => {
            const fieldNameAttr = hiddenInput.name.replace('_tags', '');
            try {
                itemData[fieldNameAttr] = JSON.parse(hiddenInput.value || '[]');
            } catch (e) {
                itemData[fieldNameAttr] = [];
            }
        });
        
        // Get existing data
        let allData = [];
        try {
            allData = JSON.parse(hiddenInput.value || '[]');
        } catch (e) {
            allData = [];
        }
        
        // Update or add item
        if (itemIndex !== null && itemIndex !== '') {
            // Find and update existing item
            const index = parseInt(itemIndex);
            if (allData[index] !== undefined) {
                allData[index] = itemData;
            }
        } else {
            // Add new item
            allData.push(itemData);
        }
        
        // Update hidden input
        hiddenInput.value = JSON.stringify(allData);
        
        // Refresh table
        refreshStructureTable(fieldName);
        
        // Close modal
        closeStructureModal(fieldName);
    }
    
    // Function to refresh structure table
    function refreshStructureTable(fieldName) {
        const wrapper = document.querySelector(`[data-field="${fieldName}"]`);
        const tbody = wrapper.querySelector(`tbody[data-field="${fieldName}"]`);
        const hiddenInput = wrapper.querySelector(`input[type="hidden"][name="${fieldName}"]`);
        const rowTemplate = wrapper.querySelector(`template.cms-structure-row-template[data-field="${fieldName}"]`);
        
        if (!tbody || !hiddenInput || !rowTemplate) return;
        
        // Get visible field names from data attribute
        let visibleFields = [];
        try {
            visibleFields = JSON.parse(wrapper.getAttribute('data-visible-fields') || '[]');
        } catch (e) {
            // Fallback: get from headers
            wrapper.querySelectorAll('thead th').forEach(th => {
                if (!th.classList.contains('cms-structure-actions-header')) {
                    const headerText = th.textContent.trim();
                    visibleFields.push(headerText.toLowerCase().replace(/\s+/g, '_'));
                }
            });
        }
        
        // Get data
        let data = [];
        try {
            data = JSON.parse(hiddenInput.value || '[]');
        } catch (e) {
            data = [];
        }
        
        // Clear tbody
        tbody.innerHTML = '';
        
        // Render rows
        if (data.length === 0) {
            const visibleFieldsCount = visibleFields.length;
            const emptyRow = document.createElement('tr');
            emptyRow.className = 'cms-structure-empty';
            emptyRow.innerHTML = `<td colspan="${visibleFieldsCount + 1}" class="cms-structure-empty-cell">No items yet. Click "Add item" to get started.</td>`;
            tbody.appendChild(emptyRow);
        } else {
            data.forEach((itemData, index) => {
                const rowHtml = rowTemplate.innerHTML.replace(/__INDEX__/g, index);
                const temp = document.createElement('tbody');
                temp.innerHTML = rowHtml;
                const row = temp.querySelector('tr');
                
                if (row) {
                    row.setAttribute('data-index', index);
                    
                    // Update preview cells with actual data
                    row.querySelectorAll('.cms-structure-preview-cell').forEach((cell, cellIndex) => {
                        if (cellIndex < visibleFields.length) {
                            const fieldNameAttr = visibleFields[cellIndex];
                            const value = itemData[fieldNameAttr] || '';
                            let preview = '';
                            
                            // Format preview based on value type
                            if (typeof value === 'boolean' || value === '1' || value === '0' || value === 'true' || value === 'false') {
                                preview = (value === true || value === '1' || value === 'true') ? '✓' : '—';
                            } else if (Array.isArray(value)) {
                                preview = value.slice(0, 3).join(', ');
                                if (value.length > 3) preview += '...';
                            } else if (typeof value === 'string') {
                                preview = value.replace(/<[^>]*>/g, '').substring(0, 50);
                                if (value.length > 50) preview += '...';
                            } else {
                                preview = String(value).substring(0, 50);
                                if (String(value).length > 50) preview += '...';
                            }
                            
                            const previewSpan = cell.querySelector('.cms-structure-preview');
                            if (previewSpan) {
                                previewSpan.textContent = preview || '—';
                            }
                        }
                    });
                    
                    // Update edit/delete buttons
                    const editBtn = row.querySelector('[data-structure-edit]');
                    const deleteBtn = row.querySelector('[data-structure-delete]');
                    if (editBtn) editBtn.setAttribute('data-index', index);
                    if (deleteBtn) deleteBtn.setAttribute('data-index', index);
                    
                    tbody.appendChild(row);
                }
            });
        }
    }
    
    // Function to initialize form components (reuse existing functionality)
    // Make it globally accessible
    window.initializeFormComponents = function(container) {
        // Initialize dropdowns (reuse existing dropdown initialization from panel.js)
        container.querySelectorAll('.cms-dropdown').forEach(dropdown => {
            const fieldName = dropdown.getAttribute('data-field');
            const trigger = dropdown.querySelector('.cms-dropdown-trigger');
            const content = dropdown.querySelector('.cms-dropdown-content');
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            
            if (!trigger || !content || !hiddenInput) return;
            
            // Check if already initialized
            if (trigger.hasAttribute('data-initialized')) return;
            trigger.setAttribute('data-initialized', 'true');
            
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = content.style.display !== 'none';
                content.style.display = isOpen ? 'none' : 'block';
                trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });
            
            content.querySelectorAll('.cms-dropdown-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const value = this.getAttribute('data-value');
                    const label = this.querySelector('span').textContent;
                    
                    hiddenInput.value = value;
                    trigger.querySelector('.cms-dropdown-value').textContent = label;
                    trigger.querySelector('.cms-dropdown-value').classList.remove('cms-dropdown-placeholder');
                    
                    content.querySelectorAll('.cms-dropdown-item').forEach(i => i.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    content.style.display = 'none';
                    trigger.setAttribute('aria-expanded', 'false');
                });
            });
            
            // Close on outside click
            document.addEventListener('click', function closeDropdown(e) {
                if (!dropdown.contains(e.target)) {
                    content.style.display = 'none';
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
        
        // Initialize switches (reuse existing switch initialization)
        container.querySelectorAll('.cms-switch[data-switch]').forEach(switchBtn => {
            const wrapper = switchBtn.closest('.cms-switch-wrapper');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            
            if (!hiddenInput) return;
            
            // Check if already initialized
            if (switchBtn.hasAttribute('data-initialized')) return;
            switchBtn.setAttribute('data-initialized', 'true');
            
            const toggleSwitch = () => {
                const isChecked = switchBtn.getAttribute('aria-checked') === 'true';
                const newState = !isChecked;
                
                switchBtn.setAttribute('aria-checked', newState ? 'true' : 'false');
                switchBtn.setAttribute('data-state', newState ? 'checked' : 'unchecked');
                hiddenInput.value = newState ? '1' : '0';
            };
            
            switchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSwitch();
            });
            
            switchBtn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSwitch();
                }
            });
        });
        
        // Initialize markdown editors (reuse existing markdown initialization)
        container.querySelectorAll('.cms-markdown-editor').forEach(editor => {
            const fieldName = editor.getAttribute('data-field-name');
            const visualEditor = editor.querySelector('.cms-markdown-visual-editor');
            const hiddenTextarea = editor.querySelector('.cms-markdown-textarea-hidden');
            
            if (!visualEditor || !hiddenTextarea) return;
            
            // Initialize markdown editor
            if (hiddenTextarea.value && typeof window.markdownToHtml === 'function') {
                visualEditor.innerHTML = window.markdownToHtml(hiddenTextarea.value);
            } else if (hiddenTextarea.value) {
                visualEditor.textContent = hiddenTextarea.value;
            }
            
            // Update hidden textarea on input
            visualEditor.addEventListener('input', function() {
                if (typeof window.htmlToMarkdown === 'function') {
                    hiddenTextarea.value = window.htmlToMarkdown(this.innerHTML);
                } else {
                    hiddenTextarea.value = this.textContent;
                }
            });
            
            // Initialize toolbar buttons
            editor.querySelectorAll('.cms-markdown-button').forEach(btn => {
                const command = btn.getAttribute('data-command');
                const action = btn.getAttribute('data-action');
                
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    visualEditor.focus();
                    
                    if (command) {
                        const value = btn.getAttribute('data-value');
                        if (value) {
                            document.execCommand(command, false, value);
                        } else {
                            document.execCommand(command, false);
                        }
                    } else if (action === 'link') {
                        // Handle link insertion using popover
                        const linkPopover = document.getElementById('cms-link-popover');
                        if (linkPopover && typeof window.showLinkPopover === 'function') {
                            window.showLinkPopover(btn, visualEditor, linkPopover);
                        } else {
                            // Fallback to prompt if popover not available
                            const url = prompt('Enter URL:');
                            if (url) {
                                document.execCommand('createLink', false, url);
                                if (typeof window.htmlToMarkdown === 'function') {
                                    hiddenTextarea.value = window.htmlToMarkdown(visualEditor.innerHTML);
                                } else {
                                    hiddenTextarea.value = visualEditor.textContent;
                                }
                            }
                        }
                    }
                    
                    // Update hidden textarea
                    if (typeof window.htmlToMarkdown === 'function') {
                        hiddenTextarea.value = window.htmlToMarkdown(visualEditor.innerHTML);
                    } else {
                        hiddenTextarea.value = visualEditor.textContent;
                    }
                });
            });
        });
        
        // Initialize tags selectors (reuse existing tags initialization)
        container.querySelectorAll('.cms-tags-selector').forEach(selector => {
            const fieldName = selector.getAttribute('data-field');
            const hiddenInput = selector.querySelector('input[id$="_tags"]');
            const input = selector.querySelector('.cms-tags-input');
            const selectedContainer = selector.querySelector('.cms-tags-selected');
            
            if (!hiddenInput || !input) return;
            
            // Use the same tags initialization logic from test.php/collections.php
            // This would need to be extracted to a shared function, but for now
            // we'll initialize it similarly
            const getSelectedValues = () => {
                try {
                    let values = JSON.parse(hiddenInput.value || '[]');
                    if (Array.isArray(values) && values.length > 0 && typeof values[0] === 'string') {
                        try {
                            values = JSON.parse(values[0]);
                        } catch (e) {}
                    }
                    return Array.isArray(values) ? values : [];
                } catch (e) {
                    return [];
                }
            };
            
            const updateHiddenInput = (values) => {
                hiddenInput.value = JSON.stringify(values);
            };
            
            const renderSelectedTags = () => {
                const values = getSelectedValues();
                if (!selectedContainer) return;
                
                selectedContainer.innerHTML = '';
                values.forEach(tagValue => {
                    const options = {};
                    selector.querySelectorAll('.cms-tag-button').forEach(btn => {
                        options[btn.getAttribute('data-value')] = btn.getAttribute('data-label');
                    });
                    
                    const tagLabel = options[tagValue] || tagValue;
                    const isCustom = !options[tagValue];
                    
                    const tag = document.createElement('span');
                    tag.className = 'cms-tag' + (isCustom ? ' cms-tag-custom' : '');
                    tag.setAttribute('data-value', tagValue);
                    tag.innerHTML = `
                        <span class="cms-tag-text">${tagLabel}</span>
                        <button type="button" class="cms-tag-remove" aria-label="Remove tag">
                            <svg width="12" height="12" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.8536 1.85355C12.0488 1.65829 12.0488 1.34171 11.8536 1.14645C11.6583 0.951184 11.3417 0.951184 11.1464 1.14645L7.5 4.79289L3.85355 1.14645C3.65829 0.951184 3.34171 0.951184 3.14645 1.14645C2.95118 1.34171 2.95118 1.65829 3.14645 1.85355L6.79289 5.5L3.14645 9.14645C2.95118 9.34171 2.95118 9.65829 3.14645 9.85355C3.34171 10.0488 3.65829 10.0488 3.85355 9.85355L7.5 6.20711L11.1464 9.85355C11.3417 10.0488 11.6583 10.0488 11.8536 9.85355C12.0488 9.65829 12.0488 9.34171 11.8536 9.14645L7.70711 5L11.8536 1.85355Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    `;
                    
                    tag.querySelector('.cms-tag-remove').addEventListener('click', function() {
                        const values = getSelectedValues();
                        const newValues = values.filter(v => v !== tagValue);
                        updateHiddenInput(newValues);
                        renderSelectedTags();
                        updateTagButtons();
                    });
                    
                    selectedContainer.appendChild(tag);
                });
            };
            
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
            
            const addTag = (tagValue) => {
                const values = getSelectedValues();
                if (values.includes(tagValue)) return;
                
                values.push(tagValue);
                updateHiddenInput(values);
                renderSelectedTags();
                updateTagButtons();
            };
            
            const removeTag = (tagValue) => {
                const values = getSelectedValues();
                const newValues = values.filter(v => v !== tagValue);
                updateHiddenInput(newValues);
                renderSelectedTags();
                updateTagButtons();
            };
            
            // Input handler - prevent form submission on Enter
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
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
            }, true); // Use capture phase to run before form handlers
            
            input.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value) {
                    addTag(value);
                    this.value = '';
                }
            });
            
            // Tag button handlers
            selector.querySelectorAll('.cms-tag-button').forEach(button => {
                button.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const values = getSelectedValues();
                    
                    if (values.includes(value)) {
                        removeTag(value);
                    } else {
                        addTag(value);
                    }
                });
            });
            
            // Initial render
            const initialValues = getSelectedValues();
            updateHiddenInput(initialValues);
            renderSelectedTags();
            updateTagButtons();
        });
    }
});

// Reusable function to initialize tags in dynamically loaded content
function initTagsInContainer(container) {
    if (!container) container = document;
    
    container.querySelectorAll('.cms-tags-selector').forEach(selector => {
        // Skip if already initialized (check for existing event listeners)
        if (selector.dataset.initialized === 'true') return;
        selector.dataset.initialized = 'true';
        
        const fieldName = selector.getAttribute('data-field');
        const hiddenInput = selector.querySelector('input[id$="_tags"]') || selector.querySelector('input[type="hidden"][name="' + fieldName + '"]');
        const input = selector.querySelector('.cms-tags-input');
        const selectedContainer = selector.querySelector('.cms-tags-selected');
        
        if (!hiddenInput || !input) return;
        
        const getSelectedValues = () => {
            try {
                let values = JSON.parse(hiddenInput.value || '[]');
                if (Array.isArray(values) && values.length > 0 && typeof values[0] === 'string') {
                    try {
                        values = JSON.parse(values[0]);
                    } catch (e) {}
                }
                return Array.isArray(values) ? values : [];
            } catch (e) {
                return [];
            }
        };
        
        const updateHiddenInput = (values) => {
            hiddenInput.value = JSON.stringify(values);
        };
        
        const renderSelectedTags = () => {
            const values = getSelectedValues();
            if (!selectedContainer) return;
            
            selectedContainer.innerHTML = '';
            values.forEach(tagValue => {
                const options = {};
                selector.querySelectorAll('.cms-tag-button').forEach(btn => {
                    options[btn.getAttribute('data-value')] = btn.getAttribute('data-label');
                });
                
                const tagLabel = options[tagValue] || tagValue;
                const isCustom = !options[tagValue];
                
                const tag = document.createElement('span');
                tag.className = 'cms-tag' + (isCustom ? ' cms-tag-custom' : '');
                tag.setAttribute('data-value', tagValue);
                tag.innerHTML = `
                    <span class="cms-tag-text">${tagLabel}</span>
                    <button type="button" class="cms-tag-remove" aria-label="Remove tag">
                        <svg width="12" height="12" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.8536 1.85355C12.0488 1.65829 12.0488 1.34171 11.8536 1.14645C11.6583 0.951184 11.3417 0.951184 11.1464 1.14645L7.5 4.79289L3.85355 1.14645C3.65829 0.951184 3.34171 0.951184 3.14645 1.14645C2.95118 1.34171 2.95118 1.65829 3.14645 1.85355L6.79289 5.5L3.14645 9.14645C2.95118 9.34171 2.95118 9.65829 3.14645 9.85355C3.34171 10.0488 3.65829 10.0488 3.85355 9.85355L7.5 6.20711L11.1464 9.85355C11.3417 10.0488 11.6583 10.0488 11.8536 9.85355C12.0488 9.65829 12.0488 9.34171 11.8536 9.14645L7.70711 5L11.8536 1.85355Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                `;
                
                tag.querySelector('.cms-tag-remove').addEventListener('click', function() {
                    const values = getSelectedValues();
                    const newValues = values.filter(v => v !== tagValue);
                    updateHiddenInput(newValues);
                    renderSelectedTags();
                    updateTagButtons();
                });
                
                selectedContainer.appendChild(tag);
            });
        };
        
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
        
        const addTag = (tagValue) => {
            const values = getSelectedValues();
            if (values.includes(tagValue)) return;
            
            values.push(tagValue);
            updateHiddenInput(values);
            renderSelectedTags();
            updateTagButtons();
        };
        
        const removeTag = (tagValue) => {
            const values = getSelectedValues();
            const newValues = values.filter(v => v !== tagValue);
            updateHiddenInput(newValues);
            renderSelectedTags();
            updateTagButtons();
        };
        
        // Input handler - prevent form submission on Enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
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
        }, true); // Use capture phase to run before form handlers
        
        input.addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                addTag(value);
                this.value = '';
            }
        });
        
        // Tag button handlers
        selector.querySelectorAll('.cms-tag-button').forEach(button => {
            button.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const values = getSelectedValues();
                
                if (values.includes(value)) {
                    removeTag(value);
                } else {
                    addTag(value);
                }
            });
        });
        
        // Initial render
        const initialValues = getSelectedValues();
        updateHiddenInput(initialValues);
        renderSelectedTags();
        updateTagButtons();
    });
}
