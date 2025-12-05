# Slider & Switch Fields

Displaying slider and switch field values in templates.

## Field Types

- `slider` - Numeric slider (number value)
- `switch` - Toggle switch (boolean value)

## Slider Field

### Basic Usage

```php
<p>Rating: <?= catalogue('rating') ?>/10</p>
```

### With Default

```php
<p>Score: <?= catalogue('score', 0) ?></p>
```

### Percentage Display

```php
<?php $progress = catalogue('progress', 0); ?>
<div class="progress-bar">
    <div class="progress-fill" style="width: <?= $progress ?>%">
        <?= $progress ?>%
    </div>
</div>
```

## Switch Field

### Basic Usage

Switch fields return boolean-like values. Use conditionals:

```php
<?php if (catalogue('featured')): ?>
    <span class="badge">Featured</span>
<?php endif; ?>
```

### Display Value

For displaying switch values, use conditionals:

```php
<?php if (catalogue('published')): ?>
    <span>Published</span>
<?php else: ?>
    <span>Draft</span>
<?php endif; ?>
```

## Examples

### Slider - Rating Display

```php
<article class="review">
    <h1><?= catalogue('title') ?></h1>
    <div class="rating">
        <?php $rating = catalogue('rating', 0); ?>
        <div class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star <?= ($i <= $rating) ? 'filled' : '' ?>">★</span>
            <?php endfor; ?>
        </div>
        <span class="rating-value"><?= $rating ?>/5</span>
    </div>
</article>
```

### Slider - Progress Bar

```php
<div class="project">
    <h2><?= catalogue('title') ?></h2>
    <?php $progress = catalogue('completion', 0); ?>
    <div class="progress">
        <div class="progress-bar" style="width: <?= $progress ?>%">
            <?= $progress ?>% Complete
        </div>
    </div>
</div>
```

### Switch - Featured Badge

```php
<article class="post">
    <?php if (catalogue('featured')): ?>
        <span class="featured-badge">Featured</span>
    <?php endif; ?>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</article>
```

### Switch - Conditional Content

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('show_author')): ?>
        <p class="author">By <?= catalogue('author') ?></p>
    <?php endif; ?>
    <div><?= catalogue('content') ?></div>
</article>
```

### Multiple Switches

```php
<div class="post-meta">
    <?php if (catalogue('featured')): ?>
        <span class="badge">Featured</span>
    <?php endif; ?>
    <?php if (catalogue('pinned')): ?>
        <span class="badge">Pinned</span>
    <?php endif; ?>
    <?php if (catalogue('archived')): ?>
        <span class="badge">Archived</span>
    <?php endif; ?>
</div>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Conditionals](./CONDITIONALS.md)
