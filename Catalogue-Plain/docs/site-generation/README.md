# Site Generation Documentation

How the CMS generates static HTML files from templates and content.

## Quick Links

- **[How It Works](./HOW_IT_WORKS.md)** - Overview of the generation process
- **[Template Mapping](./TEMPLATE_MAPPING.md)** - How templates map to content
- **[URL Structure](./URL_STRUCTURE.md)** - URL patterns and routing
- **[Regenerating Pages](./REGENERATING.md)** - Manual and automatic regeneration
- **[Home Page](./HOME_PAGE.md)** - Special handling for index.html
- **[404 Page](./404_PAGE.md)** - Custom 404 error page
- **[Collections](./COLLECTIONS.md)** - How collections generate HTML

## Overview

The CMS uses **Static Site Generation (SSG)** to convert PHP templates and JSON content into static HTML files. This provides:

- **Fast performance** - Static HTML files load instantly
- **SEO friendly** - Search engines can index HTML directly
- **No PHP required** - Frontend is pure HTML/CSS/JS
- **Easy hosting** - Works on any static hosting service

## Generation Process

1. **Content** - JSON files define content data
2. **Blueprint** - YAML files define content structure
3. **Template** - PHP files define HTML structure
4. **Generator** - Combines all three into static HTML
5. **Output** - HTML files saved to root directory

## When Generation Happens

### Automatic Generation

- **On Save** - When content is saved in admin panel
- **On Create** - When new pages/items are created
- **On Update** - When content is modified
- **On Delete** - Old HTML files are removed

### Manual Generation

- **Regenerate All** - Button in CMS Settings
- **Individual Pages** - Regenerated on save

## File Structure

```
JSONCatalogue/
├── index.html              # Home page (generated)
├── about.html              # Page (generated)
├── 404.html                # Error page (generated)
├── posts/                  # Collection directory
│   ├── my-post.html        # Collection item (generated)
│   └── another-post.html   # Collection item (generated)
└── catalogue/
    ├── templates/          # PHP templates
    ├── blueprints/         # YAML definitions
    └── content/            # JSON content
```

## Next Steps

1. Read [How It Works](./HOW_IT_WORKS.md) to understand the process
2. Check [Template Mapping](./TEMPLATE_MAPPING.md) for template rules
3. Review [URL Structure](./URL_STRUCTURE.md) for URL patterns

