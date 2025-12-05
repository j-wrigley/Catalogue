# File Metadata

Adding and managing metadata for media files.

## Overview

The metadata system allows you to add additional information to files:
- Alt text for accessibility
- Captions for images
- Descriptions
- Photo credits
- Tags for organization

## What is Metadata?

Metadata is additional data stored separately from the file itself:
- **Stored in JSON** - Separate from image files
- **Accessible in templates** - Via `catalogueMedia()` function
- **Editable** - Can be updated anytime
- **Optional** - Files work without metadata

## Metadata Fields

### Alt Text

- **Purpose** - Accessibility description
- **Required** - Recommended for images
- **Usage** - Screen readers, SEO
- **Example** - "A red sunset over the ocean"

### Caption

- **Purpose** - Display caption for images
- **Usage** - Shown below images
- **Example** - "Sunset photographed in Hawaii"

### Description

- **Purpose** - Detailed description
- **Usage** - Extended information
- **Example** - "This image was taken during a photography trip..."

### Credit

- **Purpose** - Photo credit/attribution
- **Usage** - Copyright information
- **Example** - "Photo by John Doe"

### Tags

- **Purpose** - Organization and filtering
- **Usage** - Categorize files
- **Example** - ["nature", "sunset", "hawaii"]

## Adding Metadata

### Steps

1. Right-click file in media library
2. Select **"Edit Metadata"**
3. Fill in form fields
4. Click **"Save"**
5. Metadata saved immediately

### From Media Page

1. Navigate to Media
2. Find file
3. Right-click → "Edit Metadata"
4. Fill form
5. Save

### From Media Picker

1. Open media picker
2. Right-click file
3. Select "Edit Metadata"
4. Fill form
5. Save

## Metadata Storage

### File Location

Metadata stored in:
```
/catalogue/content/media-metadata/{hash}.json
```

### File Structure

```json
{
  "file_path": "images/header.jpg",
  "alt_text": "Site header image",
  "caption": "Main site header",
  "description": "Header image for the website",
  "credit": "Photo by John Doe",
  "tags": ["header", "design"]
}
```

### Hash-Based Naming

Metadata files named by MD5 hash of file path:
- Ensures unique filenames
- Prevents conflicts
- Easy to lookup

## Editing Metadata

### Updating Existing Metadata

1. Right-click file
2. Select "Edit Metadata"
3. Modify fields
4. Save
5. Changes applied immediately

### Removing Metadata

1. Edit metadata
2. Clear fields
3. Save
4. Metadata removed (file still exists)

## Using Metadata in Templates

### Accessing Metadata

```php
<?php
$metadata = catalogueMedia('/uploads/images/header.jpg');
echo $metadata['alt_text'];
echo $metadata['caption'];
?>
```

### With catalogueFiles()

When iterating files, metadata is automatically available:

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('image') ?>
    <?php if (catalogue('caption')): ?>
        <figcaption><?= catalogue('caption') ?></figcaption>
    <?php endif; ?>
<?php endforeach; ?>
```

### Available Fields

Inside `catalogueFiles()` loop:
- `catalogue('alt_text')` - Alt text
- `catalogue('caption')` - Caption
- `catalogue('description')` - Description
- `catalogue('credit')` - Photo credit
- `catalogue('tags')` - Tags (formatted HTML)

## Metadata Blueprint

### Default Blueprint

The `media.blueprint.yml` defines metadata fields:

```yaml
title: Media Metadata
fields:
  alt_text:
    type: text
    label: Alt Text
  caption:
    type: textarea
    label: Caption
  description:
    type: textarea
    label: Description
  credit:
    type: text
    label: Photo Credit
  tags:
    type: tags
    label: Tags
```

### Customizing Fields

Edit `media.blueprint.yml` to add/remove fields:
- Add new field types
- Change labels
- Add validation
- Organize with tabs

## Best Practices

### Alt Text

- **Be descriptive** - Describe the image
- **Be concise** - Keep it brief
- **Include context** - What's important?
- **Avoid redundancy** - Don't repeat visible text

### Captions

- **Complement image** - Add context
- **Be informative** - Provide value
- **Keep it short** - One or two sentences
- **Match tone** - Match site style

### Tags

- **Use consistently** - Same tags for similar content
- **Be specific** - Use descriptive tags
- **Don't over-tag** - 3-5 tags usually enough
- **Use lowercase** - Consistent formatting

## Examples

### Image with Full Metadata

**File:** `header.jpg`

**Metadata:**
```json
{
  "alt_text": "Website header showing company logo",
  "caption": "Main site header with navigation",
  "description": "Header image designed for the homepage",
  "credit": "Design by Jane Smith",
  "tags": ["header", "design", "logo"]
}
```

### Template Usage

```php
<figure>
    <img src="/uploads/header.jpg" alt="<?= catalogueMedia('/uploads/header.jpg')['alt_text'] ?>">
    <figcaption><?= catalogueMedia('/uploads/header.jpg')['caption'] ?></figcaption>
</figure>
```

### With catalogueFiles()

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

## See Also

- [Using Media](./USING_MEDIA.md) - Using metadata in templates
- [File Operations](./FILE_OPERATIONS.md) - File management
- [Templates - Files](../templates/FILES.md) - Template usage

