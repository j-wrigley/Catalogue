# System Requirements

What you need to run JSON Catalogue CMS.

## Server Requirements

### PHP Version

**Minimum:** PHP 7.4

**Recommended:** PHP 8.0 or higher

**Check Version:**
```bash
php -v
```

### Required PHP Extensions

- **JSON** - For JSON file handling (usually included)
- **YAML** - For blueprint parsing (or Symfony YAML component)
- **mbstring** - For string handling
- **fileinfo** - For MIME type detection

**Optional:**
- **GD** or **Imagick** - For image processing

**Check Extensions:**
```bash
php -m
```

### Web Server

**Apache:**
- Version 2.4 or higher
- `mod_rewrite` module enabled
- `mod_php` or PHP-FPM

**Nginx:**
- Version 1.18 or higher
- PHP-FPM configured
- URL rewriting configured

### File System

**Requirements:**
- Write access to content directories
- Ability to create directories
- File permissions support

**Permissions:**
- Directories: `755`
- Files: `644`

## Hosting Requirements

### Shared Hosting

**Compatible:** Yes

**Requirements:**
- PHP 7.4+
- `.htaccess` support
- File write permissions
- `mod_rewrite` enabled

**Common Providers:**
- Most shared hosting providers work
- Check PHP version before installing
- Verify `.htaccess` is allowed

### VPS/Dedicated Server

**Compatible:** Yes

**Requirements:**
- Full server access
- Ability to configure Apache/Nginx
- PHP installation
- File system access

### Static Hosting

**Compatible:** Partial

**Limitations:**
- Admin panel requires PHP
- Can host generated HTML files
- Need PHP server for admin access

## PHP Configuration

### Recommended Settings

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
display_errors = Off
log_errors = On
```

### Minimum Settings

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 60
memory_limit = 128M
```

## Browser Requirements

### Admin Panel

**Modern browsers:**
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

**Features:**
- JavaScript enabled
- Cookies enabled
- Local storage support

### Frontend

**No requirements:**
- Generated HTML works in all browsers
- No JavaScript required
- Progressive enhancement supported

## Disk Space

### Minimum

**Small site:**
- CMS files: ~5 MB
- Content: ~10 MB
- Uploads: ~50 MB
- **Total: ~65 MB**

### Recommended

**Medium site:**
- CMS files: ~5 MB
- Content: ~100 MB
- Uploads: ~500 MB
- **Total: ~605 MB**

### Large Site

**Large site:**
- CMS files: ~5 MB
- Content: ~500 MB
- Uploads: ~2 GB
- **Total: ~2.5 GB**

## Performance Considerations

### Small Sites (< 100 pages)

**Performance:** Excellent
- Fast file reads
- Quick generation
- Minimal server load

### Medium Sites (100-500 pages)

**Performance:** Good
- Acceptable file reads
- Reasonable generation time
- Moderate server load

### Large Sites (500+ pages)

**Performance:** May need optimization
- Slower file operations
- Longer generation time
- Consider caching

## Compatibility

### Operating Systems

**Server:**
- Linux (all distributions)
- macOS
- Windows (with proper setup)

**Development:**
- macOS
- Linux
- Windows (WAMP/XAMPP)

### PHP Versions

**Tested:**
- PHP 7.4
- PHP 8.0
- PHP 8.1
- PHP 8.2

**Not Supported:**
- PHP 7.3 and below
- PHP 8.3+ (may work, not tested)

## Checking Your Server

### Quick Check Script

Create `check-requirements.php`:

```php
<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Required: 7.4+\n\n";

$required = ['json', 'mbstring', 'fileinfo'];
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "$ext: " . ($loaded ? "✓" : "✗") . "\n";
}

echo "\nUpload Limits:\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";

echo "\nPermissions:\n";
$dirs = ['content', 'uploads', 'logs'];
foreach ($dirs as $dir) {
    $writable = is_writable($dir);
    echo "$dir: " . ($writable ? "✓" : "✗") . "\n";
}
?>
```

## See Also

- [Installation](./INSTALLATION.md) - Installation steps
- [Environment Setup](../configuration/ENVIRONMENT.md) - Server configuration
- [Troubleshooting](../troubleshooting/README.md) - Common issues

