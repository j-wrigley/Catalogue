# Basic Configuration

Essential configuration for your CMS.

## Overview

After installation, configure:
- Site settings (public-facing)
- CMS settings (admin panel)
- Path configuration (if needed)
- Security settings

## Site Settings

### Access Site Settings

Navigate to **Site** in sidebar (or `/catalogue/index.php?page=settings`)

### Basic Information

**Required:**
- **Site Name** - Your website name
- **Site Tagline** - Short description (optional)
- **Site Description** - Full description (optional)

**Example:**
```
Site Name: My Website
Site Tagline: Built for simplicity
Site Description: A modern website built with JSON Catalogue CMS
```

### SEO Settings

**Configure:**
- **Meta Title** - Default page title
- **Meta Description** - Default description
- **Meta Keywords** - SEO keywords (tags)
- **Open Graph Image** - Social sharing image

**Example:**
```
Meta Title: My Website - Home
Meta Description: Welcome to my website...
Meta Keywords: web, design, cms
```

### Social Media

**Add links:**
- Twitter handle
- Facebook URL
- Instagram URL
- Other social links

## CMS Settings

### Access CMS Settings

Navigate to **Settings** in sidebar footer (or `/catalogue/index.php?page=cms-settings`)

### General

**CMS Name:**
- Name displayed in admin panel
- Default: "JSON Catalogue"
- Customize to your brand

### Theme

**Choose Theme:**
1. Expand "Theme" accordion
2. Click preset color card
3. Theme applied instantly
4. Save to persist

**Presets Available:**
- Red (default)
- Blue
- Green
- Purple
- Orange
- Pink
- Teal
- Indigo

**Custom Colors:**
- Accent color (primary)
- Accent hover (hover state)
- Accent text (text on accent)

### Traffic Tracking

**Enable/Disable:**
- Toggle traffic tracking
- Default: Enabled
- Controls dashboard card

**Reset Data:**
- Clear all traffic statistics
- Cannot be undone
- Use with caution

## Path Configuration

### Auto-Detection

**Paths detected automatically:**
- BASE_PATH
- CMS_URL
- ASSETS_URL

**Usually no configuration needed.**

### Manual Override

**If paths incorrect:**
```php
// config.php
define('BASE_PATH', '/subfolder');
define('CMS_URL', '/subfolder/catalogue');
```

**Check paths:**
```php
echo BASE_PATH;
echo CMS_URL;
```

## Security Configuration

### Production Settings

**config.php:**
```php
define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
```

### Development Settings

**config.php:**
```php
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Session Settings

**Default:**
- Lifetime: 8 hours
- Secure cookies (if HTTPS)
- HttpOnly cookies
- SameSite: Strict

**Modify if needed:**
```php
// config.php
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
```

## File Upload Configuration

### PHP Settings

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

### Verify Limits

**Check current limits:**
```php
<?php
echo ini_get('upload_max_filesize');
echo ini_get('post_max_size');
?>
```

## Quick Configuration Checklist

### Essential Settings

- [ ] Site name configured
- [ ] Site description added
- [ ] CMS name customized
- [ ] Theme selected
- [ ] Default password changed
- [ ] DEBUG_MODE set correctly

### Recommended Settings

- [ ] SEO metadata configured
- [ ] Social media links added
- [ ] Traffic tracking enabled
- [ ] File upload limits set
- [ ] Security settings reviewed

### Optional Settings

- [ ] Custom theme colors
- [ ] Advanced SEO settings
- [ ] Custom session timeout
- [ ] Additional social links

## Best Practices

### Configuration

- **Set site name first** - Used throughout site
- **Configure SEO early** - Better for search engines
- **Choose theme** - Match your brand
- **Review security** - Ensure production settings

### Maintenance

- **Update regularly** - Keep settings current
- **Backup settings** - Protect configuration
- **Document changes** - Note customizations
- **Test after changes** - Verify everything works

## See Also

- [Site Settings](../configuration/SITE_SETTINGS.md) - Complete site settings guide
- [CMS Settings](../configuration/CMS_SETTINGS.md) - Complete CMS settings guide
- [Core Configuration](../configuration/CORE_CONFIG.md) - PHP configuration
- [Security Configuration](../configuration/SECURITY.md) - Security settings

