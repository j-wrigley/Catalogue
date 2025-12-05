# Troubleshooting Guide

Common issues and solutions for the CMS.

## Quick Links

- **[HTML Generation Issues](./HTML_GENERATION.md)** - Pages not generating, template errors
- **[File Permissions](./FILE_PERMISSIONS.md)** - Permission errors, write failures
- **[Upload Problems](./UPLOADS.md)** - File upload failures, size limits
- **[Path & URL Issues](./PATHS_URLS.md)** - URL rewriting, path problems
- **[Content Saving](./CONTENT_SAVING.md)** - Content not saving, validation errors
- **[Authentication Issues](./AUTHENTICATION.md)** - Login problems, session issues
- **[Media Issues](./MEDIA.md)** - Media not displaying, metadata problems
- **[Template Errors](./TEMPLATES.md)** - PHP errors, missing functions
- **[404 Page Issues](./404_PAGE.md)** - Custom 404 not working

## Common Issues

### Quick Fixes

**Content not saving:**
- Check required fields are filled
- Verify CSRF token is valid
- Check file permissions

**HTML not generating:**
- Verify blueprint exists
- Check template exists
- Ensure content file exists
- Check file permissions

**Uploads failing:**
- Check file size limits
- Verify file type is allowed
- Check directory permissions
- Review error logs

**URLs not working:**
- Verify `.htaccess` exists
- Check `mod_rewrite` is enabled
- Ensure file permissions correct

## Getting Help

### Check Error Logs

**Location:** `catalogue/logs/php_errors.log`

**View Logs:**
```bash
tail -f catalogue/logs/php_errors.log
```

### Enable Debug Mode

**Development:**
```php
// config.php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Production:**
```php
// config.php
define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
```

## Next Steps

1. Read [HTML Generation Issues](./HTML_GENERATION.md) for generation problems
2. Check [File Permissions](./FILE_PERMISSIONS.md) for permission errors
3. Review [Upload Problems](./UPLOADS.md) for upload issues
4. See [Path & URL Issues](./PATHS_URLS.md) for URL problems
5. Review [Content Saving](./CONTENT_SAVING.md) for save issues

