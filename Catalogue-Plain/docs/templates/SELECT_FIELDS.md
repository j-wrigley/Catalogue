# Select, Radio & Checkbox Fields

Displaying select, radio, and checkbox field values in templates.

## Field Types

- `select` - Dropdown selection (single value)
- `radio` - Radio button group (single value)
- `checkbox` - Checkbox group (multiple values)

## Select Field

### Basic Usage

```php
<p>Category: <?= catalogue('category') ?></p>
```

### With Default

```php
<p>Status: <?= catalogue('status', 'Unknown') ?></p>
```

### Conditional Display

```php
<?php if (catalogue('status') === 'published'): ?>
    <span class="badge">Published</span>
<?php endif; ?>
```

## Radio Field

Radio fields work the same as select fields:

```php
<p>Type: <?= catalogue('type') ?></p>
```

## Checkbox Field

Checkbox fields are automatically formatted as HTML spans:

```php
<div class="categories">
    <?= catalogue('categories') ?>
</div>
```

### How It Works

Checkboxes are automatically rendered as HTML spans:

```html
<span class="checkbox-item">Design</span>
<span class="checkbox-item">Development</span>
<span class="checkbox-item">Marketing</span>
```

### Conditional Display

Only show if checkboxes exist:

```php
<?php if (catalogue('categories')): ?>
    <div class="categories">
        <?= catalogue('categories') ?>
    </div>
<?php endif; ?>
```

## Examples

### Select Field

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <p class="status">Status: <?= catalogue('status') ?></p>
    <p class="category">Category: <?= catalogue('category', 'Uncategorized') ?></p>
</article>
```

### Radio Field

```php
<div class="post">
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('visibility') === 'public'): ?>
        <span class="badge">Public</span>
    <?php endif; ?>
</div>
```

### Checkbox Field

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('categories')): ?>
        <div class="categories">
            Categories: <?= catalogue('categories') ?>
        </div>
    <?php endif; ?>
</article>
```

### Status Badge

```php
<?php 
$status = catalogue('status');
$statusClass = strtolower($status);
?>
<span class="status-badge status-<?= $statusClass ?>">
    <?= htmlspecialchars($status) ?>
</span>
```

## Styling

Checkbox items are rendered with class `checkbox-item`. Style them:

```css
.checkbox-item {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #f0f0f0;
    border-radius: 4px;
    margin-right: 0.5rem;
}
```

## Separator Support

For checkbox fields (or any array), you can join values with a custom separator:

```php
<!-- Comma-separated categories -->
<p>Categories: <?= catalogue('categories', '', 'content', ', ') ?></p>

<!-- Pipe-separated tags -->
<p>Tags: <?= catalogue('tags', '', 'content', ' | ') ?></p>
```

**Smart Detection:**
The 4th parameter can be used as a separator if it looks like one:

```php
<?= catalogue('categories', '', 'content', ', ') ?>
<!-- Automatically joins array with comma and space -->
```

**Output:**
```html
<p>Categories: Design, Development, Marketing</p>
```

**Note:** When using a separator, the values are joined as plain text (not HTML spans). For styled display, use the default rendering without a separator.

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Tags](./TAGS.md)
- [Conditionals](./CONDITIONALS.md)
