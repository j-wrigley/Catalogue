# HTML Generation Issues

Troubleshooting problems with HTML file generation.

## Common Issues

### Pages Not Generating HTML

**Symptoms:**
- Content saves but HTML file not created
- Page returns 404 error
- Template changes not reflected

**Solutions:**

1. **Check Blueprint Exists**
   ```bash
   ls catalogue/blueprints/{name}.blueprint.yml
   ```
   - Blueprint file must exist
   - Filename must match exactly
   - Case-sensitive

2. **Check Template Exists**
   ```bash
   ls catalogue/templates/{name}.php
   ```
   - Template file must exist
   - Filename must match blueprint name
   - Must be valid PHP

3. **Check Content File Exists**
   ```bash
   ls catalogue/content/pages/{name}/{name}.json
   ```
   - Content JSON must exist
   - File must be valid JSON
   - Must contain required fields

4. **Check File Permissions**
   ```bash
   ls -la catalogue/content/pages/
   ```
   - Directories: `755`
   - Files: `644`
   - Web server must have write access

5. **Check Error Logs**
   ```bash
   tail -f catalogue/logs/php_errors.log
   ```
   - Look for generation errors
   - Check template syntax errors
   - Verify function availability

### Template Errors

**Symptoms:**
- PHP errors in logs
- HTML generation fails
- Partial HTML output

**Common Errors:**

**1. Syntax Errors**
```
Parse error: syntax error, unexpected...
```

**Solution:**
- Check PHP syntax in template
- Validate template file
- Test template independently

**2. Missing Functions**
```
Fatal error: Call to undefined function...
```

**Solution:**
- Ensure `catalogue.php` is loaded
- Check function names are correct
- Verify library files included

**3. Undefined Variables**
```
Notice: Undefined variable...
```

**Solution:**
- Check field names match blueprint
- Use default values: `catalogue('field', 'default')`
- Verify content JSON structure

### Generation Fails Silently

**Symptoms:**
- No error messages
- HTML file not created
- No log entries

**Solutions:**

1. **Enable Debug Mode**
   ```php
   // config.php
   define('DEBUG_MODE', true);
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

2. **Check Output Buffering**
   - Ensure output buffering enabled
   - Check for premature output
   - Verify headers not sent

3. **Verify Write Permissions**
   ```bash
   touch catalogue/../test.html
   ```
   - Test write access to root
   - Check directory permissions
   - Verify web server ownership

### Partial HTML Generation

**Symptoms:**
- HTML file created but incomplete
- Template stops mid-render
- Missing content sections

**Solutions:**

1. **Check Template Logic**
   - Look for infinite loops
   - Check conditional statements
   - Verify loop termination

2. **Check Memory Limits**
   ```php
   ini_set('memory_limit', '256M');
   ```
   - Increase PHP memory limit
   - Check for memory leaks
   - Monitor memory usage

3. **Check Execution Time**
   ```php
   ini_set('max_execution_time', 300);
   ```
   - Increase execution time
   - Optimize template code
   - Check for slow operations

## Collection Generation Issues

### Collection Items Not Generating

**Symptoms:**
- Individual items not creating HTML
- Collection list empty
- Items missing from site

**Solutions:**

1. **Check Collection Directory**
   ```bash
   ls catalogue/content/collections/{name}/
   ```
   - Directory must exist
   - JSON files must be present
   - Files must be valid JSON

2. **Check Slug Format**
   - Slugs must be valid (lowercase, hyphens)
   - No special characters
   - Matches filename

3. **Verify Template Exists**
   ```bash
   ls catalogue/templates/{collection-name}.php
   ```
   - Template must exist
   - Must handle collection items
   - Check iteration logic

### Collection Regeneration Issues

**Symptoms:**
- New items not appearing
- Changes not reflected
- Regeneration incomplete

**Solutions:**

1. **Manual Regeneration**
   - Use "Regenerate All" in admin
   - Check regeneration status
   - Review error messages

2. **Check Collection Structure**
   - Verify JSON files valid
   - Check slug consistency
   - Ensure required fields present

## Special Page Issues

### Home Page Not Generating

**Symptoms:**
- `index.html` not created
- Home page shows 404
- Template not found

**Solutions:**

1. **Check Blueprint Name**
   - Must be `home.blueprint.yml`
   - Exact filename required
   - Case-sensitive

2. **Check Template Name**
   - Must be `templates/home.php`
   - Exact filename required
   - Must exist

3. **Check Content File**
   ```bash
   ls catalogue/content/pages/home/home.json
   ```
   - Content file must exist
   - Must be valid JSON

### 404 Page Not Generating

**Symptoms:**
- `404.html` not created
- Default Apache 404 shown
- Custom 404 not working

**Solutions:**

1. **Check Blueprint Exists**
   ```bash
   ls catalogue/blueprints/404.blueprint.yml
   ```

2. **Check Template Exists**
   ```bash
   ls catalogue/templates/404.php
   ```

3. **Check Content File**
   ```bash
   ls catalogue/content/pages/404/404.json
   ```

4. **Verify Handler**
   ```bash
   ls catalogue/404-handler.php
   ```

## Debugging Steps

### Step 1: Enable Debug Mode

```php
// config.php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

### Step 2: Check Error Logs

```bash
tail -f catalogue/logs/php_errors.log
```

### Step 3: Test Template Independently

```php
<?php
require_once 'catalogue/config.php';
require_once 'catalogue/lib/catalogue.php';

// Load content
$content = readJson('catalogue/content/pages/about/about.json');
setCatalogueContent($content);

// Test template
include 'catalogue/templates/about.php';
```

### Step 4: Verify File Permissions

```bash
# Check permissions
ls -la catalogue/content/pages/
ls -la catalogue/templates/
ls -la catalogue/blueprints/

# Fix if needed
chmod 755 catalogue/content/pages/
chmod 644 catalogue/content/pages/**/*.json
```

## Best Practices

### Prevention

- **Test templates** - Verify before deployment
- **Check syntax** - Validate PHP syntax
- **Monitor logs** - Regular log review
- **Backup before changes** - Safety first

### When Issues Occur

1. **Check error logs first** - Most issues logged
2. **Enable debug mode** - See detailed errors
3. **Test independently** - Isolate problem
4. **Verify permissions** - Common cause
5. **Check file existence** - Ensure files present

## See Also

- [File Permissions](./FILE_PERMISSIONS.md) - Permission issues
- [Template Errors](./TEMPLATES.md) - Template problems
- [Site Generation](../site-generation/HOW_IT_WORKS.md) - Generation process

