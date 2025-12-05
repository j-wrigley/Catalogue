# File Permissions Issues

Troubleshooting file permission problems.

## Common Issues

### Cannot Save Content

**Symptoms:**
- Save button doesn't work
- "Permission denied" errors
- Content not updating

**Solutions:**

1. **Check Directory Permissions**
   ```bash
   ls -la catalogue/content/pages/
   ```
   - Should be `755` (drwxr-xr-x)
   - Web server must have write access

2. **Fix Directory Permissions**
   ```bash
   chmod 755 catalogue/content/pages/
   chmod 755 catalogue/content/collections/
   chmod 755 catalogue/content/media/
   ```

3. **Check File Permissions**
   ```bash
   ls -la catalogue/content/pages/about/about.json
   ```
   - Should be `644` (-rw-r--r--)
   - Web server must have write access

4. **Fix File Permissions**
   ```bash
   find catalogue/content -type f -exec chmod 644 {} \;
   ```

### Cannot Upload Files

**Symptoms:**
- Upload fails silently
- "Permission denied" errors
- Files not appearing

**Solutions:**

1. **Check Uploads Directory**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Should be `755` (drwxr-xr-x)
   - Web server must have write access

2. **Fix Uploads Permissions**
   ```bash
   chmod 755 catalogue/uploads/
   ```

3. **Check Ownership**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Should be owned by web server user
   - `www-data` (Apache) or `nginx` (Nginx)

4. **Fix Ownership**
   ```bash
   # Apache (Debian/Ubuntu)
   chown -R www-data:www-data catalogue/uploads/
   
   # Apache (CentOS/RHEL)
   chown -R apache:apache catalogue/uploads/
   
   # Nginx
   chown -R nginx:nginx catalogue/uploads/
   ```

### Cannot Generate HTML

**Symptoms:**
- HTML files not created
- "Permission denied" errors
- Generation fails

**Solutions:**

1. **Check Root Directory**
   ```bash
   ls -la . | grep html
   ```
   - Root directory must be writable
   - Web server needs write access

2. **Fix Root Permissions**
   ```bash
   chmod 755 .
   ```

3. **Check File Creation**
   ```bash
   touch test.html
   rm test.html
   ```
   - Test write access
   - Verify permissions correct

### Cannot Write Logs

**Symptoms:**
- No error logs created
- Log file empty
- Errors not logged

**Solutions:**

1. **Check Logs Directory**
   ```bash
   ls -la catalogue/logs/
   ```
   - Should be `755` (drwxr-xr-x)
   - Web server must have write access

2. **Create Logs Directory**
   ```bash
   mkdir -p catalogue/logs/
   chmod 755 catalogue/logs/
   ```

3. **Check Log File**
   ```bash
   touch catalogue/logs/php_errors.log
   chmod 644 catalogue/logs/php_errors.log
   ```

## Permission Requirements

### Directories

**Required:** `755` (drwxr-xr-x)

**Directories:**
- `catalogue/content/`
- `catalogue/content/pages/`
- `catalogue/content/collections/`
- `catalogue/content/media/`
- `catalogue/uploads/`
- `catalogue/logs/`
- `catalogue/data/`

**Set Permissions:**
```bash
find catalogue/content -type d -exec chmod 755 {} \;
chmod 755 catalogue/uploads/
chmod 755 catalogue/logs/
chmod 755 catalogue/data/
```

### Files

**Required:** `644` (-rw-r--r--)

**Files:**
- JSON content files
- Generated HTML files
- Log files
- Configuration files

**Set Permissions:**
```bash
find catalogue/content -type f -exec chmod 644 {} \;
find catalogue/data -type f -exec chmod 644 {} \;
chmod 644 catalogue/logs/*.log
chmod 644 catalogue/config.php
```

## Ownership Issues

### Wrong Ownership

**Symptoms:**
- Files created as wrong user
- Permission errors
- Cannot access files

**Solutions:**

1. **Identify Web Server User**
   ```bash
   ps aux | grep -E 'apache|nginx|httpd'
   ```

2. **Fix Ownership**
   ```bash
   # Apache (Debian/Ubuntu)
   chown -R www-data:www-data catalogue/
   
   # Apache (CentOS/RHEL)
   chown -R apache:apache catalogue/
   
   # Nginx
   chown -R nginx:nginx catalogue/
   ```

### Mixed Ownership

**Symptoms:**
- Some files owned by different users
- Inconsistent permissions
- Random permission errors

**Solutions:**

1. **Check Ownership**
   ```bash
   find catalogue/ -ls | awk '{print $3, $5, $11}'
   ```

2. **Fix All Ownership**
   ```bash
   chown -R www-data:www-data catalogue/
   ```

## Shared Hosting Issues

### Limited Permissions

**Symptoms:**
- Cannot change permissions
- Permission errors persist
- Hosting restrictions

**Solutions:**

1. **Use Hosting Control Panel**
   - File manager permissions
   - CHMOD tool
   - Set via control panel

2. **Contact Hosting Support**
   - Request permission changes
   - Explain requirements
   - Provide specific paths

3. **Check .htaccess**
   - May override permissions
   - Verify .htaccess rules
   - Test without .htaccess

## Permission Checking Script

### Test Permissions

```php
<?php
// test-permissions.php
$dirs = [
    'catalogue/content',
    'catalogue/uploads',
    'catalogue/logs',
    'catalogue/data'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        echo "Missing: $dir\n";
        continue;
    }
    
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    $writable = is_writable($dir);
    
    echo "$dir: $perms " . ($writable ? "✓" : "✗") . "\n";
}
```

## Best Practices

### Setting Permissions

- **Use 755 for directories** - Standard web directory
- **Use 644 for files** - Standard web file
- **Never use 777** - Security risk
- **Set ownership correctly** - Web server user

### Maintenance

- **Check permissions regularly** - Monitor changes
- **Fix immediately** - Don't ignore errors
- **Document changes** - Note permission changes
- **Backup before changes** - Safety first

## See Also

- [Environment Setup](../configuration/ENVIRONMENT.md) - Server configuration
- [File Security](../security/FILE_SECURITY.md) - Security considerations

