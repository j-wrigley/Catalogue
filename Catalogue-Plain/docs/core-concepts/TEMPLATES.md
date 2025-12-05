# Template System

How PHP templates work and how to use them effectively.

## Overview

The CMS uses **PHP templates** to generate HTML files. Templates combine PHP code with HTML markup to create dynamic, content-driven pages.

## What are Templates?

### Definition

**Templates** are PHP files that:
- Define HTML structure
- Access content data
- Generate static HTML
- Create final output

### File Location

```
catalogue/templates/{name}.php
```

**Examples:**
- `templates/about.php`
- `templates/posts.php`
- `templates/home.php`

## Template Mapping

### 1:1 Blueprint Mapping

**Rule:** One blueprint = One template

**Pattern:**
```
{name}.blueprint.yml → templates/{name}.php
```

**Examples:**
- `about.blueprint.yml` → `templates/about.php`
- `posts.blueprint.yml` → `templates/posts.php`

### Special Cases

**Home Page:**
- Blueprint: `home.blueprint.yml`
- Template: `templates/home.php`
- Output: `index.html`

**404 Page:**
- Blueprint: `404.blueprint.yml`
- Template: `templates/404.php`
- Output: `404.html`

## Template Functions

### catalogue()

**Purpose:** Access content data

**Syntax:**
```php
<?= catalogue($field, $default, $context) ?>
```

**Parameters:**
- `$field` - Field name
- `$default` - Default value (optional)
- `$context` - Context ('site' for settings)

**Examples:**
```php
<?= catalogue('title') ?>
<?= catalogue('content', 'No content') ?>
<?= catalogue('site_name', 'Site', 'site') ?>
```

### catalogueCollection()

**Purpose:** Iterate through collection items

**Syntax:**
```php
<?php foreach (catalogueCollection($collection, $filter) as $item): ?>
    <!-- Template code -->
<?php endforeach; ?>
```

**Parameters:**
- `$collection` - Collection name
- `$filter` - Filter array (optional)

**Example:**
```php
<?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
    <h2><?= catalogue('title') ?></h2>
    <p><?= catalogue('content') ?></p>
<?php endforeach; ?>
```

### catalogueFiles()

**Purpose:** Iterate through file fields

**Syntax:**
```php
<?php foreach (catalogueFiles($field) as $file): ?>
    <!-- Template code -->
<?php endforeach; ?>
```

**Example:**
```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('image') ?>
<?php endforeach; ?>
```

### snippet()

**Purpose:** Include reusable template parts

**Syntax:**
```php
<?= snippet('header') ?>
<?= snippet('footer') ?>
```

**File Location:**
```
templates/snippets/{name}.php
```

**Example:**
```php
<!-- templates/snippets/header.php -->
<header>
    <h1><?= catalogue('site_name', 'Site', 'site') ?></h1>
</header>
```

## Template Structure

### Basic Template

```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</main>
<?= snippet('footer') ?>
```

### With Conditionals

```php
<?php if (catalogue('featured_image')): ?>
    <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
<?php endif; ?>
```

### With Loops

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <p><?= catalogue('content') ?></p>
    </article>
<?php endforeach; ?>
```

## Content Access

### Field Access

**Simple Fields:**
```php
<?= catalogue('title') ?>
<?= catalogue('description') ?>
```

### Nested Fields

**Dot Notation:**
```php
<?= catalogue('image.src') ?>
<?= catalogue('author.name') ?>
```

### Default Values

**Fallback:**
```php
<?= catalogue('title', 'Default Title') ?>
<?= catalogue('content', 'No content available') ?>
```

### Site Settings

**Context Parameter:**
```php
<?= catalogue('site_name', 'Site', 'site') ?>
<?= catalogue('site_description', '', 'site') ?>
```

## Field Rendering

### Text Fields

**Simple Output:**
```php
<h1><?= catalogue('title') ?></h1>
<p><?= catalogue('description') ?></p>
```

### Markdown Fields

**Pre-rendered:**
```php
<div><?= catalogue('content') ?></div>
```

**Note:** Markdown is converted to HTML automatically

### File Fields

**Single File:**
```php
<img src="<?= catalogue('image') ?>" alt="<?= catalogue('title') ?>">
```

**Multiple Files:**
```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <?= catalogue('image') ?>
<?php endforeach; ?>
```

### Tags Fields

**Formatted Output:**
```php
<div class="tags"><?= catalogue('tags') ?></div>
```

**Note:** Tags are rendered as HTML automatically

## Template Examples

### Simple Page Template

```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title') ?></h1>
    <div class="content">
        <?= catalogue('content') ?>
    </div>
</main>
<?= snippet('footer') ?>
```

### Collection Template

```php
<?= snippet('header') ?>
<main>
    <h1>Blog Posts</h1>
    <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
        <article>
            <h2><?= catalogue('title') ?></h2>
            <?php if (catalogue('featured_image')): ?>
                <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
            <?php endif; ?>
            <div><?= catalogue('content') ?></div>
            <?php if (catalogue('tags')): ?>
                <div class="tags"><?= catalogue('tags') ?></div>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</main>
<?= snippet('footer') ?>
```

### Complex Template

```php
<?= snippet('header') ?>
<main>
    <header>
        <h1><?= catalogue('title') ?></h1>
        <?php if (catalogue('subtitle')): ?>
            <p class="subtitle"><?= catalogue('subtitle') ?></p>
        <?php endif; ?>
    </header>
    
    <?php if (catalogue('featured_image')): ?>
        <figure>
            <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
            <?php if (catalogueMedia(catalogue('featured_image'))['caption']): ?>
                <figcaption><?= catalogueMedia(catalogue('featured_image'))['caption'] ?></figcaption>
            <?php endif; ?>
        </figure>
    <?php endif; ?>
    
    <div class="content">
        <?= catalogue('content') ?>
    </div>
    
    <?php if (catalogueFiles('gallery')): ?>
        <div class="gallery">
            <?php foreach (catalogueFiles('gallery') as $file): ?>
                <?= catalogue('image') ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?= snippet('footer') ?>
```

## Best Practices

### Template Design

- **Keep it simple** - Easy to understand
- **Use snippets** - Reusable components
- **Organize code** - Clear structure
- **Test templates** - Verify output

### Content Access

- **Use catalogue()** - Consistent access
- **Provide defaults** - Fallback values
- **Check existence** - Use conditionals
- **Escape output** - Already handled

### Performance

- **Efficient loops** - Don't over-iterate
- **Conditional rendering** - Only render when needed
- **Use snippets** - Reduce duplication
- **Test performance** - Monitor generation time

## See Also

- [Templates Documentation](../templates/README.md) - Complete template guide
- [Static Site Generation](./STATIC_GENERATION.md) - Generation process
- [API Reference](../api-reference/TEMPLATE_FUNCTIONS.md) - Function reference

