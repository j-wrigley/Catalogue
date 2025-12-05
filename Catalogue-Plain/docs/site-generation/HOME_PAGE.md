# Home Page Generation

Special handling for the home page (index.html).

## Overview

The home page is special because:
- It generates `index.html` (not `home.html`)
- It's accessible at the root URL (`/`)
- It can have its own blueprint and content

## Blueprint

### Creating Home Blueprint

Create `home.blueprint.yml`:

```yaml
title: Home Page
fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
```

## Template

### Creating Home Template

Create `templates/home.php`:

```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title', 'Welcome') ?></h1>
    <div><?= catalogue('content') ?></div>
</main>
<?= snippet('footer') ?>
```

## Content

### Creating Home Content

Content file: `content/pages/home/home.json`

```json
{
  "title": "Welcome to My Site",
  "content": "This is the home page content."
}
```

## Generation

### Automatic Generation

Home page generates automatically when:
- Home content is saved
- "Regenerate All" is clicked
- Site settings are updated

### Manual Generation

```php
generateHomeHtml();
// Generates: index.html
```

## Output File

### Location

Home page generates to:
```
/index.html (root directory)
```

### URL

Accessible at:
```
/ (root URL)
```

## Special Behavior

### Default Content

If no home content exists:

```php
$homeContent = [
    'title' => 'Home',
    'content' => 'Welcome to the site'
];
```

### Blueprint Fallback

If no home blueprint exists:
- Uses empty blueprint
- Template still executes
- Uses default content

### Template Fallback

If no `home.php` template exists:
- Checks for `default.php`
- Falls back to error if neither exists

## Examples

### Simple Home Page

**Blueprint:** `home.blueprint.yml`
```yaml
title: Home Page
fields:
  title:
    type: text
  hero_text:
    type: textarea
```

**Template:** `templates/home.php`
```php
<?= snippet('header') ?>
<section class="hero">
    <h1><?= catalogue('title') ?></h1>
    <p><?= catalogue('hero_text') ?></p>
</section>
<?= snippet('footer') ?>
```

**Content:** `content/pages/home/home.json`
```json
{
  "title": "Welcome",
  "hero_text": "This is my awesome site"
}
```

**Output:** `index.html`

### Home Page with Collections

**Template:** `templates/home.php`
```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title') ?></h1>
    
    <section class="featured-posts">
        <h2>Featured Posts</h2>
        <?php foreach (catalogueCollection('posts', ['featured' => true]) as $post): ?>
            <article>
                <h3><?= catalogue('title') ?></h3>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?= snippet('footer') ?>
```

## Editing Home Page

### Through CMS

1. Navigate to **Pages**
2. Find **Home** page
3. Edit content
4. Save
5. `index.html` regenerates automatically

### File Location

Content file:
```
/catalogue/content/pages/home/home.json
```

## See Also

- [How It Works](./HOW_IT_WORKS.md) - Generation process
- [Template Mapping](./TEMPLATE_MAPPING.md) - Template rules

