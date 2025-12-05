# Quick Reference

Common tasks and code snippets for quick lookup.

## Creating Content

### Create a Page

1. Create blueprint: `blueprints/about.blueprint.yml`
2. Create template: `templates/about.php`
3. Create content: `content/pages/about/about.json`
4. HTML generates automatically: `about.html`

### Create a Collection

1. Create blueprint: `blueprints/posts.blueprint.yml`
2. Create template: `templates/posts.php`
3. Create folder: `content/collections/posts/`
4. Add items: `content/collections/posts/item-1.json`
5. HTML generates: `posts/item-1.html`

## Template Patterns

### Basic Page Template

```php
<?= snippet('header') ?>
<h1><?= catalogue('title') ?></h1>
<div><?= catalogue('content') ?></div>
<?= snippet('footer') ?>
```

### Collection Item Template

```php
<?= snippet('header') ?>
<article>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</article>
<?= snippet('footer') ?>
```

### Home Page Template

```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title', 'Welcome') ?></h1>
    <div><?= catalogue('content') ?></div>
</main>
<?= snippet('footer') ?>
```

## Blueprint Patterns

### Simple Page

```yaml
title: About Page

fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
```

### Blog Post

```yaml
title: Blog Post

fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
  tags:
    type: tags
    label: Tags
  featured-image:
    type: file
    label: Featured Image
```

### Two-Column Layout

```yaml
title: Page

fields:
  title:
    type: text
    column: 1
    span: 1
  content:
    type: markdown
    column: 1
    span: 1
  image:
    type: file
    column: 2
    span: 1
```

## Common Tasks

### Display Site Name

```php
<?= catalogue('site_name', 'My Site', 'site') ?>
```

### Display Navigation

```php
<nav>
    <?php foreach (catalogueNav() as $page): ?>
        <?= navLink($page) ?>
    <?php endforeach; ?>
</nav>
```

### Display Featured Posts

```php
<?php foreach (catalogueCollection('posts', ['featured' => true]) as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
    </article>
<?php endforeach; ?>
```

### Display Image Gallery

```php
<?php foreach (catalogueFiles('gallery') as $file): ?>
    <figure>
        <?= catalogue('image') ?>
        <?php if (catalogue('caption')): ?>
            <figcaption><?= catalogue('caption') ?></figcaption>
        <?php endif; ?>
    </figure>
<?php endforeach; ?>
```

### Display Tags

```php
<?php if (catalogue('tags')): ?>
    <div class="tags"><?= catalogue('tags') ?></div>
<?php endif; ?>
```

## File Operations

### Read JSON

```php
$content = readJson('/path/to/file.json');
```

### Write JSON

```php
writeJson('/path/to/file.json', $data);
```

### List JSON Files

```php
$files = listJsonFiles('/path/to/directory');
```

## Security

### Escape Output

```php
<?= esc($user_input) ?>
<div class="<?= esc_attr($class) ?>">
<a href="<?= esc_url($url) ?>">
```

### CSRF Token

```php
<input type="hidden" name="csrf_token" value="<?= esc_attr(generateCsrfToken()) ?>">
```

## URL Patterns

### Page URLs

```
/about          → about.html
/contact        → contact.html
/               → index.html
```

### Collection URLs

```
/posts/my-post          → posts/my-post.html
/projects/project-1     → projects/project-1.html
```

## See Also

- [Cheat Sheet](./CHEAT_SHEET.md) - Syntax reference
- [Templates](../templates/README.md) - Template guide
- [Blueprints](../blueprints/README.md) - Blueprint guide

