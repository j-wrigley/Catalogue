# Pages vs Collections

Understanding the difference between pages and collections.

## Overview

The CMS supports two content types:
- **Pages** - Single content items
- **Collections** - Multiple content items

## Pages

### Definition

Pages are single content items:
- One JSON file per page
- Each page is unique
- Examples: About, Contact, Home, Services

### Structure

```
content/pages/
  about/
    about.json
  contact/
    contact.json
  home/
    home.json
```

### Characteristics

- **Single instance** - One file per page type
- **Unique content** - Each page has its own content
- **Direct access** - Accessed via URL directly
- **Simple structure** - One blueprint, one file

### Examples

**About Page:**
- Blueprint: `about.blueprint.yml`
- Content: `content/pages/about/about.json`
- URL: `/about`

**Contact Page:**
- Blueprint: `contact.blueprint.yml`
- Content: `content/pages/contact/contact.json`
- URL: `/contact`

## Collections

### Definition

Collections are multiple content items:
- Multiple JSON files per collection
- Each item is unique
- Examples: Blog posts, Projects, Products, Team members

### Structure

```
content/collections/
  posts/
    my-first-post.json
    another-post.json
    third-post.json
  projects/
    project-1.json
    project-2.json
```

### Characteristics

- **Multiple instances** - Many files per collection type
- **Individual items** - Each item has its own content
- **Slug-based URLs** - Accessed via slug
- **List pages** - Can have archive/list pages

### Examples

**Blog Posts:**
- Blueprint: `posts.blueprint.yml`
- Content: `content/collections/posts/{slug}.json`
- URLs: `/posts/my-first-post`, `/posts/another-post`

**Projects:**
- Blueprint: `projects.blueprint.yml`
- Content: `content/collections/projects/{slug}.json`
- URLs: `/projects/project-1`, `/projects/project-2`

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
  {item-slug}.json
  {another-slug}.json
```

### URL Structure

**Pages:**
- `/about` → `about.html`
- `/contact` → `contact.html`

**Collections:**
- `/posts/my-post` → `posts/my-post.html`
- `/projects/project-1` → `projects/project-1.html`

### Content Access

**Pages:**
- Direct access via blueprint name
- One content file per page

**Collections:**
- Access via slug
- Multiple content files per collection

## When to Use Pages

Use pages for:
- **Static content** - About, Contact, Services
- **Single instances** - One of each type
- **Simple structure** - No need for multiple items
- **Direct access** - Accessed directly by name

**Examples:**
- About page
- Contact page
- Privacy policy
- Terms of service
- Home page

## When to Use Collections

Use collections for:
- **Dynamic content** - Blog posts, News articles
- **Multiple instances** - Many items of same type
- **List pages** - Archive/list pages needed
- **Slug-based URLs** - Individual item URLs

**Examples:**
- Blog posts
- Portfolio projects
- Product catalog
- Team members
- Testimonials

## Blueprint Relationship

### Pages

**One blueprint = One page:**
```
about.blueprint.yml → content/pages/about/about.json
```

### Collections

**One blueprint = Many items:**
```
posts.blueprint.yml → content/collections/posts/*.json
```

## Content Management

### Pages

- **Create** - Create blueprint, then add content
- **Edit** - Edit single content file
- **Delete** - Delete page directory
- **Access** - Via Pages table in admin

### Collections

- **Create** - Create blueprint, then add items
- **Edit** - Edit individual items
- **Delete** - Delete individual items
- **Access** - Via Collections table in admin

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

### Choosing Content Type

- **Pages** - For static, single-instance content
- **Collections** - For dynamic, multiple-instance content

### Naming

- **Pages** - Use descriptive names (about, contact)
- **Collections** - Use plural names (posts, projects)

### Organization

- **Pages** - Keep simple, one per type
- **Collections** - Organize by collection type

## See Also

- [Creating Content](./CREATING_CONTENT.md) - How to create pages and collections
- [Content Structure](./CONTENT_STRUCTURE.md) - JSON structure
- [Blueprints & Content](./BLUEPRINTS_CONTENT.md) - Blueprint relationship

