# Text Fields

Displaying text and textarea fields in templates.

## Field Types

- `text` - Single-line text input
- `textarea` - Multi-line text input

## Basic Usage

### Text Field

```php
<h1><?= catalogue('title') ?></h1>
<p class="author"><?= catalogue('author_name') ?></p>
```

### Textarea Field

```php
<p class="description"><?= catalogue('description') ?></p>
```

## With Default Values

```php
<h1><?= catalogue('title', 'Untitled') ?></h1>
<p><?= catalogue('subtitle', 'No subtitle available') ?></p>
```

## Conditional Display

Only show if field has content:

```php
<?php if (catalogue('subtitle')): ?>
    <h2><?= catalogue('subtitle') ?></h2>
<?php endif; ?>
```

## Escaping

All text fields are automatically escaped for security. No need to use `htmlspecialchars()`.

## Examples

### Page Header

```php
<header>
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('subtitle')): ?>
        <p class="subtitle"><?= catalogue('subtitle') ?></p>
    <?php endif; ?>
</header>
```

### Article Meta

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <div class="meta">
        <?php if (catalogue('author')): ?>
            <span>By <?= catalogue('author') ?></span>
        <?php endif; ?>
        <?php if (catalogue('date')): ?>
            <span><?= catalogue('date') ?></span>
        <?php endif; ?>
    </div>
    <div class="content">
        <?= catalogue('description') ?>
    </div>
</article>
```

### Formatted Text

```php
<div class="post">
    <h1><?= catalogue('title') ?></h1>
    <div class="excerpt">
        <?= catalogue('excerpt', 'No excerpt available') ?>
    </div>
    <div class="body">
        <?= catalogue('body') ?>
    </div>
</div>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Conditionals](./CONDITIONALS.md)

