# Blueprints & Content

How blueprints define and structure content.

## Overview

Blueprints define content structure:
- **Field definitions** - What fields content has
- **Field types** - What type each field is
- **Validation** - Required fields, options
- **Layout** - How fields are organized

## Blueprint to Content Relationship

### One-to-One (Pages)

**One blueprint = One page:**

```
about.blueprint.yml → content/pages/about/about.json
```

### One-to-Many (Collections)

**One blueprint = Many items:**

```
posts.blueprint.yml → content/collections/posts/*.json
```

## Blueprint Definition

### Basic Structure

```yaml
title: Page Title
fields:
  field_name:
    type: field_type
    label: Field Label
```

### Field Definition

```yaml
fields:
  title:
    type: text
    label: Title
    required: true
  content:
    type: markdown
    label: Content
    rows: 10
```

## Content Structure

### Matching Blueprint

Content JSON matches blueprint fields:

**Blueprint:**
```yaml
fields:
  title:
    type: text
  content:
    type: markdown
```

**Content:**
```json
{
  "title": "My Title",
  "content": "My content..."
}
```

### Field Mapping

**Blueprint Field → JSON Key:**

- Field name becomes JSON key
- Field type determines value type
- Field options affect validation

## Field Types

### Text

**Blueprint:**
```yaml
title:
  type: text
```

**Content:**
```json
{
  "title": "String value"
}
```

### Textarea

**Blueprint:**
```yaml
description:
  type: textarea
```

**Content:**
```json
{
  "description": "Multi-line\ntext"
}
```

### Markdown

**Blueprint:**
```yaml
content:
  type: markdown
```

**Content:**
```json
{
  "content": "# Heading\n\nParagraph."
}
```

### File

**Blueprint:**
```yaml
image:
  type: file
```

**Content:**
```json
{
  "image": "/uploads/images/file.jpg"
}
```

### Select

**Blueprint:**
```yaml
category:
  type: select
  options:
    - news
    - blog
```

**Content:**
```json
{
  "category": "news"
}
```

## Core Fields

### Automatic Addition

Core fields added to all content:

- **Featured** (`_featured`) - Boolean switch
- **Status** (`_status`) - Dropdown (draft/published/unlisted)

**Not in Blueprint:** Added automatically

**In Content:**
```json
{
  "_featured": false,
  "_status": "published"
}
```

## Blueprint Options

### Required Fields

**Blueprint:**
```yaml
title:
  type: text
  required: true
```

**Effect:** Field must be filled before saving

### Default Values

**Blueprint:**
```yaml
status:
  type: select
  default: draft
```

**Effect:** Value used if not provided

### Field Options

**Blueprint:**
```yaml
category:
  type: select
  options:
    - news
    - blog
```

**Effect:** Only these values allowed

## Layout Organization

### Tabs

**Blueprint:**
```yaml
tabs:
  content:
    label: Content
  settings:
    label: Settings

fields:
  title:
    category: content
  meta:
    category: settings
```

**Effect:** Fields organized into tabs

### Groups

**Blueprint:**
```yaml
fields:
  title:
    group: main
    column: 1
  image:
    group: main
    column: 2
```

**Effect:** Fields grouped horizontally

### Columns

**Blueprint:**
```yaml
fields:
  title:
    column: 1
  description:
    column: 2
```

**Effect:** Fields in columns

## Content Validation

### Blueprint Validation

**Required Fields:**
- Must be filled
- Validated on save
- Error if empty

**Field Types:**
- Type checked
- Format validated
- Options checked

### Content Structure

**Must Match Blueprint:**
- Fields match blueprint
- Types match definition
- Options match allowed values

## Examples

### Simple Page

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
  "content": "# About\n\nWe are a company...",
  "_featured": false,
  "_status": "published"
}
```

### Complex Collection

**Blueprint:** `posts.blueprint.yml`
```yaml
title: Blog Post
fields:
  title:
    type: text
    required: true
  content:
    type: markdown
  category:
    type: select
    options:
      - news
      - tutorial
  tags:
    type: tags
```

**Content:** `content/collections/posts/my-post.json`
```json
{
  "title": "My Post",
  "content": "# Post Title\n\nContent...",
  "category": "news",
  "tags": ["blog", "tutorial"],
  "_slug": "my-post",
  "_featured": true,
  "_status": "published"
}
```

## Best Practices

### Blueprint Design

- **Define structure first** - Plan fields before content
- **Use appropriate types** - Match field types to data
- **Add validation** - Use required fields
- **Organize logically** - Use tabs and groups

### Content Creation

- **Follow blueprint** - Match field structure
- **Fill required fields** - Complete all required
- **Use correct types** - Match field types
- **Validate values** - Ensure correct format

## See Also

- [Blueprints Documentation](../blueprints/README.md) - Blueprint reference
- [Content Structure](./CONTENT_STRUCTURE.md) - JSON structure
- [Creating Content](./CREATING_CONTENT.md) - Creating content

