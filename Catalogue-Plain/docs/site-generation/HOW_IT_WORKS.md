# How Site Generation Works

Understanding the static HTML generation process.

## Overview

The CMS generates static HTML files by combining:
- **JSON content** - Your content data
- **YAML blueprints** - Content structure definitions
- **PHP templates** - HTML structure and presentation

## Generation Flow

### 1. Content Loading

```php
// Load JSON content file
$content = readJson('/path/to/content.json');

// Remove metadata
unset($content['_meta']);
```

### 2. Blueprint Loading

```php
// Get blueprint for content type
$blueprint = getBlueprint('about');

// Set blueprint context
setCatalogueBlueprint($blueprint);
```

### 3. Site Settings Loading

```php
// Load site settings
$siteSettings = readJson(PAGES_DIR . '/settings/settings.json');

// Set site context
setCatalogueSite($siteSettings);
```

### 4. Template Execution

```php
// Set content context
setCatalogueContent($content);

// Include template
include '/path/to/template.php';

// Capture output
$html = ob_get_clean();
```

### 5. HTML Output

```php
// Save to file
file_put_contents('/path/to/output.html', $html);
```

## Generation Functions

### `generateHtmlFile()`

Main function for generating HTML:

```php
generateHtmlFile(string $contentType, string $contentKind, string $itemSlug = null): bool
```

**Parameters:**
- `$contentType` - Content type name (e.g., 'about', 'posts')
- `$contentKind` - 'page' or 'collection'
- `$itemSlug` - For collections, specific item slug (null = all)

**Returns:** `true` on success, `false` on failure

### `generatePageHtml()`

Generates HTML for a single page:

```php
generatePageHtml(string $contentType, array $blueprint): bool
```

### `generateCollectionHtml()`

Generates HTML for collection items:

```php
generateCollectionHtml(string $contentType, array $blueprint, string $itemSlug = null): bool
```

### `generateHomeHtml()`

Generates home page (index.html):

```php
generateHomeHtml(): bool
```

## Output Buffering

The generator uses output buffering to capture template output:

```php
// Start buffering
ob_start();

// Include template (outputs HTML)
include $templateFile;

// Capture output
$html = ob_get_clean();
```

### Error Suppression

Errors are suppressed during generation to prevent corrupting HTML:

```php
$old_error_reporting = error_reporting(0);
ini_set('display_errors', 0);

// Generate HTML

error_reporting($old_error_reporting);
```

## File Locations

### Input Files

- **Templates**: `/catalogue/templates/{type}.php`
- **Blueprints**: `/catalogue/blueprints/{type}.blueprint.yml`
- **Content**: `/catalogue/content/pages/{type}/{type}.json`

### Output Files

- **Pages**: `/{type}.html` (root directory)
- **Home**: `/index.html` (root directory)
- **404**: `/404.html` (root directory)
- **Collections**: `/{collection}/{slug}.html`

## Special Cases

### Home Page

- Template: `home.php`
- Content: `pages/home/home.json`
- Output: `index.html` (not `home.html`)

### 404 Page

- Template: `404.php`
- Content: `pages/404/404.json`
- Output: `404.html`
- Handler: `catalogue/404-handler.php`

### Settings Page

- **Not generated** - Settings are admin-only
- Excluded from HTML generation

## Context Setting

Before template execution, context is set:

```php
// Content context
setCatalogueContent($content);

// Blueprint context
setCatalogueBlueprint($blueprint);

// Site settings context
setCatalogueSite($siteSettings);

// Page identifier (for traffic tracking)
$GLOBALS['cms_page_identifier'] = $contentType;
```

This allows templates to use `catalogue()` function to access data.

## Error Handling

### Missing Files

- **Blueprint missing** - Generation skipped, error logged
- **Template missing** - Generation skipped, error logged
- **Content missing** - Generation skipped, error logged

### Generation Errors

- Errors logged to error log
- HTML generation continues for other files
- Failed files reported in regenerate results

## See Also

- [Template Mapping](./TEMPLATE_MAPPING.md) - Template rules
- [Regenerating](./REGENERATING.md) - Manual regeneration

