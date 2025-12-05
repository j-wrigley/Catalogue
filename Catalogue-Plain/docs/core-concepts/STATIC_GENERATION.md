# Static Site Generation

How the CMS generates static HTML files from templates and content.

## Overview

The CMS uses **static site generation (SSG)** to create HTML files from PHP templates and JSON content. This approach provides fast performance, SEO benefits, and easy deployment.

## What is Static Generation?

### Definition

**Static site generation** converts:
- PHP templates + JSON content → Static HTML files

**Process:**
1. Template reads JSON content
2. PHP executes template
3. HTML output generated
4. HTML file saved

### Traditional Dynamic Sites

```
Request → PHP Processing → Database Query → HTML Output
```

**Characteristics:**
- Processing on each request
- Database queries
- Server-side rendering
- Slower performance

### Static Generated Sites

```
Request → Static HTML File → Direct Response
```

**Characteristics:**
- Pre-generated HTML
- No processing needed
- Fast response time
- Easy to cache

## Generation Process

### Step-by-Step

1. **Content Saved**
   - User saves content in admin panel
   - JSON file written to filesystem

2. **Generation Triggered**
   - Save action triggers generation
   - Template loaded
   - Content loaded

3. **Template Execution**
   - PHP template executed
   - Content data injected
   - HTML output generated

4. **HTML File Saved**
   - HTML written to file
   - File saved to appropriate location
   - URL mapping created

### Generation Flow

```
Save Content → Load Blueprint → Load Template → Execute PHP → Generate HTML → Save File
```

## File Mapping

### Pages

**Blueprint:** `about.blueprint.yml`
**Template:** `templates/about.php`
**Content:** `content/pages/about/about.json`
**Output:** `about.html`

**URL:** `/about`

### Collections

**Blueprint:** `posts.blueprint.yml`
**Template:** `templates/posts.php`
**Content:** `content/collections/posts/my-post.json`
**Output:** `posts/my-post.html`

**URL:** `/posts/my-post`

### Special Cases

**Home Page:**
- Blueprint: `home.blueprint.yml`
- Template: `templates/home.php`
- Output: `index.html`
- URL: `/`

**404 Page:**
- Blueprint: `404.blueprint.yml`
- Template: `templates/404.php`
- Output: `404.html`
- URL: Handled by Apache

## Template System

### PHP Templates

**Format:** PHP files with `catalogue()` functions

**Example:**
```php
<h1><?= catalogue('title') ?></h1>
<div><?= catalogue('content') ?></div>
```

### Content Access

**Function:** `catalogue($field, $default, $context)`

**Usage:**
```php
<?= catalogue('title') ?>
<?= catalogue('content', 'No content') ?>
<?= catalogue('site_name', 'Site', 'site') ?>
```

### Iteration

**Collections:**
```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <h2><?= catalogue('title') ?></h2>
    <p><?= catalogue('content') ?></p>
<?php endforeach; ?>
```

## Generation Triggers

### Automatic Generation

**On Content Save:**
- Page saved → HTML regenerated
- Collection item saved → HTML regenerated
- Settings saved → All pages regenerated

### Manual Generation

**Regenerate All:**
- Via admin panel
- Regenerates all pages
- Updates all HTML files

## Benefits

### 1. Performance

**Advantages:**
- Fast page loads
- No server processing
- Direct file serving
- Easy caching

**Performance:**
- HTML files served directly
- No PHP execution needed
- No database queries
- Minimal server load

### 2. SEO

**Advantages:**
- Search engines can crawl
- Fast indexing
- Clean URLs
- Proper HTML structure

**SEO Benefits:**
- Static HTML easily indexed
- Fast page loads
- Clean URL structure
- Proper meta tags

### 3. Deployment

**Advantages:**
- Easy to deploy
- Can use CDN
- Simple hosting
- Version control friendly

**Deployment:**
- Copy HTML files
- No server configuration
- Works on any hosting
- Easy to backup

### 4. Security

**Advantages:**
- No server-side code execution
- Reduced attack surface
- Static files are safe
- No database vulnerabilities

## HTML Output

### File Structure

**Pages:**
```
about.html
contact.html
```

**Collections:**
```
posts/
  my-post.html
  another-post.html
```

### File Contents

**Generated HTML:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>My Page</title>
</head>
<body>
    <h1>My Page Title</h1>
    <div>Page content...</div>
</body>
</html>
```

## Regeneration

### When Regeneration Happens

**Automatic:**
- Content saved
- Content updated
- Settings changed

**Manual:**
- Via admin panel
- "Regenerate All" button
- After template changes

### Regeneration Process

**Steps:**
1. Load all blueprints
2. Load all content
3. Execute templates
4. Generate HTML files
5. Save to filesystem

## Best Practices

### Template Design

- **Keep templates simple** - Easy to understand
- **Use catalogue() functions** - Consistent access
- **Organize code** - Clear structure
- **Test templates** - Verify output

### Generation Strategy

- **Regenerate after changes** - Keep HTML current
- **Monitor generation** - Check for errors
- **Backup before regeneration** - Safety first
- **Test after regeneration** - Verify output

## Limitations

### Dynamic Content

**Challenges:**
- No real-time updates
- Requires regeneration
- Not suitable for dynamic data

**Solutions:**
- Use JavaScript for dynamic content
- Regenerate when needed
- Consider hybrid approach

### Scale

**Considerations:**
- Many files can be slow
- Regeneration time increases
- File system limits

**Solutions:**
- Organize content well
- Use efficient templates
- Consider caching strategies

## See Also

- [Site Generation Documentation](../site-generation/README.md) - Complete generation guide
- [Template System](./TEMPLATES.md) - Template details
- [How It Works](../site-generation/HOW_IT_WORKS.md) - Generation process

