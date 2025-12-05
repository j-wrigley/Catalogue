# Path & URL Issues

Troubleshooting path and URL problems.

## Common Issues

### URLs Not Working (404 Errors)

**Symptoms:**
- Pages return 404
- URLs don't resolve
- `.html` extension required

**Solutions:**

1. **Check .htaccess Exists**
   ```bash
   ls -la .htaccess
   ```
   - Must be in root directory
   - Must contain rewrite rules
   - Check file permissions

2. **Verify mod_rewrite Enabled**
   ```bash
   apache2ctl -M | grep rewrite
   ```
   - Should show `rewrite_module`
   - Enable if missing: `sudo a2enmod rewrite`

3. **Check Rewrite Rules**
   ```apache
   # .htaccess should contain:
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME}.html -f
   RewriteRule ^(.*)$ $1.html [L]
   ```

4. **Verify AllowOverride**
   ```apache
   # Apache config
   <Directory /var/www/html>
       AllowOverride All
   </Directory>
   ```

### URLs Include `/catalogue/` Path

**Symptoms:**
- URLs have `/catalogue/` prefix
- Links incorrect
- Navigation broken

**Solutions:**

1. **Check BASE_PATH**
   ```php
   // config.php
   echo BASE_PATH;
   ```
   - Should be empty for root install
   - Should be `/subfolder` for subfolder install

2. **Verify Path Detection**
   - Check `config.php` path detection
   - Verify document root correct
   - Ensure paths auto-detected

3. **Check Template Usage**
   ```php
   // Use BASE_PATH in templates
   <link href="<?= BASE_PATH ?>/assets/css/style.css">
   ```

### Subfolder Installation Issues

**Symptoms:**
- CMS in subfolder not working
- Paths incorrect
- Assets not loading

**Solutions:**

1. **Verify BASE_PATH**
   ```php
   // Should be /subfolder for subfolder install
   define('BASE_PATH', '/subfolder');
   ```

2. **Check .htaccess Location**
   - Must be in root (not subfolder)
   - Rewrite rules must account for subfolder

3. **Update Asset Paths**
   ```php
   // Use BASE_PATH for all assets
   <link href="<?= BASE_PATH ?>/assets/css/style.css">
   ```

### Assets Not Loading

**Symptoms:**
- CSS/JS files 404
- Images not displaying
- Broken asset links

**Solutions:**

1. **Check Asset Paths**
   ```php
   // Verify BASE_PATH used
   <link href="<?= BASE_PATH ?>/assets/css/style.css">
   ```

2. **Verify File Exists**
   ```bash
   ls -la assets/css/style.css
   ```

3. **Check File Permissions**
   ```bash
   ls -la assets/
   ```
   - Should be readable
   - Check permissions correct

### Path Traversal Errors

**Symptoms:**
- "Invalid path" errors
- File access denied
- Security warnings

**Solutions:**

1. **Check Path Validation**
   - Paths validated for security
   - Directory traversal prevented
   - Real paths checked

2. **Verify File Locations**
   - Files must be in allowed directories
   - Paths must be relative
   - No `../` allowed

3. **Check Error Logs**
   ```bash
   tail -f catalogue/logs/php_errors.log
   ```
   - Look for path traversal attempts
   - Check validation errors

## URL Configuration

### Root Installation

**Structure:**
```
/var/www/html/
  catalogue/
  assets/
  index.html
```

**BASE_PATH:** `''` (empty)

**URLs:**
- Site: `http://example.com/`
- Admin: `http://example.com/catalogue/`
- Assets: `http://example.com/assets/`

### Subfolder Installation

**Structure:**
```
/var/www/html/
  mysite/
    catalogue/
    assets/
    index.html
```

**BASE_PATH:** `'/mysite'`

**URLs:**
- Site: `http://example.com/mysite/`
- Admin: `http://example.com/mysite/catalogue/`
- Assets: `http://example.com/mysite/assets/`

## .htaccess Configuration

### Required Rules

```apache
# URL Rewriting
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Skip CMS and assets directories
    RewriteCond %{REQUEST_URI} ^/([^/]+/)?(catalogue|assets)/ [NC]
    RewriteRule ^ - [L]
    
    # Add .html extension if file exists
    RewriteCond %{REQUEST_FILENAME}.html -f
    RewriteRule ^(.*)$ $1.html [L]
</IfModule>
```

### Common Problems

**1. Rewrite Rules Not Working**
- Check `mod_rewrite` enabled
- Verify `AllowOverride All`
- Check .htaccess location

**2. Infinite Redirects**
- Check rewrite conditions
- Verify file existence checks
- Review rule order

**3. Wrong File Served**
- Check rule priority
- Verify file matching
- Review conditions

## Path Detection

### Auto-Detection

**Process:**
1. Detect document root
2. Calculate CMS path
3. Determine BASE_PATH
4. Set CMS_URL

**Manual Override:**
```php
// config.php
define('BASE_PATH', '/custom-path');
define('CMS_URL', '/custom-path/catalogue');
```

### Debugging Path Detection

```php
<?php
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "BASE_PATH: " . BASE_PATH . "\n";
echo "CMS_URL: " . CMS_URL . "\n";
?>
```

## Best Practices

### Path Management

- **Use BASE_PATH** - Always use in templates
- **Relative paths** - Avoid absolute paths
- **Test paths** - Verify after changes
- **Document paths** - Note custom paths

### URL Management

- **Clean URLs** - Use .htaccess rewriting
- **Consistent structure** - Follow conventions
- **Test URLs** - Verify all links work
- **Monitor 404s** - Check for broken links

## See Also

- [Path Configuration](../configuration/PATHS.md) - Path details
- [URL Structure](../site-generation/URL_STRUCTURE.md) - URL patterns
- [Environment Setup](../configuration/ENVIRONMENT.md) - Server config

