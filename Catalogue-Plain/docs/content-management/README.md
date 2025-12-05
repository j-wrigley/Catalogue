# Content Management Documentation

Complete guide to managing content in the CMS.

## Quick Links

- **[Pages vs Collections](./PAGES_VS_COLLECTIONS.md)** - Understanding content types
- **[Creating Content](./CREATING_CONTENT.md)** - How to create pages and collections
- **[Editing Content](./EDITING_CONTENT.md)** - Modifying existing content
- **[Content Structure](./CONTENT_STRUCTURE.md)** - JSON structure and organization
- **[Blueprints & Content](./BLUEPRINTS_CONTENT.md)** - How blueprints define content
- **[Content Storage](./CONTENT_STORAGE.md)** - File storage and organization
- **[Content Workflow](./CONTENT_WORKFLOW.md)** - Best practices and workflows

## Overview

The CMS uses a flat-file content management system:
- **No database** - All content stored as JSON files
- **Blueprint-driven** - Content structure defined by blueprints
- **Two content types** - Pages (single) and Collections (multiple)
- **Automatic generation** - HTML files generated from JSON

## Content Types

### Pages

Single content items:
- One JSON file per page
- Examples: About, Contact, Home
- Structure: `content/pages/{page-name}/{page-name}.json`

### Collections

Multiple content items:
- Multiple JSON files per collection
- Examples: Blog posts, Projects, Products
- Structure: `content/collections/{collection-name}/{item-slug}.json`

## Key Concepts

### Blueprints

Define content structure:
- Field types and options
- Required fields
- Layout and organization
- Form generation

### JSON Storage

Content stored as JSON:
- Human-readable format
- Easy to edit manually
- Version control friendly
- No database needed

### HTML Generation

Static HTML files generated:
- From JSON content
- Using PHP templates
- Automatic regeneration
- SEO-friendly URLs

## Quick Start

1. **Create Blueprint** - Define content structure
2. **Create Content** - Add content via admin panel
3. **Edit Content** - Modify as needed
4. **View Site** - HTML generated automatically

## Next Steps

1. Read [Pages vs Collections](./PAGES_VS_COLLECTIONS.md) to understand content types
2. Check [Creating Content](./CREATING_CONTENT.md) to learn how to create
3. Review [Editing Content](./EDITING_CONTENT.md) for modification
4. See [Content Structure](./CONTENT_STRUCTURE.md) for JSON format
5. Review [Blueprints & Content](./BLUEPRINTS_CONTENT.md) for blueprint relationship

