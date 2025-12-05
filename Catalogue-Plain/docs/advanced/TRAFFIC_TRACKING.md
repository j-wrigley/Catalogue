# Traffic Tracking Documentation

## Overview

The CMS includes a built-in traffic tracking system that logs page views and visitor statistics. This data is displayed in the dashboard and can be used for analytics purposes.

## How It Works

The traffic tracking system:
- Logs page views automatically when visitors load pages
- Tracks unique visitors (using hashed IP addresses for privacy)
- Stores data in daily JSON files (`/catalogue/data/traffic/YYYY-MM-DD.json`)
- Displays analytics in the dashboard's "Traffic Overview" card

## Adding Traffic Tracking to Templates

### Basic Usage

Add the `traffic()` function to any template file to enable tracking:

```php
<?= traffic('log'); ?>
```

Or simply:

```php
<?= traffic(); ?>
```

(since `'log'` is the default action)

### Example Template

```php
<?= snippet('header') ?>

<?= traffic('log'); ?>

<main>
    <h1><?= catalogue('title') ?></h1>
    <p><?= catalogue('body') ?></p>
</main>

<?= snippet('footer') ?>
```

### Placement

The `traffic()` function outputs JavaScript that runs when the page loads. It's recommended to place it:
- In the `<head>` section (for early tracking)
- Or anywhere in the template (it will run when the browser processes it)

**Best Practice**: Add it to your header snippet so all pages are tracked automatically:

```php
<!-- templates/snippets/header.php -->
<head>
    <meta charset="UTF-8">
    <title><?= catalogue('title', 'My Site') ?></title>
    <?= traffic('log'); ?>
</head>
```

## Function Parameters

### `traffic($action = 'log', $page = null)`

**Parameters:**
- `$action` (string): 
  - `'log'` - Log a page view (default)
  - `'get'` - Retrieve traffic statistics (for dashboard use)
  
- `$page` (string|null): 
  - `null` - Auto-detect page identifier from URL (default)
  - Custom string - Use a custom page identifier

**Returns:**
- When `$action = 'log'`: Returns JavaScript code that logs the page view
- When `$action = 'get'`: Returns an array of traffic data

## Page Identifier

The system automatically detects page identifiers from URLs:

- `/` or `/index.html` → `home`
- `/about.html` → `about`
- `/blog/post-slug.html` → `blog/post-slug`
- Custom identifiers can be specified: `traffic('log', 'custom-page')`

## Custom Page Identifiers

If you want to use a custom identifier instead of auto-detection:

```php
<?= traffic('log', 'my-custom-page-name'); ?>
```

This is useful for:
- Tracking specific sections or features
- Grouping related pages
- Custom analytics categories

## Data Storage

Traffic data is stored in:
```
/catalogue/data/traffic/YYYY-MM-DD.json
```

Each file contains daily statistics:
```json
{
  "home": {
    "views": 150,
    "visitors": ["a1b2c3d4", "e5f6g7h8", ...],
    "last_view": "2025-01-18T14:30:00+00:00"
  },
  "about": {
    "views": 45,
    "visitors": ["a1b2c3d4", ...],
    "last_view": "2025-01-18T15:20:00+00:00"
  }
}
```

## Privacy & Security

- **IP Addresses**: Visitor IPs are hashed using SHA-256 for privacy
- **Visitor Tracking**: Only the first 8 characters of the hash are stored
- **Data Retention**: Last 1000 unique visitors per page per day are kept
- **No Cookies**: The system doesn't use cookies
- **No Personal Data**: No personally identifiable information is stored

## Viewing Analytics

### Dashboard

Traffic data is automatically displayed in the CMS dashboard:
- **Total Views**: Sum of all page views
- **Visitors**: Unique visitor count
- **7-Day Chart**: Visual representation of traffic over the last week

### Accessing Raw Data

Traffic data files are stored in `/catalogue/data/traffic/`:
- Files are named by date: `2025-01-18.json`
- Each file contains all page views for that day
- Data is in JSON format for easy parsing

## Technical Details

### JavaScript Implementation

The `traffic('log')` function outputs JavaScript that:
1. Creates an XMLHttpRequest
2. Sends a POST request to `/catalogue/data/traffic-log.php`
3. Includes the page identifier
4. Runs asynchronously (doesn't block page load)

### Server-Side Processing

The `traffic-log.php` endpoint:
- Validates POST requests only
- Sanitizes page identifiers
- Hashes visitor IP addresses
- Updates daily JSON files
- Returns success response

### Page Detection

During HTML generation, the system sets a global page identifier:
- Pages: Uses the blueprint name (e.g., `about`, `home`)
- Collections: Uses collection name + slug (e.g., `blog/my-post`)
- Custom: Can be overridden with the `$page` parameter

## Troubleshooting

### Traffic Not Showing in Dashboard

1. **Check if tracking is added**: Ensure `<?= traffic('log'); ?>` is in your templates
2. **Verify file permissions**: `/catalogue/data/traffic/` must be writable
3. **Check browser console**: Look for JavaScript errors
4. **Verify path**: Ensure the traffic-log.php path is correct for your installation

### Custom Page Identifiers Not Working

- Ensure the identifier contains only: letters, numbers, underscores, hyphens, and slashes
- Invalid characters are automatically removed
- Empty identifiers default to `'home'`

### Data Not Updating

- Traffic is logged client-side, so it requires JavaScript to be enabled
- Check browser console for XHR errors
- Verify the `/catalogue/data/traffic-log.php` endpoint is accessible
- Ensure the base path in `catalogue.php` matches your installation

## Best Practices

1. **Add to Header Snippet**: Include `<?= traffic('log'); ?>` in your header snippet so all pages are tracked automatically

2. **Don't Duplicate**: Only add it once per page (preferably in the header)

3. **Test After Changes**: After modifying templates, regenerate HTML files using "Regenerate All" in CMS Settings

4. **Monitor Dashboard**: Check the dashboard regularly to ensure tracking is working

5. **Custom Identifiers**: Use descriptive identifiers for better analytics organization

## Example: Complete Template with Tracking

```php
<?php
// templates/home.php
?>
<?= snippet('header') ?>

<?= traffic('log'); ?>

<main>
    <h1><?= catalogue('title', 'Welcome') ?></h1>
    <p><?= catalogue('description', '') ?></p>
    
    <?php if (catalogue('show_cta')): ?>
        <a href="<?= catalogue('cta_link', '#') ?>">
            <?= catalogue('cta_text', 'Learn More') ?>
        </a>
    <?php endif; ?>
</main>

<?= snippet('footer') ?>
```

## Related Functions

- `catalogue()` - Access content fields in templates
- `catalogueNav()` - Get navigation data
- `snippet()` - Include reusable template snippets

## See Also

- **Dashboard**: `/catalogue/index.php?page=dashboard` - View traffic analytics
- **Templates**: `/catalogue/templates/` - Template files
- **Snippets**: `/catalogue/templates/snippets/` - Reusable template parts
- **Data Files**: `/catalogue/data/traffic/` - Raw traffic data

