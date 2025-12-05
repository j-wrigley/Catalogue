# Core Configuration

The main configuration file (`config.php`) contains core system settings.

## File Location

```
/catalogue/config.php
```

## Configuration Constants

### Path Constants

#### CMS_ROOT

Root directory of the CMS installation.

```php
define('CMS_ROOT', __DIR__);
```

**Default:** Directory containing `config.php`  
**Usage:** Base path for all other directory constants

#### BASE_PATH

Base path for the site (empty if at root, or subfolder path).

```php
define('BASE_PATH', ''); // Root installation
define('BASE_PATH', '/subfolder'); // Subfolder installation
```

**Default:** Auto-detected  
**Usage:** Used in templates for URLs

#### CMS_URL

URL path to the CMS admin panel.

```php
define('CMS_URL', '/catalogue');
```

**Default:** Auto-detected  
**Usage:** Admin panel URLs

#### ASSETS_URL

URL path to assets directory.

```php
define('ASSETS_URL', BASE_PATH);
```

**Default:** Same as `BASE_PATH`  
**Usage:** Frontend asset URLs

### Directory Constants

#### BLUEPRINTS_DIR

Directory containing blueprint files.

```php
define('BLUEPRINTS_DIR', CMS_ROOT . '/blueprints');
```

**Default:** `/catalogue/blueprints`  
**Files:** `*.blueprint.yml`

#### CONTENT_DIR

Root content directory.

```php
define('CONTENT_DIR', CMS_ROOT . '/content');
```

**Default:** `/catalogue/content`  
**Contains:** Pages, collections, media metadata

#### PAGES_DIR

Directory for page content.

```php
define('PAGES_DIR', CONTENT_DIR . '/pages');
```

**Default:** `/catalogue/content/pages`  
**Structure:** `{page-name}/{page-name}.json`

#### COLLECTIONS_DIR

Directory for collection content.

```php
define('COLLECTIONS_DIR', CONTENT_DIR . '/collections');
```

**Default:** `/catalogue/content/collections`  
**Structure:** `{collection-name}/{item-slug}.json`

#### MEDIA_METADATA_DIR

Directory for media metadata files.

```php
define('MEDIA_METADATA_DIR', CONTENT_DIR . '/media');
```

**Default:** `/catalogue/content/media`  
**Files:** `{hash}.json` (metadata for uploaded files)

#### DATA_DIR

Directory for public JSON data.

```php
define('DATA_DIR', CMS_ROOT . '/data');
```

**Default:** `/catalogue/data`  
**Files:** Aggregated JSON for frontend

#### UPLOADS_DIR

Directory for uploaded media files.

```php
define('UPLOADS_DIR', CMS_ROOT . '/uploads');
```

**Default:** `/catalogue/uploads`  
**Files:** Images, documents, media

#### LIB_DIR

Directory for PHP library files.

```php
define('LIB_DIR', CMS_ROOT . '/lib');
```

**Default:** `/catalogue/lib`  
**Files:** Core PHP functions

#### PANEL_DIR

Directory for admin panel files.

```php
define('PANEL_DIR', CMS_ROOT . '/panel');
```

**Default:** `/catalogue/panel`  
**Contains:** Pages, partials, actions, assets

#### LOGS_DIR

Directory for log files.

```php
define('LOGS_DIR', CMS_ROOT . '/logs');
```

**Default:** `/catalogue/logs`  
**Files:** Error logs, action logs

### Site Constants

#### SITE_NAME

Default site name.

```php
define('SITE_NAME', 'JSON Catalogue');
```

**Default:** `'JSON Catalogue'`  
**Usage:** Fallback if site settings not set

#### SESSION_NAME

Session cookie name.

```php
define('SESSION_NAME', 'cms_session');
```

**Default:** `'cms_session'`  
**Usage:** PHP session identifier

### Security Constants

#### SESSION_LIFETIME

Session lifetime in seconds.

```php
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
```

**Default:** `28800` (8 hours)  
**Usage:** How long sessions remain active

#### CSRF_TOKEN_EXPIRY

CSRF token expiry in seconds.

```php
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
```

**Default:** `3600` (1 hour)  
**Usage:** How long CSRF tokens are valid

#### DEBUG_MODE

Enable debug mode (show errors).

```php
define('DEBUG_MODE', false);
```

**Default:** `false`  
**Usage:** Set to `true` for development, `false` for production

## Auto-Detection

### Path Detection

The CMS automatically detects paths:

1. **Document Root Detection** - Uses `$_SERVER['DOCUMENT_ROOT']`
2. **Script Path Detection** - Falls back to `$_SERVER['SCRIPT_NAME']`
3. **Subfolder Detection** - Removes `/catalogue` from path

### Example Detection

**Root Installation:**
```
Document Root: /var/www/html
CMS Root: /var/www/html/catalogue
BASE_PATH: ''
CMS_URL: '/catalogue'
```

**Subfolder Installation:**
```
Document Root: /var/www/html
CMS Root: /var/www/html/site/catalogue
BASE_PATH: '/site'
CMS_URL: '/site/catalogue'
```

## Directory Creation

The config file automatically creates required directories:

```php
$required_dirs = [
    BLUEPRINTS_DIR,
    CONTENT_DIR,
    PAGES_DIR,
    COLLECTIONS_DIR,
    MEDIA_METADATA_DIR,
    DATA_DIR,
    UPLOADS_DIR,
    LIB_DIR,
    PANEL_DIR,
    LOGS_DIR
];
```

Directories are created with `0755` permissions if they don't exist.

## Security Headers

The config file sets security headers:

```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: ...');
```

See [Security Configuration](./SECURITY.md) for details.

## Session Configuration

Sessions are configured with security settings:

```php
session_set_cookie_params([
    'lifetime' => ...,
    'path' => ...,
    'domain' => ...,
    'secure' => true, // HTTPS only if available
    'httponly' => true, // No JavaScript access
    'samesite' => 'Strict' // CSRF protection
]);
```

## Modifying Configuration

### Changing Paths

Edit `config.php` directly:

```php
// Change uploads directory
define('UPLOADS_DIR', CMS_ROOT . '/media');

// Change session lifetime
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
```

### Adding Constants

Add custom constants:

```php
// Custom setting
define('CUSTOM_SETTING', 'value');
```

### Best Practices

- **Don't modify** - Unless necessary
- **Backup first** - Before making changes
- **Test changes** - Verify after modification
- **Document changes** - Note any customizations

## See Also

- [Path Configuration](./PATHS.md) - Directory structure
- [Security Configuration](./SECURITY.md) - Security settings
- [Environment Setup](./ENVIRONMENT.md) - Server configuration

