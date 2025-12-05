# Blueprints Documentation

Blueprints define the structure and fields for your content types. They're written in YAML and stored in `/catalogue/blueprints/`.

## Quick Links

- **[Field Types Reference](./FIELD_TYPES_REFERENCE.md)** - Complete table of all available field types
- **[Basic Structure](./BASIC_STRUCTURE.md)** - How to structure a blueprint file
- **[Layout System](./LAYOUT.md)** - Organizing fields with columns, span, rows, and groups
- **[Tabs](./TABS.md)** - Organizing fields into tabbed sections
- **[Field Options](./FIELD_OPTIONS.md)** - Common options available for all fields

## What are Blueprints?

Blueprints are YAML files that define:
- What fields your content type has
- What type each field is (text, image, markdown, etc.)
- How fields are organized and displayed
- Validation rules

## Blueprint File Naming

Blueprints must follow this naming convention:
```
{content-type}.blueprint.yml
```

Examples:
- `posts.blueprint.yml` → Creates a "posts" content type
- `about.blueprint.yml` → Creates an "about" page
- `settings.blueprint.yml` → Creates a "settings" page

## Basic Example

```yaml
title: Blog Post

fields:
  title:
    type: text
    label: Title
    required: true
  content:
    type: markdown
    label: Content
```

## Next Steps

1. Read [Basic Structure](./BASIC_STRUCTURE.md) to understand blueprint syntax
2. Check [Field Types Reference](./FIELD_TYPES_REFERENCE.md) for available field types
3. Learn about [Layout System](./LAYOUT.md) to organize your fields
4. Use [Tabs](./TABS.md) to group related fields

