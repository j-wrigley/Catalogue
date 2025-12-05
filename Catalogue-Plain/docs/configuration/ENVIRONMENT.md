# Environment Setup

Server and environment configuration for the CMS.

## Overview

The CMS requires specific server and PHP configuration to function correctly. This guide covers:
- PHP requirements
- Apache configuration
- File permissions
- Upload limits
- Error handling

## PHP Requirements

### Minimum Version

**PHP 7.4+** required

**Check Version:**
```bash
php -v
```

### Required Extensions

- **JSON** - For JSON file handling
- **YAML** - For blueprint parsing (or use Symfony YAML)
- **GD** or **Imagick** - For image processing (optional)
- **mbstring** - For string handling
- **fileinfo** - For MIME type detection

**Check Extensions:**
```bash
php -m
```

### Recommended Settings

**php.ini Configuration:**
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
display_errors = Off
log_errors = On
error_log = /path/to/logs/php_errors.log
```

## Apache Configuration

### Required Modules

- **mod_rewrite** - For URL rewriting
- **mod_php** or **PHP-FPM** - PHP processing

**Check Modules:**
```bash
apache2ctl -M
# or
httpd -M
```

**Enable mod_rewrite:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### .htaccess Configuration

**Location:** Root directory (same level as `catalogue/`)

**Required Directives:**

1. **PHP Upload Limits:**
```apache
<IfModule mod_php7.c>
    php_value upload_max_filesize 50M
    php_value post_max_size 50M
    php_value max_execution_time 300
    php_value max_input_time 300
</IfModule>
```

2. **URL Rewriting:**
```apache
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

3. **File Protection:**
```apache
<FilesMatch "\.(yml|yaml|json|log|md)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>
```

### AllowOverride

Ensure `.htaccess` is allowed:

**Apache Configuration:**
```apache
<Directory /var/www/html>
    AllowOverride All
    Require all granted
</Directory>
```

## File Permissions

### Directory Permissions

**Required:** `755` (drwxr-xr-x)

**Directories:**
```bash
chmod 755 catalogue/content
chmod 755 catalogue/data
chmod 755 catalogue/uploads
chmod 755 catalogue/logs
chmod 755 catalogue/blueprints
chmod 755 catalogue/templates
```

### File Permissions

**Required:** `644` (-rw-r--r--)

**Files:**
```bash
chmod 644 catalogue/content/**/*.json
chmod 644 catalogue/data/*.json
chmod 644 catalogue/config.php
```

### Ownership

**Web Server User:**
```bash
# Apache (Debian/Ubuntu)
chown -R www-data:www-data catalogue/

# Apache (CentOS/RHEL)
chown -R apache:apache catalogue/

# Nginx
chown -R nginx:nginx catalogue/
```

## Upload Configuration

### PHP Upload Limits

**php.ini:**
```ini
upload_max_filesize = 50M
post_max_size = 50M
```

**.htaccess:**
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
```

**Verify:**
```php
<?php
echo ini_get('upload_max_filesize');
echo ini_get('post_max_size');
?>
```

### Upload Directory

**Location:** `catalogue/uploads/`

**Permissions:** `755` (writable by web server)

**Security:**
- Files validated before upload
- MIME type checked
- File extension validated
- Path traversal prevented

## Error Handling

### Production Settings

**config.php:**
```php
define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
```

### Development Settings

**config.php:**
```php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

### Error Log Location

**Default:** `catalogue/logs/php_errors.log`

**Permissions:** `644` (readable by web server)

**Rotation:** Consider log rotation for production

## Shared Hosting

### Common Limitations

- **PHP Version** - May be limited
- **.htaccess** - May be restricted
- **File Permissions** - May be fixed
- **Upload Limits** - May be lower
- **mod_rewrite** - May need enabling

### Configuration Tips

1. **Check PHP Version** - Ensure 7.4+
2. **Enable mod_rewrite** - Via hosting control panel
3. **Set Permissions** - Via FTP/file manager
4. **Test Uploads** - Verify limits work
5. **Check Error Logs** - Monitor for issues

## Nginx Configuration

### Basic Setup

**nginx.conf:**
```nginx
server {
    listen 80;
    server_name example.com;
    root /var/www/html;
    index index.html index.php;

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # URL rewriting (similar to Apache mod_rewrite)
    location / {
        try_files $uri $uri.html $uri/ =404;
    }

    # Protect sensitive files
    location ~ \.(yml|yaml|json|log|md)$ {
        deny all;
    }
}
```

## SSL/HTTPS

### HTTPS Configuration

**Benefits:**
- Secure session cookies
- Encrypted data transmission
- Better security headers
- SEO benefits

### Apache SSL

**Virtual Host:**
```apache
<VirtualHost *:443>
    ServerName example.com
    DocumentRoot /var/www/html
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
</VirtualHost>
```

### Let's Encrypt

**Certbot:**
```bash
sudo certbot --apache -d example.com
```

## Performance

### Caching

**Static Files:**
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Compression

**Gzip:**
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

## Troubleshooting

### Common Issues

**Problem:** URLs not rewriting

**Solutions:**
1. Check `mod_rewrite` is enabled
2. Verify `.htaccess` is in root
3. Check `AllowOverride` is set
4. Verify file permissions

**Problem:** Uploads failing

**Solutions:**
1. Check PHP upload limits
2. Verify directory permissions
3. Check disk space
4. Review error logs

**Problem:** Errors not logging

**Solutions:**
1. Check log file permissions
2. Verify error_log path
3. Check disk space
4. Verify log directory exists

## Best Practices

### Server Configuration

- **Use HTTPS** - Always in production
- **Set appropriate limits** - Balance security and usability
- **Monitor logs** - Regular log review
- **Keep updated** - PHP and server software
- **Backup regularly** - Content and configuration

### Security

- **Disable debug mode** - In production
- **Restrict file access** - Protect sensitive files
- **Use strong passwords** - For all accounts
- **Monitor uploads** - Review uploaded files
- **Keep software updated** - Security patches

## See Also

- [Core Configuration](./CORE_CONFIG.md) - PHP configuration
- [Security Configuration](./SECURITY.md) - Security settings
- [Path Configuration](./PATHS.md) - Directory structure

