# Content Structure

Understanding JSON content structure and organization.

## Overview

Content is stored as JSON files:
- **Human-readable** - Easy to edit manually
- **Structured** - Organized by blueprint
- **Metadata** - Includes creation/update info
- **Version control friendly** - Text-based format

## JSON Structure

### Basic Structure

```json
{
  "field_name": "value",
  "another_field": "value",
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-01-01T00:00:00+00:00",
    "author": "username"
  }
}
```

### Field Values

Fields match blueprint definition:

```json
{
  "title": "My Page Title",
  "content": "Page content here...",
  "image": "/uploads/images/header.jpg",
  "tags": ["tag1", "tag2"]
}
```

## Page Structure

### File Location

```
content/pages/{page-name}/{page-name}.json
```

### Example

**File:** `content/pages/about/about.json`

```json
{
  "title": "About Us",
  "subtitle": "Our Story",
  "content": "We are a company dedicated to...",
  "image": "/uploads/images/about.jpg",
  "_featured": false,
  "_status": "published",
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-11-25T12:00:00+00:00",
    "author": "admin"
  }
}
```

## Collection Structure

### File Location

```
content/collections/{collection-name}/{slug}.json
```

### Example

**File:** `content/collections/posts/my-first-post.json`

```json
{
  "title": "My First Post",
  "content": "This is my first blog post...",
  "featured_image": "/uploads/images/post.jpg",
  "tags": ["blog", "first"],
  "_slug": "my-first-post",
  "_featured": true,
  "_status": "published",
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-11-25T12:00:00+00:00",
    "author": "admin"
  }
}
```

## Field Types

### Text Fields

**Type:** `text`

**JSON:**
```json
{
  "title": "My Title"
}
```

### Textarea Fields

**Type:** `textarea`

**JSON:**
```json
{
  "description": "Multi-line\ntext content"
}
```

### Markdown Fields

**Type:** `markdown`

**JSON:**
```json
{
  "content": "# Heading\n\nParagraph with **bold** text."
}
```

### File Fields

**Type:** `file`

**Single File:**
```json
{
  "image": "/uploads/images/header.jpg"
}
```

**Multiple Files:**
```json
{
  "gallery": [
    "/uploads/images/image1.jpg",
    "/uploads/images/image2.jpg"
  ]
}
```

### Select Fields

**Type:** `select`

**JSON:**
```json
{
  "category": "news"
}
```

### Radio Fields

**Type:** `radio`

**JSON:**
```json
{
  "layout": "full-width"
}
```

### Checkbox Fields

**Type:** `checkbox`

**JSON:**
```json
{
  "options": ["option1", "option2"]
}
```

### Tags Fields

**Type:** `tags`

**JSON:**
```json
{
  "tags": ["tag1", "tag2", "tag3"]
}
```

### Slider Fields

**Type:** `slider`

**JSON:**
```json
{
  "rating": 75
}
```

### Switch Fields

**Type:** `switch`

**JSON:**
```json
{
  "featured": true
}
```

### Structure Fields

**Type:** `structure`

**JSON:**
```json
{
  "items": [
    {
      "name": "Item 1",
      "value": "Value 1"
    },
    {
      "name": "Item 2",
      "value": "Value 2"
    }
  ]
}
```

## Core Fields

### Featured

**Field:** `_featured`

**Type:** Boolean

**JSON:**
```json
{
  "_featured": true
}
```

### Status

**Field:** `_status`

**Type:** String

**Options:** `draft`, `published`, `unlisted`

**JSON:**
```json
{
  "_status": "published"
}
```

### Slug (Collections Only)

**Field:** `_slug`

**Type:** String

**JSON:**
```json
{
  "_slug": "my-post-slug"
}
```

## Metadata

### _meta Object

Automatically added to all content:

```json
{
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-11-25T12:00:00+00:00",
    "updated_by": "admin",
    "author": "admin"
  }
}
```

### Fields

**created:**
- ISO 8601 timestamp
- Set on creation
- Never changes
- Format: `YYYY-MM-DDTHH:MM:SS+00:00`

**updated:**
- ISO 8601 timestamp
- Updated on each save
- Tracks last modification
- Format: `YYYY-MM-DDTHH:MM:SS+00:00`

**updated_by:**
- Username of user who last updated
- Updated on each save
- Tracks who made changes

**author:**
- Username of creator
- Set on creation
- Never changes
- Tracks content creator

### Accessing Metadata in Templates

**For Collections:**
```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <?php if (catalogue('created_at')): ?>
            <time><?= catalogue('created_at') ?></time>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
```

**Convenience Fields:**
- `created_at` - Extracted from `_meta.created`
- `updated_at` - Extracted from `_meta.updated`

These convenience fields are automatically available in collection item templates (single item pages).

## File Organization

### Pages

```
content/pages/
  about/
    about.json
  contact/
    contact.json
  home/
    home.json
```

### Collections

```
content/collections/
  posts/
    my-first-post.json
    another-post.json
  projects/
    project-1.json
    project-2.json
```

## Manual Editing

### Editing JSON Directly

**Possible:** Yes, JSON files can be edited manually

**Location:** `content/pages/` or `content/collections/`

**Format:** Pretty-printed JSON

**Caution:**
- Validate JSON syntax
- Regenerate HTML after changes
- Backup before editing

### JSON Validation

**Required:** Valid JSON syntax

**Tools:**
- JSON validators online
- Text editor with JSON support
- PHP `json_decode()` validation

## Best Practices

### Structure

- **Follow blueprint** - Match blueprint fields
- **Use consistent naming** - Clear field names
- **Organize logically** - Group related fields
- **Keep it simple** - Don't over-complicate

### Values

- **Use appropriate types** - Match field types
- **Validate data** - Ensure correct format
- **Escape special chars** - JSON-safe values
- **Keep it clean** - Remove unused fields

### Metadata

- **Don't edit manually** - Let CMS manage
- **Preserve timestamps** - Keep accurate dates
- **Track authors** - Maintain author info

## See Also

- [Content Storage](./CONTENT_STORAGE.md) - File storage details
- [Blueprints & Content](./BLUEPRINTS_CONTENT.md) - Blueprint relationship
- [Creating Content](./CREATING_CONTENT.md) - Creating content

