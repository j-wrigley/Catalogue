# Snippets

Reusable template components.

## Function

```php
snippet($name, $vars = [])
```

## Basic Usage

```php
<?= snippet('header') ?>
<!-- Your content -->
<?= snippet('footer') ?>
```

## Snippet Location

Snippets are stored in `/catalogue/templates/snippets/`:

```
/catalogue/templates/snippets/
  header.php
  footer.php
  sidebar.php
```

## Creating Snippets

Create a PHP file in `/catalogue/templates/snippets/`:

**`header.php`:**
```php
<!DOCTYPE html>
<html>
<head>
    <title><?= catalogue('site_name', 'My Site', 'site') ?></title>
</head>
<body>
```

**`footer.php`:**
```php
<footer>
    <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'My Site', 'site') ?></p>
</footer>
</body>
</html>
```

## Using Snippets

```php
<?= snippet('header') ?>

<main>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</main>

<?= snippet('footer') ?>
```

## Passing Variables

Pass variables to snippets:

```php
<?= snippet('card', ['title' => 'Card Title', 'content' => 'Card content']) ?>
```

**In snippet (`card.php`):**
```php
<div class="card">
    <h3><?= $vars['title'] ?? 'Default Title' ?></h3>
    <p><?= $vars['content'] ?? '' ?></p>
</div>
```

## Examples

### Header Snippet

**`snippets/header.php`:**
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= catalogue('title', 'Page') ?> - <?= catalogue('site_name', 'Site', 'site') ?></title>
    <meta name="description" content="<?= catalogue('meta_description') ?>">
    <meta name="keywords" content="<?= catalogue('meta_keywords', '', 'content', ', ') ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <?php
            $nav_pages = ['home', 'about', 'information'];
            foreach ($nav_pages as $page_slug):
                $page = catalogueNav($page_slug);
                if ($page && isset($page['status']) && $page['status'] === 'published'):
                    echo navLink($page);
                endif;
            endforeach;
            ?>
        </nav>
    </header>
```

### Footer Snippet

**`snippets/footer.php`:**
```php
    <footer>
        <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'My Site', 'site') ?></p>
    </footer>
</body>
</html>
```

### Template Using Snippets

```php
<?= snippet('header') ?>

<main>
    <article>
        <h1><?= catalogue('title') ?></h1>
        <div class="content"><?= catalogue('content') ?></div>
    </article>
</main>

<?= snippet('footer') ?>
```

## Common Snippets

### Navigation

**`snippets/nav.php`:**
```php
<nav>
    <ul>
        <?php foreach (catalogueNav() as $page): ?>
            <?php if (isset($page['status']) && $page['status'] === 'published'): ?>
                <li><?= navLink($page) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
```

**Usage:**
```php
<?= snippet('nav') ?>
```

### Card Component

**`snippets/card.php`:**
```php
<div class="card">
    <?php if (isset($vars['title'])): ?>
        <h3><?= htmlspecialchars($vars['title']) ?></h3>
    <?php endif; ?>
    <?php if (isset($vars['content'])): ?>
        <div><?= $vars['content'] ?></div>
    <?php endif; ?>
</div>
```

**Usage:**
```php
<?= snippet('card', ['title' => 'Card Title', 'content' => 'Card content']) ?>
```

## Best Practices

1. **Keep snippets focused** - One component per snippet
2. **Use variables** - Pass data via `$vars` parameter
3. **Reuse common elements** - Headers, footers, navigation
4. **Keep snippets simple** - Avoid complex logic

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Navigation](./NAVIGATION.md)

