# Path Configuration

Understanding directory structures and URL paths in the CMS.

## Overview

The CMS uses a structured directory layout with automatic path detection. Understanding paths is essential for:
- Installing in subfolders
- Configuring URLs correctly
- Understanding file locations
- Troubleshooting path issues

## Directory Structure

### Root Structure

```
JSONCatalogue/ (or your project root)
├── .htaccess                    # Apache configuration
├── index.html                   # Generated home page
├── 404.html                     # Generated 404 page
├── {page}.html                  # Generated pages
├── assets/                      # Frontend assets (CSS, JS)
│   ├── css/
│   └── js/
└── catalogue/                   # CMS directory
    ├── config.php               # Core configuration
    ├── index.php                # Admin panel router
    ├── 404-handler.php         # 404 error handler
    ├── blueprints/              # Content type definitions
    ├── content/                 # Content storage
    │   ├── pages/              # Page content
    │   ├── collections/        # Collection content
    │   ├── media/              # Media metadata
    │   └── users/              # User accounts
    ├── data/                    # Public JSON data
    ├── uploads/                 # Uploaded media files
    ├── lib/                     # PHP library files
    ├── panel/                   # Admin panel
    │   ├── pages/              # Panel pages
    │   ├── partials/           # Panel templates
    │   ├── actions/            # AJAX handlers
    │   └── assets/             # Panel assets
    ├── templates/               # Frontend templates
    ├── logs/                    # Log files
    └── README.md
```

## Path Constants

### CMS_ROOT

Root directory of the CMS installation.

**Location:** `/catalogue/` (or custom location)

**Definition:**
```php
define('CMS_ROOT', __DIR__);
```

**Usage:** Base path for all other directory constants

### BASE_PATH

Base path for the site (empty if at root, or subfolder path).

**Examples:**
- Root installation: `''` (empty)
- Subfolder: `'/mysite'`

**Auto-detected:** Yes

**Usage:** Used in templates for URLs

### CMS_URL

URL path to the CMS admin panel.

**Examples:**
- Root installation: `'/catalogue'`
- Subfolder: `'/mysite/catalogue'`

**Auto-detected:** Yes

**Usage:** Admin panel URLs

### ASSETS_URL

URL path to assets directory.

**Default:** Same as `BASE_PATH`

**Usage:** Frontend asset URLs (CSS, JS)

## Content Directories

### BLUEPRINTS_DIR

**Path:** `CMS_ROOT . '/blueprints'`

**Default:** `/catalogue/blueprints`

**Contains:** `*.blueprint.yml` files

### CONTENT_DIR

**Path:** `CMS_ROOT . '/content'`

**Default:** `/catalogue/content`

**Contains:** Pages, collections, media metadata, users

### PAGES_DIR

**Path:** `CONTENT_DIR . '/pages'`

**Default:** `/catalogue/content/pages`

**Structure:**
```
pages/
  {page-name}/
    {page-name}.json
```

**Example:**
```
pages/
  about/
    about.json
  home/
    home.json
```

### COLLECTIONS_DIR

**Path:** `CONTENT_DIR . '/collections'`

**Default:** `/catalogue/content/collections`

**Structure:**
```
collections/
  {collection-name}/
    {item-slug}.json
```

**Example:**
```
collections/
  posts/
    my-first-post.json
    another-post.json
  projects/
    project-1.json
```

### MEDIA_METADATA_DIR

**Path:** `CONTENT_DIR . '/media'`

**Default:** `/catalogue/content/media`

**Contains:** `{hash}.json` metadata files

### UPLOADS_DIR

**Path:** `CMS_ROOT . '/uploads'`

**Default:** `/catalogue/uploads`

**Contains:** Uploaded media files (images, documents)

**Structure:**
```
uploads/
  images/
    header.jpg
  documents/
    guide.pdf
  file.jpg
```

## URL Paths

### Frontend URLs

**Root Installation:**
```
/                    → index.html
/about               → about.html
/posts/my-post        → posts/my-post.html
```

**Subfolder Installation:**
```
/mysite/              → index.html
/mysite/about         → about.html
/mysite/posts/post    → posts/post.html
```

### Admin Panel URLs

**Root Installation:**
```
/catalogue/index.php?page=dashboard
/catalogue/index.php?page=pages
/catalogue/index.php?page=media
```

**Subfolder Installation:**
```
/mysite/catalogue/index.php?page=dashboard
/mysite/catalogue/index.php?page=pages
```

### Asset URLs

**Root Installation:**
```
/assets/css/style.css
/assets/js/app.js
```

**Subfolder Installation:**
```
/mysite/assets/css/style.css
/mysite/assets/js/app.js
```

## Path Detection

### Automatic Detection

The CMS automatically detects paths:

1. **Document Root Detection** - Uses `$_SERVER['DOCUMENT_ROOT']`
2. **Script Path Detection** - Falls back to `$_SERVER['SCRIPT_NAME']`
3. **Subfolder Detection** - Removes `/catalogue` from path

### Detection Logic

```php
// Calculate CMS_URL relative to document root
$document_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
$cms_root_real = realpath(CMS_ROOT);

if ($cms_root_real && strpos($cms_root_real, $document_root) === 0) {
    // Calculate relative path from document root
    $cms_path = substr($cms_root_real, strlen($document_root));
    define('CMS_URL', $cms_path);
    
    // BASE_PATH is parent directory if CMS is in subdirectory
    $path_parts = explode('/', trim($cms_path, '/'));
    if (count($path_parts) > 1) {
        array_pop($path_parts);
        define('BASE_PATH', empty($path_parts) ? '' : '/' . implode('/', $path_parts));
    } else {
        define('BASE_PATH', '');
    }
}
```

## Installation Types

### Root Installation

**Structure:**
```
/var/www/html/
  catalogue/          # CMS directory
  assets/            # Frontend assets
  index.html         # Generated home page
```

**Paths:**
- `BASE_PATH`: `''` (empty)
- `CMS_URL`: `'/catalogue'`
- `ASSETS_URL`: `''` (empty)

**URLs:**
- Site: `http://example.com/`
- Admin: `http://example.com/catalogue/`
- Assets: `http://example.com/assets/`

### Subfolder Installation

**Structure:**
```
/var/www/html/
  mysite/
    catalogue/       # CMS directory
    assets/          # Frontend assets
    index.html       # Generated home page
```

**Paths:**
- `BASE_PATH`: `'/mysite'`
- `CMS_URL`: `'/mysite/catalogue'`
- `ASSETS_URL`: `'/mysite'`

**URLs:**
- Site: `http://example.com/mysite/`
- Admin: `http://example.com/mysite/catalogue/`
- Assets: `http://example.com/mysite/assets/`

## Path Usage in Templates

### Using BASE_PATH

```php
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/style.css">
<script src="<?= BASE_PATH ?>/assets/js/app.js"></script>
```

### Using catalogue() Function

The `catalogue()` function automatically handles paths:

```php
<?= catalogue('image') ?>
// Returns: /uploads/images/header.jpg (with BASE_PATH if needed)
```

### Navigation Links

```php
<?php foreach (catalogueNav() as $page): ?>
    <a href="<?= navLink($page) ?>"><?= catalogue('title') ?></a>
<?php endforeach; ?>
```

## Modifying Paths

### Changing Directory Structure

Edit `config.php`:

```php
// Change uploads directory
define('UPLOADS_DIR', CMS_ROOT . '/media');

// Change content directory
define('CONTENT_DIR', CMS_ROOT . '/data');
```

### Custom Installation Location

If installing in a custom location:

1. Update `CMS_ROOT` if needed
2. Paths auto-detect from `CMS_ROOT`
3. Verify `BASE_PATH` and `CMS_URL` are correct

## Troubleshooting

### Path Issues

**Problem:** URLs not working correctly

**Solutions:**
1. Check `BASE_PATH` is set correctly
2. Verify `.htaccess` is in root
3. Check Apache `mod_rewrite` is enabled
4. Verify file permissions

### Subfolder Issues

**Problem:** CMS not working in subfolder

**Solutions:**
1. Verify `BASE_PATH` includes subfolder path
2. Check `CMS_URL` includes subfolder
3. Ensure `.htaccess` is in root
4. Check Apache configuration

### Asset Loading Issues

**Problem:** CSS/JS not loading

**Solutions:**
1. Check `ASSETS_URL` matches `BASE_PATH`
2. Verify asset files exist
3. Check file permissions
4. Verify paths in HTML source

## Best Practices

### Path Configuration

- **Don't hardcode paths** - Use constants
- **Test in subfolder** - Verify subfolder support
- **Use relative paths** - When possible
- **Document custom paths** - Note any changes

### Directory Structure

- **Keep structure consistent** - Don't move directories
- **Use default locations** - Unless necessary
- **Document changes** - Note any modifications
- **Backup before changes** - Always backup first

## See Also

- [Core Configuration](./CORE_CONFIG.md) - Configuration constants
- [Environment Setup](./ENVIRONMENT.md) - Server configuration
- [Security Configuration](./SECURITY.md) - Security settings

