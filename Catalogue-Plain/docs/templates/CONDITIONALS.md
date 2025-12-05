# Conditionals

Conditional rendering in templates.

## Basic Conditional

```php
<?php if (catalogue('field')): ?>
    <!-- Show if field has content -->
<?php endif; ?>
```

## Common Patterns

### Show if Field Exists

```php
<?php if (catalogue('subtitle')): ?>
    <h2><?= catalogue('subtitle') ?></h2>
<?php endif; ?>
```

### Show if Field is Empty

```php
<?php if (!catalogue('description')): ?>
    <p>No description available.</p>
<?php endif; ?>
```

### Compare Values

```php
<?php if (catalogue('status') === 'published'): ?>
    <span class="badge">Published</span>
<?php endif; ?>
```

### Multiple Conditions

```php
<?php if (catalogue('featured') && catalogue('status') === 'published'): ?>
    <span class="featured">Featured</span>
<?php endif; ?>
```

## Examples

### Conditional Image

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('featured_image')): ?>
        <?= catalogue('featured_image') ?>
    <?php endif; ?>
    <div><?= catalogue('content') ?></div>
</article>
```

### Conditional Tags

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
    <?php if (catalogue('tags')): ?>
        <div class="tags">
            Tags: <?= catalogue('tags') ?>
        </div>
    <?php endif; ?>
</article>
```

### Status-Based Display

```php
<?php if (catalogue('_status') === 'published'): ?>
    <article>
        <h1><?= catalogue('title') ?></h1>
        <div><?= catalogue('content') ?></div>
    </article>
<?php else: ?>
    <p>This content is not published.</p>
<?php endif; ?>
```

### Featured Content

```php
<?php if (catalogue('_featured')): ?>
    <div class="featured-badge">Featured</div>
<?php endif; ?>
```

### Collection Filtering

```php
<?php if (catalogueCollection('posts', ['status' => 'published'])): ?>
    <ul>
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <li><?= catalogue('title') ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No posts available.</p>
<?php endif; ?>
```

### Multiple Field Check

```php
<?php if (catalogue('author') && catalogue('date')): ?>
    <div class="meta">
        By <?= catalogue('author') ?> on <?= catalogue('date') ?>
    </div>
<?php endif; ?>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Collections](./COLLECTIONS.md)

