# Extending Templates

Advanced template techniques and patterns.

## Overview

This guide covers advanced techniques for extending and customizing templates beyond basic usage.

## Custom Template Functions

### Creating Helper Functions

Add custom functions to `catalogue.php`:

```php
// In catalogue/lib/catalogue.php

/**
 * Format date in custom format
 */
function formatCustomDate($date, $format = 'F j, Y') {
    if (empty($date)) {
        return '';
    }
    return date($format, strtotime($date));
}

/**
 * Get related items
 */
function getRelatedItems($collection, $currentSlug, $limit = 3) {
    $items = catalogueCollection($collection, ['status' => 'published']);
    $related = [];
    $count = 0;
    
    foreach ($items as $item) {
        if (catalogue('slug') !== $currentSlug && $count < $limit) {
            $related[] = $item;
            $count++;
        }
    }
    
    return $related;
}
```

### Using Custom Functions

```php
<!-- In templates -->
<p>Published: <?= formatCustomDate(catalogue('date')) ?></p>

<?php $related = getRelatedItems('posts', catalogue('slug')); ?>
<?php foreach ($related as $post): ?>
    <h3><?= catalogue('title') ?></h3>
<?php endforeach; ?>
```

## Advanced Iteration Patterns

### Filtered Collections

```php
<?php
// Get featured posts only
$featured = [];
foreach (catalogueCollection('posts', ['status' => 'published']) as $post) {
    if (catalogue('featured')) {
        $featured[] = $post;
    }
}
?>

<?php foreach ($featured as $post): ?>
    <!-- Display featured post -->
<?php endforeach; ?>
```

### Grouped Collections

```php
<?php
// Group posts by category
$grouped = [];
foreach (catalogueCollection('posts', ['status' => 'published']) as $post) {
    $category = catalogue('category', 'uncategorized');
    if (!isset($grouped[$category])) {
        $grouped[$category] = [];
    }
    $grouped[$category][] = $post;
}
?>

<?php foreach ($grouped as $category => $posts): ?>
    <h2><?= htmlspecialchars($category) ?></h2>
    <?php foreach ($posts as $post): ?>
        <!-- Display post -->
    <?php endforeach; ?>
<?php endforeach; ?>
```

## Conditional Rendering Patterns

### Complex Conditionals

```php
<?php
$status = catalogue('status');
$featured = catalogue('featured');
$date = catalogue('date');

// Multiple conditions
if ($status === 'published' && $featured && strtotime($date) > strtotime('-7 days')) {
    // Show featured recent post
}
?>
```

### Switch Patterns

```php
<?php
$layout = catalogue('layout_type', 'default');

switch ($layout) {
    case 'grid':
        // Grid layout
        break;
    case 'list':
        // List layout
        break;
    case 'masonry':
        // Masonry layout
        break;
    default:
        // Default layout
}
?>
```

## Template Inheritance

### Base Template Pattern

Create a base template:

```php
<!-- templates/base.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?= catalogue('title', 'Site') ?></title>
</head>
<body>
    <?= snippet('header') ?>
    
    <main>
        <?php include $content_template; ?>
    </main>
    
    <?= snippet('footer') ?>
</body>
</html>
```

### Extending Base Template

```php
<!-- templates/page.php -->
<?php
$content_template = __DIR__ . '/page-content.php';
include __DIR__ . '/base.php';
?>
```

## Advanced Snippet Usage

### Snippets with Logic

```php
<!-- snippets/related-posts.php -->
<?php
$current_slug = catalogue('slug');
$related = [];

foreach (catalogueCollection('posts', ['status' => 'published']) as $post) {
    if (catalogue('slug') !== $current_slug) {
        $related[] = $post;
        if (count($related) >= 3) break;
    }
}
?>

<?php if (!empty($related)): ?>
    <section class="related-posts">
        <h2>Related Posts</h2>
        <?php foreach ($related as $post): ?>
            <article>
                <h3><?= catalogue('title') ?></h3>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
```

### Dynamic Snippet Loading

```php
<?php
$snippet_name = catalogue('snippet_name', 'default');
if (file_exists(__DIR__ . '/snippets/' . $snippet_name . '.php')) {
    echo snippet($snippet_name);
}
?>
```

## Data Transformation

### Transforming Collection Data

```php
<?php
// Transform collection items
$transformed = [];
foreach (catalogueCollection('products') as $product) {
    $transformed[] = [
        'title' => catalogue('title'),
        'price' => floatval(catalogue('price')),
        'formatted_price' => '$' . number_format(floatval(catalogue('price')), 2),
        'url' => catalogue('url'),
    ];
}
?>

<?php foreach ($transformed as $item): ?>
    <div class="product">
        <h3><?= htmlspecialchars($item['title']) ?></h3>
        <p class="price"><?= htmlspecialchars($item['formatted_price']) ?></p>
    </div>
<?php endforeach; ?>
```

## Advanced Markdown Usage

### Custom Markdown Processing

```php
<?php
$content = catalogue('content');
// Custom processing
$content = str_replace('[CTA]', '<div class="cta">Call to Action</div>', $content);
echo $content;
?>
```

## Error Handling

### Graceful Degradation

```php
<?php
try {
    $posts = catalogueCollection('posts');
    if ($posts) {
        foreach ($posts as $post) {
            // Display posts
        }
    }
} catch (Exception $e) {
    // Fallback content
    echo '<p>Unable to load posts at this time.</p>';
}
?>
```

## Performance Optimization

### Caching Calculations

```php
<?php
// Cache expensive calculations
static $cached_nav = null;
if ($cached_nav === null) {
    $cached_nav = catalogueNav();
}
?>
```

### Lazy Loading

```php
<!-- Load content only when needed -->
<div class="lazy-content" data-load="<?= catalogue('url') ?>">
    Loading...
</div>

<script>
document.querySelectorAll('.lazy-content').forEach(el => {
    const url = el.dataset.load;
    fetch(url).then(r => r.text()).then(html => {
        el.innerHTML = html;
    });
});
</script>
```

## Best Practices

1. **Keep Logic Simple**: Avoid complex logic in templates
2. **Use Helper Functions**: Create reusable functions for common tasks
3. **Cache Expensive Operations**: Cache calculations and queries
4. **Handle Errors Gracefully**: Always provide fallbacks
5. **Document Custom Functions**: Comment complex template code
6. **Test Thoroughly**: Test templates with various data scenarios

## Examples

### Advanced Archive Template

```php
<?= snippet('header') ?>

<?php
// Group posts by year/month
$archived = [];
foreach (catalogueCollection('posts', ['status' => 'published']) as $post) {
    $date = catalogue('date');
    $year = date('Y', strtotime($date));
    $month = date('F', strtotime($date));
    
    if (!isset($archived[$year][$month])) {
        $archived[$year][$month] = [];
    }
    $archived[$year][$month][] = $post;
}

// Sort by date (newest first)
krsort($archived);
?>

<main class="archive">
    <?php foreach ($archived as $year => $months): ?>
        <h2><?= $year ?></h2>
        <?php foreach ($months as $month => $posts): ?>
            <h3><?= $month ?></h3>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li>
                        <a href="<?= catalogue('url') ?>">
                            <?= catalogue('title') ?>
                        </a>
                        <span class="date"><?= formatCustomDate(catalogue('date'), 'j M') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endforeach; ?>
</main>

<?= snippet('footer') ?>
```

## See Also

- [Templates](../templates/README.md) - Basic template usage
- [Collections](../templates/COLLECTIONS.md) - Collection iteration
- [Snippets](../templates/SNIPPETS.md) - Reusable components
- [Performance](./PERFORMANCE.md) - Performance optimization

