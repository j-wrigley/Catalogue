# Content Types

Understanding pages and collections - the two fundamental content types.

## Overview

The CMS supports two content types:
- **Pages** - Single content items
- **Collections** - Multiple content items

Understanding the difference is fundamental to using the CMS effectively.

## Pages

### Definition

**Pages** are single, unique content items:
- One JSON file per page type
- Each page is standalone
- Examples: About, Contact, Home, Services

### Characteristics

- **Single instance** - One file per page type
- **Unique content** - Each page has its own content
- **Direct access** - Accessed via URL directly
- **Simple structure** - One blueprint, one file

### File Structure

```
content/pages/
  {page-name}/
    {page-name}.json
```

**Example:**
```
content/pages/
  about/
    about.json
  contact/
    contact.json
```

### URL Structure

**Pattern:** `/{page-name}`

**Examples:**
- `/about` → `about.html`
- `/contact` → `contact.html`
- `/` → `index.html` (home page)

### Blueprint Relationship

**One blueprint = One page:**

```
about.blueprint.yml → content/pages/about/about.json
```

### Use Cases

**Ideal for:**
- Static pages (About, Contact)
- Single-instance content
- Simple content structure
- Direct URL access

**Examples:**
- About page
- Contact page
- Privacy policy
- Terms of service
- Home page

## Collections

### Definition

**Collections** are groups of similar content items:
- Multiple JSON files per collection type
- Each item is unique
- Examples: Blog posts, Projects, Products, Team members

### Characteristics

- **Multiple instances** - Many files per collection type
- **Individual items** - Each item has its own content
- **Slug-based URLs** - Accessed via slug
- **List pages** - Can have archive/list pages

### File Structure

```
content/collections/
  {collection-name}/
    {slug}.json
```

**Example:**
```
content/collections/
  posts/
    my-first-post.json
    another-post.json
  projects/
    project-1.json
    project-2.json
```

### URL Structure

**Pattern:** `/{collection-name}/{slug}`

**Examples:**
- `/posts/my-post` → `posts/my-post.html`
- `/projects/project-1` → `projects/project-1.html`

### Blueprint Relationship

**One blueprint = Many items:**

```
posts.blueprint.yml → content/collections/posts/*.json
```

### Use Cases

**Ideal for:**
- Dynamic content (Blog posts, News)
- Multiple instances
- List/archive pages
- Slug-based URLs

**Examples:**
- Blog posts
- Portfolio projects
- Product catalog
- Team members
- Testimonials

## Key Differences

### File Structure

**Pages:**
```
{page-name}/
  {page-name}.json
```

**Collections:**
```
{collection-name}/
  {slug}.json
  {another-slug}.json
```

### URL Structure

**Pages:**
- `/about` → Direct access
- `/contact` → Direct access

**Collections:**
- `/posts/my-post` → Slug-based
- `/projects/project-1` → Slug-based

### Content Management

**Pages:**
- Edit single file
- One instance per type
- Simple management

**Collections:**
- Edit individual items
- Multiple instances
- List management

### Template Usage

**Pages:**
```php
<?= catalogue('title') ?>
<?= catalogue('content') ?>
```

**Collections:**
```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <?= catalogue('title') ?>
    <?= catalogue('content') ?>
<?php endforeach; ?>
```

## Choosing Content Type

### Use Pages When

- **Static content** - Content doesn't change often
- **Single instance** - Only one of this type needed
- **Simple structure** - No need for multiple items
- **Direct access** - Accessed directly by name

### Use Collections When

- **Dynamic content** - Content added regularly
- **Multiple instances** - Many items of same type
- **List pages** - Need archive/list pages
- **Slug-based URLs** - Individual item URLs needed

## Examples

### Page Example

**Blueprint:** `about.blueprint.yml`
```yaml
title: About Page
fields:
  title:
    type: text
  content:
    type: markdown
```

**Content:** `content/pages/about/about.json`
```json
{
  "title": "About Us",
  "content": "We are a company..."
}
```

**URL:** `/about`

### Collection Example

**Blueprint:** `posts.blueprint.yml`
```yaml
title: Blog Post
fields:
  title:
    type: text
  content:
    type: markdown
```

**Content:** `content/collections/posts/my-post.json`
```json
{
  "title": "My First Post",
  "content": "This is my first blog post..."
}
```

**URL:** `/posts/my-post`

## Best Practices

### Naming

- **Pages** - Use descriptive names (about, contact)
- **Collections** - Use plural names (posts, projects)

### Organization

- **Pages** - Keep simple, one per type
- **Collections** - Organize by collection type

### Structure

- **Pages** - Simple, focused content
- **Collections** - Consistent structure across items

## See Also

- [Pages vs Collections](../content-management/PAGES_VS_COLLECTIONS.md) - Detailed comparison
- [Creating Content](../content-management/CREATING_CONTENT.md) - How to create
- [Blueprints System](./BLUEPRINTS.md) - Blueprint definition

