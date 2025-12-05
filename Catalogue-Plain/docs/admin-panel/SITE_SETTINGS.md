# Site Settings

Configuring site-wide settings and information.

## Overview

Site settings control the public-facing aspects of your site, such as site name, description, SEO metadata, and other site-wide configuration.

## Accessing

Navigate to **Site** in the sidebar or visit:
```
/catalogue/index.php?page=settings
```

## Settings Structure

Site settings are organized into tabs (if configured in blueprint):

### Common Tabs

- **General** - Basic site information
- **SEO** - Search engine optimization
- **Social** - Social media settings
- **Other** - Additional settings

## Common Settings

### Site Information

- **Site Name** - Name of your site
- **Site Description** - Brief description
- **Site Tagline** - Short tagline
- **Site URL** - Base URL of site

### SEO Settings

- **Meta Title** - Default page title
- **Meta Description** - Default description
- **Meta Keywords** - Keywords (tags)
- **OG Image** - Open Graph image

### Social Media

- **Twitter Handle** - Twitter username
- **Facebook URL** - Facebook page URL
- **Instagram URL** - Instagram profile URL

## Editing Settings

### Steps

1. Navigate to Site settings
2. Click tab to view section
3. Modify fields as needed
4. Click **"Save Settings"**
5. Settings updated immediately

### Form Features

- **Tabs** - Organize settings into sections
- **Columns** - Multi-column layout
- **Groups** - Related fields grouped together
- **Validation** - Required fields validated

## Saving Settings

### Process

1. Click **"Save Settings"** button
2. Settings saved to JSON file
3. Toast notification confirms save
4. Settings available in templates

### Settings File

Settings are stored in:
```
/catalogue/content/pages/settings/settings.json
```

## Using Settings in Templates

Access settings in templates using:

```php
<?= catalogue('site_name', 'Default', 'site') ?>
<?= catalogue('site_description', '', 'site') ?>
```

See [Site Settings](../templates/SITE_SETTINGS.md) for details.

## Examples

### Setting Site Name

1. Navigate to Site settings
2. Find "Site Name" field
3. Enter: "My Awesome Site"
4. Click "Save Settings"
5. Name appears in templates

### Adding SEO Meta

1. Navigate to Site settings
2. Click "SEO" tab
3. Enter meta description
4. Add keywords
5. Save settings

### Configuring Social Links

1. Navigate to Site settings
2. Click "Social" tab
3. Enter social media URLs
4. Save settings
5. Links available in templates

## Settings Blueprint

Settings are defined by `settings.blueprint.yml`:

```yaml
title: Site Settings
tabs:
  general:
    label: General
  seo:
    label: SEO

fields:
  site_name:
    type: text
    label: Site Name
    category: general
  site_description:
    type: textarea
    label: Description
    category: general
```

## See Also

- [CMS Settings](./CMS_SETTINGS.md) - CMS system settings
- [Blueprints](../blueprints/README.md) - Creating settings blueprints
- [Templates](../templates/SITE_SETTINGS.md) - Using settings in templates

