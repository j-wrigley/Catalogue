# Content Saving Issues

Troubleshooting problems saving content.

## Common Issues

### Content Not Saving

**Symptoms:**
- Save button does nothing
- No success message
- Content reverts after save

**Solutions:**

1. **Check Required Fields**
   - All required fields must be filled
   - Validation prevents save if empty
   - Check form for error messages

2. **Verify CSRF Token**
   - Token may have expired
   - Refresh page and try again
   - Check session is active

3. **Check File Permissions**
   ```bash
   ls -la catalogue/content/pages/
   ```
   - Directory must be writable (`755`)
   - Web server needs write access

4. **Check Error Logs**
   ```bash
   tail -f catalogue/logs/php_errors.log
   ```
   - Look for save errors
   - Check validation errors
   - Verify JSON encoding

### "Invalid JSON" Error

**Symptoms:**
- Save fails with JSON error
- Content not valid JSON
- Encoding issues

**Solutions:**

1. **Check Field Values**
   - Special characters may cause issues
   - Quotes must be escaped
   - Check for invalid characters

2. **Validate JSON**
   ```php
   json_decode($content_data);
   if (json_last_error() !== JSON_ERROR_NONE) {
       echo json_last_error_msg();
   }
   ```

3. **Check Encoding**
   - Ensure UTF-8 encoding
   - Check for BOM characters
   - Verify character encoding

### Content Saves But Reverts

**Symptoms:**
- Save succeeds
- Content reverts on reload
- Changes not persisted

**Solutions:**

1. **Check File Write**
   ```bash
   ls -la catalogue/content/pages/{name}/{name}.json
   ```
   - Verify file updated
   - Check modification time
   - Ensure write succeeded

2. **Check File Permissions**
   ```bash
   ls -la catalogue/content/pages/{name}/
   ```
   - Directory: `755`
   - File: `644`
   - Web server write access

3. **Verify JSON Structure**
   ```bash
   cat catalogue/content/pages/{name}/{name}.json | python -m json.tool
   ```
   - Validate JSON syntax
   - Check structure correct
   - Verify encoding

### Slug Not Updating

**Symptoms:**
- Slug changed but URL same
- Filename not updated
- Old file still exists

**Solutions:**

1. **Check Slug Format**
   - Must be lowercase
   - Hyphens allowed
   - No special characters

2. **Verify File Rename**
   ```bash
   ls -la catalogue/content/collections/{name}/
   ```
   - Old file should be deleted
   - New file should exist
   - Filename matches slug

3. **Check File Permissions**
   - Directory must be writable
   - Files must be deletable
   - Web server needs access

### Collection Item Not Saving

**Symptoms:**
- Item save fails
- New item not created
- Existing item not updated

**Solutions:**

1. **Check Collection Directory**
   ```bash
   ls -la catalogue/content/collections/{name}/
   ```
   - Directory must exist
   - Must be writable
   - Check permissions

2. **Verify Slug**
   - Slug must be valid
   - Must be unique
   - Check for conflicts

3. **Check Blueprint**
   ```bash
   ls catalogue/blueprints/{name}.blueprint.yml
   ```
   - Blueprint must exist
   - Must be valid YAML
   - Check field definitions

## Validation Issues

### Required Fields Not Filled

**Symptoms:**
- Save button disabled
- Error message shown
- Validation prevents save

**Solutions:**

1. **Fill Required Fields**
   - Check form for required indicators
   - Fill all required fields
   - Verify values correct

2. **Check Field Types**
   - Ensure correct data type
   - Verify format matches
   - Check validation rules

### Field Validation Errors

**Symptoms:**
- Specific field errors
- Invalid format messages
- Type mismatch errors

**Solutions:**

1. **Check Field Type**
   - Verify correct type used
   - Check format requirements
   - Review validation rules

2. **Verify Field Value**
   - Check value format
   - Ensure matches type
   - Verify options valid

## Debugging Steps

### Step 1: Enable Debug Mode

```php
// config.php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Step 2: Check Browser Console

- Open browser developer tools
- Check for JavaScript errors
- Verify AJAX requests
- Check response messages

### Step 3: Check Server Logs

```bash
tail -f catalogue/logs/php_errors.log
```

### Step 4: Test Save Manually

```php
<?php
require_once 'catalogue/config.php';
require_once 'catalogue/lib/storage.php';

$data = [
    'title' => 'Test',
    'content' => 'Test content'
];

$result = writeJson('catalogue/content/pages/test/test.json', $data);
var_dump($result);
?>
```

## Best Practices

### Prevention

- **Fill required fields** - Complete all required
- **Validate before save** - Check values correct
- **Check permissions** - Ensure writable
- **Test saves** - Verify after changes

### When Issues Occur

1. **Check error messages** - Usually informative
2. **Verify permissions** - Common cause
3. **Check validation** - May be too strict
4. **Review logs** - Detailed error info
5. **Test manually** - Isolate problem

## See Also

- [File Permissions](./FILE_PERMISSIONS.md) - Permission issues
- [Content Management](../content-management/CREATING_CONTENT.md) - Creating content
- [Content Structure](../content-management/CONTENT_STRUCTURE.md) - JSON format

