# Files & Images

Working with file fields and media in templates.

## Field Type

- `file` - File/media upload field

## Single File

### Basic Usage

Single file fields automatically render as pre-formatted image HTML:

```php
<?php if (catalogue('featured_image')): ?>
    <?= catalogue('featured_image') ?>
<?php endif; ?>
```

### With Default

```php
<?= catalogue('image', '<img src="/default-image.jpg" alt="Default">') ?>
```

## Multiple Files

Use `catalogueFiles()` to iterate through multiple files:

```php
<?php foreach (catalogueFiles('files') as $file): ?>
    <figure>
        <?= catalogue('image') ?>
    </figure>
<?php endforeach; ?>
```

## File Metadata

Inside a `catalogueFiles()` loop, access metadata:

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <figure class="gallery-item">
        <?= catalogue('image') ?>
        <?php if (catalogue('caption')): ?>
            <figcaption><?= catalogue('caption') ?></figcaption>
        <?php endif; ?>
        <?php if (catalogue('credit')): ?>
            <p class="credit">Credit: <?= catalogue('credit') ?></p>
        <?php endif; ?>
    </figure>
<?php endforeach; ?>
```

## Available Metadata Fields

| Field | Description |
|-------|-------------|
| `image` | Pre-rendered image HTML (with alt text and caption) |
| `alt_text` | Alt text for accessibility |
| `caption` | Image caption |
| `description` | Detailed description |
| `credit` | Photo credit/attribution |
| `tags` | Tags (formatted as HTML) |

## Examples

### Featured Image

```php
<article class="post">
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('featured_image')): ?>
        <div class="featured-image">
            <?= catalogue('featured_image') ?>
        </div>
    <?php endif; ?>
    <div class="content"><?= catalogue('content') ?></div>
</article>
```

### Image Gallery

```php
<div class="gallery">
    <?php foreach (catalogueFiles('images') as $file): ?>
        <figure class="gallery-item">
            <?= catalogue('image') ?>
            <?php if (catalogue('caption')): ?>
                <figcaption><?= catalogue('caption') ?></figcaption>
            <?php endif; ?>
        </figure>
    <?php endforeach; ?>
</div>
```

### File List with Metadata

```php
<div class="file-list">
    <?php foreach (catalogueFiles('documents') as $file): ?>
        <div class="file-item">
            <?= catalogue('image') ?>
            <div class="file-info">
                <?php if (catalogue('description')): ?>
                    <p><?= catalogue('description') ?></p>
                <?php endif; ?>
                <?php if (catalogue('credit')): ?>
                    <p class="credit"><?= catalogue('credit') ?></p>
                <?php endif; ?>
                <?php if (catalogue('tags')): ?>
                    <div class="file-tags"><?= catalogue('tags') ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

### Conditional Gallery

```php
<?php if (catalogueFiles('gallery')): ?>
    <div class="gallery">
        <?php foreach (catalogueFiles('gallery') as $file): ?>
            <?= catalogue('image') ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

## Image HTML Output

The `image` field (in `catalogueFiles()` loops) and single file fields return pre-rendered HTML:

```html
<img src="/uploads/image.jpg" alt="Alt text" width="800" height="600">
```

Includes:
- `src` - Image URL
- `alt` - Alt text from metadata
- `width` / `height` - Image dimensions
- `caption` - Rendered as `<figcaption>` if present

## Context

Inside a `catalogueFiles()` loop, `catalogue()` automatically uses the current file:

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('image') ?>  <!-- Gets image from current $file -->
    <?= catalogue('caption') ?>  <!-- Gets caption from current $file -->
<?php endforeach; ?>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Collections](./COLLECTIONS.md)
