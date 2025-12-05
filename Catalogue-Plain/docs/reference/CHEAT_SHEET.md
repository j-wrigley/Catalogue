# Cheat Sheet

Quick syntax reference for common CMS tasks.

## Template Functions

### Basic Content Access

```php
<?= catalogue('title') ?>
<?= catalogue('description', 'No description') ?>
<?= catalogue('site_name', 'Site', 'site') ?>
```

### Collections

```php
<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
    <p><?= catalogue('description') ?></p>
<?php endforeach; ?>
```

### Files

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('image') ?>
    <p><?= catalogue('caption') ?></p>
<?php endforeach; ?>
```

### Navigation

```php
<?php foreach (catalogueNav() as $page): ?>
    <?= navLink($page) ?>
<?php endforeach; ?>
```

### Snippets

```php
<?= snippet('header') ?>
<?= snippet('footer') ?>
<?= snippet('card', ['title' => 'Card', 'content' => 'Text']) ?>
```

## Blueprint Syntax

### Basic Blueprint

```yaml
title: Page Title

fields:
  title:
    type: text
    label: Title
    required: true
  content:
    type: markdown
    label: Content
```

### With Layout

```yaml
fields:
  title:
    type: text
    column: 1
    span: 1
  image:
    type: file
    column: 2
    span: 1
```

### With Tabs

```yaml
tabs:
  content:
    label: Content
  settings:
    label: Settings

fields:
  title:
    type: text
    category: content
  description:
    type: textarea
    category: content
```

### With Groups

```yaml
fields:
  title:
    type: text
    column: 1
    group: main-content
  description:
    type: textarea
    column: 1
    group: main-content
  image:
    type: file
    column: 2
    group: media-content
```

## Field Types

### Text Fields

```yaml
title:
  type: text
  label: Title
  placeholder: Enter title
  required: true
```

### Markdown

```yaml
content:
  type: markdown
  label: Content
  rows: 10
```

### File Upload

```yaml
image:
  type: file
  label: Image
  multiple: false
```

### Multiple Files

```yaml
gallery:
  type: file
  label: Gallery
  multiple: true
  max_files: 10
```

### Tags

```yaml
tags:
  type: tags
  label: Tags
  options:
    - Design
    - Development
```

### Select

```yaml
status:
  type: select
  label: Status
  options:
    - Published
    - Draft
    - Unlisted
```

### Switch

```yaml
featured:
  type: switch
  label: Featured
```

### Slider

```yaml
rating:
  type: slider
  label: Rating
  min: 0
  max: 10
  step: 1
```

### Structure

```yaml
items:
  type: structure
  label: Items
  fields:
    title:
      type: text
    value:
      type: text
```

## Common Patterns

### Conditional Display

```php
<?php if (catalogue('subtitle')): ?>
    <h2><?= catalogue('subtitle') ?></h2>
<?php endif; ?>
```

### Featured Items

```php
<?php foreach (catalogueCollection('posts', ['featured' => true]) as $post): ?>
    <?= catalogue('title') ?>
<?php endforeach; ?>
```

### Published Only

```php
<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <?= catalogue('title') ?>
<?php endforeach; ?>
```

### Empty State

```php
<?php 
$posts = catalogueCollection('posts');
$hasPosts = false;
foreach ($posts as $post) {
    $hasPosts = true;
    break;
}
?>

<?php if ($hasPosts): ?>
    <!-- Show posts -->
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>
```

## File Paths

### Content Files

```
/catalogue/content/pages/{type}/{type}.json
/catalogue/content/collections/{collection}/{slug}.json
/catalogue/content/users/{username}.json
```

### Templates

```
/catalogue/templates/{type}.php
/catalogue/templates/snippets/{name}.php
```

### Blueprints

```
/catalogue/blueprints/{type}.blueprint.yml
```

### Generated HTML

```
/{type}.html                    # Pages
/index.html                     # Home page
/404.html                       # Error page
/{collection}/{slug}.html       # Collection items
```

## Constants

### Path Constants

```php
CMS_ROOT          // CMS root directory
CONTENT_DIR       // Content directory
PAGES_DIR         // Pages directory
COLLECTIONS_DIR   // Collections directory
DATA_DIR          // Public data directory
UPLOADS_DIR       // Uploads directory
BLUEPRINTS_DIR    // Blueprints directory
TEMPLATES_DIR     // Templates directory
```

### URL Constants

```php
BASE_PATH         // Base path for URLs
CMS_URL           // CMS admin URL
ASSETS_URL        // Assets URL
```

## See Also

- [Quick Reference](./QUICK_REFERENCE.md) - Common tasks
- [API Reference](../api-reference/README.md) - Function reference
- [Templates](../templates/README.md) - Template guide

