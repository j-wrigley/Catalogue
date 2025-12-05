# Template Mapping

How templates map to content types and generate HTML files.

## Mapping Rules

### 1:1 Blueprint-to-Template Mapping

Each blueprint has a corresponding template file:

- `about.blueprint.yml` → `about.php` → `about.html`
- `posts.blueprint.yml` → `posts.php` → `posts/{slug}.html`
- `home.blueprint.yml` → `home.php` → `index.html`

## Template File Naming

### Pages

Template file name matches blueprint name:

```
Blueprint: about.blueprint.yml
Template:  templates/about.php
Output:    about.html
```

### Collections

Template file name matches collection name:

```
Blueprint: posts.blueprint.yml
Template:  templates/posts.php
Output:    posts/{slug}.html (for each item)
```

### Special Cases

#### Home Page

```
Blueprint: home.blueprint.yml
Template:  templates/home.php
Output:    index.html (not home.html)
```

#### 404 Page

```
Blueprint: 404.blueprint.yml
Template:  templates/404.php
Output:    404.html
```

## Template Location

Templates are stored in:
```
/catalogue/templates/
  home.php
  about.php
  posts.php
  404.php
  ...
```

## Template Resolution

The generator looks for templates in this order:

1. **Specific template** - `templates/{contentType}.php`
2. **Default template** - `templates/default.php` (if exists)
3. **Failure** - Returns `null`, generation skipped

### Example

For content type `about`:
1. Check `templates/about.php` ✓
2. If not found, check `templates/default.php`
3. If not found, skip generation

## Template Requirements

### Required Elements

Templates must:
- Be valid PHP files
- Output HTML when executed
- Use `catalogue()` function to access content
- Handle missing content gracefully

### Optional Elements

Templates can:
- Use snippets (`snippet('header')`)
- Include conditional logic
- Loop through collections
- Access site settings

## Content-to-Template Binding

### Pages

Each page type has one template:

```
Content: pages/about/about.json
Template: templates/about.php
Output: about.html
```

### Collections

One template generates multiple HTML files:

```
Content: collections/posts/item-1.json
Template: templates/posts.php
Output: posts/item-1.html

Content: collections/posts/item-2.json
Template: templates/posts.php
Output: posts/item-2.html
```

## Template Execution

### Context Setup

Before template execution:

```php
// Set content
setCatalogueContent($content);

// Set blueprint
setCatalogueBlueprint($blueprint);

// Set site settings
setCatalogueSite($siteSettings);
```

### Template Code

Templates use `catalogue()` to access data:

```php
<h1><?= catalogue('title') ?></h1>
<div><?= catalogue('content') ?></div>
```

### Output Capture

Template output is captured:

```php
ob_start();
include $templateFile;
$html = ob_get_clean();
```

## Examples

### Simple Page Template

**Template:** `templates/about.php`
```php
<?= snippet('header') ?>
<h1><?= catalogue('title') ?></h1>
<p><?= catalogue('description') ?></p>
<?= snippet('footer') ?>
```

**Output:** `about.html`

### Collection Template

**Template:** `templates/posts.php`
```php
<?= snippet('header') ?>
<article>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</article>
<?= snippet('footer') ?>
```

**Output:** `posts/{slug}.html` (one file per item)

### Home Page Template

**Template:** `templates/home.php`
```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title') ?></h1>
</main>
<?= snippet('footer') ?>
```

**Output:** `index.html`

## Excluded Content Types

These content types are **not** generated:

- `settings` - Admin-only, not public
- `users` - User data, not public
- `media` - Media metadata, not public

## See Also

- [How It Works](./HOW_IT_WORKS.md) - Generation process
- [Templates Documentation](../templates/README.md) - Template usage

