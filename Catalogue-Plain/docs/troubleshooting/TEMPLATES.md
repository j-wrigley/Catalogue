# Template Errors

Troubleshooting PHP template problems.

## Common Issues

### PHP Syntax Errors

**Symptoms:**
- Template fails to generate
- PHP parse errors
- HTML generation stops

**Common Errors:**

**1. Missing Semicolon**
```php
<?= catalogue('title') ?>  // Missing semicolon
```

**Solution:**
```php
<?= catalogue('title'); ?>
```

**2. Unclosed Tags**
```php
<?php if (condition): ?>
    Content
// Missing endif
```

**Solution:**
```php
<?php if (condition): ?>
    Content
<?php endif; ?>
```

**3. Quote Mismatch**
```php
<?= catalogue('title') ?>  // Wrong quotes
```

**Solution:**
```php
<?= catalogue('title'); ?>
```

### Missing Functions

**Symptoms:**
- "Call to undefined function" error
- Template functions not available
- catalogue() not found

**Solutions:**

1. **Check catalogue.php Loaded**
   ```php
   // Should be loaded in generator.php
   require_once LIB_DIR . '/catalogue.php';
   ```

2. **Verify Function Names**
   ```php
   // Correct
   <?= catalogue('title') ?>
   
   // Wrong
   <?= catalog('title') ?>
   ```

3. **Check Function Availability**
   ```php
   if (function_exists('catalogue')) {
       echo 'Function exists';
   }
   ```

### Undefined Variables

**Symptoms:**
- "Undefined variable" notices
- Missing content in output
- Template warnings

**Solutions:**

1. **Use Default Values**
   ```php
   <?= catalogue('title', 'Default Title') ?>
   ```

2. **Check Field Names**
   ```php
   // Verify field exists in blueprint
   <?= catalogue('title') ?>
   ```

3. **Use Conditionals**
   ```php
   <?php if (catalogue('title')): ?>
       <?= catalogue('title') ?>
   <?php endif; ?>
   ```

### Template Not Found

**Symptoms:**
- "Template not found" error
- HTML generation fails
- Missing template file

**Solutions:**

1. **Check Template Exists**
   ```bash
   ls catalogue/templates/{name}.php
   ```
   - File must exist
   - Filename must match blueprint
   - Case-sensitive

2. **Verify Template Name**
   - Must match blueprint name
   - `about.blueprint.yml` → `about.php`
   - Check exact match

3. **Check File Permissions**
   ```bash
   ls -la catalogue/templates/
   ```
   - Files must be readable
   - Check permissions (`644`)

### Template Output Issues

**Symptoms:**
- Partial HTML output
- Template stops mid-render
- Missing content sections

**Solutions:**

1. **Check for Fatal Errors**
   ```bash
   tail -f catalogue/logs/php_errors.log
   ```
   - Look for fatal errors
   - Check error messages
   - Verify function calls

2. **Check Memory Limits**
   ```php
   ini_set('memory_limit', '256M');
   ```
   - Increase if needed
   - Check for memory leaks
   - Monitor usage

3. **Check Execution Time**
   ```php
   ini_set('max_execution_time', 300);
   ```
   - Increase if needed
   - Optimize template code
   - Check for infinite loops

## Function-Specific Issues

### catalogue() Not Working

**Symptoms:**
- Function returns empty
- Default values not used
- Content not accessible

**Solutions:**

1. **Check Content Loaded**
   ```php
   // Content must be set before use
   setCatalogueContent($content);
   ```

2. **Verify Field Names**
   ```php
   // Check JSON structure
   {
     "title": "My Title"
   }
   
   // Use correct field name
   <?= catalogue('title') ?>
   ```

3. **Check Context**
   ```php
   // For site settings
   <?= catalogue('site_name', 'Site', 'site') ?>
   ```

### catalogueCollection() Issues

**Symptoms:**
- Collection not iterating
- Items not showing
- Loop not working

**Solutions:**

1. **Check Collection Name**
   ```php
   // Verify collection exists
   <?php foreach (catalogueCollection('posts') as $post): ?>
   ```

2. **Verify Content Files**
   ```bash
   ls catalogue/content/collections/posts/
   ```
   - Files must exist
   - Must be valid JSON
   - Check structure

3. **Check Filter**
   ```php
   // Filter by status
   <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
   ```

### snippet() Not Working

**Symptoms:**
- Snippet not included
- "Snippet not found" error
- Missing content sections

**Solutions:**

1. **Check Snippet File**
   ```bash
   ls catalogue/templates/snippets/{name}.php
   ```
   - File must exist
   - Must be in snippets directory
   - Check filename

2. **Verify Snippet Name**
   ```php
   <?= snippet('header') ?>
   ```
   - Name must match filename
   - Case-sensitive
   - No extension

3. **Check File Permissions**
   ```bash
   ls -la catalogue/templates/snippets/
   ```
   - Files must be readable
   - Check permissions

## Debugging Templates

### Enable Error Display

```php
// config.php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Test Template Independently

```php
<?php
require_once 'catalogue/config.php';
require_once 'catalogue/lib/catalogue.php';
require_once 'catalogue/lib/storage.php';

// Load content
$content = readJson('catalogue/content/pages/about/about.json');
setCatalogueContent($content);

// Test template
include 'catalogue/templates/about.php';
?>
```

### Check Template Output

```php
<?php
ob_start();
include 'catalogue/templates/about.php';
$output = ob_get_clean();
echo $output;
?>
```

## Best Practices

### Template Development

- **Test templates** - Verify before use
- **Check syntax** - Validate PHP syntax
- **Use defaults** - Provide fallback values
- **Handle errors** - Check for missing data

### Error Prevention

- **Validate field names** - Match blueprint
- **Use conditionals** - Check before use
- **Test independently** - Isolate problems
- **Review logs** - Check for errors

## See Also

- [Template System](../core-concepts/TEMPLATES.md) - Template details
- [HTML Generation Issues](./HTML_GENERATION.md) - Generation problems
- [Templates Documentation](../templates/README.md) - Template guide

