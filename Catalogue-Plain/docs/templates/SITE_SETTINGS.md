# Site Settings

Accessing site configuration in templates.

## Function

```php
catalogue($key, $default, 'site')
```

## Basic Usage

```php
<h1><?= catalogue('site_name', 'My Site', 'site') ?></h1>
<p><?= catalogue('site_description', 'Site description', 'site') ?></p>
```

## Available Settings

Common site settings (from `settings` blueprint):

| Field | Description |
|-------|-------------|
| `site_name` | Site name |
| `site_description` | Site description |
| `site_tagline` | Site tagline |
| `meta_title` | SEO meta title |
| `meta_description` | SEO meta description |
| `meta_keywords` | SEO keywords (tags) |

## Examples

### Page Title

```php
<title><?= catalogue('title') ?> - <?= catalogue('site_name', 'Site', 'site') ?></title>
```

### Site Header

```php
<header>
    <h1><?= catalogue('site_name', 'My Site', 'site') ?></h1>
    <?php if (catalogue('site_tagline', '', 'site')): ?>
        <p class="tagline"><?= catalogue('site_tagline', '', 'site') ?></p>
    <?php endif; ?>
</header>
```

### Meta Tags

```php
<head>
    <meta name="description" content="<?= catalogue('meta_description', '', 'site') ?>">
    <meta name="keywords" content="<?= catalogue('meta_keywords', '', 'site') ?>">
</head>
```

### Footer

```php
<footer>
    <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'My Site', 'site') ?></p>
    <?php if (catalogue('site_description', '', 'site')): ?>
        <p><?= catalogue('site_description', '', 'site') ?></p>
    <?php endif; ?>
</footer>
```

### Complete Page Template

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= catalogue('title') ?> - <?= catalogue('site_name', 'Site', 'site') ?></title>
    <meta name="description" content="<?= catalogue('meta_description', catalogue('description'), 'site') ?>">
</head>
<body>
    <header>
        <h1><?= catalogue('site_name', 'My Site', 'site') ?></h1>
    </header>
    
    <main>
        <h1><?= catalogue('title') ?></h1>
        <div><?= catalogue('content') ?></div>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'Site', 'site') ?></p>
    </footer>
</body>
</html>
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Snippets](./SNIPPETS.md)

