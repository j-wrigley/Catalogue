# Using Media in Templates

How to display media files and their metadata in templates.

## Overview

Media files can be used in templates through:
- Direct file URLs
- `catalogueFiles()` iteration
- `catalogueMedia()` function
- Automatic metadata access

## Basic File Display

### Single File

```php
<img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
```

### File URL

File fields return URLs:
```php
<?= catalogue('image') ?>
// Output: /catalogue/uploads/images/header.jpg
```

## Iterating Through Files

### Using catalogueFiles()

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <figure>
        <?= catalogue('image') ?>
    </figure>
<?php endforeach; ?>
```

### Available Fields

Inside `catalogueFiles()` loop:
- `catalogue('image')` - Pre-rendered image HTML
- `catalogue('alt_text')` - Alt text
- `catalogue('caption')` - Caption
- `catalogue('description')` - Description
- `catalogue('credit')` - Photo credit
- `catalogue('tags')` - Tags (formatted HTML)

## Image Display

### Pre-rendered Image

The `image` field returns complete HTML:

```php
<?= catalogue('image') ?>
```

**Output:**
```html
<img src="/uploads/image.jpg" alt="Alt text" width="800" height="600">
```

Includes:
- `src` - Image URL
- `alt` - Alt text from metadata
- `width` / `height` - Image dimensions
- `caption` - Rendered as `<figcaption>` if present

### Custom Image Tag

```php
<img src="<?= catalogue('image') ?>" 
     alt="<?= catalogue('alt_text', 'Image') ?>"
     class="custom-class">
```

## File Metadata

### Accessing Metadata

```php
<?php
$metadata = catalogueMedia('/uploads/images/header.jpg');
echo $metadata['alt_text'];
echo $metadata['caption'];
?>
```

### With catalogueFiles()

Metadata automatically available:

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <figure>
        <?= catalogue('image') ?>
        <?php if (catalogue('caption')): ?>
            <figcaption><?= catalogue('caption') ?></figcaption>
        <?php endif; ?>
        <?php if (catalogue('credit')): ?>
            <p class="credit"><?= catalogue('credit') ?></p>
        <?php endif; ?>
    </figure>
<?php endforeach; ?>
```

## Examples

### Image Gallery

```php
<div class="gallery">
    <?php foreach (catalogueFiles('gallery') as $file): ?>
        <figure class="gallery-item">
            <?= catalogue('image') ?>
            <?php if (catalogue('caption')): ?>
                <figcaption><?= catalogue('caption') ?></figcaption>
            <?php endif; ?>
        </figure>
    <?php endforeach; ?>
</div>
```

### Featured Image with Metadata

```php
<?php if (catalogue('featured_image')): ?>
    <figure class="featured-image">
        <img src="<?= catalogue('featured_image') ?>" 
             alt="<?= catalogue('alt_text', catalogue('title')) ?>">
        <?php 
        $metadata = catalogueMedia(catalogue('featured_image'));
        if ($metadata && isset($metadata['caption'])): 
        ?>
            <figcaption><?= esc($metadata['caption']) ?></figcaption>
        <?php endif; ?>
    </figure>
<?php endif; ?>
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

### Conditional Display

```php
<?php 
$gallery = catalogueFiles('gallery');
if (iterator_count($gallery) > 0):
?>
    <div class="gallery">
        <?php foreach ($gallery as $file): ?>
            <?= catalogue('image') ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No images available.</p>
<?php endif; ?>
```

## File Paths

### Relative Paths

Files stored with relative paths:
```
uploads/images/header.jpg
```

### Full URLs

Access via:
```php
<?= catalogue('image') ?>
// Returns: /catalogue/uploads/images/header.jpg
```

### Base Path

URLs include base path automatically:
- Root installation: `/uploads/image.jpg`
- Subfolder: `/subfolder/uploads/image.jpg`

## Metadata Access Patterns

### Direct Access

```php
$metadata = catalogueMedia('/uploads/images/header.jpg');
echo $metadata['alt_text'] ?? 'Default alt text';
```

### In Loop

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('alt_text') ?>
    <?= catalogue('caption') ?>
<?php endforeach; ?>
```

### Conditional Metadata

```php
<?php if (catalogue('caption')): ?>
    <figcaption><?= catalogue('caption') ?></figcaption>
<?php endif; ?>
```

## Best Practices

### Always Use Alt Text

```php
<img src="<?= catalogue('image') ?>" 
     alt="<?= catalogue('alt_text', 'Image description') ?>">
```

### Use Pre-rendered Images

```php
<?= catalogue('image') ?>
// Automatically includes alt text, dimensions, caption
```

### Check for Files

```php
<?php if (catalogue('featured_image')): ?>
    <?= catalogue('image') ?>
<?php endif; ?>
```

### Organize with Metadata

- Add metadata to all images
- Use consistent tags
- Include captions for context
- Add credits when needed

## See Also

- [Templates - Files](../templates/FILES.md) - Template file usage
- [File Metadata](./METADATA.md) - Metadata system
- [Media Picker](./MEDIA_PICKER.md) - Selecting files

