# Collections

Iterating through collection items in templates.

## Function

```php
catalogueCollection($collection, $filter = null)
```

## Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `collection` | String | Collection name (e.g., `'posts'`) |
| `filter` | Array | Filter options (optional) |

## Basic Usage

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <p><?= catalogue('description') ?></p>
    </article>
<?php endforeach; ?>
```

## Filters

Filter collection items by status, featured flag, and custom fields. Filters can be combined with sorting and pagination.

### Filter Options

| Option | Type | Values | Description |
|--------|------|--------|-------------|
| `status` | string | `'published'`, `'draft'`, `'unlisted'` | Filter by publication status |
| `featured` | boolean | `true` | Show only featured items |
| `sort` | string | `'date'`, `'created_at'`, `'updated_at'`, `'title'`, or any field name | Field to sort by |
| `order` | string | `'asc'`, `'desc'` | Sort order (default: `'desc'`) |
| `limit` | integer | Any positive number | Maximum items per page (for pagination) |
| `offset` | integer | Any positive number | Number of items to skip (auto-calculated from `?page=X` if `limit` is set) |

### Filter by Status

Show only published items:

```php
<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

Show only draft items:

```php
<?php foreach (catalogueCollection('posts', ['status' => 'draft']) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

Show unlisted items:

```php
<?php foreach (catalogueCollection('posts', ['status' => 'unlisted']) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Filter by Featured

Show only featured items:

```php
<?php foreach (catalogueCollection('posts', ['featured' => true]) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Combine Filters

Filter by multiple criteria:

```php
<?php foreach (catalogueCollection('posts', [
    'status' => 'published',
    'featured' => true
]) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Filter by Custom Fields

You can filter by any custom field from your blueprint. For example, if you have a `project-status` field:

```php
<?php 
// Note: Custom field filtering requires checking values in your template
// The filter system currently supports status and featured only
// For custom fields, filter in your template logic:
foreach (catalogueCollection('posts', ['status' => 'published']) as $post): 
    if (catalogue('project-status') === 'active'): ?>
        <h2><?= catalogue('title') ?></h2>
    <?php endif;
endforeach; ?>
```

**Note:** Currently, only `status` and `featured` are supported as filter options. For custom field filtering, check values in your template logic. Custom field filtering may be added in future versions.

## Sorting

Sort collection items by any field. By default, items are sorted by `updated_at` (newest first).

### Sort Options

| Option | Values | Description |
|--------|--------|-------------|
| `sort` | `'date'`, `'created_at'`, `'updated_at'`, `'title'`, or any field name | Field to sort by |
| `order` | `'asc'` or `'desc'` | Sort order (default: `'desc'`) |

### Sort by Date (Newest First)

```php
<?php foreach (catalogueCollection('posts', [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc'
]) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Sort by Created Date

```php
<?php foreach (catalogueCollection('posts', [
    'status' => 'published',
    'sort' => 'created_at',
    'order' => 'desc'
]) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Sort by Title (Alphabetical)

```php
<?php foreach (catalogueCollection('posts', [
    'status' => 'published',
    'sort' => 'title',
    'order' => 'asc'
]) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Sort by Custom Field

```php
<?php foreach (catalogueCollection('projects', [
    'status' => 'published',
    'sort' => 'project-status',
    'order' => 'asc'
]) as $project): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

**Note:** The `sort` field `'date'` is a shortcut for `'updated_at'`. For custom fields, numeric and date values are automatically detected and sorted appropriately.

## Pagination

Limit the number of items returned or skip items for pagination. Works automatically for both static HTML files (JavaScript pagination) and PHP templates (server-side pagination).

### Pagination Options

| Option | Type | Description |
|--------|------|-------------|
| `limit` | Integer | Maximum number of items per page |
| `offset` | Integer | Number of items to skip (optional, auto-calculated from `?page=X`) |

**Important:** During static HTML generation, `limit` is automatically ignored so all items are included in the HTML. JavaScript then handles pagination client-side. During PHP template execution, `limit` is applied normally for server-side pagination.

### Limit Results

```php
<?php 
// Show only the 5 most recent posts
foreach (catalogueCollection('posts', [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 5
]) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
<?php endforeach; ?>
```

### Pagination Example

When you add `limit` to your filters, pagination is automatically handled. Just add `cataloguePagination()` after your loop:

```php
<?php 
$filters = [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10  // Pagination automatically handled
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

**That's it!** The `limit` parameter automatically:
- **For static HTML files**: All items are included in the HTML, JavaScript handles pagination client-side
- **For PHP templates**: Server-side pagination applies the limit based on `?page=X` in the URL
- Shows pagination controls when there's more than one page
- Calculates the current page and offset automatically

### Combined: Filter, Sort, and Paginate

```php
<?php 
// One set of filters works for both collection and pagination
$filters = [
    'status' => 'published',
    'featured' => true,
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10  // Used for pagination calculation
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

**Note:** Use the same `$filters` array for both `catalogueCollection()` and `cataloguePagination()`. The `limit` is automatically handled:
- **Static HTML**: Ignored during generation (all items included), JavaScript paginates
- **PHP Templates**: Applied normally (server-side pagination)

### Manual Offset (Advanced)

If you need manual control over offset, you can specify it:

```php
<?php 
$filters = [
    'status' => 'published',
    'limit' => 10,
    'offset' => 20  // Skip first 20 items (only applies to PHP templates)
];
?>
```

**Note:** Manual `offset` only applies to PHP template execution. During static HTML generation, it's ignored.

## Available Fields

Inside the loop, access any field from the collection item:

```php
<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <article>
        <h2><a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a></h2>
        <p><?= catalogue('description') ?></p>
        <div class="content"><?= catalogue('content') ?></div>
        <?php if (catalogue('tags')): ?>
            <div class="tags"><?= catalogue('tags') ?></div>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
```

## Reserved Field Names

**⚠️ Do not use these field names in your blueprints** - they are automatically added by the system and will conflict:

### System-Generated Fields

| Field | Purpose | Why Reserved |
|-------|---------|--------------|
| `url` | Generated page URL | Automatically created from collection name and slug (e.g., `/posts/my-post.html`) |
| `slug` | Item slug | Automatically extracted from filename or `_slug` field |
| `status` | Publication status | Automatically added from `_status` core field |
| `featured` | Featured flag | Automatically added from `_featured` core field |
| `collection` | Collection name | Automatically added for reference (e.g., `'posts'`) |

### Core Fields (Underscore Prefix)

| Field | Purpose | Why Reserved |
|-------|---------|--------------|
| `_slug` | Core slug field | System field for URL generation |
| `_status` | Core status field | System field (draft/published/unlisted) |
| `_featured` | Core featured field | System field (boolean switch) |
| `_meta` | Metadata object | System field (created, updated, author timestamps) |

### Safe Alternatives

When you need similar functionality, use these field names instead:

- **Instead of `url`** → Use `link`, `external_url`, `spline-url`, `embed_url`, `video_url`, `external_link`
- **Instead of `slug`** → Use `permalink`, `identifier`, `custom_slug`, `url_slug`
- **Instead of `status`** → Use `visibility`, `state`, `publish_state`, `content_status`
- **Instead of `featured`** → Use `highlighted`, `promoted`, `spotlight`, `showcase`

### Example

```yaml
# ❌ Don't use reserved names
fields:
  url:
    type: text
    label: External Link  # Conflicts with system 'url' field!
  
  slug:
    type: text
    label: Custom Slug  # Conflicts with system 'slug' field!

# ✅ Use safe alternatives
fields:
  external_url:
    type: text
    label: External Link  # Safe!
  
  spline-url:
    type: text
    label: Spline Embed URL  # Safe!
  
  custom_slug:
    type: text
    label: Custom Slug  # Safe!
```

## Common Fields

| Field | Description |
|-------|-------------|
| `title` | Item title |
| `url` | **Reserved** - Item URL (automatically generated) |
| `slug` | **Reserved** - Item slug (automatically generated) |
| `description` | Item description |
| `content` | Item content (markdown rendered) |
| `tags` | Tags (formatted HTML) |
| `status` | **Reserved** - Publication status (use `_status` instead) |
| `featured` | **Reserved** - Featured flag (use `_featured` instead) |
| `created_at` | Creation timestamp (ISO 8601 format) |
| `updated_at` | Last update timestamp (ISO 8601 format) |

## Metadata Fields

Metadata is automatically added to all collection items. Access it using convenience fields:

### Timestamps

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <?php if (catalogue('created_at')): ?>
            <time datetime="<?= catalogue('created_at') ?>">
                Created: <?= catalogue('created_at') ?>
            </time>
        <?php endif; ?>
        <?php if (catalogue('updated_at')): ?>
            <time datetime="<?= catalogue('updated_at') ?>">
                Updated: <?= catalogue('updated_at') ?>
            </time>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
```

### Available Metadata Fields

| Field | Description | Format |
|-------|-------------|--------|
| `created_at` | Creation timestamp | ISO 8601 (e.g., `2025-11-27T16:34:03+00:00`) |
| `updated_at` | Last update timestamp | ISO 8601 (e.g., `2025-11-27T16:34:03+00:00`) |

**Note:** These fields are automatically extracted from the `_meta` object. The raw `_meta` object contains:
- `created` - Creation timestamp
- `updated` - Update timestamp  
- `updated_by` - Username who last updated
- `author` - Username who created

**Format:** All timestamps are in ISO 8601 format (e.g., `2025-11-27T16:34:03+00:00`).

## Examples

### Blog Archive

```php
<main class="archive">
    <h1>Blog Posts</h1>
    <ul class="post-list">
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <li>
                <a href="<?= catalogue('url') ?>">
                    <?= catalogue('title') ?>
                </a>
                <?php if (catalogue('description')): ?>
                    <p><?= catalogue('description') ?></p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</main>
```

### Featured Posts Grid

```php
<div class="featured-posts">
    <?php foreach (catalogueCollection('posts', [
        'status' => 'published',
        'featured' => true
    ]) as $post): ?>
        <article class="featured-post">
            <?php if (catalogue('featured_image')): ?>
                <?= catalogue('featured_image') ?>
            <?php endif; ?>
            <h2><a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a></h2>
            <p><?= catalogue('description') ?></p>
            <div class="content"><?= catalogue('content') ?></div>
        </article>
    <?php endforeach; ?>
</div>
```

### Collection with Images

```php
<div class="projects">
    <?php foreach (catalogueCollection('projects', ['status' => 'published']) as $project): ?>
        <article class="project">
            <?php if (catalogue('featured_image')): ?>
                <div class="project-image">
                    <?= catalogue('featured_image') ?>
                </div>
            <?php endif; ?>
            <div class="project-info">
                <h2><?= catalogue('title') ?></h2>
                <p><?= catalogue('description') ?></p>
                <?php if (catalogue('tags')): ?>
                    <div class="project-tags"><?= catalogue('tags') ?></div>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</div>
```

### Post with Metadata

```php
<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <article>
        <h2><a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a></h2>
        <p><?= catalogue('description') ?></p>
        <div class="content"><?= catalogue('content') ?></div>
        
        <footer class="post-meta">
            <?php if (catalogue('created_at')): ?>
                <time datetime="<?= catalogue('created_at') ?>">
                    Published: <?= catalogue('created_at') ?>
                </time>
            <?php endif; ?>
            <?php if (catalogue('updated_at') && catalogue('updated_at') !== catalogue('created_at')): ?>
                <time datetime="<?= catalogue('updated_at') ?>">
                    Updated: <?= catalogue('updated_at') ?>
                </time>
            <?php endif; ?>
        </footer>
    </article>
<?php endforeach; ?>
```

### Empty State

```php
<?php if (catalogueCollection('posts', ['status' => 'published'])): ?>
    <ul>
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <li><?= catalogue('title') ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>
```

## Pagination Controls

When using `limit`, pagination is automatically handled. Just add `cataloguePagination()` after your loop.

### Basic Pagination

```php
<?php
$filters = [
    'status' => 'published',
    'sort' => 'date',
    'order' => 'desc',
    'limit' => 10  // Pagination automatically handled
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

**That's it!** The `limit` parameter automatically:
- **For static HTML files**: All items are included in the HTML, JavaScript handles pagination client-side
- **For PHP templates**: Server-side pagination applies the limit based on `?page=X` in the URL
- Shows pagination controls when there's more than one page
- Calculates the current page and offset automatically

### How Pagination Works

**Static HTML Generation:**
- When generating static HTML files, `limit` is automatically ignored
- All items are included in the HTML output
- JavaScript pagination handles showing/hiding items based on the `?page=X` URL parameter
- No server-side processing needed - works on any static hosting

**PHP Template Execution:**
- When templates are executed directly (not during generation), `limit` is applied normally
- Server-side pagination calculates offset from `?page=X` parameter
- Only the requested page's items are returned

### Custom Pagination Options

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

### Pagination HTML Structure

The pagination function generates semantic HTML:

```html
<nav class="catalogue-pagination" aria-label="Pagination">
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

### Get Total Count

You can also get the total count of items (useful for displaying "Showing X of Y"):

```php
<?php
$totalItems = catalogueCollectionCount('posts', ['status' => 'published']);
$filters = ['status' => 'published', 'limit' => 10];
$items = catalogueCollection('posts', $filters);
echo "Showing " . iterator_count($items) . " of " . $totalItems . " posts";
?>
```

## Context

Inside a `catalogueCollection()` loop, `catalogue()` automatically uses the current item:

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <?= catalogue('title') ?>  <!-- Gets title from current $post -->
<?php endforeach; ?>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Files & Images](./FILES.md)
- [Conditionals](./CONDITIONALS.md)
