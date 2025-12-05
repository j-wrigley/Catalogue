# Upload Problems

Troubleshooting file upload issues.

## Common Issues

### Upload Fails Silently

**Symptoms:**
- No error message
- File doesn't appear
- Upload button does nothing

**Solutions:**

1. **Check File Size Limits**
   ```php
   <?php
   echo ini_get('upload_max_filesize');
   echo ini_get('post_max_size');
   ?>
   ```
   - `post_max_size` must be larger than `upload_max_filesize`
   - Check both PHP and .htaccess settings

2. **Increase Limits**
   ```apache
   # .htaccess
   php_value upload_max_filesize 50M
   php_value post_max_size 50M
   ```

3. **Check Error Logs**
   ```bash
   tail -f catalogue/logs/php_errors.log
   ```
   - Look for upload errors
   - Check PHP error messages
   - Verify file validation

### "File Size Exceeds Server Limit"

**Symptoms:**
- Error message displayed
- Upload rejected
- File too large

**Solutions:**

1. **Check Current Limits**
   ```php
   echo ini_get('upload_max_filesize');
   echo ini_get('post_max_size');
   ```

2. **Increase PHP Limits**
   ```ini
   # php.ini
   upload_max_filesize = 50M
   post_max_size = 50M
   ```

3. **Update .htaccess**
   ```apache
   php_value upload_max_filesize 50M
   php_value post_max_size 50M
   ```

4. **Restart Server**
   ```bash
   sudo systemctl restart apache2
   # or
   sudo systemctl restart php-fpm
   ```

### "Invalid File Type"

**Symptoms:**
- File rejected
- "Invalid file type" error
- Upload fails

**Solutions:**

1. **Check Allowed Types**
   - Images: JPG, PNG, GIF, WebP, SVG
   - Documents: PDF, ZIP
   - Check file extension

2. **Verify MIME Type**
   - Server checks MIME type
   - Extension must match type
   - File must be valid

3. **Check File Validation**
   - Review validation code
   - Check allowed extensions
   - Verify MIME type list

### Upload Directory Not Writable

**Symptoms:**
- "Permission denied" error
- Upload fails
- Directory errors

**Solutions:**

1. **Check Directory Permissions**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Should be `755` (drwxr-xr-x)
   - Web server must have write access

2. **Fix Permissions**
   ```bash
   chmod 755 catalogue/uploads/
   ```

3. **Check Ownership**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Should be owned by web server user

4. **Fix Ownership**
   ```bash
   chown -R www-data:www-data catalogue/uploads/
   ```

### Files Upload But Don't Appear

**Symptoms:**
- Upload succeeds
- Files not visible
- Media library empty

**Solutions:**

1. **Check Upload Location**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Verify files uploaded
   - Check correct directory
   - Verify folder structure

2. **Check File Permissions**
   ```bash
   ls -la catalogue/uploads/*.jpg
   ```
   - Files should be readable
   - Check permissions correct

3. **Refresh Media Library**
   - Reload media page
   - Clear browser cache
   - Check folder navigation

### Multiple File Upload Issues

**Symptoms:**
- Only some files upload
- Upload stops mid-process
- Partial upload success

**Solutions:**

1. **Check Individual Files**
   - Verify each file valid
   - Check file sizes
   - Ensure types allowed

2. **Increase Limits**
   ```apache
   php_value max_file_uploads 20
   php_value max_execution_time 300
   ```

3. **Check Server Resources**
   - Monitor memory usage
   - Check disk space
   - Verify server capacity

## Upload Configuration

### PHP Settings

**Required Settings:**
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_file_uploads = 20
max_execution_time = 300
memory_limit = 256M
```

### .htaccess Settings

**Required Settings:**
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300
```

### Verification

**Test Upload Limits:**
```php
<?php
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
?>
```

## Error Messages

### "POST Content-Length Exceeded"

**Cause:** File size exceeds `post_max_size`

**Solution:**
- Increase `post_max_size`
- Must be larger than `upload_max_filesize`
- Restart server

### "Failed to Move Uploaded File"

**Cause:** Directory not writable or path invalid

**Solution:**
- Check directory permissions
- Verify path is correct
- Check disk space

### "Invalid CSRF Token"

**Cause:** CSRF token expired or invalid

**Solution:**
- Refresh page
- Try upload again
- Check session active

## Debugging Steps

### Step 1: Check PHP Configuration

```php
<?php
phpinfo();
// Look for upload settings
?>
```

### Step 2: Test Upload Directory

```bash
# Test write access
touch catalogue/uploads/test.txt
rm catalogue/uploads/test.txt
```

### Step 3: Check Error Logs

```bash
tail -f catalogue/logs/php_errors.log
# Try upload and watch logs
```

### Step 4: Enable Debug Mode

```php
// config.php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Best Practices

### Prevention

- **Set appropriate limits** - Balance needs and security
- **Monitor uploads** - Watch for issues
- **Validate files** - Check types and sizes
- **Regular cleanup** - Remove unused files

### When Issues Occur

1. **Check error logs** - Most issues logged
2. **Verify permissions** - Common cause
3. **Test limits** - May be too restrictive
4. **Check disk space** - May be full
5. **Review validation** - May be too strict

## See Also

- [File Permissions](./FILE_PERMISSIONS.md) - Permission issues
- [Media Management](../media-management/UPLOADING.md) - Upload guide
- [Environment Setup](../configuration/ENVIRONMENT.md) - Server config

