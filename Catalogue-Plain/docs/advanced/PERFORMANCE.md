# Performance Optimization

Optimizing site speed, efficiency, and resource usage.

## Overview

This guide covers techniques for optimizing your CMS installation for better performance, faster page loads, and efficient resource usage.

## Template Optimization

### Minimize PHP Processing

Keep templates simple and efficient:

```php
<!-- Good: Simple and direct -->
<h1><?= catalogue('title') ?></h1>

<!-- Avoid: Complex logic in templates -->
<?php
$title = catalogue('title');
if ($title) {
    $title = strtoupper($title);
    echo '<h1>' . htmlspecialchars($title) . '</h1>';
}
?>
```

### Cache Repeated Calculations

```php
<?php
// Calculate once, use multiple times
$site_name = catalogue('site_name', 'Site', 'site');
?>
<title><?= $site_name ?> - <?= catalogue('title') ?></title>
<meta property="og:site_name" content="<?= $site_name ?>">
```

### Limit Collection Iterations

```php
<!-- Good: Limit results -->
<?php 
$posts = catalogueCollection('posts', ['status' => 'published']);
$count = 0;
foreach ($posts as $post):
    if ($count++ >= 10) break; // Limit to 10 items
    ?>
    <!-- Display post -->
<?php endforeach; ?>
```

## File Optimization

### Image Optimization

Optimize images before uploading:

- **Compress images**: Use tools like TinyPNG or ImageOptim
- **Choose appropriate formats**: Use WebP for modern browsers
- **Resize appropriately**: Don't upload oversized images
- **Use appropriate dimensions**: Match image size to display size

### File Size Limits

Configure appropriate upload limits in `.htaccess`:

```apache
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

## HTML Generation Optimization

### Regenerate Strategically

- **Regenerate on save**: Automatic regeneration keeps site updated
- **Batch regeneration**: Use "Regenerate All" sparingly
- **Selective regeneration**: Regenerate only changed collections

### Output Buffering

The CMS uses output buffering to prevent errors. Ensure templates don't output unnecessary content:

```php
<?php
// Good: No output before HTML
?>
<!DOCTYPE html>
<html>
```

## Database-Free Architecture

The CMS uses flat files (JSON) instead of a database, which provides:

- **Fast reads**: Direct file access
- **No query overhead**: No SQL queries
- **Simple caching**: File system caching
- **Easy backup**: Copy files

### File System Performance

- **Use SSD storage**: Faster file access
- **Optimize directory structure**: Keep content organized
- **Limit file count**: Avoid thousands of files in one directory

## Caching Strategies

### Browser Caching

Set appropriate cache headers in `.htaccess`:

```apache
# Cache static assets
<FilesMatch "\.(jpg|jpeg|png|gif|css|js)$">
    ExpiresActive On
    ExpiresDefault "access plus 1 year"
</FilesMatch>
```

### Server-Side Caching

For high-traffic sites, consider:

- **OPcache**: PHP opcode caching
- **File system cache**: Cache generated HTML
- **CDN**: Use a CDN for static assets

## Code Optimization

### Minimize Includes

Only include necessary files:

```php
// Good: Include only what's needed
require_once CMS_ROOT . '/lib/catalogue.php';

// Avoid: Including unnecessary files
require_once CMS_ROOT . '/lib/*.php'; // Don't do this
```

### Efficient Loops

```php
<!-- Good: Simple iteration -->
<?php foreach (catalogueCollection('posts') as $post): ?>
    <?= catalogue('title') ?>
<?php endforeach; ?>

<!-- Avoid: Nested complex logic -->
<?php
$posts = catalogueCollection('posts');
foreach ($posts as $post) {
    $data = processComplexData($post);
    foreach ($data as $item) {
        // Complex nested logic
    }
}
?>
```

## Media Optimization

### Lazy Loading

Implement lazy loading for images:

```php
<img src="<?= catalogue('image') ?>" loading="lazy" alt="<?= catalogue('title') ?>">
```

### Responsive Images

Use appropriate image sizes:

```php
<?php if (catalogue('featured_image')): ?>
    <img src="<?= catalogue('featured_image') ?>" 
         srcset="<?= catalogue('featured_image') ?> 1x, <?= catalogue('featured_image_large') ?> 2x"
         alt="<?= catalogue('title') ?>">
<?php endif; ?>
```

## Traffic Tracking Optimization

### Async Loading

Traffic tracking uses async JavaScript by default, which doesn't block page load.

### Disable When Not Needed

Disable traffic tracking if not using analytics:

1. Go to **Settings** → **CMS Settings**
2. Toggle **Enable Traffic Tracking** off

## Server Configuration

### PHP Optimization

```ini
; php.ini optimizations
memory_limit = 128M
max_execution_time = 30
opcache.enable = 1
opcache.memory_consumption = 128
```

### Apache Optimization

```apache
# Enable compression
LoadModule deflate_module modules/mod_deflate.so
<Location />
    SetOutputFilter DEFLATE
</Location>
```

## Monitoring Performance

### Check File Sizes

Monitor content file sizes:

```bash
# Check content directory size
du -sh /path/to/catalogue/content/

# Check individual files
ls -lh /path/to/catalogue/content/**/*.json
```

### Monitor Generation Time

Check HTML generation performance:

- Use browser DevTools Network tab
- Monitor server response times
- Check PHP error logs for slow queries

## Best Practices

1. **Optimize Images**: Compress and resize images before upload
2. **Limit Collections**: Don't load thousands of items at once
3. **Use Pagination**: Implement pagination for large lists
4. **Cache Strategically**: Use browser and server caching
5. **Monitor Performance**: Regularly check site speed
6. **Clean Up**: Remove unused content and media files
7. **Regenerate Selectively**: Only regenerate what's needed

## Performance Checklist

- [ ] Images optimized and compressed
- [ ] File upload limits configured appropriately
- [ ] Browser caching enabled
- [ ] PHP OPcache enabled
- [ ] Unused content removed
- [ ] Collections paginated (if large)
- [ ] Traffic tracking disabled (if not needed)
- [ ] Server response times monitored
- [ ] File sizes reasonable
- [ ] Templates optimized

## See Also

- [Templates](../templates/README.md) - Template optimization
- [Media Management](../media-management/README.md) - Media optimization
- [Configuration](../configuration/ENVIRONMENT.md) - Server configuration
- [Troubleshooting](../troubleshooting/README.md) - Performance issues

