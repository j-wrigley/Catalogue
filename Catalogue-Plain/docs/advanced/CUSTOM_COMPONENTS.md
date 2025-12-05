# Custom Components

Creating custom form fields and extending CMS functionality.

## Overview

While the CMS includes a comprehensive set of built-in field types, you may need to extend functionality with custom components or modify existing behavior.

## Extending Form Fields

### Custom Field Types

To add a custom field type, you need to:

1. **Add Field Type to Blueprint Parser** (`/catalogue/lib/blueprint.php`)
2. **Add Form Rendering** (`/catalogue/lib/form.php`)
3. **Add Template Rendering** (`/catalogue/lib/catalogue.php`)
4. **Add JavaScript Handler** (`/catalogue/panel/assets/js/panel.js`)

### Example: Adding a Date Picker Field

#### 1. Update Blueprint Parser

```php
// In catalogue/lib/blueprint.php
// Add 'date' to allowed field types
```

#### 2. Add Form Rendering

```php
// In catalogue/lib/form.php
case 'date':
    return '<input type="date" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" class="cms-input" />';
```

#### 3. Add Template Rendering

```php
// In catalogue/lib/catalogue.php renderField()
case 'date':
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
```

#### 4. Add JavaScript (if needed)

```javascript
// In catalogue/panel/assets/js/panel.js
// Add date picker initialization if needed
```

## Modifying Existing Components

### Customizing Form Field Appearance

You can customize field appearance by modifying CSS:

```css
/* Customize input fields */
.cms-input[type="text"] {
    /* Your custom styles */
}

/* Customize specific field types */
.cms-form-field[data-type="markdown"] {
    /* Custom markdown editor styles */
}
```

### Extending JavaScript Functionality

Add custom JavaScript handlers:

```javascript
// In panel.js or custom script
document.addEventListener('DOMContentLoaded', function() {
    // Add custom handlers for specific fields
    document.querySelectorAll('[data-field="custom_field"]').forEach(field => {
        // Custom logic
    });
});
```

## Custom Validation

### Client-Side Validation

Add custom validation in form submission handlers:

```javascript
// In panel.js
form.addEventListener('submit', function(e) {
    const customField = this.querySelector('[name="custom_field"]');
    
    if (!customField.value || customField.value.length < 5) {
        e.preventDefault();
        toast.error('Custom field must be at least 5 characters');
        return false;
    }
});
```

### Server-Side Validation

Add validation in `save.php`:

```php
// In catalogue/panel/actions/save.php
if (isset($content_data['custom_field'])) {
    if (strlen($content_data['custom_field']) < 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Custom field validation failed']);
        exit;
    }
}
```

## Custom Template Functions

### Adding Helper Functions

Create custom template functions in `catalogue.php`:

```php
// In catalogue/lib/catalogue.php

/**
 * Custom helper function
 */
function customHelper($param) {
    // Your custom logic
    return $result;
}
```

### Using Custom Functions

```php
<!-- In templates -->
<?= customHelper('value') ?>
```

## Extending Media Handling

### Custom Media Metadata

Add custom metadata fields to media blueprint:

```yaml
# media.blueprint.yml
fields:
  custom_field:
    type: text
    label: Custom Metadata
```

### Custom Media Processing

Modify media upload handler (`/catalogue/panel/actions/media.php`) to add custom processing:

```php
// After file upload
if ($file_type === 'image') {
    // Custom image processing
    // Resize, optimize, etc.
}
```

## Custom Dashboard Widgets

### Adding Dashboard Cards

Modify dashboard page (`/catalogue/panel/pages/dashboard.php`):

```php
// Add custom widget
<div class="cms-card">
    <div class="cms-card-body">
        <h3>Custom Widget</h3>
        <?php
        // Your custom logic
        ?>
    </div>
</div>
```

## Best Practices

1. **Don't Modify Core Files**: Create extension files instead of modifying core CMS files
2. **Use Hooks/Events**: If available, use extension points rather than direct modification
3. **Document Changes**: Document any customizations for future reference
4. **Test Thoroughly**: Test custom components across different scenarios
5. **Backup Before Changes**: Always backup before making modifications

## Limitations

### What You Can't Easily Extend

- Core authentication system
- File storage structure
- Blueprint parsing logic (without core modifications)
- Template generation engine

### Recommended Approach

For major customizations:
1. Fork the CMS repository
2. Make modifications in your fork
3. Document all changes
4. Keep fork updated with main repository

## Examples

### Custom Field: Rating Stars

```php
// In form.php
case 'rating':
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $checked = ($value == $i) ? 'checked' : '';
        $stars .= '<input type="radio" name="' . $name . '" value="' . $i . '" ' . $checked . ' />';
    }
    return '<div class="rating-stars">' . $stars . '</div>';
```

### Custom Template Helper

```php
// In catalogue.php
function formatCurrency($amount, $currency = 'USD') {
    return $currency . ' ' . number_format($amount, 2);
}
```

```php
<!-- In template -->
Price: <?= formatCurrency(catalogue('price'), 'USD') ?>
```

## See Also

- [API Reference](../api-reference/README.md) - Available functions
- [Blueprints](../blueprints/README.md) - Field types and options
- [Templates](../templates/README.md) - Template system
- [Configuration](../configuration/README.md) - System configuration

