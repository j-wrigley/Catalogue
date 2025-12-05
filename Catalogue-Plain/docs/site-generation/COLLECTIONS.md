# Collection HTML Generation

How collections generate multiple HTML files from one template.

## Overview

Collections are different from pages:
- **One template** generates **multiple HTML files**
- Each collection item gets its own HTML file
- Files use slug-based URLs
- Stored in collection-specific directories

## Generation Process

### Step 1: Load Collection Items

```php
// Get all JSON files in collection directory
$jsonFiles = listJsonFiles(COLLECTIONS_DIR . '/posts');

// Load each item
foreach ($jsonFiles as $jsonFile) {
    $item = readJson($jsonFile);
    // Process item...
}
```

### Step 2: Generate HTML for Each Item

```php
foreach ($items as $item) {
    // Set content context
    setCatalogueContent($item);
    
    // Include template
    include 'templates/posts.php';
    
    // Save HTML file
    file_put_contents("posts/{$item['_slug']}.html", $html);
}
```

## File Structure

### Collection Directory

```
collections/
  posts/
    my-first-post.json
    another-post.json
    third-post.json
```

### Generated HTML Files

```
posts/
  my-first-post.html
  another-post.html
  third-post.html
```

## URL Structure

### Slug-Based URLs

Each item uses its slug for the URL:

```
Content: collections/posts/my-awesome-post.json
Slug:    my-awesome-post
Output:  posts/my-awesome-post.html
URL:     /posts/my-awesome-post
```

### Slug Generation

Slugs are generated from titles:

```php
// Title: "My Awesome Post"
// Slug: "my-awesome-post"
```

## Template Execution

### Single Template, Multiple Outputs

One template generates multiple files:

**Template:** `templates/posts.php`
```php
<?= snippet('header') ?>
<article>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</article>
<?= snippet('footer') ?>
```

**Generated Files:**
- `posts/item-1.html`
- `posts/item-2.html`
- `posts/item-3.html`

### Context Per Item

Each item sets its own context:

```php
// Item 1
setCatalogueContent($item1);
include 'templates/posts.php'; // Generates posts/item-1.html

// Item 2
setCatalogueContent($item2);
include 'templates/posts.php'; // Generates posts/item-2.html
```

## Generation Functions

### `generateCollectionHtml()`

Generates all items in a collection:

```php
generateCollectionHtml(string $contentType, array $blueprint, string $itemSlug = null): bool
```

**Parameters:**
- `$contentType` - Collection name (e.g., 'posts')
- `$blueprint` - Blueprint array
- `$itemSlug` - Specific item slug (null = all items)

**Returns:** `true` if at least one item generated

### `generateCollectionItemHtml()`

Generates HTML for a single item:

```php
generateCollectionItemHtml(string $contentType, array $item, array $blueprint, string $templateFile): bool
```

## Automatic Generation

### On Item Save

When saving a collection item:

1. Item saved to JSON file
2. **All items** in collection regenerated
3. Ensures consistency
4. New item appears immediately

### On Item Delete

When deleting an item:

1. JSON file deleted
2. HTML file deleted
3. Item removed from frontend

### On Slug Change

When changing an item slug:

1. New HTML file created with new slug
2. Old HTML file deleted
3. URL changes automatically

## Directory Creation

### Auto-Creation

Collection directories are created automatically:

```php
$collectionHtmlDir = CMS_ROOT . '/../' . $contentType;
if (!is_dir($collectionHtmlDir)) {
    mkdir($collectionHtmlDir, 0755, true);
}
```

### Example

For `posts` collection:
```
posts/ (created automatically)
  item-1.html
  item-2.html
```

## Examples

### Blog Posts Collection

**Blueprint:** `posts.blueprint.yml`
```yaml
title: Posts
fields:
  title:
    type: text
  content:
    type: markdown
```

**Template:** `templates/posts.php`
```php
<?= snippet('header') ?>
<article class="post">
    <h1><?= catalogue('title') ?></h1>
    <div class="content"><?= catalogue('content') ?></div>
</article>
<?= snippet('footer') ?>
```

**Content:** `collections/posts/post-1.json`
```json
{
  "_slug": "my-first-post",
  "title": "My First Post",
  "content": "Post content here"
}
```

**Output:** `posts/my-first-post.html`
**URL:** `/posts/my-first-post`

### Projects Collection

**Template:** `templates/projects.php`
```php
<?= snippet('header') ?>
<article class="project">
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('image')): ?>
        <img src="<?= catalogue('image') ?>" alt="<?= catalogue('title') ?>">
    <?php endif; ?>
    <div><?= catalogue('description') ?></div>
</article>
<?= snippet('footer') ?>
```

**Output:** `projects/{slug}.html` for each project

## Filtering

### Published Items Only

Only published items generate HTML:

```php
foreach ($items as $item) {
    if (isset($item['_status']) && $item['_status'] === 'published') {
        generateCollectionItemHtml(...);
    }
}
```

### Draft Items

Draft items:
- JSON file exists
- HTML file **not** generated
- Not accessible on frontend

## See Also

- [How It Works](./HOW_IT_WORKS.md) - Generation process
- [URL Structure](./URL_STRUCTURE.md) - URL patterns
- [Regenerating](./REGENERATING.md) - Manual regeneration

