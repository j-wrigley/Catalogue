/**
 * JSON Catalogue Frontend Library
 * Secure data binding system for CMS content
 * 
 * Usage:
 *   <section page="about">
 *     <h1 item="title"></h1>
 *   </section>
 * 
 *   <section collection="projects">
 *     <article>
 *       <h2 items="title"></h2>
 *     </article>
 *   </section>
 */

(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        dataPath: '',
        autoDetectPath: true
    };
    
    // Auto-detect CMS data path
    if (CONFIG.autoDetectPath) {
        // Get base path from current page location
        const pathParts = window.location.pathname.split('/').filter(p => p);
        // Remove the current page filename if present
        const currentPage = pathParts[pathParts.length - 1];
        if (currentPage && currentPage.includes('.')) {
            pathParts.pop(); // Remove page filename
        }
        const basePath = pathParts.length > 0 ? '/' + pathParts.join('/') : '';
        CONFIG.dataPath = basePath + '/catalogue/data';
    }
    
    /**
     * Security: Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        if (text == null) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }
    
    /**
     * Security: Sanitize URL to prevent injection
     */
    function sanitizeUrl(url) {
        if (!url || typeof url !== 'string') return '';
        // Only allow safe protocols
        if (/^(https?:\/\/|\/|\.\/|\.\.\/)/.test(url)) {
            return url;
        }
        return '';
    }
    
    /**
     * Security: Validate and sanitize JSON data
     */
    function validateJsonData(data) {
        if (!data || typeof data !== 'object') return null;
        // Prevent prototype pollution
        return JSON.parse(JSON.stringify(data));
    }
    
    /**
     * Get nested property from object using dot notation
     * Example: getNestedValue(data, 'image.src')
     */
    function getNestedValue(obj, path) {
        if (!obj || !path) return null;
        const keys = path.split('.');
        let value = obj;
        
        for (let key of keys) {
            if (value == null || typeof value !== 'object') return null;
            value = value[key];
        }
        
        return value;
    }
    
    /**
     * Set content to element based on data binding
     */
    function bindElement(element, data, attribute) {
        // Extract path from attribute (item="title" or items="title")
        const path = attribute.replace(/^items?\./, '');
        const value = getNestedValue(data, path);
        
        if (value == null) return;
        
        // Handle different element types securely
        if (element.tagName === 'IMG') {
            const url = sanitizeUrl(value);
            if (url) {
                element.src = url;
                // Auto-set alt text if empty and alt path exists
                if (element.alt === '') {
                    const altPath = path.replace(/\.src$/, '.alt');
                    const altValue = getNestedValue(data, altPath);
                    if (altValue) {
                        element.alt = escapeHtml(String(altValue));
                    }
                }
            }
        } else if (element.tagName === 'A') {
            const url = sanitizeUrl(value);
            if (url) {
                element.href = url;
            }
        } else {
            // Security: Always use textContent, never innerHTML
            element.textContent = String(value);
        }
    }
    
    /**
     * Bind single page data
     */
    function bindPage(section, pageName) {
        const dataFile = `${CONFIG.dataPath}/${pageName}.json`;
        
        fetch(dataFile)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load ${pageName}`);
                }
                return response.json();
            })
            .then(data => {
                const safeData = validateJsonData(data);
                if (!safeData) return;
                
                // Bind all elements with item="..." attribute
                const bindings = section.querySelectorAll('[item]');
                bindings.forEach(element => {
                    const attribute = element.getAttribute('item');
                    bindElement(element, safeData, attribute);
                });
                
                // Mark as loaded
                section.setAttribute('data-loaded', 'true');
                
                // Trigger custom event
                const event = new CustomEvent('cms:page:loaded', {
                    detail: { page: pageName, data: safeData }
                });
                section.dispatchEvent(event);
            })
            .catch(error => {
                console.error(`Error loading page "${pageName}":`, error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'cms-error';
                errorDiv.textContent = `Error loading content: ${pageName}`;
                section.appendChild(errorDiv);
            });
    }
    
    /**
     * Bind collection data
     */
    function bindCollection(section, collectionName) {
        const dataFile = `${CONFIG.dataPath}/${collectionName}.json`;
        
        fetch(dataFile)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load ${collectionName}`);
                }
                return response.json();
            })
            .then(data => {
                const safeData = validateJsonData(data);
                if (!safeData) return;
                
                // Ensure data is an array
                const items = Array.isArray(safeData) ? safeData : [safeData];
                
                if (items.length === 0) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'cms-empty';
                    emptyDiv.textContent = 'No items available.';
                    section.appendChild(emptyDiv);
                    section.setAttribute('data-loaded', 'true');
                    return;
                }
                
                // Find template element - look for article containing items attribute first
                let template = null;
                const itemsElement = section.querySelector('[items]');
                if (itemsElement) {
                    // Find parent article or div containing the items element
                    template = itemsElement.closest('article') || 
                              itemsElement.closest('div') || 
                              itemsElement.parentElement;
                } else {
                    // Fallback to first article or div
                    template = section.querySelector('article') || 
                             section.querySelector('div') ||
                             section.firstElementChild;
                }
                
                if (!template || template === section) {
                    console.warn(`No template found for collection "${collectionName}"`);
                    section.setAttribute('data-loaded', 'true');
                    return;
                }
                
                // Clone template for each item
                items.forEach((item, index) => {
                    const clone = template.cloneNode(true);
                    
                    // Bind all elements with items="..." attribute
                    const bindings = clone.querySelectorAll('[items]');
                    bindings.forEach(element => {
                        const attribute = element.getAttribute('items');
                        bindElement(element, item, attribute);
                    });
                    
                    // Add to section
                    section.appendChild(clone);
                });
                
                // Remove original template
                if (template.parentNode === section) {
                    template.remove();
                }
                
                // Mark as loaded
                section.setAttribute('data-loaded', 'true');
                
                // Trigger custom event
                const event = new CustomEvent('cms:collection:loaded', {
                    detail: { collection: collectionName, items: items }
                });
                section.dispatchEvent(event);
            })
            .catch(error => {
                console.error(`Error loading collection "${collectionName}":`, error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'cms-error';
                errorDiv.textContent = `Error loading collection: ${collectionName}`;
                section.appendChild(errorDiv);
                section.setAttribute('data-loaded', 'true');
            });
    }
    
    /**
     * Initialize CMS data binding
     */
    function init() {
        // Bind all page sections
        document.querySelectorAll('section[page]').forEach(section => {
            const pageName = section.getAttribute('page');
            if (pageName) {
                bindPage(section, pageName);
            }
        });
        
        // Bind all collection sections
        document.querySelectorAll('section[collection]').forEach(section => {
            const collectionName = section.getAttribute('collection');
            if (collectionName) {
                bindCollection(section, collectionName);
            }
        });
        
        // Load settings for site-wide data
        fetch(`${CONFIG.dataPath}/settings.json`)
            .then(response => response.json())
            .then(data => {
                const safeData = validateJsonData(data);
                if (safeData) {
                    // Bind to elements with data-cms attribute
                    document.querySelectorAll('[data-cms]').forEach(element => {
                        const key = element.getAttribute('data-cms');
                        const value = getNestedValue(safeData, key);
                        if (value != null) {
                            element.textContent = String(value);
                        }
                    });
                    
                    // Set document title
                    if (safeData.site_name) {
                        document.title = safeData.site_name;
                    }
                }
            })
            .catch(error => {
                console.error('Error loading settings:', error);
            });
    }
    
    // Auto-initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Export API for manual initialization
    window.CMS = {
        init: init,
        bindPage: bindPage,
        bindCollection: bindCollection,
        escapeHtml: escapeHtml,
        sanitizeUrl: sanitizeUrl
    };
})();

