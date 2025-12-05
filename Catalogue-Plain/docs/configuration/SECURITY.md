# Security Configuration

Security settings and configuration for the CMS.

## Overview

The CMS includes multiple security layers:
- Session security
- CSRF protection
- Security headers
- File upload validation
- Path traversal protection
- XSS prevention

## Security Headers

### Content Security Policy (CSP)

**Location:** `config.php`

**Default:**
```php
$csp = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: http: https:; font-src 'self' data:; connect-src 'self';";
header("Content-Security-Policy: $csp");
```

**Note:** `'unsafe-inline'` is currently needed for inline scripts/styles. Consider removing in future versions.

### X-Content-Type-Options

**Purpose:** Prevents MIME type sniffing

**Setting:**
```php
header('X-Content-Type-Options: nosniff');
```

### X-Frame-Options

**Purpose:** Prevents clickjacking

**Setting:**
```php
header('X-Frame-Options: DENY');
```

**Options:**
- `DENY` - No framing allowed
- `SAMEORIGIN` - Allow same origin framing

### X-XSS-Protection

**Purpose:** Enables browser XSS filter

**Setting:**
```php
header('X-XSS-Protection: 1; mode=block');
```

### Referrer-Policy

**Purpose:** Controls referrer information

**Setting:**
```php
header('Referrer-Policy: strict-origin-when-cross-origin');
```

**Options:**
- `no-referrer` - Never send referrer
- `strict-origin-when-cross-origin` - Send origin only for cross-origin
- `same-origin` - Send referrer for same origin only

## Session Security

### Session Configuration

**Location:** `config.php`

**Settings:**
```php
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Strict'
]);
```

### Session Lifetime

**Default:** 8 hours (28800 seconds)

**Configuration:**
```php
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
```

**Modifying:**
```php
// 24 hours
define('SESSION_LIFETIME', 3600 * 24);

// 1 hour
define('SESSION_LIFETIME', 3600);
```

### Session Name

**Default:** `'cms_session'`

**Configuration:**
```php
define('SESSION_NAME', 'cms_session');
```

**Modifying:**
```php
define('SESSION_NAME', 'my_custom_session');
```

## CSRF Protection

### CSRF Token Expiry

**Default:** 1 hour (3600 seconds)

**Configuration:**
```php
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
```

**Modifying:**
```php
// 30 minutes
define('CSRF_TOKEN_EXPIRY', 1800);

// 2 hours
define('CSRF_TOKEN_EXPIRY', 7200);
```

### CSRF Token Generation

Tokens are automatically generated and validated:
- Generated on form load
- Validated on form submission
- Expires after configured time
- Regenerated on each request

## File Upload Security

### File Type Validation

**Allowed Extensions:**
```php
$allowed_extensions = [
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
    'pdf', 'zip'
];
```

**Allowed MIME Types:**
```php
$allowed_mime_types = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp',
    'image/svg+xml',
    'application/pdf',
    'application/zip'
];
```

### File Size Limits

**PHP Configuration:**
```php
@ini_set('upload_max_filesize', '50M');
@ini_set('post_max_size', '50M');
```

**.htaccess Configuration:**
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
```

### Path Traversal Protection

Files are validated to prevent path traversal:

```php
$real_destination = realpath($destination_dir);
$real_uploads = realpath(UPLOADS_DIR);

if (!$real_destination || strpos($real_destination, $real_uploads) !== 0) {
    // Invalid path - reject
}
```

## Authentication Security

### Password Hashing

**Algorithm:** `password_hash()` with `PASSWORD_DEFAULT`

**Storage:** Hashed passwords in user JSON files

**Verification:** `password_verify()`

### Rate Limiting

**Location:** `lib/auth.php`

**Default:** 5 attempts per 15 minutes

**Configuration:**
```php
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
```

### Session Regeneration

Sessions are regenerated on login to prevent fixation:

```php
session_regenerate_id(true);
```

## Input Validation

### Sanitization

All user input is sanitized:
- Paths: `preg_replace('/[^a-z0-9_\/-]/i', '', $path)`
- Filenames: `sanitizeFilename()`
- URLs: `esc_url()`
- Text: `esc()`

### XSS Prevention

**Output Escaping:**
```php
<?= esc($user_input) ?>
<?= esc_attr($attribute_value) ?>
<?= esc_url($url) ?>
```

**Markdown Parsing:**
- HTML tags stripped
- Only safe markdown allowed
- Script tags removed

## Directory Security

### File Access Control

**.htaccess Protection:**
```apache
<FilesMatch "\.(yml|yaml|json|log|md)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>
```

**Protected Files:**
- Blueprint files (`.yml`, `.yaml`)
- JSON configs (`.json`)
- Log files (`.log`)
- Markdown files (`.md`)

### Directory Listing

**Prevented:** Direct access to directories

**Method:** `.htaccess` and `index.php` files

## Debug Mode

### Production Settings

**Location:** `config.php`

**Default:**
```php
define('DEBUG_MODE', false);
```

**Production:**
```php
define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
```

**Development:**
```php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Error Logging

**Location:** `catalogue/logs/php_errors.log`

**Configuration:**
```php
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
```

## Best Practices

### Production Deployment

1. **Set DEBUG_MODE to false**
2. **Enable error logging**
3. **Disable error display**
4. **Use HTTPS**
5. **Review security headers**
6. **Set appropriate session lifetime**
7. **Configure file upload limits**

### Regular Maintenance

1. **Review logs** - Check for suspicious activity
2. **Update passwords** - Regularly change passwords
3. **Review users** - Remove unused accounts
4. **Check permissions** - Verify file permissions
5. **Monitor uploads** - Review uploaded files

### Security Checklist

- [ ] DEBUG_MODE set to false
- [ ] Error display disabled
- [ ] Error logging enabled
- [ ] HTTPS enabled
- [ ] Strong passwords required
- [ ] CSRF protection enabled
- [ ] File upload limits set
- [ ] Security headers configured
- [ ] Session security enabled
- [ ] Path traversal protection verified

## See Also

- [Security Documentation](../security/README.md) - Complete security guide
- [Production Checklist](../security/PRODUCTION_CHECKLIST.md) - Deployment checklist
- [Core Configuration](./CORE_CONFIG.md) - Configuration constants

