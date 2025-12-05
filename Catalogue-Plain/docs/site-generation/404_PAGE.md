# 404 Error Page

Custom 404 error page generation and handling.

## Overview

The CMS includes a fully customizable 404 error page that:
- Is editable through the admin panel
- Shows when visitors access non-existent pages
- Uses Apache configuration and PHP handler
- Generates static HTML file

## File Structure

```
JSONCatalogue/
├── 404.html                    # Generated static HTML
├── .htaccess                   # Apache config
└── catalogue/
    ├── 404-handler.php         # PHP handler
    ├── blueprints/
    │   └── 404.blueprint.yml   # Blueprint definition
    ├── templates/
    │   └── 404.php             # Template file
    └── content/
        └── pages/
            └── 404/
                └── 404.json    # Content data
```

## Blueprint

### Default Blueprint

`404.blueprint.yml`:

```yaml
title: 404 Error Page
fields:
  title:
    type: text
    label: Page Title
  message:
    type: textarea
    label: Error Message
  show_navigation:
    type: switch
    label: Show Navigation Links
  navigation_text:
    type: text
    label: Navigation Text
```

## Template

### Default Template

`templates/404.php`:

```php
<?= snippet('header') ?>
<main class="error-page">
    <h1><?= catalogue('title', '404 - Page Not Found') ?></h1>
    <p><?= catalogue('message', 'Sorry, the page you are looking for could not be found.') ?></p>
    
    <?php if (catalogue('show_navigation', false)): ?>
        <nav>
            <p><?= catalogue('navigation_text', 'You might want to visit:') ?></p>
            <?php foreach (catalogueNav() as $page): ?>
                <?= navLink($page) ?>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</main>
<?= snippet('footer') ?>
```

## Content

### Default Content

`content/pages/404/404.json`:

```json
{
  "title": "404 - Page Not Found",
  "message": "Sorry, the page you are looking for could not be found.",
  "show_navigation": true,
  "navigation_text": "You might want to visit:",
  "_status": "published"
}
```

## Apache Configuration

### .htaccess

```apache
ErrorDocument 404 /catalogue/404-handler.php
```

**Important:** Update path if CMS is in subfolder:
- Root: `/catalogue/404-handler.php`
- Subfolder: `/your-folder/catalogue/404-handler.php`

## PHP Handler

### 404-handler.php

The handler:
1. Sets HTTP status code to `404`
2. Locates `404.html` file
3. Serves it with proper headers
4. Falls back to basic HTML if file missing

### Why PHP Handler?

- Sets proper HTTP status code (static files can't)
- Handles path resolution in subfolders
- Provides fallback if HTML missing

## Generation

### Automatic Generation

404 page generates when:
- 404 content is saved
- "Regenerate All" is clicked
- Template is modified

### Output File

Generates to:
```
/404.html (root directory)
```

## Editing 404 Page

### Through CMS

1. Navigate to **Pages**
2. Find **404** page
3. Edit fields:
   - Page Title
   - Error Message
   - Show Navigation Links
   - Navigation Text
4. Click **Save**
5. `404.html` regenerates automatically

### Manual Editing

Edit content file:
```
/catalogue/content/pages/404/404.json
```

Then regenerate or save through CMS.

## Testing

### Test 404 Page

1. Visit non-existent URL: `http://yoursite.com/non-existent`
2. Should see custom 404 page
3. Check browser dev tools for HTTP 404 status

### Verify Status Code

```bash
curl -I http://yoursite.com/non-existent
# Should return: HTTP/1.1 404 Not Found
```

## Troubleshooting

### Default Apache Page Shows

**Issue:** Apache default error page appears

**Solutions:**
1. Check `.htaccess` `ErrorDocument` path
2. Verify `404-handler.php` exists
3. Ensure `404.html` exists
4. Check Apache `mod_rewrite` enabled
5. Verify file permissions

### Handler Not Found

**Issue:** "404 handler not found" error

**Solution:** Update `ErrorDocument` path in `.htaccess`

### Content Not Updating

**Issue:** Changes don't appear

**Solutions:**
1. Click **Save** after editing
2. Use **Regenerate All**
3. Clear browser cache
4. Check `404.html` modification time

## Best Practices

1. **Keep it Simple** - Clear, helpful message
2. **Include Navigation** - Help users find their way
3. **Match Design** - Use consistent styling
4. **Test Regularly** - Verify after updates
5. **Monitor Errors** - Track 404 URLs

## See Also

- [How It Works](./HOW_IT_WORKS.md) - Generation process
- [URL Structure](./URL_STRUCTURE.md) - URL patterns

