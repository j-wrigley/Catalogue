# Navigation

Building navigation menus in templates using the clean `catalogue()` pattern.

## Functions

| Function | Purpose |
|----------|---------|
| `catalogueNav()` | Get navigation data (returns iterator) |
| `navLink()` | Generate link HTML (works with context) |
| `catalogue()` | Access navigation fields (title, url, description, etc.) |

## Basic Usage

### Simple Navigation Loop

```php
<ul>
    <?php foreach (catalogueNav() as $page): ?>
        <li><?= navLink() ?></li>
    <?php endforeach; ?>
</ul>
```

**What happens:**
- `catalogueNav()` returns an iterator that sets navigation context
- Inside the loop, `navLink()` automatically uses the current page
- Link text uses `description` if available, otherwise `title`

### Using `catalogue()` Directly

Access any blueprint field using `catalogue()`:

```php
<ul>
    <?php foreach (catalogueNav() as $page): ?>
        <li>
            <a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a>
        </li>
    <?php endforeach; ?>
</ul>
```

### Custom Field Selection

```php
<ul>
    <?php foreach (catalogueNav() as $page): ?>
        <li>
            <a href="<?= catalogue('url') ?>">
                <?= catalogue('description') ?: catalogue('title') ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
```

## Filtering

### Filter by Status

```php
<ul>
    <?php foreach (catalogueNav(['status' => 'published']) as $page): ?>
        <li><?= navLink() ?></li>
    <?php endforeach; ?>
</ul>
```

### Filter by Featured

```php
<ul>
    <?php foreach (catalogueNav(['featured' => true]) as $page): ?>
        <li><?= navLink() ?></li>
    <?php endforeach; ?>
</ul>
```

### Multiple Filters

```php
<ul>
    <?php foreach (catalogueNav(['featured' => true, 'status' => 'published']) as $page): ?>
        <li><?= navLink() ?></li>
    <?php endforeach; ?>
</ul>
```

## Single Page Access

### Get Single Page Data

```php
<?php $page = catalogueNav('about'); ?>
<!-- Returns page array when not in loop -->
```

### Get Single Field

```php
<?php $title = catalogueNav('about', 'title'); ?>
<!-- Returns: "About Us" -->
```

**Note:** Single page access doesn't set context, so `catalogue()` won't work. Use array access instead:

```php
<?php $page = catalogueNav('about'); ?>
<a href="<?= $page['url'] ?>"><?= $page['title'] ?></a>
```

## Helper Functions

### `navLink()`

Generate link HTML. Works automatically in navigation loops.

**In Navigation Loop (Recommended):**
```php
<?php foreach (catalogueNav() as $page): ?>
    <li><?= navLink() ?></li>
<?php endforeach; ?>
```

**With Custom Text:**
```php
<?php foreach (catalogueNav() as $page): ?>
    <li><?= navLink(null, 'Custom Text') ?></li>
<?php endforeach; ?>
```

**With Attributes:**
```php
<?php foreach (catalogueNav() as $page): ?>
    <li><?= navLink(null, null, ['class' => 'nav-link']) ?></li>
<?php endforeach; ?>
```

**Outside Loop (Legacy):**
```php
<?php $page = catalogueNav('about'); ?>
<?= navLink($page) ?>
```

### `catalogue()` in Navigation Context

When inside a `catalogueNav()` loop, `catalogue()` automatically accesses navigation fields:

```php
<?php foreach (catalogueNav() as $page): ?>
    <li>
        <a href="<?= catalogue('url') ?>">
            <?= catalogue('title') ?>
        </a>
        <span><?= catalogue('description') ?></span>
    </li>
<?php endforeach; ?>
```

**Available Fields:**
- `catalogue('url')` - Page URL
- `catalogue('title')` - Page title
- `catalogue('description')` - Page description
- `catalogue('slug')` - Page slug
- `catalogue('status')` - Page status
- `catalogue('featured')` - Featured status
- Any other blueprint field

## Examples

### Featured Navigation

```php
<nav>
    <ul>
        <?php foreach (catalogueNav(['featured' => true, 'status' => 'published']) as $page): ?>
            <li><?= navLink() ?></li>
        <?php endforeach; ?>
    </ul>
</nav>
```

### Navigation with Active State

```php
<?php
$current_slug = basename($_SERVER['REQUEST_URI'], '.html');
?>
<nav>
    <ul>
        <?php foreach (catalogueNav(['status' => 'published']) as $page): ?>
            <li>
                <?php
                $isActive = (catalogue('slug') === $current_slug);
                $class = $isActive ? 'active' : '';
                echo navLink(null, null, ['class' => $class]);
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
```

### Custom Navigation with Field Selection

```php
<nav>
    <ul>
        <?php foreach (catalogueNav(['status' => 'published']) as $page): ?>
            <li>
                <a href="<?= catalogue('url') ?>" 
                   title="<?= catalogue('description') ?>">
                    <?= catalogue('title') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
```

### Ordered Navigation

```php
<?php
$nav_order = ['home', 'about', 'information'];
?>
<nav>
    <ul>
        <?php foreach ($nav_order as $slug): ?>
            <?php $page = catalogueNav($slug); ?>
            <?php if ($page && $page['status'] === 'published'): ?>
                <li>
                    <a href="<?= $page['url'] ?>"><?= $page['title'] ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
```

### Footer Navigation

```php
<footer>
    <nav>
        <?php foreach (catalogueNav(['status' => 'published']) as $page): ?>
            <a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a>
            <?php if (!$loop->last): ?> | <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</footer>
```

## Page Data Structure

Pages returned by `catalogueNav()` include:

```php
[
    'slug' => 'about',
    'title' => 'About Us',
    'url' => '/about.html',
    'status' => 'published',
    'featured' => true,
    'description' => 'Learn about us',
    // ... all other blueprint fields
]
```

## Context Awareness

`catalogueNav()` returns an iterator that sets context automatically. Inside the loop:

- ✅ `catalogue('title')` works
- ✅ `catalogue('url')` works  
- ✅ `navLink()` works without parameters
- ✅ All blueprint fields accessible via `catalogue()`

Outside the loop:

- ❌ `catalogue()` doesn't work (no context)
- ✅ Use array access: `$page['title']`
- ✅ Use `navLink($page)` with page data

## Best Practices

### ✅ Recommended

```php
<!-- Clean and simple -->
<?php foreach (catalogueNav(['status' => 'published']) as $page): ?>
    <li><?= navLink() ?></li>
<?php endforeach; ?>
```

```php
<!-- Using catalogue() for flexibility -->
<?php foreach (catalogueNav(['featured' => true]) as $page): ?>
    <li>
        <a href="<?= catalogue('url') ?>"><?= catalogue('title') ?></a>
    </li>
<?php endforeach; ?>
```

### ❌ Avoid

```php
<!-- Don't manually access arrays in loops -->
<?php foreach (catalogueNav() as $page): ?>
    <li><a href="<?= $page['url'] ?>"><?= $page['title'] ?></a></li>
<?php endforeach; ?>
```

Use `catalogue()` instead for consistency with the rest of your templates.

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md) - Core templating function
- [Conditionals](./CONDITIONALS.md) - Conditional rendering
- [Collections](./COLLECTIONS.md) - Similar pattern for collections
