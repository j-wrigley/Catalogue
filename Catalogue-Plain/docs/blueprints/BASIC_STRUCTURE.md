# Blueprint Basic Structure

How to structure a blueprint file.

## File Structure

```yaml
title: Content Type Name

tabs:
  tab-name:
    label: Tab Label

fields:
  field-name:
    type: field-type
    label: Field Label
    # ... field options
```

## Required Elements

### `title`

The display name for your content type.

```yaml
title: Blog Post
```

### `fields`

Object defining all fields for this content type.

```yaml
fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
```

## Optional Elements

### `tabs`

Organize fields into tabbed sections. See [Tabs](./TABS.md) for details.

```yaml
tabs:
  content:
    label: Content
  settings:
    label: Settings
```

## Field Definition

Each field requires:
- `type` - Field type (see [Field Types Reference](./FIELD_TYPES_REFERENCE.md))
- `label` - Display label

```yaml
fields:
  title:
    type: text
    label: Title
```

## Complete Example

```yaml
title: Blog Post

tabs:
  content:
    label: Content
  metadata:
    label: Metadata

fields:
  # Content Tab
  title:
    type: text
    label: Title
    category: content
    required: true
    column: 1
    span: 2
  
  content:
    type: markdown
    label: Content
    category: content
    column: 1
    span: 2
    rows: 10
  
  # Metadata Tab
  tags:
    type: tags
    label: Tags
    category: metadata
    column: 1
```

## Field Naming

- Use lowercase letters, numbers, hyphens, and underscores
- Avoid spaces and special characters
- Use descriptive names: `featured_image` not `img1`

**Good:**
- `title`
- `featured_image`
- `post_date`
- `author_name`

**Bad:**
- `Title` (uppercase)
- `featured image` (space)
- `img-1` (not descriptive)

## Next Steps

- Learn about [Layout System](./LAYOUT.md) to organize fields
- Use [Tabs](./TABS.md) to group related fields
- Check [Field Types Reference](./FIELD_TYPES_REFERENCE.md) for available types

