# URL Structure

Understanding URL patterns and routing in the generated site.

## URL Patterns

### Pages

Pages generate URLs without `.html` extension:

```
Content: about.blueprint.yml
Output:  about.html
URL:     /about
```

### Collections

Collection items use slug-based URLs:

```
Content: collections/posts/my-post.json
Slug:    my-post
Output:  posts/my-post.html
URL:     /posts/my-post
```

### Home Page

Home page uses root URL:

```
Content: home.blueprint.yml
Output:  index.html
URL:     /
```

### 404 Page

404 page uses special URL:

```
Content: 404.blueprint.yml
Output:  404.html
URL:     /404 (or any non-existent URL)
```

## URL Rewriting

### Apache Configuration

The `.htaccess` file rewrites URLs:

```apache
# Remove .html extension
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ $1.html [L]
```

### How It Works

1. User requests `/about`
2. Apache checks if `/about` exists (file or directory)
3. If not found, rewrites to `/about.html`
4. Serves `about.html` file

## URL Examples

### Pages

| Content Type | Output File | URL |
|--------------|-------------|-----|
| `about` | `about.html` | `/about` |
| `contact` | `contact.html` | `/contact` |
| `information` | `information.html` | `/information` |

### Collections

| Collection | Item Slug | Output File | URL |
|------------|-----------|-------------|-----|
| `posts` | `my-first-post` | `posts/my-first-post.html` | `/posts/my-first-post` |
| `posts` | `another-post` | `posts/another-post.html` | `/posts/another-post` |
| `projects` | `project-1` | `projects/project-1.html` | `/projects/project-1` |

### Special Pages

| Page | Output File | URL |
|------|-------------|-----|
| `home` | `index.html` | `/` |
| `404` | `404.html` | `/404` or any non-existent URL |

## Slug-Based URLs

### Collection Items

Collection items use slugs for URLs:

```php
// Content file: collections/posts/my-awesome-post.json
{
  "_slug": "my-awesome-post",
  "title": "My Awesome Post"
}

// Generated URL: /posts/my-awesome-post
// Output file: posts/my-awesome-post.html
```

### Slug Generation

Slugs are generated from titles:

```php
// Title: "My Awesome Post"
// Slug: "my-awesome-post"
```

### Slug Editing

Slugs can be edited in the admin panel:
- Edit collection item
- Modify slug in banner
- Save
- HTML file renamed automatically

## Base Path

### Root Installation

If CMS is in root directory:

```
URL: http://example.com/about
File: /about.html
```

### Subfolder Installation

If CMS is in subfolder:

```
URL: http://example.com/subfolder/about
File: /subfolder/about.html
```

### BASE_PATH Constant

The `BASE_PATH` constant handles subfolder installations:

```php
define('BASE_PATH', '/subfolder'); // or '/' for root
```

## URL Generation in Templates

### Using `catalogueNav()`

Generate navigation URLs:

```php
<?php foreach (catalogueNav() as $page): ?>
    <a href="<?= navLink($page, 'url') ?>">
        <?= navLink($page, 'text') ?>
    </a>
<?php endforeach; ?>
```

### Collection URLs

Generate collection item URLs:

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <a href="<?= catalogue('url') ?>">
        <?= catalogue('title') ?>
    </a>
<?php endforeach; ?>
```

## URL Validation

### Valid Characters

URLs use URL-safe characters:
- Letters (a-z, A-Z)
- Numbers (0-9)
- Hyphens (-)
- Underscores (_)

### Invalid Characters

These are removed or replaced:
- Spaces → Hyphens
- Special characters → Removed
- Multiple hyphens → Single hyphen

## See Also

- [Template Mapping](./TEMPLATE_MAPPING.md) - Template rules
- [Collections](./COLLECTIONS.md) - Collection URLs

