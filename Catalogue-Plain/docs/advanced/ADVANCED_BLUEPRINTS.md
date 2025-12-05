# Advanced Blueprints

Complex blueprint patterns and advanced techniques.

## Overview

This guide covers advanced blueprint techniques for creating sophisticated content structures and layouts.

## Complex Field Combinations

### Nested Structures

Create nested data structures using structure fields:

```yaml
fields:
  team:
    type: structure
    label: Team Members
    fields:
      name:
        type: text
        required: true
      role:
        type: text
      contact:
        type: structure
        fields:
          email:
            type: text
          phone:
            type: text
```

### Conditional Fields

Use field options to create conditional logic:

```yaml
fields:
  content_type:
    type: select
    label: Content Type
    options:
      post: Blog Post
      page: Static Page
      gallery: Image Gallery
  content:
    type: markdown
    label: Content
    # Show only for posts
  gallery_images:
    type: file
    label: Gallery Images
    max_files: 10
    # Show only for galleries
```

**Note**: Conditional display logic is handled in templates, not blueprints. Blueprints define all available fields.

## Advanced Layout Techniques

### Multi-Column Complex Layouts

Combine `column`, `span`, and `rows` for sophisticated layouts:

```yaml
fields:
  hero_image:
    type: file
    column: 1
    span: 2
    rows: 3
  hero_title:
    type: text
    column: 1
    span: 1
  hero_subtitle:
    type: text
    column: 2
    span: 1
  content:
    type: markdown
    column: 1
    span: 2
    rows: 5
  sidebar:
    type: structure
    column: 3
    span: 1
    rows: 5
```

### Grouped Fields

Use `group` to keep related fields aligned horizontally:

```yaml
fields:
  meta_title:
    type: text
    group: meta
    column: 1
  meta_description:
    type: textarea
    group: meta
    column: 2
  og_image:
    type: file
    group: meta
    column: 3
```

## Tabbed Complex Forms

Organize many fields into logical tabs:

```yaml
tabs:
  content:
    label: Content
  media:
    label: Media
  seo:
    label: SEO
  advanced:
    label: Advanced

fields:
  title:
    type: text
    category: content
  body:
    type: markdown
    category: content
  images:
    type: file
    category: media
    max_files: 5
  meta_title:
    type: text
    category: seo
  custom_css:
    type: textarea
    category: advanced
```

## Dynamic Field Patterns

### Repeating Sections

Use structure fields for repeatable content:

```yaml
fields:
  testimonials:
    type: structure
    label: Testimonials
    fields:
      quote:
        type: textarea
        rows: 3
      author:
        type: text
      role:
        type: text
      image:
        type: file
```

### Flexible Content Blocks

Create flexible content systems:

```yaml
fields:
  sections:
    type: structure
    label: Content Sections
    fields:
      section_type:
        type: select
        options:
          text: Text Block
          image: Image Block
          gallery: Gallery Block
          quote: Quote Block
      content:
        type: markdown
      images:
        type: file
        max_files: 10
      quote_text:
        type: textarea
      quote_author:
        type: text
```

## Field Validation Patterns

### Required Field Combinations

```yaml
fields:
  show_cta:
    type: switch
    label: Show Call to Action
  cta_text:
    type: text
    label: CTA Text
    # Required if show_cta is true (handled in templates)
  cta_link:
    type: text
    label: CTA Link
    # Required if show_cta is true
```

### File Type Restrictions

```yaml
fields:
  logo:
    type: file
    label: Logo
    # Only images allowed (enforced by CMS)
  document:
    type: file
    label: Document
    # Any file type allowed
```

## Advanced Field Options

### Custom Defaults

```yaml
fields:
  status:
    type: select
    label: Status
    options:
      draft: Draft
      published: Published
    default: draft
  featured:
    type: switch
    label: Featured
    default: false
```

### Field Dependencies

While blueprints don't support conditional fields directly, you can structure fields to work together:

```yaml
fields:
  layout_type:
    type: select
    label: Layout Type
    options:
      grid: Grid
      list: List
      masonry: Masonry
  columns:
    type: slider
    label: Columns
    min: 1
    max: 6
    # Use in templates: if layout_type === 'grid', use columns value
```

## Performance Considerations

### Large Structure Fields

For structures with many items:

```yaml
fields:
  items:
    type: structure
    label: Items
    # Consider pagination in templates for large datasets
```

### File Field Optimization

```yaml
fields:
  gallery:
    type: file
    label: Gallery
    max_files: 20
    # Limit file count for performance
```

## Best Practices

1. **Organize with Tabs**: Use tabs for blueprints with many fields
2. **Group Related Fields**: Use `group` to keep related fields together
3. **Use Appropriate Field Types**: Choose the right field type for the data
4. **Plan Layout**: Consider how fields will appear on different screen sizes
5. **Document Complex Fields**: Add comments in YAML for complex structures

## Examples

### Blog Post with Advanced Features

```yaml
title: Blog Post

tabs:
  content:
    label: Content
  media:
    label: Media
  seo:
    label: SEO

fields:
  title:
    type: text
    required: true
    category: content
  excerpt:
    type: textarea
    rows: 3
    category: content
  content:
    type: markdown
    rows: 10
    category: content
  featured_image:
    type: file
    category: media
  gallery:
    type: file
    max_files: 10
    category: media
  tags:
    type: tags
    category: content
  meta_title:
    type: text
    category: seo
  meta_description:
    type: textarea
    category: seo
```

### Product Catalog

```yaml
title: Product

fields:
  name:
    type: text
    required: true
    column: 1
    span: 2
  price:
    type: text
    column: 1
  sku:
    type: text
    column: 2
  description:
    type: markdown
    column: 1
    span: 2
    rows: 5
  images:
    type: file
    max_files: 5
    column: 1
    span: 2
  specifications:
    type: structure
    column: 1
    span: 2
    fields:
      name:
        type: text
      value:
        type: text
  categories:
    type: checkbox
    options:
      electronics: Electronics
      clothing: Clothing
      home: Home
```

## See Also

- [Blueprint Basics](../blueprints/BASIC_STRUCTURE.md) - Basic blueprint structure
- [Field Types](../blueprints/FIELD_TYPES_REFERENCE.md) - All available field types
- [Layout](../blueprints/LAYOUT.md) - Layout attributes
- [Tabs](../blueprints/TABS.md) - Tabbed forms

