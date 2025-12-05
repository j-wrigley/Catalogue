# Tags

Displaying tags in templates.

## Field Type

- `tags` - Tag input field (stores array of strings)

## Basic Usage

```php
<div class="tags">
    <?= catalogue('tags') ?>
</div>
```

## How It Works

Tags are automatically rendered as HTML spans:

```html
<span class="tag">Design</span>
<span class="tag">Development</span>
<span class="tag">Marketing</span>
```

## Conditional Display

Only show if tags exist:

```php
<?php if (catalogue('tags')): ?>
    <div class="post-tags">
        <?= catalogue('tags') ?>
    </div>
<?php endif; ?>
```

## Examples

### Post Tags

```php
<article class="post">
    <h1><?= catalogue('title') ?></h1>
    <div class="content"><?= catalogue('content') ?></div>
    <?php if (catalogue('tags')): ?>
        <div class="post-tags">
            Tags: <?= catalogue('tags') ?>
        </div>
    <?php endif; ?>
</article>
```

### Category Tags

```php
<div class="article-meta">
    <?php if (catalogue('categories')): ?>
        <div class="categories">
            <?= catalogue('categories') ?>
        </div>
    <?php endif; ?>
</div>
```

### With Default

```php
<div class="tags">
    <?= catalogue('tags', '<span class="tag">No tags</span>') ?>
</div>
```

## Styling

Tags are rendered with class `tag`. Style them:

```css
.tag {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #f0f0f0;
    border-radius: 4px;
    margin-right: 0.5rem;
}
```

## Separator Support

For meta tags or comma-separated lists, use the separator parameter:

```php
<!-- Meta keywords (comma-separated) -->
<meta name="keywords" content="<?= catalogue('meta_keywords', '', 'content', ', ') ?>">

<!-- Categories (pipe-separated) -->
<p>Categories: <?= catalogue('categories', '', 'content', ' | ') ?></p>
```

**Smart Detection:**
The 4th parameter can be used as a separator if it looks like one:

```php
<?= catalogue('tags', '', 'content', ', ') ?>
<!-- Automatically joins array with comma and space -->
```

**Output:**
```html
<meta name="keywords" content="Design, Development, Marketing">
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Conditionals](./CONDITIONALS.md)
- [Select Fields](./SELECT_FIELDS.md)

