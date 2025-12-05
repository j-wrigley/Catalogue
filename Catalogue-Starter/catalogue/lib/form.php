<?php
/**
 * Form Generation Functions
 * Generate HTML forms from blueprint definitions
 */

/**
 * Render a structure table row
 */
function renderStructureRow($fieldName, $itemIndex, $structureFields, $visibleFields, $itemData, $columns) {
    $html = '<tr class="cms-structure-row" data-index="' . esc_attr($itemIndex) . '">';
    
    // Render cells for visible fields - show preview/summary only
    foreach ($visibleFields as $colFieldName) {
        if (!isset($structureFields[$colFieldName])) continue;
        $colDef = $structureFields[$colFieldName];
        $colType = $colDef['type'] ?? 'text';
        $colValue = isset($itemData[$colFieldName]) ? $itemData[$colFieldName] : '';
        $colAlign = isset($columns[$colFieldName]['align']) ? $columns[$colFieldName]['align'] : 'left';
        
        // Format preview based on field type
        $preview = '';
        switch ($colType) {
            case 'switch':
            case 'toggle':
                $checked = ($colValue === true || $colValue === 'true' || $colValue === '1' || $colValue === 'on');
                $preview = $checked ? '✓' : '—';
                break;
            case 'textarea':
                $preview = mb_substr(strip_tags($colValue), 0, 50);
                if (mb_strlen($colValue) > 50) $preview .= '...';
                break;
            case 'markdown':
                $preview = mb_substr(strip_tags($colValue), 0, 50);
                if (mb_strlen($colValue) > 50) $preview .= '...';
                break;
            case 'select':
                $options = $colDef['options'] ?? [];
                $preview = isset($options[$colValue]) ? $options[$colValue] : $colValue;
                break;
            case 'tags':
                $options = $colDef['options'] ?? [];
                $selectedValues = is_array($colValue) ? $colValue : ($colValue ? [$colValue] : []);
                $previewTags = [];
                foreach ($selectedValues as $tagValue) {
                    $previewTags[] = isset($options[$tagValue]) ? $options[$tagValue] : $tagValue;
                }
                $preview = !empty($previewTags) ? implode(', ', array_slice($previewTags, 0, 3)) : '—';
                if (count($previewTags) > 3) $preview .= '...';
                break;
            default:
                $preview = mb_substr(strip_tags($colValue), 0, 50);
                if (mb_strlen($colValue) > 50) $preview .= '...';
                break;
        }
        
        $html .= '<td style="text-align: ' . esc_attr($colAlign) . ';" class="cms-structure-preview-cell">';
        $html .= '<span class="cms-structure-preview">' . esc($preview ?: '—') . '</span>';
        $html .= '</td>';
    }
    
    // Actions column
    $html .= '<td class="cms-structure-row-actions">';
    $html .= '<button type="button" class="cms-button cms-button-ghost cms-button-sm" data-structure-edit="' . esc_attr($fieldName) . '" data-index="' . esc_attr($itemIndex) . '" title="Edit">';
    $html .= icon('cursor-text', 'cms-icon');
    $html .= '</button>';
    $html .= '<button type="button" class="cms-button cms-button-ghost cms-button-sm" data-structure-delete="' . esc_attr($fieldName) . '" data-index="' . esc_attr($itemIndex) . '" title="Delete">';
    $html .= icon('trash', 'cms-icon');
    $html .= '</button>';
    $html .= '</td>';
    $html .= '</tr>';
    
    return $html;
}

/**
 * Render structure form fields for modal dialog
 */
function renderStructureFormFields($fieldName, $structureFields, $itemData = [], $itemIndex = null) {
    $html = '<div class="cms-structure-form-fields">';
    
    foreach ($structureFields as $subFieldName => $subFieldDef) {
        $subFieldValue = isset($itemData[$subFieldName]) ? $itemData[$subFieldName] : null;
        $html .= renderFormField($subFieldName, $subFieldDef, $subFieldValue);
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Render form field based on blueprint definition
 */
function renderFormField($fieldName, $fieldDef, $value = null, $column = null, $span = null, $rows = null) {
    $type = $fieldDef['type'] ?? 'text';
    $required = isset($fieldDef['required']) && ($fieldDef['required'] === true || $fieldDef['required'] === 1 || $fieldDef['required'] === 'true');
    $label = $fieldDef['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
    
    // Handle nested values (e.g., image.src)
    // BUT: Don't extract src for multiple file fields (they're arrays of URLs)
    $fieldValue = $value;
    $isMultipleFile = ($type === 'file' && isset($fieldDef['multiple']) && ($fieldDef['multiple'] === true || $fieldDef['multiple'] === 1 || $fieldDef['multiple'] === 'true'));
    
    if ($type === 'file' && is_array($value) && !$isMultipleFile) {
        // Single file field - extract src/url from object
        $fieldValue = $value['src'] ?? $value['url'] ?? '';
    }
    // For multiple file fields, keep $fieldValue as the array
    
    // Build classes and style attributes for grid positioning
    $classes = ['cms-form-group'];
    $styles = [];
    
    if ($column !== null) {
        $classes[] = 'cms-form-group-column-' . esc_attr($column);
        
        if ($span !== null && $span > 1) {
            // Span across multiple columns
            $spanValue = intval($span);
            $styles[] = 'grid-column: ' . intval($column) . ' / span ' . $spanValue;
        } else {
            // Single column
            $styles[] = 'grid-column: ' . intval($column);
        }
    }
    
    if ($rows !== null && $rows > 1) {
        // Span across multiple rows
        $rowsValue = intval($rows);
        $styles[] = 'grid-row: span ' . $rowsValue;
        $classes[] = 'cms-form-group-rows-' . $rowsValue;
    }
    
    $classAttr = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
    $styleAttr = !empty($styles) ? ' style="' . implode('; ', $styles) . ';"' : '';
    
    $html = '<div' . $classAttr . $styleAttr . '>';
    
    // Switches and structures handle their own label
    if ($type !== 'switch' && $type !== 'toggle' && $type !== 'structure') {
        $html .= '<label for="' . esc_attr($fieldName) . '" class="cms-label">';
        $html .= esc($label);
        if ($required) {
            $html .= ' <span class="cms-required">*</span>';
        }
        $html .= '</label>';
    }
    
    switch ($type) {
        case 'textarea':
            $textareaStyle = '';
            if ($rows !== null && $rows > 1) {
                // Make textarea expand to fill available height
                $textareaStyle = ' style="height: 100%; min-height: calc(' . ($rows * 2.5) . 'rem + ' . ($rows * 0.5) . 'rem);"';
            }
            $html .= '<textarea id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" class="cms-input cms-textarea"' . $textareaStyle;
            if ($required) {
                $html .= ' required';
            }
            $html .= '>' . esc($fieldValue ?? '') . '</textarea>';
            break;
            
        case 'markdown':
            // Visual markdown editor with toolbar
            $markdownValue = $fieldValue ?? '';
            $htmlValue = '';
            if (!empty($markdownValue)) {
                // Convert markdown to HTML for display (will be done in JS, but set initial value)
                $htmlValue = htmlspecialchars($markdownValue, ENT_QUOTES, 'UTF-8');
            }
            
            $markdownStyle = '';
            if ($rows !== null && $rows > 1) {
                // Make markdown editor expand to fill available height
                $markdownStyle = ' style="flex: 1; min-height: calc(' . ($rows * 2.5) . 'rem + ' . ($rows * 0.5) . 'rem);"';
            }
            
            $html .= '<div class="cms-markdown-editor"' . ($rows !== null && $rows > 1 ? ' style="display: flex; flex-direction: column; height: 100%;"' : '') . ' data-field-name="' . esc_attr($fieldName) . '">';
            $html .= '<div class="cms-markdown-toolbar">';
            $html .= '<div class="cms-markdown-toolbar-group">';
            $html .= '<button type="button" class="cms-markdown-button" data-command="bold" title="Bold">' . icon('font-bold', 'cms-icon') . '</button>';
            $html .= '<button type="button" class="cms-markdown-button" data-command="italic" title="Italic">' . icon('font-italic', 'cms-icon') . '</button>';
            $html .= '<button type="button" class="cms-markdown-button" data-command="strikeThrough" title="Strikethrough">' . icon('strikethrough', 'cms-icon') . '</button>';
            $html .= '<button type="button" class="cms-markdown-button" data-command="underline" title="Underline">' . icon('underline', 'cms-icon') . '</button>';
            $html .= '</div>';
            $html .= '<div class="cms-markdown-toolbar-divider"></div>';
            $html .= '<div class="cms-markdown-toolbar-group">';
            $html .= '<button type="button" class="cms-markdown-button" data-command="formatBlock" data-value="h1" title="Heading 1"><span class="cms-markdown-heading">H1</span></button>';
            $html .= '<button type="button" class="cms-markdown-button" data-command="formatBlock" data-value="h2" title="Heading 2"><span class="cms-markdown-heading">H2</span></button>';
            $html .= '<button type="button" class="cms-markdown-button" data-command="formatBlock" data-value="h3" title="Heading 3"><span class="cms-markdown-heading">H3</span></button>';
            $html .= '</div>';
            $html .= '<div class="cms-markdown-toolbar-divider"></div>';
            $html .= '<div class="cms-markdown-toolbar-group">';
            $html .= '<button type="button" class="cms-markdown-button" data-action="link" title="Link">' . icon('link-1', 'cms-icon') . '</button>';
            $html .= '<button type="button" class="cms-markdown-button" data-action="code" title="Code">' . icon('code', 'cms-icon') . '</button>';
            $html .= '<button type="button" class="cms-markdown-button" data-action="quote" title="Quote">' . icon('quote', 'cms-icon') . '</button>';
            $html .= '</div>';
            $html .= '<div class="cms-markdown-toolbar-divider"></div>';
            $html .= '<div class="cms-markdown-toolbar-group">';
            $html .= '<button type="button" class="cms-markdown-button" data-command="insertUnorderedList" title="Bullet List">' . icon('list-bullet', 'cms-icon') . '</button>';
            $html .= '<button type="button" class="cms-markdown-button" data-command="insertOrderedList" title="Ordered List"><span class="cms-markdown-numbered">1.</span></button>';
            $html .= '</div>';
            $html .= '</div>';
            // Hidden textarea for form submission (stores markdown)
            $html .= '<textarea id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" class="cms-markdown-textarea-hidden" style="display: none;"';
            if ($required) {
                $html .= ' required';
            }
            $html .= '>' . esc($markdownValue) . '</textarea>';
            // Visual editor (contenteditable div)
            $html .= '<div class="cms-markdown-visual-editor"' . $markdownStyle . ' contenteditable="true" id="' . esc_attr($fieldName) . '_visual" role="textbox" aria-label="' . esc_attr($label) . '" aria-placeholder="Start typing..."></div>';
            $html .= '</div>';
            break;
            
        case 'select':
            // Custom dropdown component
            $options = $fieldDef['options'] ?? [];
            $selectedValue = $fieldValue ?? '';
            $selectedLabel = '';
            
            // Find selected label
            if (!empty($selectedValue) && isset($options[$selectedValue])) {
                $selectedLabel = $options[$selectedValue];
            } else {
                $selectedLabel = '';
            }
            
            $html .= '<div class="cms-dropdown" data-field="' . esc_attr($fieldName) . '">';
            $html .= '<input type="hidden" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" value="' . esc_attr($selectedValue) . '"';
            if ($required) {
                $html .= ' required';
            }
            $html .= ' />';
            $html .= '<button type="button" class="cms-dropdown-trigger" aria-haspopup="true" aria-expanded="false" data-dropdown-trigger="' . esc_attr($fieldName) . '">';
            if (!empty($selectedLabel)) {
                $html .= '<span class="cms-dropdown-value">' . esc($selectedLabel) . '</span>';
            } else {
                $html .= '<span class="cms-dropdown-value cms-dropdown-placeholder">' . (!isset($fieldDef['required']) || !$fieldDef['required'] ? '-- Select --' : '') . '</span>';
            }
            $html .= '<svg class="cms-dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L1 4h10z" fill="currentColor"/></svg>';
            $html .= '</button>';
            $html .= '<div class="cms-dropdown-content" data-dropdown-content="' . esc_attr($fieldName) . '" role="menu" aria-orientation="vertical" style="display: none;">';
            if (!isset($fieldDef['required']) || !$fieldDef['required']) {
                $html .= '<button type="button" class="cms-dropdown-item' . (empty($selectedValue) ? ' selected' : '') . '" data-value="" data-dropdown-item="' . esc_attr($fieldName) . '">';
                $html .= '<span>-- Select --</span>';
                $html .= '</button>';
            }
            foreach ($options as $optionValue => $optionLabel) {
                $selected = ($selectedValue == $optionValue) ? ' selected' : '';
                $html .= '<button type="button" class="cms-dropdown-item' . $selected . '" data-value="' . esc_attr($optionValue) . '" data-dropdown-item="' . esc_attr($fieldName) . '">';
                $html .= '<span>' . esc($optionLabel) . '</span>';
                $html .= '</button>';
            }
            $html .= '</div>';
            $html .= '</div>';
            break;
            
        case 'radio':
            // Radio button group
            $options = $fieldDef['options'] ?? [];
            $layout = $fieldDef['layout'] ?? 'grid'; // 'grid' or 'list'
            $columns = isset($fieldDef['columns']) ? intval($fieldDef['columns']) : 5;
            $html .= '<div class="cms-radio-group cms-radio-group-' . esc_attr($layout) . '" data-columns="' . esc_attr($columns) . '">';
            foreach ($options as $optionValue => $optionLabel) {
                $checked = ($fieldValue == $optionValue) ? ' checked' : '';
                $html .= '<label class="cms-radio-option">';
                $html .= '<input type="radio" name="' . esc_attr($fieldName) . '" value="' . esc_attr($optionValue) . '"' . $checked;
                if ($required) {
                    $html .= ' required';
                }
                $html .= ' />';
                $html .= '<span class="cms-radio-label">' . esc($optionLabel) . '</span>';
                $html .= '</label>';
            }
            $html .= '</div>';
            break;
            
        case 'checkbox':
            // Checkbox group (multiple values)
            $options = $fieldDef['options'] ?? [];
            $layout = $fieldDef['layout'] ?? 'grid'; // 'grid' or 'list'
            $columns = isset($fieldDef['columns']) ? intval($fieldDef['columns']) : 5;
            $selectedValues = is_array($fieldValue) ? $fieldValue : ($fieldValue ? [$fieldValue] : []);
            $html .= '<div class="cms-checkbox-group cms-checkbox-group-' . esc_attr($layout) . '" data-columns="' . esc_attr($columns) . '">';
            foreach ($options as $optionValue => $optionLabel) {
                $checked = in_array($optionValue, $selectedValues) ? ' checked' : '';
                $html .= '<label class="cms-checkbox-option">';
                $html .= '<input type="checkbox" name="' . esc_attr($fieldName) . '[]" value="' . esc_attr($optionValue) . '"' . $checked . ' />';
                $html .= '<span class="cms-checkbox-label">' . esc($optionLabel) . '</span>';
                $html .= '</label>';
            }
            $html .= '</div>';
            break;
            
        case 'tags':
            // Tag selector/picker with ability to add new tags
            $options = $fieldDef['options'] ?? [];
            $selectedValues = is_array($fieldValue) ? $fieldValue : ($fieldValue ? [$fieldValue] : []);
            
            // Separate predefined tags from custom tags
            $predefinedTags = [];
            $customTags = [];
            foreach ($selectedValues as $tag) {
                if (isset($options[$tag])) {
                    $predefinedTags[] = $tag;
                } else {
                    $customTags[] = $tag;
                }
            }
            
            $html .= '<div class="cms-tags-selector" data-field="' . esc_attr($fieldName) . '">';
            
            // Display selected tags (both predefined and custom) - always render container
            $html .= '<div class="cms-tags-selected">';
            if (!empty($selectedValues)) {
                foreach ($selectedValues as $tagValue) {
                    $tagLabel = isset($options[$tagValue]) ? $options[$tagValue] : $tagValue;
                    $isCustom = !isset($options[$tagValue]);
                    $html .= '<span class="cms-tag' . ($isCustom ? ' cms-tag-custom' : '') . '" data-value="' . esc_attr($tagValue) . '">';
                    $html .= '<span class="cms-tag-text">' . esc($tagLabel) . '</span>';
                    $html .= '<button type="button" class="cms-tag-remove" aria-label="Remove tag">';
                    $html .= '<svg width="12" height="12" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.8536 1.85355C12.0488 1.65829 12.0488 1.34171 11.8536 1.14645C11.6583 0.951184 11.3417 0.951184 11.1464 1.14645L7.5 4.79289L3.85355 1.14645C3.65829 0.951184 3.34171 0.951184 3.14645 1.14645C2.95118 1.34171 2.95118 1.65829 3.14645 1.85355L6.79289 5.5L3.14645 9.14645C2.95118 9.34171 2.95118 9.65829 3.14645 9.85355C3.34171 10.0488 3.65829 10.0488 3.85355 9.85355L7.5 6.20711L11.1464 9.85355C11.3417 10.0488 11.6583 10.0488 11.8536 9.85355C12.0488 9.65829 12.0488 9.34171 11.8536 9.14645L7.70711 5L11.8536 1.85355Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>';
                    $html .= '</button>';
                    $html .= '</span>';
                }
            }
            $html .= '</div>';
            
            // Input for adding new tags
            $html .= '<div class="cms-tags-input-wrapper">';
            $html .= '<input type="text" class="cms-tags-input" placeholder="Add tag..." data-field="' . esc_attr($fieldName) . '" />';
            $html .= '</div>';
            
            // Grid of available predefined tags
            if (!empty($options)) {
                $html .= '<div class="cms-tags-grid">';
                foreach ($options as $optionValue => $optionLabel) {
                    $selected = in_array($optionValue, $selectedValues) ? ' selected' : '';
                    $html .= '<button type="button" class="cms-tag-button' . $selected . '" data-value="' . esc_attr($optionValue) . '" data-label="' . esc_attr($optionLabel) . '" data-field="' . esc_attr($fieldName) . '">';
                    $html .= esc($optionLabel);
                    $html .= '</button>';
                }
                $html .= '</div>';
            }
            
            // Hidden input to store selected values
            $selectedJson = json_encode($selectedValues);
            $html .= '<input type="hidden" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '_tags" value="' . esc_attr($selectedJson) . '" />';
            $html .= '</div>';
            break;
            
        case 'file':
            // File upload field - opens media picker
            $multiple = isset($fieldDef['multiple']) && ($fieldDef['multiple'] === true || $fieldDef['multiple'] === 1 || $fieldDef['multiple'] === 'true');
            $maxFiles = isset($fieldDef['max_files']) ? intval($fieldDef['max_files']) : null;
            
            $html .= '<div class="cms-file-upload" data-field="' . esc_attr($fieldName) . '" data-multiple="' . ($multiple ? 'true' : 'false') . '"' . ($maxFiles ? ' data-max-files="' . esc_attr($maxFiles) . '"' : '') . '>';
            
            // Handle multiple files
            if ($multiple) {
                $fileValues = [];
            if (!empty($fieldValue)) {
                    // Handle JSON string (from form submission)
                    if (is_string($fieldValue) && (substr($fieldValue, 0, 1) === '[' || substr($fieldValue, 0, 1) === '{')) {
                        $decoded = json_decode($fieldValue, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $fieldValue = $decoded;
                        }
                    }
                    
                    if (is_array($fieldValue)) {
                        // Check if it's an array of URLs or array of objects
                        if (isset($fieldValue[0])) {
                            $fileValues = $fieldValue;
                        } else {
                            // Single object, convert to array
                            $fileValues = [$fieldValue];
                        }
                    } else {
                        // Single string URL, convert to array
                        $fileValues = [$fieldValue];
                    }
                }
                
                // Display selected files
                if (!empty($fileValues)) {
                    $html .= '<div class="cms-file-preview-grid">';
                    foreach ($fileValues as $index => $fileValue) {
                        $previewUrl = is_array($fileValue) ? ($fileValue['src'] ?? $fileValue['url'] ?? '') : $fileValue;
                        if (!empty($previewUrl)) {
                            $html .= '<div class="cms-file-preview-item">';
                            $html .= '<img src="' . esc_url($previewUrl) . '" alt="Preview" class="cms-file-preview-image" />';
                $html .= '<div class="cms-file-preview-overlay">';
                            $html .= '<button type="button" class="cms-button cms-button-ghost cms-button-sm" data-action="remove-file-item" data-field="' . esc_attr($fieldName) . '" data-index="' . esc_attr($index) . '">';
                $html .= icon('trash', 'cms-icon');
                $html .= '<span>Remove</span>';
                $html .= '</button>';
                $html .= '</div>';
                $html .= '</div>';
            }
                    }
                    $html .= '</div>';
                }
                
                // Store as JSON array
                $fileValueJson = json_encode($fileValues);
                $html .= '<input type="hidden" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" value="' . esc_attr($fileValueJson) . '"';
                if ($required && empty($fileValues)) {
                $html .= ' required';
            }
            $html .= ' />';
                $html .= '<input type="hidden" name="' . esc_attr($fieldName) . '_current" value="' . esc_attr($fileValueJson) . '" />';
                
                // Button text based on selection
                $buttonText = !empty($fileValues) ? 'Add more files' : 'Select files';
                if ($maxFiles) {
                    $remaining = $maxFiles - count($fileValues);
                    if ($remaining > 0) {
                        $buttonText .= ' (' . $remaining . ' remaining)';
            } else {
                        $buttonText = 'Maximum files reached';
                    }
                }
            } else {
                // Single file mode (existing code)
                if (!empty($fieldValue)) {
                    // Handle both string URLs and array objects
                    $previewUrl = is_array($fieldValue) ? ($fieldValue['src'] ?? $fieldValue['url'] ?? '') : $fieldValue;
                    if (!empty($previewUrl)) {
                        $html .= '<div class="cms-file-preview">';
                        $html .= '<img src="' . esc_url($previewUrl) . '" alt="Preview" class="cms-file-preview-image" />';
                        $html .= '<div class="cms-file-preview-overlay">';
                        $html .= '<button type="button" class="cms-button cms-button-ghost cms-button-sm" data-action="open-media-picker" data-field="' . esc_attr($fieldName) . '">';
                        $html .= icon('image', 'cms-icon');
                        $html .= '<span>Change</span>';
                        $html .= '</button>';
                        $html .= '<button type="button" class="cms-button cms-button-ghost cms-button-sm" data-action="remove-file" data-field="' . esc_attr($fieldName) . '">';
                        $html .= icon('trash', 'cms-icon');
                        $html .= '<span>Remove</span>';
                        $html .= '</button>';
            $html .= '</div>';
                        $html .= '</div>';
                    }
                }
                
                // Hidden input to store the selected file URL
                $fileValue = is_array($fieldValue) ? ($fieldValue['src'] ?? $fieldValue['url'] ?? '') : ($fieldValue ?? '');
                $html .= '<input type="hidden" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" value="' . esc_attr($fileValue) . '"';
                if ($required && empty($fileValue)) {
                    $html .= ' required';
                }
                $html .= ' />';
                $html .= '<input type="hidden" name="' . esc_attr($fieldName) . '_current" value="' . esc_attr($fileValue) . '" />';
                
                $buttonText = !empty($fieldValue) ? 'Change file' : 'Select from media';
            }
            
            $html .= '<div class="cms-file-trigger-wrapper">';
            $html .= '<button type="button" class="cms-file-trigger" data-action="open-media-picker" data-field="' . esc_attr($fieldName) . '"' . ($multiple && $maxFiles && count($fileValues ?? []) >= $maxFiles ? ' disabled' : '') . '>';
            $html .= '<span class="cms-file-trigger-text">' . esc($buttonText) . '</span>';
            $html .= '</button>';
            $html .= '</div>';
            $html .= '</div>';
            break;
            
        case 'slider':
        case 'range':
            // Custom slider component
            $min = isset($fieldDef['min']) ? floatval($fieldDef['min']) : 0;
            $max = isset($fieldDef['max']) ? floatval($fieldDef['max']) : 100;
            $step = isset($fieldDef['step']) ? floatval($fieldDef['step']) : 1;
            $defaultValue = isset($fieldDef['default']) ? floatval($fieldDef['default']) : $min;
            $currentValue = isset($fieldValue) && $fieldValue !== '' ? floatval($fieldValue) : $defaultValue;
            $currentValue = max($min, min($max, $currentValue)); // Clamp between min and max
            
            // Calculate percentage for positioning
            $percentage = (($currentValue - $min) / ($max - $min)) * 100;
            
            $html .= '<div class="cms-slider-wrapper" data-field="' . esc_attr($fieldName) . '">';
            $html .= '<input type="hidden" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" value="' . esc_attr($currentValue) . '"';
            if ($required) {
                $html .= ' required';
            }
            $html .= ' />';
            $html .= '<div class="cms-slider" role="slider" aria-valuemin="' . esc_attr($min) . '" aria-valuemax="' . esc_attr($max) . '" aria-valuenow="' . esc_attr($currentValue) . '" aria-label="' . esc_attr($label) . '" data-slider="' . esc_attr($fieldName) . '" data-min="' . esc_attr($min) . '" data-max="' . esc_attr($max) . '" data-step="' . esc_attr($step) . '" tabindex="0" data-orientation="horizontal">';
            $html .= '<div class="cms-slider-track">';
            $html .= '<div class="cms-slider-range" style="width: ' . esc_attr($percentage) . '%;"></div>';
            $html .= '</div>';
            $html .= '<div class="cms-slider-thumb" style="left: ' . esc_attr($percentage) . '%;" data-slider-thumb="' . esc_attr($fieldName) . '"></div>';
            $html .= '</div>';
            $html .= '<div class="cms-slider-value">';
            $html .= '<span class="cms-slider-value-display" data-slider-value="' . esc_attr($fieldName) . '">' . esc($currentValue) . '</span>';
            if (isset($fieldDef['unit'])) {
                $html .= '<span class="cms-slider-unit">' . esc($fieldDef['unit']) . '</span>';
            }
            $html .= '</div>';
            $html .= '</div>';
            break;
            
        case 'switch':
        case 'toggle':
            // Custom switch/toggle component
            $checked = ($fieldValue === true || $fieldValue === 'true' || $fieldValue === '1' || $fieldValue === 'on');
            
            $html .= '<div class="cms-switch-wrapper" data-field="' . esc_attr($fieldName) . '">';
            $html .= '<input type="hidden" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" value="' . ($checked ? '1' : '0') . '"';
            if ($required) {
                $html .= ' required';
            }
            $html .= ' />';
            $html .= '<label class="cms-switch-label">';
            $html .= '<button type="button" role="switch" aria-checked="' . ($checked ? 'true' : 'false') . '" data-state="' . ($checked ? 'checked' : 'unchecked') . '" aria-label="' . esc_attr($label) . '" class="cms-switch" data-switch="' . esc_attr($fieldName) . '" tabindex="0">';
            $html .= '<span class="cms-switch-thumb"></span>';
            $html .= '</button>';
            $html .= '<span class="cms-switch-label-text">' . esc($label);
            if ($required) {
                $html .= ' <span class="cms-required">*</span>';
            }
            $html .= '</span>';
            $html .= '</label>';
            $html .= '</div>';
            break;
            
        case 'structure':
            // Structure field - repeatable table items
            $structureFields = $fieldDef['fields'] ?? [];
            $structureData = is_array($fieldValue) ? $fieldValue : [];
            
            // Get column definitions (which fields to show in table)
            $columns = $fieldDef['columns'] ?? [];
            $visibleFields = !empty($columns) ? array_keys($columns) : array_keys($structureFields);
            
            // Store visible fields in data attribute for JavaScript
            $visibleFieldsJson = json_encode($visibleFields);
            
            $html .= '<div class="cms-structure-wrapper" data-field="' . esc_attr($fieldName) . '" data-visible-fields="' . esc_attr($visibleFieldsJson) . '">';
            $html .= '<div class="cms-structure-header">';
            $html .= '<label class="cms-label">' . esc($label);
            if ($required) {
                $html .= ' <span class="cms-required">*</span>';
            }
            $html .= '</label>';
            $html .= '</div>';
            $html .= '<input type="hidden" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" value="' . esc_attr(json_encode($structureData)) . '"';
            if ($required) {
                $html .= ' required';
            }
            $html .= ' />';
            
            // Table for displaying items
            $html .= '<div class="cms-structure-table-wrapper">';
            $html .= '<table class="cms-structure-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            foreach ($visibleFields as $colFieldName) {
                if (!isset($structureFields[$colFieldName])) continue;
                $colDef = $structureFields[$colFieldName];
                $colLabel = $colDef['label'] ?? ucfirst(str_replace('_', ' ', $colFieldName));
                $colWidth = isset($columns[$colFieldName]['width']) ? $columns[$colFieldName]['width'] : '';
                $colAlign = isset($columns[$colFieldName]['align']) ? $columns[$colFieldName]['align'] : 'left';
                $html .= '<th style="' . ($colWidth ? 'width: ' . esc_attr($colWidth) . ';' : '') . 'text-align: ' . esc_attr($colAlign) . ';">' . esc($colLabel) . '</th>';
            }
            $html .= '<th class="cms-structure-actions-header">Actions</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody class="cms-structure-tbody" data-field="' . esc_attr($fieldName) . '">';
            
            // Render existing items
            if (empty($structureData)) {
                $html .= '<tr class="cms-structure-empty">';
                $html .= '<td colspan="' . (count($visibleFields) + 1) . '" class="cms-structure-empty-cell">No items yet. Click "Add item" to get started.</td>';
                $html .= '</tr>';
            } else {
                foreach ($structureData as $itemIndex => $itemData) {
                    $html .= renderStructureRow($fieldName, $itemIndex, $structureFields, $visibleFields, $itemData, $columns);
                }
            }
            
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            
            // Add new item button
            $html .= '<div class="cms-structure-actions">';
            $html .= '<button type="button" class="cms-button cms-button-outline cms-button-sm" data-structure-add="' . esc_attr($fieldName) . '">';
            $html .= icon('plus', 'cms-icon');
            $html .= '<span>Add item</span>';
            $html .= '</button>';
            $html .= '</div>';
            
            // Hidden template for new rows (used by JavaScript)
            $html .= '<template class="cms-structure-row-template" data-field="' . esc_attr($fieldName) . '">';
            $html .= renderStructureRow($fieldName, '__INDEX__', $structureFields, $visibleFields, [], $columns);
            $html .= '</template>';
            
            // Hidden template for form fields (used in modal)
            $html .= '<template class="cms-structure-form-template" data-field="' . esc_attr($fieldName) . '">';
            $html .= renderStructureFormFields($fieldName, $structureFields, []);
            $html .= '</template>';
            
            $html .= '</div>';
            break;
            
        case 'text':
        default:
            $html .= '<input type="text" id="' . esc_attr($fieldName) . '" name="' . esc_attr($fieldName) . '" class="cms-input" value="' . esc_attr($fieldValue ?? '') . '"';
            if ($required) {
                $html .= ' required';
            }
            $html .= ' />';
            break;
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Generate form from blueprint
 */
function generateFormFromBlueprint($blueprint, $content = null) {
    if (!isset($blueprint['fields'])) {
        return '<p class="cms-text-muted">No fields defined in blueprint.</p>';
    }
    
    // Check if tabs are defined - if so, use tabbed form generation
    $tabsConfig = $blueprint['tabs'] ?? [];
    if (!empty($tabsConfig) && is_array($tabsConfig)) {
        return generateFormFromBlueprintWithTabs($blueprint, $content, false);
    }
    
    // Determine if we need a grid layout and calculate max columns
    $hasColumns = false;
    $maxColumns = 1;
    foreach ($blueprint['fields'] as $fieldDef) {
        if (isset($fieldDef['column'])) {
            $hasColumns = true;
            $col = intval($fieldDef['column']);
            $span = isset($fieldDef['span']) ? intval($fieldDef['span']) : 1;
            // Calculate max columns accounting for spans (column + span - 1)
            $maxColumns = max($maxColumns, $col + $span - 1);
        }
    }
    
    $html = '<form id="content-form" class="cms-form" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="csrf_token" value="' . esc_attr(generateCsrfToken()) . '" />';
    
    if ($hasColumns) {
        // Group fields by their group attribute
        $groupedFields = [];
        $ungroupedFields = [];
        
        foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
            $group = isset($fieldDef['group']) ? $fieldDef['group'] : null;
            $column = isset($fieldDef['column']) ? intval($fieldDef['column']) : null;
            $span = isset($fieldDef['span']) ? intval($fieldDef['span']) : null;
            $rows = isset($fieldDef['rows']) ? intval($fieldDef['rows']) : null;
            
            $fieldData = [
                'name' => $fieldName,
                'def' => $fieldDef,
                'column' => $column,
                'span' => $span,
                'rows' => $rows
            ];
            
            if ($group !== null) {
                if (!isset($groupedFields[$group])) {
                    $groupedFields[$group] = [];
                }
                $groupedFields[$group][] = $fieldData;
            } else {
                $ungroupedFields[] = $fieldData;
            }
        }
        
        // Build grid layout with support for column, span, and rows
        $html .= '<div class="cms-form-grid" data-columns="' . esc_attr($maxColumns) . '">';
        
        // Render grouped fields - organize by columns for independent column flow
        foreach ($groupedFields as $groupName => $groupFields) {
            // Organize fields by column
            $fieldsByColumn = [];
            foreach ($groupFields as $field) {
                $col = $field['column'] ?? 1;
                if (!isset($fieldsByColumn[$col])) {
                    $fieldsByColumn[$col] = [];
                }
                $fieldsByColumn[$col][] = $field;
            }
            
            // Create a group row container
            $html .= '<div class="cms-form-group-row" data-group="' . esc_attr($groupName) . '" data-columns="' . esc_attr(count($fieldsByColumn)) . '">';
            
            // Render only columns that have fields (in order)
            ksort($fieldsByColumn);
            foreach ($fieldsByColumn as $col => $fields) {
                $html .= '<div class="cms-form-group-column" data-column="' . esc_attr($col) . '">';
        foreach ($fields as $field) {
                    $value = null;
                    if ($content && isset($content[$field['name']])) {
                        $value = $content[$field['name']];
                    }
                    // Remove column/span from field rendering since we're in a column container
                    $html .= renderFormField($field['name'], $field['def'], $value, null, null, $field['rows']);
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }
        
        // Render ungrouped fields (normal grid flow)
        foreach ($ungroupedFields as $field) {
            $value = null;
            if ($content && isset($content[$field['name']])) {
                $value = $content[$field['name']];
            }
            $html .= renderFormField($field['name'], $field['def'], $value, $field['column'], $field['span'], $field['rows']);
        }
        
        $html .= '</div>';
    } else {
        // Standard single column layout
        foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
            $value = null;
            if ($content && isset($content[$fieldName])) {
                $value = $content[$fieldName];
            }
            $html .= renderFormField($fieldName, $fieldDef, $value);
        }
    }
    
    // Note: Core fields (Featured and Status) are now rendered separately
    // using generateCoreFieldsCards() function, not inside this form
    
    // Form actions removed - now in banner
    // Buttons are now in generateCoreFieldsCards() banner
    
    $html .= '</form>';
    
    // Render structure modals OUTSIDE the main form (after form closes)
    // This prevents nested forms which break form submission
    if (isset($blueprint['fields'])) {
        foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
            if (($fieldDef['type'] ?? '') === 'structure') {
                $structureFields = $fieldDef['fields'] ?? [];
                
                $html .= '<div class="cms-modal cms-structure-modal" id="cms-structure-modal-' . esc_attr($fieldName) . '" style="display: none;">';
                $html .= '<div class="cms-modal-backdrop" data-structure-modal-close="' . esc_attr($fieldName) . '"></div>';
                $html .= '<div class="cms-modal-content cms-structure-modal-content">';
                $html .= '<div class="cms-modal-header">';
                $html .= '<h3 class="cms-modal-title" data-structure-modal-title="' . esc_attr($fieldName) . '">Edit Item</h3>';
                $html .= '<button type="button" class="cms-modal-close" data-structure-modal-close="' . esc_attr($fieldName) . '" aria-label="Close">×</button>';
                $html .= '</div>';
                $html .= '<form class="cms-structure-item-form" data-field="' . esc_attr($fieldName) . '">';
                $html .= '<div class="cms-modal-body" data-structure-form-container="' . esc_attr($fieldName) . '">';
                // Form fields will be inserted here by JavaScript
                $html .= '</div>';
                $html .= '<div class="cms-modal-footer">';
                $html .= '<button type="button" class="cms-button cms-button-ghost" data-structure-modal-close="' . esc_attr($fieldName) . '">Cancel</button>';
                $html .= '<button type="submit" class="cms-button cms-button-primary">Save Item</button>';
                $html .= '</div>';
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
    }
    
    return $html;
}

/**
 * Generate form from blueprint with tabs (for settings page)
 */
function generateFormFromBlueprintWithTabs($blueprint, $content = null, $isSettingsPage = false) {
    if (!isset($blueprint['fields'])) {
        return '<p class="cms-text-muted">No fields defined in blueprint.</p>';
    }
    
    // Get tabs configuration from blueprint, or use defaults
    $tabsConfig = $blueprint['tabs'] ?? [];
    
    // If tabs are defined in blueprint, use them
    if (!empty($tabsConfig) && is_array($tabsConfig)) {
        $categories = [];
        foreach ($tabsConfig as $tabKey => $tabConfig) {
            if (is_string($tabConfig)) {
                // Simple format: tab_key: Tab Label
                $categories[$tabKey] = ['label' => $tabConfig, 'fields' => []];
            } elseif (is_array($tabConfig) && isset($tabConfig['label'])) {
                // Detailed format: tab_key: { label: Tab Label }
                $categories[$tabKey] = ['label' => $tabConfig['label'], 'fields' => []];
            }
        }
    } else {
        // Fallback to default categories if no tabs defined
    $categories = [
        'basic' => ['label' => 'Basic Information', 'fields' => []],
        'seo' => ['label' => 'SEO Settings', 'fields' => []],
        'general' => ['label' => 'General Settings', 'fields' => []]
    ];
    }
    
    // Determine default category (first tab if tabs defined, otherwise 'basic')
    $defaultCategory = !empty($tabsConfig) ? array_key_first($categories) : 'basic';
    
    // Group fields by category
    foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
        $category = $fieldDef['category'] ?? $defaultCategory;
        // Only add to category if it exists in tabs config
        if (isset($categories[$category])) {
            $categories[$category]['fields'][$fieldName] = $fieldDef;
        } elseif (empty($tabsConfig)) {
            // If no tabs config, auto-create category
            $categories[$category] = ['label' => ucfirst($category), 'fields' => []];
        $categories[$category]['fields'][$fieldName] = $fieldDef;
        }
    }
    
    // Remove empty categories
    $categories = array_filter($categories, function($cat) {
        return !empty($cat['fields']);
    });
    
    if (empty($categories)) {
        return '<p class="cms-text-muted">No fields defined in blueprint.</p>';
    }
    
    $html = '<form id="content-form" class="cms-form" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="csrf_token" value="' . esc_attr(generateCsrfToken()) . '" />';
    
    // Tab list
    $html .= '<div class="cms-tabs" role="tablist" aria-orientation="horizontal">';
    $tabIndex = 0;
    foreach ($categories as $categoryKey => $category) {
        $isActive = $tabIndex === 0 ? 'true' : 'false';
        $dataState = $tabIndex === 0 ? 'active' : 'inactive';
        $html .= '<button type="button" class="cms-tabs-trigger ' . ($tabIndex === 0 ? 'active' : '') . '"';
        $html .= ' role="tab"';
        $html .= ' aria-selected="' . $isActive . '"';
        $html .= ' aria-controls="cms-tab-content-' . esc_attr($categoryKey) . '"';
        $html .= ' data-tab="' . esc_attr($categoryKey) . '"';
        $html .= ' data-state="' . esc_attr($dataState) . '"';
        $html .= ' data-orientation="horizontal"';
        $html .= ' id="cms-tab-trigger-' . esc_attr($categoryKey) . '"';
        if ($tabIndex !== 0) {
            $html .= ' tabindex="-1"';
        }
        $html .= '>';
        $html .= esc($category['label']);
        $html .= '</button>';
        $tabIndex++;
    }
    $html .= '</div>';
    
    // Tab panels
    $tabIndex = 0;
    foreach ($categories as $categoryKey => $category) {
        $isActive = $tabIndex === 0;
        $isActiveClass = $isActive ? 'active' : '';
        $dataState = $isActive ? 'active' : 'inactive';
        // Always start with display: none, JavaScript will show active one
        $html .= '<div class="cms-tabs-content ' . $isActiveClass . '"';
        $html .= ' role="tabpanel"';
        $html .= ' aria-labelledby="cms-tab-trigger-' . esc_attr($categoryKey) . '"';
        $html .= ' id="cms-tab-content-' . esc_attr($categoryKey) . '"';
        $html .= ' data-tab="' . esc_attr($categoryKey) . '"';
        $html .= ' data-state="' . esc_attr($dataState) . '"';
        $html .= ' data-orientation="horizontal"';
        $html .= ' style="display: none !important;"';
        if ($isActive) {
            $html .= ' tabindex="0"';
        } else {
            $html .= ' tabindex="-1"';
        }
        $html .= '>';
        
        // Determine if this tab needs a grid layout
        $hasColumns = false;
        $maxColumns = 1;
        foreach ($category['fields'] as $fieldDef) {
            if (isset($fieldDef['column'])) {
                $hasColumns = true;
                $col = intval($fieldDef['column']);
                $span = isset($fieldDef['span']) ? intval($fieldDef['span']) : 1;
                // Calculate max columns accounting for spans
                $maxColumns = max($maxColumns, $col + $span - 1);
            }
        }
        
        if ($hasColumns) {
            // Group fields by their group attribute
            $groupedFields = [];
            $ungroupedFields = [];
            
            foreach ($category['fields'] as $fieldName => $fieldDef) {
                $group = isset($fieldDef['group']) ? $fieldDef['group'] : null;
                $column = isset($fieldDef['column']) ? intval($fieldDef['column']) : null;
                $span = isset($fieldDef['span']) ? intval($fieldDef['span']) : null;
                $rows = isset($fieldDef['rows']) ? intval($fieldDef['rows']) : null;
                
                $fieldData = [
                    'name' => $fieldName,
                    'def' => $fieldDef,
                    'column' => $column,
                    'span' => $span,
                    'rows' => $rows
                ];
                
                if ($group !== null) {
                    if (!isset($groupedFields[$group])) {
                        $groupedFields[$group] = [];
                    }
                    $groupedFields[$group][] = $fieldData;
                } else {
                    $ungroupedFields[] = $fieldData;
                }
            }
            
            // Build grid layout with support for column, span, and rows
            $html .= '<div class="cms-form-grid" data-columns="' . esc_attr($maxColumns) . '">';
            
            // Render grouped fields - organize by columns for independent column flow
            foreach ($groupedFields as $groupName => $groupFields) {
                // Organize fields by column
                $fieldsByColumn = [];
                foreach ($groupFields as $field) {
                    $col = $field['column'] ?? 1;
                    if (!isset($fieldsByColumn[$col])) {
                        $fieldsByColumn[$col] = [];
                    }
                    $fieldsByColumn[$col][] = $field;
                }
                
                // Create a group row container
                $html .= '<div class="cms-form-group-row" data-group="' . esc_attr($groupName) . '" data-columns="' . esc_attr(count($fieldsByColumn)) . '">';
                
                // Render only columns that have fields (in order)
                ksort($fieldsByColumn);
                foreach ($fieldsByColumn as $col => $fields) {
                    $html .= '<div class="cms-form-group-column" data-column="' . esc_attr($col) . '">';
                    foreach ($fields as $field) {
                        $value = null;
                        if ($content && isset($content[$field['name']])) {
                            $value = $content[$field['name']];
                        }
                        // Remove column/span from field rendering since we're in a column container
                        $html .= renderFormField($field['name'], $field['def'], $value, null, null, $field['rows']);
                    }
                    $html .= '</div>';
                }
                
                $html .= '</div>';
            }
            
            // Render ungrouped fields (normal grid flow)
            foreach ($ungroupedFields as $field) {
                $value = null;
                if ($content && isset($content[$field['name']])) {
                    $value = $content[$field['name']];
                }
                $html .= renderFormField($field['name'], $field['def'], $value, $field['column'], $field['span'], $field['rows']);
            }
            
            $html .= '</div>';
        } else {
            // Standard single column layout
            foreach ($category['fields'] as $fieldName => $fieldDef) {
                $value = null;
                if ($content && isset($content[$fieldName])) {
                    $value = $content[$fieldName];
                }
                $html .= renderFormField($fieldName, $fieldDef, $value);
            }
        }
        
        $html .= '</div>';
        $tabIndex++;
    }
    
    // Form actions only for settings page (no banner, so buttons go here)
    // Pages and collections have buttons in the banner, so skip buttons here
    if ($isSettingsPage) {
        $html .= '<div class="cms-form-actions">';
        $html .= '<button type="submit" class="cms-button cms-button-primary">Save Settings</button>';
        $html .= '<button type="button" class="cms-button cms-button-ghost" onclick="window.history.back()">Cancel</button>';
        $html .= '</div>';
    }
    
    $html .= '</form>';
    
    // Add inline script to immediately hide inactive tabs
    $html .= '<script>';
    $html .= '(function(){';
    $html .= 'var tabs=document.querySelectorAll(".cms-tabs-content");';
    $html .= 'var found=false;';
    $html .= 'for(var i=0;i<tabs.length;i++){';
    $html .= 'var isActive=tabs[i].classList.contains("active")||tabs[i].getAttribute("data-state")==="active"||i===0;';
    $html .= 'if(isActive&&!found){';
    $html .= 'tabs[i].style.setProperty("display","block","important");';
    $html .= 'tabs[i].classList.add("active");';
    $html .= 'tabs[i].setAttribute("data-state","active");';
    $html .= 'found=true;';
    $html .= '}else{';
    $html .= 'tabs[i].style.setProperty("display","none","important");';
    $html .= 'tabs[i].classList.remove("active");';
    $html .= 'tabs[i].setAttribute("data-state","inactive");';
    $html .= '}';
    $html .= '}';
    $html .= '})();';
    $html .= '</script>';
    
    // Render structure modals OUTSIDE the main form (after form closes)
    // This prevents nested forms which break form submission
    if (isset($blueprint['fields'])) {
        foreach ($blueprint['fields'] as $fieldName => $fieldDef) {
            if (($fieldDef['type'] ?? '') === 'structure') {
                $structureFields = $fieldDef['fields'] ?? [];
                
                $html .= '<div class="cms-modal cms-structure-modal" id="cms-structure-modal-' . esc_attr($fieldName) . '" style="display: none;">';
                $html .= '<div class="cms-modal-backdrop" data-structure-modal-close="' . esc_attr($fieldName) . '"></div>';
                $html .= '<div class="cms-modal-content cms-structure-modal-content">';
                $html .= '<div class="cms-modal-header">';
                $html .= '<h3 class="cms-modal-title" data-structure-modal-title="' . esc_attr($fieldName) . '">Edit Item</h3>';
                $html .= '<button type="button" class="cms-modal-close" data-structure-modal-close="' . esc_attr($fieldName) . '" aria-label="Close">×</button>';
                $html .= '</div>';
                $html .= '<form class="cms-structure-item-form" data-field="' . esc_attr($fieldName) . '">';
                $html .= '<div class="cms-modal-body" data-structure-form-container="' . esc_attr($fieldName) . '">';
                // Form fields will be inserted here by JavaScript
                $html .= '</div>';
                $html .= '<div class="cms-modal-footer">';
                $html .= '<button type="button" class="cms-button cms-button-ghost" data-structure-modal-close="' . esc_attr($fieldName) . '">Cancel</button>';
                $html .= '<button type="submit" class="cms-button cms-button-primary">Save Item</button>';
                $html .= '</div>';
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
    }
    
    return $html;
}

/**
 * Generate core fields banner (Featured and Status)
 * These appear as a banner at the top of edit pages
 * 
 * @param array|null $content Content data (without _meta)
 * @param string $content_kind 'page' or 'collection'
 * @param string $content_type Content type name
 * @param string $action 'edit' or 'create'
 * @param array|null $meta Metadata (_meta array)
 * @param string $item_id Item ID for collections (filename without .json)
 */
function generateCoreFieldsCards($content = null, $content_kind = 'page', $content_type = '', $action = 'edit', $meta = null, $item_id = '') {
    $html = '<div class="cms-core-fields-banner">';
    
    // Featured field
    $featuredValue = isset($content['_featured']) ? $content['_featured'] : (isset($content['featured']) ? $content['featured'] : false);
    $checked = ($featuredValue === true || $featuredValue === '1' || $featuredValue === 'true' || $featuredValue === 'on');
    
    $html .= '<div class="cms-core-field-item">';
    $html .= '<div class="cms-switch-wrapper" data-field="_featured">';
    $html .= '<input type="hidden" name="_featured" value="' . ($featuredValue ? '1' : '0') . '" form="content-form" />';
    $html .= '<label class="cms-switch-label">';
    $html .= '<button type="button" role="switch" aria-checked="' . ($checked ? 'true' : 'false') . '" data-state="' . ($checked ? 'checked' : 'unchecked') . '" aria-label="Featured" class="cms-switch" data-switch="_featured" tabindex="0">';
    $html .= '<span class="cms-switch-thumb"></span>';
    $html .= '</button>';
    $html .= '<span class="cms-switch-label-text">Featured</span>';
    $html .= '</label>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Separator
    $html .= '<div class="cms-core-field-separator"></div>';
    
    // Status field
    $statusValue = isset($content['_status']) ? $content['_status'] : (isset($content['status']) ? $content['status'] : 'draft');
    $statusOptions = [
        'draft' => 'Draft',
        'published' => 'Published',
        'unlisted' => 'Unlisted'
    ];
    $selectedLabel = isset($statusOptions[$statusValue]) ? $statusOptions[$statusValue] : 'Draft';
    
    $html .= '<div class="cms-core-field-item">';
    $html .= '<div class="cms-dropdown" data-field="_status">';
    $html .= '<input type="hidden" name="_status" value="' . esc_attr($statusValue) . '" form="content-form" />';
    $html .= '<button type="button" class="cms-dropdown-trigger" aria-haspopup="true" aria-expanded="false" data-dropdown-trigger="_status">';
    $html .= '<span class="cms-dropdown-value">' . esc($selectedLabel) . '</span>';
    $html .= '<svg class="cms-dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L1 4h10z" fill="currentColor"/></svg>';
    $html .= '</button>';
    $html .= '<div class="cms-dropdown-content" data-dropdown-content="_status" role="menu" aria-orientation="vertical" style="display: none;">';
    foreach ($statusOptions as $optValue => $optLabel) {
        $selected = ($statusValue == $optValue) ? ' selected' : '';
        $html .= '<button type="button" class="cms-dropdown-item' . $selected . '" data-value="' . esc_attr($optValue) . '" data-dropdown-item="_status">';
        $html .= '<span>' . esc($optLabel) . '</span>';
        $html .= '</button>';
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Slug editor (for collections only)
    if ($content_kind === 'collection') {
        $html .= '<div class="cms-core-field-separator"></div>';
        $html .= '<div class="cms-core-field-item">';
        
        // Get slug value - prioritize content data, then extract from filename
        $slug_value = '';
        
        // First, try to get from content data
        if (!empty($content) && isset($content['_slug']) && !empty($content['_slug'])) {
            $slug_value = esc_attr($content['_slug']);
        } elseif ($action === 'edit' && !empty($item_id)) {
            // Extract slug from filename (remove timestamp)
            // Format: {slug}-{timestamp}.json
            $slug_base = preg_replace('/\.json$/', '', $item_id);
            $slug_base = preg_replace('/-\d+$/', '', $slug_base);
            $slug_value = !empty($slug_base) ? esc_attr($slug_base) : '';
            
            // If still empty, try loading from file
            if (empty($slug_value)) {
                $content_dir = COLLECTIONS_DIR . '/' . $content_type;
                $item_file = $content_dir . '/' . sanitizeFilename($item_id) . '.json';
                if (file_exists($item_file)) {
                    $item_data = readJson($item_file);
                    if ($item_data && isset($item_data['_slug']) && !empty($item_data['_slug'])) {
                        $slug_value = esc_attr($item_data['_slug']);
                    }
                }
            }
        }
        
        $html .= '<input type="text" name="_slug" value="' . $slug_value . '" form="content-form" class="cms-input" style="width: 200px; min-width: 150px;" placeholder="Enter slug (e.g., my-item)" />';
        $html .= '</div>';
    }
    
    // Separator before info items - REMOVED
    // Created/Updated timestamps section removed per user request
    
    // Preview/View button (always show when editing)
    if ($action === 'edit') {
        $html .= '<div class="cms-core-field-separator"></div>';
        
        // Build preview URL based on content type
        // Use BASE_PATH if available, otherwise use relative path
        $base_path = defined('BASE_PATH') ? BASE_PATH : '';
        $preview_url = $base_path;
        
        if ($content_kind === 'page') {
            // Pages: /{content_type}.html
            $preview_url .= '/' . urlencode($content_type) . '.html';
        } elseif ($content_kind === 'collection' && !empty($item_id)) {
            // Collections: /{content_type}/{slug}.html
            // Get slug from content or extract from filename
            $slug = '';
            if (!empty($content) && isset($content['_slug']) && !empty($content['_slug'])) {
                $slug = $content['_slug'];
            } else {
                // Extract from filename
                $slug_base = preg_replace('/\.json$/', '', $item_id);
                $slug = preg_replace('/-\d+$/', '', $slug_base);
            }
            
            if (!empty($slug)) {
                $preview_url .= '/' . urlencode($content_type) . '/' . urlencode($slug) . '.html';
            } else {
                // Fallback if no slug available
                $preview_url .= '/' . urlencode($content_type) . '/';
            }
        }
        
        // Ensure URL starts with /
        if (empty($preview_url) || $preview_url[0] !== '/') {
            $preview_url = '/' . ltrim($preview_url, '/');
        }
        
        $html .= '<div class="cms-core-field-item">';
        $html .= '<a href="' . esc_attr($preview_url) . '" target="_blank" class="cms-button cms-button-outline" style="text-decoration: none;">';
        $html .= '<span class="cms-button-text">View Page</span>';
        $html .= icon('external-link', 'cms-icon');
        $html .= '</a>';
        $html .= '</div>';
    }
    
    // Save and Cancel buttons (always show when editing or creating)
    // Add spacer to push buttons to the right
    $html .= '<div class="cms-core-field-separator"></div>';
    $html .= '<div class="cms-core-field-item" style="margin-left: auto;">';
    $html .= '<div style="display: flex; gap: var(--space-2);">';
    
    // Determine button text based on action
    $save_text = ($action === 'create') ? 'Create' : 'Save';
    if ($content_kind === 'collection') {
        $save_text .= ' Item';
    } elseif ($content_kind === 'page') {
        $save_text .= ' Content';
    } else {
        $save_text .= ' Settings';
    }
    
    $html .= '<button type="submit" form="content-form" class="cms-button cms-button-primary">' . esc($save_text) . '</button>';
    $html .= '<button type="button" class="cms-button cms-button-ghost" onclick="window.history.back()">Cancel</button>';
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    return $html;
}

