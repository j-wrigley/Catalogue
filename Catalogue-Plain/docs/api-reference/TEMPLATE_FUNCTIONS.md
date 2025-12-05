# Template Functions

Functions for accessing and rendering content in templates.

## `catalogue()`

Get and render field values from content.

### Syntax

```php
catalogue(string $key, string $default = '', string $source = 'content', string|null $dateFormat = null, string|null $separator = null): string
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$key` | string | Field name (supports dot notation) | Required |
| `$default` | string | Default value if field is empty | `''` |
| `$source` | string | Data source (`content`, `site`, or `file`) | `'content'` |
| `$dateFormat` | string\|null | PHP date format string (e.g., `'F j, Y'`) or separator string (e.g., `', '`) | `null` |
| `$separator` | string\|null | Separator for array values (e.g., `', '`, `' \| '`) | `null` |

### Returns

Rendered field value as string (HTML escaped or rendered based on field type).

### Examples

```php
<?= catalogue('title') ?>
<?= catalogue('description', 'No description') ?>
<?= catalogue('site_name', 'Site', 'site') ?>
<?= catalogue('image.src') ?>
<?= catalogue('created_at', '', 'content', 'F j, Y') ?>
<?= catalogue('meta_keywords', '', 'content', ', ') ?>
<?= catalogue('tags', '', 'content', null, ' | ') ?>
```

### Field Type Rendering

Automatically renders based on blueprint field type:
- `text` / `textarea` → Escaped HTML string
- `markdown` → Rendered HTML
- `tags` → Formatted HTML spans (or comma-separated string with separator)
- `checkbox` → Formatted HTML spans (or comma-separated string with separator)
- `file` → Image HTML or file URL
- `select` / `radio` → Option label

### Date Formatting

Format date fields using the `$dateFormat` parameter:

```php
<?= catalogue('created_at', '', 'content', 'F j, Y') ?>
<!-- Output: November 24, 2025 -->
```

### Array Separator

Join array values (tags, checkboxes) with a custom separator:

```php
<?= catalogue('meta_keywords', '', 'content', ', ') ?>
<!-- Output: tag1, tag2, tag3 -->

<?= catalogue('categories', '', 'content', null, ' | ') ?>
<!-- Output: Design | Development | Marketing -->
```

**Smart Detection:** If `$dateFormat` looks like a separator (contains `,`, `|`, `;`, `:`, or spaces but no date format characters), it will be used as a separator instead.

### Context Awareness

Automatically detects context:
- **Page content** - Current page being rendered
- **Collection item** - When inside `catalogueCollection()` loop
- **File metadata** - When inside `catalogueFiles()` loop

---

## `catalogueRaw()`

Get raw field values without rendering.

### Syntax

```php
catalogueRaw(string $key, mixed $default = null, string $source = 'content'): mixed
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$key` | string | Field name | Required |
| `$default` | mixed | Default value | `null` |
| `$source` | string | Data source | `'content'` |

### Returns

Raw field value (string, array, boolean, etc.).

### Examples

```php
<?php $tags = catalogueRaw('tags', []); ?>
<?php $isFeatured = catalogueRaw('featured', false); ?>
<?php $items = catalogueRaw('items', []); ?>
```

---

## `catalogueFiles()`

Iterate through file fields.

### Syntax

```php
catalogueFiles(string $field, string $source = 'content'): CatalogueFilesIterator
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$field` | string | Field name containing files | Required |
| `$source` | string | Data source | `'content'` |

### Returns

`CatalogueFilesIterator` object for foreach loops.

### Examples

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('image') ?>
    <?= catalogue('caption') ?>
<?php endforeach; ?>
```

### Available Fields

Inside loop, access:
- `catalogue('image')` - Pre-rendered image HTML
- `catalogue('alt_text')` - Alt text
- `catalogue('caption')` - Caption
- `catalogue('description')` - Description
- `catalogue('credit')` - Photo credit
- `catalogue('tags')` - Tags

---

## `catalogueCollection()`

Iterate through collection items.

### Syntax

```php
catalogueCollection(string $collection, array $filter = null, string $field = null): CatalogueCollectionIterator|array
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$collection` | string | Collection name | Required |
| `$filter` | array | Filter options | `null` |
| `$field` | string | Specific field to return | `null` |

### Returns

`CatalogueCollectionIterator` for foreach loops, or array if `$field` specified.

### Examples

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>

<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <?= catalogue('title') ?>
<?php endforeach; ?>
```

### Filter Options

```php
['status' => 'published']           // Published items only
['featured' => true]               // Featured items
['status' => 'published', 'featured' => true]  // Both conditions
```

### Sorting Options

```php
['sort' => 'date']                  // Sort by updated_at (newest first)
['sort' => 'date', 'order' => 'asc']  // Sort by updated_at (oldest first)
['sort' => 'created_at', 'order' => 'desc']  // Sort by created date
['sort' => 'title', 'order' => 'asc']  // Sort alphabetically by title
['sort' => 'field-name']            // Sort by any custom field
```

**Sort Fields:**
- `'date'` - Shortcut for `'updated_at'`
- `'created_at'` - Creation timestamp
- `'updated_at'` - Last update timestamp
- `'title'` - Item title
- Any custom field name from your blueprint

**Order:**
- `'desc'` - Descending (default)
- `'asc'` - Ascending

### Pagination Options

```php
['limit' => 10]                     // Maximum items per page
['offset' => 0]                     // Skip first N items (optional, auto-calculated from ?page=X)
['limit' => 10, 'offset' => 20]    // Manual offset (advanced)
```

**Note:** During static HTML generation, `limit` is automatically ignored so all items are included for JavaScript pagination. During PHP template execution, `limit` is applied normally for server-side pagination.

### Complete Example

```php
<?php 
$filters = [
    'status' => 'published',
    'featured' => true,
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10  // Pagination automatically handled
];
?>

<?php foreach (catalogueCollection('posts', $filters) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>

<?php echo cataloguePagination('posts', $filters); ?>
```

**How it works:**
- Put `limit` in your filters once
- Use the same filters for both `catalogueCollection()` and `cataloguePagination()`
- For static HTML: All items included, JavaScript paginates
- For PHP templates: Server-side pagination applies limit

---

## `catalogueCollectionCount()`

Get total count of collection items (useful for pagination).

### Syntax

```php
catalogueCollectionCount(string $collection, array $filter = null): int
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$collection` | string | Collection name | Required |
| `$filter` | array | Filter options (same as catalogueCollection, limit/offset ignored) | `null` |

### Returns

Total count of matching items (integer).

### Example

```php
<?php
$totalItems = catalogueCollectionCount('posts', ['status' => 'published']);
echo "Total posts: " . $totalItems;
?>
```

---

## `cataloguePagination()`

Generate pagination controls (prev/next buttons and page numbers).

### Syntax

```php
cataloguePagination(string $collection, array $filters = [], array $options = []): string
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$collection` | string | Collection name | Required |
| `$filters` | array | Filter options (must include 'limit') | `[]` |
| `$options` | array | Additional options (class, prevText, etc.) | `[]` |

### Returns

HTML string for pagination controls (empty if no limit or single page).

### Example

```php
<?php
$filters = ['status' => 'published', 'limit' => 10];
echo cataloguePagination('posts', $filters);
?>
```

### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `class` | string | `'catalogue-pagination'` | CSS class for pagination container |
| `prevText` | string | `'Previous'` | Text for previous button |
| `nextText` | string | `'Next'` | Text for next button |
| `showPageNumbers` | bool | `true` | Show page number buttons |
| `maxPageNumbers` | int | `7` | Maximum page numbers to show before using ellipsis |

### Complete Pagination Example

```php
<?php
$filters = [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10
];
?>

<?php foreach (catalogueCollection('posts', $filters) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>

<?php echo cataloguePagination('posts', $filters, [
    'class' => 'my-pagination',
    'prevText' => '← Previous',
    'nextText' => 'Next →'
]); ?>
```

---

## `catalogueNav()`

Get navigation data.

### Syntax

```php
catalogueNav(string $page = null, string $field = null): array|string|null
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$page` | string | Page slug (null = all pages) | `null` |
| `$field` | string | Specific field to return | `null` |

### Returns

Array of pages, single page array, or field value.

### Examples

```php
<?php $pages = catalogueNav(); ?>
<?php $about = catalogueNav('about'); ?>
<?php $title = catalogueNav('about', 'title'); ?>
```

---

## `snippet()`

Include reusable template components.

### Syntax

```php
snippet(string $name, array $vars = []): void
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$name` | string | Snippet name (without .php) | Required |
| `$vars` | array | Variables to pass to snippet | `[]` |

### Returns

Void (outputs HTML directly).

### Examples

```php
<?= snippet('header') ?>
<?= snippet('card', ['title' => 'Card', 'content' => 'Text']) ?>
```

### Snippet Location

Snippets stored in `/catalogue/templates/snippets/`:
- `header.php`
- `footer.php`
- `card.php`

---

## `traffic()`

Track page views.

### Syntax

```php
traffic(string $action = 'log', string $page = null): void|int
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$action` | string | Action (`log` or `get`) | `'log'` |
| `$page` | string | Page identifier | `null` |

### Returns

Void for `log`, integer count for `get`.

### Examples

```php
<?= traffic('log') ?>
<?php $views = traffic('get', 'about'); ?>
```

### Usage

Add to templates to track views:
```php
<?= traffic('log') ?>
```

---

## Helper Functions

### `navLink()`

Generate link HTML from page data.

```php
navLink(array $page, string $field = null, array $attributes = []): string
```

### `navList()`

Generate list HTML from pages array.

```php
navList(array $pages, string $listClass = null, string $itemClass = null, string $linkClass = null): string
```

### `catalogueMedia()`

Get media metadata.

```php
catalogueMedia(string $filePath, string $field = null): array|string|null
```

## See Also

- [Templates Documentation](../templates/README.md) - Template usage guide
- [Storage Functions](./STORAGE_FUNCTIONS.md) - File operations

