# Pagination

Paginate collection items with automatic handling for both static HTML files and PHP templates.

## Overview

Pagination allows you to split large collections into multiple pages. Catalogue automatically handles pagination differently depending on how your site is served:

- **Static HTML files**: All items are included in the HTML, JavaScript handles pagination client-side
- **PHP templates**: Server-side pagination applies limits based on URL parameters

## Basic Usage

Add `limit` to your filters and call `cataloguePagination()` after your loop:

```php
<?php 
$filters = [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10  // Items per page
];
?>

<?php foreach (catalogueCollection('posts', $filters) as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <p><?= catalogue('description') ?></p>
    </article>
<?php endforeach; ?>

<?php echo cataloguePagination('posts', $filters); ?>
```

**That's it!** Use the same `$filters` array for both `catalogueCollection()` and `cataloguePagination()`.

## How It Works

### Static HTML Generation

When generating static HTML files:

- `limit` is **automatically ignored** during generation
- **All items** are included in the HTML output
- JavaScript pagination handles showing/hiding items based on `?page=X` URL parameter
- No server-side processing needed - works on any static hosting
- Browser back/forward buttons work correctly

**Benefits:**
- Fast page loads (no server processing)
- Works on CDN/static hosting
- SEO-friendly (all content in HTML)
- Smooth navigation without page reloads

### PHP Template Execution

When templates are executed directly (not during generation):

- `limit` is **applied normally** for server-side pagination
- Offset is automatically calculated from `?page=X` parameter
- Only the requested page's items are returned
- Reduces server load and memory usage

**Benefits:**
- Efficient for large collections
- Reduces memory usage
- Faster database queries (if using database)

## Filter Options

### Limit

Set the maximum number of items per page:

```php
$filters = [
    'status' => 'published',
    'limit' => 10  // Show 10 items per page
];
```

### Sort and Order

Sort items before pagination (recommended):

```php
$filters = [
    'status' => 'published',
    'sort' => 'date',        // Sort by date
    'order' => 'desc',       // Newest first
    'limit' => 10
];
```

See [Sorting and Ordering](#sorting-and-ordering) section below for details.

### Offset (Advanced)

Manually control which items to skip. Usually not needed - offset is auto-calculated from `?page=X`:

```php
$filters = [
    'status' => 'published',
    'limit' => 10,
    'offset' => 20  // Skip first 20 items (only applies to PHP templates)
];
```

**Note:** Manual `offset` only applies to PHP template execution. During static HTML generation, it's ignored.

## Sorting and Ordering

Always sort when paginating to ensure consistent ordering across pages. Sorting is applied before pagination, so items are ordered first, then split into pages.

### Sort Options

| Option | Values | Description |
|--------|--------|-------------|
| `sort` | `'date'`, `'created_at'`, `'updated_at'`, `'title'`, or any field name | Field to sort by |
| `order` | `'asc'` or `'desc'` | Sort order (default: `'desc'`) |

### Sort by Date (Newest First)

Most common for blog posts and news:

```php
$filters = [
    'status' => 'published',
    'sort' => 'date',        // Shortcut for 'updated_at'
    'order' => 'desc',       // Newest first
    'limit' => 10
];
```

### Sort by Created Date

Sort by when items were originally created:

```php
$filters = [
    'status' => 'published',
    'sort' => 'created_at',
    'order' => 'desc',       // Newest first
    'limit' => 10
];
```

### Sort by Title (Alphabetical)

Sort alphabetically by title:

```php
$filters = [
    'status' => 'published',
    'sort' => 'title',
    'order' => 'asc',        // A to Z
    'limit' => 10
];
```

### Sort by Custom Field

Sort by any custom field from your blueprint:

```php
$filters = [
    'status' => 'published',
    'sort' => 'project-status',  // Custom field
    'order' => 'asc',
    'limit' => 10
];
```

### Available Sort Fields

**Built-in fields:**
- `'date'` - Shortcut for `'updated_at'` (last modified date)
- `'created_at'` - Creation timestamp
- `'updated_at'` - Last update timestamp
- `'title'` - Item title

**Custom fields:**
- Any field name from your blueprint
- Numeric and date values are automatically detected
- Text fields are sorted alphabetically

### Sort Order

- `'desc'` - Descending (default)
  - Dates: Newest first
  - Numbers: Highest first
  - Text: Z to A
- `'asc'` - Ascending
  - Dates: Oldest first
  - Numbers: Lowest first
  - Text: A to Z

### Why Sorting Matters

**Without sorting:**
- Items appear in random order
- Order may change between page loads
- Pagination becomes confusing

**With sorting:**
- Consistent ordering across all pages
- Predictable pagination behavior
- Better user experience

### Example: Sorted and Paginated

```php
<?php
$filters = [
    'status' => 'published',
    'sort' => 'date',        // Sort by date
    'order' => 'desc',       // Newest first
    'limit' => 10            // 10 items per page
];
?>

<?php foreach (catalogueCollection('posts', $filters) as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <time><?= catalogue('created_at', '', 'content', 'F j, Y') ?></time>
    </article>
<?php endforeach; ?>

<?php echo cataloguePagination('posts', $filters); ?>
```

## Pagination Controls

The `cataloguePagination()` function generates pagination controls with:

- **Previous button** - Links to previous page (disabled on first page)
- **Page numbers** - Clickable page numbers with active state
- **Next button** - Links to next page (disabled on last page)
- **Ellipsis** - Shows `…` when there are many pages

### Basic Example

```php
<?php
$filters = [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10
];
?>

<div class="posts">
    <?php foreach (catalogueCollection('posts', $filters) as $post): ?>
        <article>
            <h2><?= catalogue('title') ?></h2>
        </article>
    <?php endforeach; ?>
</div>

<?php echo cataloguePagination('posts', $filters); ?>
```

### Custom Options

Customize pagination appearance and behavior:

```php
<?php
echo cataloguePagination('posts', $filters, [
    'class' => 'my-pagination',
    'prevText' => '← Previous',
    'nextText' => 'Next →',
    'showPageNumbers' => true,
    'maxPageNumbers' => 5
]);
?>
```

### Pagination Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `class` | string | `'catalogue-pagination'` | CSS class for pagination container |
| `prevText` | string | `'Previous'` | Text for previous button |
| `nextText` | string | `'Next'` | Text for next button |
| `showPageNumbers` | bool | `true` | Show page number buttons |
| `maxPageNumbers` | int | `7` | Maximum page numbers to show before using ellipsis |

## Examples

### Blog Archive with Pagination

```php
<?php
$filters = [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 12
];
?>

<main class="blog-archive">
    <h1>Blog Posts</h1>
    
    <div class="posts-grid">
        <?php foreach (catalogueCollection('posts', $filters) as $post): ?>
            <article class="post-card">
                <h2><a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a></h2>
                <p><?= catalogue('description') ?></p>
                <?php if (catalogue('created_at')): ?>
                    <time><?= catalogue('created_at', '', 'content', 'F j, Y') ?></time>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    
    <?php echo cataloguePagination('posts', $filters); ?>
</main>
```

### Featured Posts Feed

```php
<?php
$filters = [
    'status' => 'published',
    'featured' => true,
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 6
];
?>

<section class="featured-posts">
    <h2>Featured Posts</h2>
    
    <?php foreach (catalogueCollection('posts', $filters) as $post): ?>
        <article>
            <?php if (catalogue('featured_image')): ?>
                <?= catalogue('featured_image') ?>
            <?php endif; ?>
            <h3><?= catalogue('title') ?></h3>
            <p><?= catalogue('description') ?></p>
        </article>
    <?php endforeach; ?>
    
    <?php echo cataloguePagination('posts', $filters); ?>
</section>
```

### Combined: Filter, Sort, and Paginate

```php
<?php
// One set of filters works for both collection and pagination
$filters = [
    'status' => 'published',
    'featured' => true,
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10
];
?>

<?php foreach (catalogueCollection('posts', $filters) as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <p><?= catalogue('description') ?></p>
    </article>
<?php endforeach; ?>

<?php echo cataloguePagination('posts', $filters); ?>
```

## HTML Structure

The pagination function generates semantic HTML:

```html
<nav class="catalogue-pagination" aria-label="Pagination" data-limit="10">
    <div class="catalogue-pagination__controls">
        <a href="?page=1" class="catalogue-pagination__prev">Previous</a>
        <div class="catalogue-pagination__pages">
            <a href="?page=1" class="catalogue-pagination__page">1</a>
            <span class="catalogue-pagination__page catalogue-pagination__page--active">2</span>
            <a href="?page=3" class="catalogue-pagination__page">3</a>
        </div>
        <a href="?page=3" class="catalogue-pagination__next">Next</a>
    </div>
</nav>
```

### CSS Classes

- `.catalogue-pagination` - Main container
- `.catalogue-pagination__controls` - Controls wrapper
- `.catalogue-pagination__prev` - Previous button
- `.catalogue-pagination__prev--disabled` - Disabled previous button
- `.catalogue-pagination__next` - Next button
- `.catalogue-pagination__next--disabled` - Disabled next button
- `.catalogue-pagination__pages` - Page numbers container
- `.catalogue-pagination__page` - Page number link
- `.catalogue-pagination__page--active` - Active page indicator
- `.catalogue-pagination__ellipsis` - Ellipsis (`…`)

## Getting Total Count

Get the total count of items (useful for displaying "Showing X of Y"):

```php
<?php
$totalItems = catalogueCollectionCount('posts', ['status' => 'published']);
$filters = ['status' => 'published', 'limit' => 10];
$items = catalogueCollection('posts', $filters);
$showing = iterator_count($items);
echo "Showing $showing of $totalItems posts";
?>
```

## URL Parameters

Pagination uses the `page` URL parameter:

- `?page=1` - First page (or no parameter)
- `?page=2` - Second page
- `?page=3` - Third page
- etc.

The pagination function automatically:
- Reads `?page=X` from the URL
- Calculates offset: `(page - 1) * limit`
- Updates pagination links correctly
- Handles browser back/forward buttons

## Best Practices

### 1. Use Same Filters

Always use the same filters array for both `catalogueCollection()` and `cataloguePagination()`:

```php
<?php
$filters = [
    'status' => 'published',
    'limit' => 10
];
?>

<?php foreach (catalogueCollection('posts', $filters) as $post): ?>
    <!-- content -->
<?php endforeach; ?>

<?php echo cataloguePagination('posts', $filters); ?>
```

### 2. Set Reasonable Limits

Choose a limit that balances usability and performance:

```php
'limit' => 10   // Good for most cases
'limit' => 20   // For grids/lists
'limit' => 5    // For featured items
```

### 3. Always Sort When Paginating

Always sort when paginating to ensure consistent ordering across pages:

```php
$filters = [
    'status' => 'published',
    'sort' => 'date',        // Sort by date
    'order' => 'desc',       // Newest first
    'limit' => 10
];
```

**Why:** Without sorting, items appear in random order and pagination becomes confusing. Sorting ensures:
- Consistent order across all pages
- Predictable pagination behavior
- Better user experience

### 4. Style Pagination Controls

Add CSS to style the pagination controls:

```css
.catalogue-pagination {
    display: flex;
    justify-content: center;
    margin: 2rem 0;
}

.catalogue-pagination__page--active {
    font-weight: bold;
    background: #000;
    color: #fff;
}
```

## Troubleshooting

### Pagination Not Showing

**Issue:** Pagination controls don't appear.

**Solutions:**
1. Ensure `limit` is set in filters
2. Check that there are more items than the limit
3. Verify `cataloguePagination()` is called after the loop

### All Items Showing (Static HTML)

**Issue:** All items visible on static HTML pages.

**Expected behavior:** During HTML generation, all items are included. JavaScript handles pagination client-side. This is correct!

### Wrong Page Showing

**Issue:** Clicking page 2 shows page 1 content.

**Solutions:**
1. Check that JavaScript is enabled
2. Verify pagination script is included in HTML
3. Check browser console for JavaScript errors

### Pagination Links Wrong

**Issue:** Pagination links point to wrong URLs.

**Solutions:**
1. Regenerate your site to update pagination HTML
2. Check that `BASE_PATH` is set correctly
3. Verify URL structure matches your site setup

## See Also

- [Collections](./COLLECTIONS.md) - Collection iteration and filtering
- [Sorting](./COLLECTIONS.md#sorting) - Sorting collection items
- [Template Functions](../api-reference/TEMPLATE_FUNCTIONS.md) - API reference

