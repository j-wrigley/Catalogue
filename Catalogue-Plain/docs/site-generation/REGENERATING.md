# Regenerating Pages

How to manually regenerate HTML files and when it happens automatically.

## Automatic Regeneration

### On Content Save

When you save content in the admin panel:

1. Content saved to JSON file
2. HTML file automatically generated
3. Old HTML file updated or replaced
4. Toast notification confirms save

### On Content Create

When creating new content:

1. New JSON file created
2. HTML file automatically generated
3. File appears on frontend immediately

### On Content Delete

When deleting content:

1. JSON file deleted
2. HTML file automatically deleted
3. File removed from frontend

### On Slug Change

When changing a collection item slug:

1. New HTML file created with new slug
2. Old HTML file deleted
3. URL changes automatically

## Manual Regeneration

### Regenerate All

Regenerate all pages and collections:

1. Navigate to **CMS Settings**
2. Expand **"Update All Pages"** accordion
3. Click **"Regenerate All Pages & Collections"**
4. Wait for completion
5. Review results

### Regeneration Results

After regeneration, you'll see:

- **Generated** - Successfully generated files
- **Skipped** - Files skipped (missing blueprint/template/content)
- **Errors** - Generation errors

### Example Results

```json
{
  "success": true,
  "generated": [
    "Home page (index.html)",
    "Page: about",
    "Page: contact",
    "Collection: posts (5 items)",
    "404 error page (404.html)"
  ],
  "skipped": [
    "Page 'draft': No content file found"
  ],
  "errors": [],
  "count": 5
}
```

## When to Regenerate

### After Template Changes

If you modify a template file:

1. Template changes won't appear automatically
2. Click **"Regenerate All"** to update HTML
3. All pages using that template update

### After Blueprint Changes

If you modify a blueprint:

1. Blueprint changes affect form generation
2. Existing content may need regeneration
3. Click **"Regenerate All"** to update HTML

### After Site Settings Changes

If you change site settings:

1. Settings saved automatically
2. Pages regenerate automatically
3. No manual regeneration needed

### After Bulk Changes

If you make many changes:

1. Individual saves regenerate automatically
2. Or use **"Regenerate All"** for bulk update
3. Faster than saving each item individually

## Regeneration Process

### Step 1: Home Page

```php
generateHomeHtml();
// Generates: index.html
```

### Step 2: 404 Page

```php
generateHtmlFile('404', 'page');
// Generates: 404.html
```

### Step 3: All Pages

```php
foreach ($pageDirs as $pageDir) {
    generateHtmlFile($contentType, 'page');
}
// Generates: {type}.html for each page
```

### Step 4: All Collections

```php
foreach ($collectionDirs as $collectionDir) {
    generateHtmlFile($contentType, 'collection');
}
// Generates: {collection}/{slug}.html for each item
```

## Skipped Items

Items are skipped if:

- **No blueprint** - Blueprint file doesn't exist
- **No template** - Template file doesn't exist
- **No content** - Content JSON file doesn't exist
- **Empty collection** - Collection has no items

### Handling Skipped Items

1. **Create missing files** - Add blueprint/template/content
2. **Regenerate again** - Run regeneration after creating files
3. **Check error logs** - Review skipped items for issues

## Error Handling

### Generation Errors

If generation fails:

1. Error logged to error log
2. Error reported in results
3. Other files continue generating
4. Check error log for details

### Common Errors

- **Template syntax error** - Fix PHP syntax in template
- **Missing function** - Ensure all functions are available
- **File permissions** - Check write permissions
- **Memory limit** - Increase PHP memory if needed

## Performance

### Generation Speed

- **Single page** - < 1 second
- **All pages** - Depends on number of pages
- **Collections** - Depends on number of items

### Optimization Tips

- Regenerate only when needed
- Use automatic regeneration for individual changes
- Use manual regeneration for bulk updates
- Check skipped items before regenerating

## See Also

- [How It Works](./HOW_IT_WORKS.md) - Generation process
- [Template Mapping](./TEMPLATE_MAPPING.md) - Template rules

