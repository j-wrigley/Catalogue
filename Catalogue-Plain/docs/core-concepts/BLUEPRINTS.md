# Blueprints System

How blueprints define content structure and drive the CMS.

## Overview

**Blueprints** are YAML files that define the structure of content types. They tell the CMS:
- What fields content has
- What type each field is
- How fields are organized
- What validation is needed

## What is a Blueprint?

### Definition

A blueprint is a YAML file that defines:
- **Content structure** - Fields and types
- **Form layout** - Organization and grouping
- **Validation rules** - Required fields, options
- **Field options** - Labels, descriptions, defaults

### File Location

```
catalogue/blueprints/{name}.blueprint.yml
```

**Examples:**
- `about.blueprint.yml`
- `posts.blueprint.yml`
- `settings.blueprint.yml`

## Blueprint Structure

### Basic Structure

```yaml
title: Content Type Name
fields:
  field_name:
    type: field_type
    label: Field Label
```

### Example

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
    rows: 10
  category:
    type: select
    label: Category
    options:
      - news
      - tutorial
```

## How Blueprints Work

### 1. Define Structure

**Blueprint defines:**
- Field names
- Field types
- Field options
- Validation rules

### 2. Generate Forms

**CMS uses blueprint to:**
- Create form fields
- Set up validation
- Organize layout
- Handle input

### 3. Validate Content

**On save:**
- Check required fields
- Validate field types
- Verify options
- Ensure structure matches

### 4. Store Content

**Content JSON matches blueprint:**
- Same field names
- Correct types
- Valid values
- Proper structure

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

## Field Types

### Available Types

- **text** - Single line text
- **textarea** - Multi-line text
- **markdown** - Rich text editor
- **file** - File upload
- **select** - Dropdown
- **radio** - Radio buttons
- **checkbox** - Checkboxes
- **tags** - Tag input
- **slider** - Number slider
- **switch** - Toggle switch
- **structure** - Repeatable table

### Field Definition

```yaml
field_name:
  type: text
  label: Field Label
  required: true
  default: Default Value
  description: Field description
```

## Layout Organization

### Tabs

**Organize fields into tabs:**

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

### Groups

**Group fields horizontally:**

```yaml
fields:
  title:
    group: main
    column: 1
  image:
    group: main
    column: 2
```

### Columns

**Organize fields in columns:**

```yaml
fields:
  title:
    column: 1
  description:
    column: 2
```

## Core Fields

### Automatic Addition

Core fields added automatically:
- **Featured** (`_featured`) - Boolean switch
- **Status** (`_status`) - Dropdown

**Not in Blueprint:** Added automatically

**In Content:**
```json
{
  "_featured": false,
  "_status": "published"
}
```

## Blueprint Benefits

### 1. Structure Definition

**Advantages:**
- Clear content structure
- Consistent data format
- Easy to understand
- Self-documenting

### 2. Form Generation

**Advantages:**
- Forms created automatically
- No manual form coding
- Consistent interface
- Easy to modify

### 3. Validation

**Advantages:**
- Built-in validation
- Required fields enforced
- Type checking
- Option validation

### 4. Flexibility

**Advantages:**
- Easy to modify
- Add fields easily
- Change structure
- Extend functionality

## Blueprint Workflow

### 1. Create Blueprint

**Steps:**
1. Create YAML file
2. Define fields
3. Set options
4. Organize layout

### 2. Create Content

**Steps:**
1. Blueprint read by CMS
2. Form generated
3. Content created
4. JSON matches blueprint

### 3. Modify Blueprint

**Steps:**
1. Edit YAML file
2. Add/remove fields
3. Update options
4. Forms update automatically

## Best Practices

### Blueprint Design

- **Start simple** - Add complexity gradually
- **Use appropriate types** - Match data needs
- **Organize logically** - Use tabs and groups
- **Add validation** - Use required fields
- **Document fields** - Use descriptions

### Field Organization

- **Group related fields** - Use groups
- **Organize by purpose** - Use tabs
- **Keep it clean** - Don't over-complicate
- **Consistent naming** - Follow conventions

## Examples

### Simple Page Blueprint

```yaml
title: About Page
fields:
  title:
    type: text
    label: Title
    required: true
  content:
    type: markdown
    label: Content
```

### Complex Collection Blueprint

```yaml
title: Blog Post
tabs:
  content:
    label: Content
  media:
    label: Media

fields:
  title:
    type: text
    label: Title
    required: true
    category: content
  content:
    type: markdown
    label: Content
    category: content
  featured_image:
    type: file
    label: Featured Image
    category: media
  gallery:
    type: file
    label: Gallery
    multiple: true
    category: media
```

## See Also

- [Blueprints Documentation](../blueprints/README.md) - Complete blueprint reference
- [Blueprints & Content](../content-management/BLUEPRINTS_CONTENT.md) - Blueprint relationship
- [Content Types](./CONTENT_TYPES.md) - Pages vs Collections

