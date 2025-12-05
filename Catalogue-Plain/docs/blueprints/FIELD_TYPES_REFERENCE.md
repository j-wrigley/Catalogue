# Field Types Reference

Complete reference table of all available field types in blueprints.

## Field Types Table

| Field Type | Description | Value Type | Use Case |
|------------|-------------|------------|----------|
| `text` | Single-line text input | String | Titles, names, short text |
| `textarea` | Multi-line text input | String | Descriptions, longer text |
| `markdown` | Visual markdown editor | String (Markdown) | Rich content, blog posts |
| `select` | Dropdown selection | String | Single choice from options |
| `radio` | Radio button group | String | Single choice (visual) |
| `checkbox` | Checkbox group | Array | Multiple selections |
| `tags` | Tag input with suggestions | Array | Categories, tags, keywords |
| `file` | File/media upload | String/Array | Images, documents, media |
| `slider` | Numeric slider | Number | Ranges, ratings, quantities |
| `switch` | Toggle switch | Boolean | Yes/no, on/off, enabled/disabled |
| `structure` | Repeatable table items | Array | Tables, lists, repeatable content |

---

## Field Type Details

### `text`

Single-line text input field.

**Options:**
- `label` - Field label
- `placeholder` - Placeholder text
- `required` - Boolean, make field required
- `default` - Default value

**Example:**
```yaml
title:
  type: text
  label: Page Title
  placeholder: Enter title
  required: true
```

**Stored as:** String

---

### `textarea`

Multi-line text input field.

**Options:**
- `label` - Field label
- `placeholder` - Placeholder text
- `required` - Boolean, make field required
- `rows` - Number of rows (height)
- `default` - Default value

**Example:**
```yaml
description:
  type: textarea
  label: Description
  rows: 5
  required: true
```

**Stored as:** String

---

### `markdown`

Visual markdown editor with formatting toolbar.

**Options:**
- `label` - Field label
- `required` - Boolean, make field required
- `rows` - Number of rows (height)
- `default` - Default value

**Toolbar Features:**
- Bold, Italic, Strikethrough, Underline
- Headings (H1, H2, H3)
- Lists (ordered, unordered)
- Links
- Code blocks

**Example:**
```yaml
content:
  type: markdown
  label: Content
  rows: 10
```

**Stored as:** String (Markdown format)

---

### `select`

Dropdown selection menu.

**Options:**
- `label` - Field label
- `options` - Object of value:label pairs
- `required` - Boolean, make field required
- `default` - Default value

**Example:**
```yaml
status:
  type: select
  label: Status
  options:
    draft: Draft
    published: Published
    archived: Archived
  default: draft
```

**Stored as:** String (selected value)

---

### `radio`

Radio button group for single selection.

**Options:**
- `label` - Field label
- `options` - Object of value:label pairs
- `layout` - `grid` or `list` (default: `grid`)
- `columns` - Number of columns for grid layout (default: 5)
- `required` - Boolean, make field required
- `default` - Default value

**Example:**
```yaml
priority:
  type: radio
  label: Priority
  options:
    low: Low
    medium: Medium
    high: High
  layout: grid
  columns: 3
```

**Stored as:** String (selected value)

---

### `checkbox`

Checkbox group for multiple selections.

**Options:**
- `label` - Field label
- `options` - Object of value:label pairs
- `layout` - `grid` or `list` (default: `grid`)
- `columns` - Number of columns for grid layout (default: 5)
- `required` - Boolean, make field required
- `default` - Array of default values

**Example:**
```yaml
categories:
  type: checkbox
  label: Categories
  options:
    design: Design
    development: Development
    marketing: Marketing
  layout: grid
  columns: 3
```

**Stored as:** Array of selected values

---

### `tags`

Tag input with add/remove functionality and optional predefined options.

**Options:**
- `label` - Field label
- `options` - Array of predefined tag options (optional)
- `required` - Boolean, make field required
- `default` - Array of default tags

**Example:**
```yaml
tags:
  type: tags
  label: Tags
  options:
    - Design
    - Development
    - Marketing
```

**Stored as:** Array of tag strings

---

### `file`

File/media upload field with media picker.

**Options:**
- `label` - Field label
- `multiple` - Boolean, allow multiple files (default: false)
- `max_files` - Maximum number of files (when multiple: true)
- `required` - Boolean, make field required

**Example:**
```yaml
featured_image:
  type: file
  label: Featured Image
  multiple: false

gallery:
  type: file
  label: Gallery Images
  multiple: true
  max_files: 10
```

**Stored as:** String (single file) or Array (multiple files)

**Note:** Files can have metadata (alt text, caption, description, credit, tags) managed separately in the Media section.

---

### `slider`

Numeric slider input.

**Options:**
- `label` - Field label
- `min` - Minimum value (default: 0)
- `max` - Maximum value (default: 100)
- `step` - Step increment (default: 1)
- `default` - Default value
- `required` - Boolean, make field required

**Example:**
```yaml
rating:
  type: slider
  label: Rating
  min: 0
  max: 10
  step: 1
  default: 5
```

**Stored as:** Number

---

### `switch`

Toggle switch for boolean values.

**Options:**
- `label` - Field label
- `default` - Boolean default value (default: false)
- `required` - Boolean, make field required

**Example:**
```yaml
featured:
  type: switch
  label: Featured
  default: false
```

**Stored as:** Boolean (`true` or `false`)

**Note:** `toggle` is an alias for `switch` - they work identically.

---

### `structure`

Repeatable table structure for complex data.

**Options:**
- `label` - Field label
- `fields` - Object defining sub-fields
- `columns` - Object defining visible columns in table
- `required` - Boolean, make field required

**Example:**
```yaml
team_members:
  type: structure
  label: Team Members
  fields:
    name:
      type: text
      label: Name
    role:
      type: text
      label: Role
    bio:
      type: textarea
      label: Bio
  columns:
    name:
      label: Name
      align: left
    role:
      label: Role
      align: left
```

**Stored as:** Array of objects

**Note:** Structure fields support all other field types as sub-fields. Each item can be edited in a modal dialog.

---

## Core Fields

These fields are automatically added to all blueprints:

| Field | Type | Description |
|-------|------|-------------|
| `_featured` | `switch` | Mark content as featured |
| `_status` | `select` | Content status (draft, published, unlisted) |

These appear in a banner at the top of the edit form and don't need to be defined in your blueprint.

---

## Field Options Summary

All field types support these common options:

- `type` - Field type (required)
- `label` - Display label
- `required` - Boolean, make field required
- `default` - Default value
- `column` - Column position (1, 2, 3, 4...)
- `span` - Number of columns to span
- `rows` - Height in rows (for textarea, markdown)
- `group` - Group name for horizontal alignment
- `category` - Tab category (for tabs)

See [Field Options](./FIELD_OPTIONS.md) for detailed information.

