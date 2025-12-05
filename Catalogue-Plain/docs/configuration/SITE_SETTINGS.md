# Site Settings

Public-facing site configuration editable through the admin panel.

## Overview

Site settings control the public-facing aspects of your site:
- Site name and description
- SEO metadata
- Social media links
- Other site-wide configuration

## Accessing

Navigate to **Site** in the sidebar or visit:
```
/catalogue/index.php?page=settings
```

## Settings Structure

Settings are organized into tabs (defined in `settings.blueprint.yml`):

- **General** - Basic site information
- **SEO** - Search engine optimization
- **Social** - Social media settings
- **Other** - Additional settings

## Common Settings

### Site Information

#### Site Name

- **Field:** `site_name`
- **Type:** Text
- **Usage:** Site title, displayed in templates
- **Example:** "My Website"

#### Site Tagline

- **Field:** `site-tagline`
- **Type:** Text
- **Usage:** Short tagline or slogan
- **Example:** "Built for simplicity"

#### Site Description

- **Field:** `site_description`
- **Type:** Textarea
- **Usage:** Brief site description
- **Example:** "A modern flat-file CMS for developers"

### SEO Settings

#### Meta Title

- **Field:** `meta_title`
- **Type:** Text
- **Usage:** Default page title for SEO
- **Example:** "My Website - Home"

#### Meta Description

- **Field:** `meta_description`
- **Type:** Textarea
- **Usage:** Default meta description
- **Example:** "Welcome to my website..."

#### Meta Keywords

- **Field:** `meta_keywords`
- **Type:** Tags
- **Usage:** SEO keywords/tags
- **Example:** ["web", "design", "cms"]

#### Open Graph Image

- **Field:** `og_image`
- **Type:** File
- **Usage:** Image for social media sharing
- **Example:** `/uploads/og-image.jpg`

### Social Media

#### Twitter Handle

- **Field:** `twitter_handle`
- **Type:** Text
- **Usage:** Twitter username
- **Example:** "@username"

#### Facebook URL

- **Field:** `facebook_url`
- **Type:** Text
- **Usage:** Facebook page URL
- **Example:** "https://facebook.com/page"

#### Instagram URL

- **Field:** `instagram_url`
- **Type:** Text
- **Usage:** Instagram profile URL
- **Example:** "https://instagram.com/profile"

## Editing Settings

### Steps

1. Navigate to **Site** in sidebar
2. Click tab to view section
3. Modify fields as needed
4. Click **"Save Settings"**
5. Settings saved immediately

### Form Features

- **Tabs** - Organize settings into sections
- **Columns** - Multi-column layout
- **Groups** - Related fields grouped
- **Validation** - Required fields validated

## Settings Storage

### File Location

Settings stored in:
```
/catalogue/content/pages/settings/settings.json
```

### File Structure

```json
{
  "site_name": "My Website",
  "site-tagline": "Built for simplicity",
  "site_description": "A modern CMS",
  "meta_title": "My Website - Home",
  "meta_description": "Welcome...",
  "meta_keywords": ["web", "design"],
  "og_image": "/uploads/og-image.jpg",
  "twitter_handle": "@username",
  "facebook_url": "https://facebook.com/page",
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-01-01T00:00:00+00:00"
  }
}
```

## Using Settings in Templates

### Basic Usage

```php
<?= catalogue('site_name', 'Site', 'site') ?>
<?= catalogue('site_description', '', 'site') ?>
```

### In Head Section

```php
<head>
    <title><?= catalogue('meta_title', catalogue('site_name', 'Site', 'site'), 'site') ?></title>
    <meta name="description" content="<?= catalogue('meta_description', '', 'site') ?>">
    <?php if (catalogue('og_image', '', 'site')): ?>
        <meta property="og:image" content="<?= catalogue('og_image', '', 'site') ?>">
    <?php endif; ?>
</head>
```

### Site Name in Footer

```php
<footer>
    <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'Site', 'site') ?></p>
</footer>
```

## Customizing Settings

### Adding Fields

Edit `settings.blueprint.yml`:

```yaml
fields:
  custom_field:
    type: text
    label: Custom Field
    category: general
```

### Adding Tabs

```yaml
tabs:
  general:
    label: General
  custom:
    label: Custom Tab

fields:
  custom_field:
    type: text
    category: custom
```

## Best Practices

### SEO

- **Unique titles** - Don't duplicate across pages
- **Descriptive descriptions** - 150-160 characters
- **Relevant keywords** - Use actual site keywords
- **OG image** - Use high-quality image (1200x630px)

### Social Media

- **Complete URLs** - Include `https://`
- **Verify links** - Test all social links
- **Keep updated** - Update when URLs change

### Site Information

- **Clear name** - Use recognizable site name
- **Descriptive tagline** - Brief and memorable
- **Complete description** - Full site description

## Examples

### Complete Settings Example

```json
{
  "site_name": "My Blog",
  "site-tagline": "Thoughts on web development",
  "site_description": "A blog about modern web development and design",
  "meta_title": "My Blog - Web Development",
  "meta_description": "Thoughts and tutorials on web development",
  "meta_keywords": ["web", "development", "design", "tutorials"],
  "og_image": "/uploads/og-image.jpg",
  "twitter_handle": "@myblog",
  "facebook_url": "https://facebook.com/myblog"
}
```

### Template Usage

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= catalogue('meta_title', catalogue('site_name', 'Site', 'site'), 'site') ?></title>
    <meta name="description" content="<?= catalogue('meta_description', '', 'site') ?>">
    <?php if (catalogue('og_image', '', 'site')): ?>
        <meta property="og:image" content="<?= catalogue('og_image', '', 'site') ?>">
    <?php endif; ?>
</head>
<body>
    <header>
        <h1><?= catalogue('site_name', 'Site', 'site') ?></h1>
        <p><?= catalogue('site-tagline', '', 'site') ?></p>
    </header>
    <!-- Content -->
    <footer>
        <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'Site', 'site') ?></p>
    </footer>
</body>
</html>
```

## See Also

- [Templates - Site Settings](../templates/SITE_SETTINGS.md) - Using settings in templates
- [Admin Panel - Site Settings](../admin-panel/SITE_SETTINGS.md) - Admin interface
- [Blueprints - Field Types](../blueprints/FIELD_TYPES_REFERENCE.md) - Field types

