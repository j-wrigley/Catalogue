# Field Options

Common options available for all field types.

## Common Options

| Option | Type | Description | Default |
|--------|------|-------------|---------|
| `type` | String | Field type (required) | - |
| `label` | String | Display label | Auto-generated |
| `required` | Boolean | Make field required | `false` |
| `default` | Mixed | Default value | `null` |
| `column` | Number | Column position | `1` |
| `span` | Number | Columns to span | `1` |
| `rows` | Number | Height in rows | `1` |
| `group` | String | Group name | None |
| `category` | String | Tab category | First tab |

---

## `type`

**Required.** Field type identifier.

```yaml
title:
  type: text
```

See [Field Types Reference](./FIELD_TYPES_REFERENCE.md) for all types.

---

## `label`

Display label shown in the admin panel.

```yaml
title:
  type: text
  label: Page Title
```

**Default:** Auto-generated from field name (e.g., `featured_image` → "Featured Image")

---

## `required`

Make field required (cannot be empty).

```yaml
title:
  type: text
  required: true
```

**Values:** `true`, `false`  
**Default:** `false`

---

## `default`

Default value for the field.

```yaml
status:
  type: select
  default: draft
  options:
    draft: Draft
    published: Published
```

**Type:** Depends on field type (string, number, boolean, array)

---

## `column`

Column position (1-based).

```yaml
title:
  type: text
  column: 1
featured_image:
  type: file
  column: 2
```

**Values:** `1`, `2`, `3`, `4`...  
**Default:** `1`

See [Layout System](./LAYOUT.md) for details.

---

## `span`

Number of columns to span.

```yaml
title:
  type: text
  column: 1
  span: 2  # Spans full width
```

**Values:** `1`, `2`, `3`, `4`...  
**Default:** `1`

See [Layout System](./LAYOUT.md) for details.

---

## `rows`

Height in rows (for `textarea` and `markdown` fields).

```yaml
content:
  type: markdown
  rows: 10
```

**Values:** `1`, `2`, `3`, `4`...  
**Default:** `1`  
**Applies to:** `textarea`, `markdown`

---

## `group`

Group name for horizontal alignment.

```yaml
first_name:
  type: text
  column: 1
  group: name-group
last_name:
  type: text
  column: 2
  group: name-group
```

**Values:** Any string  
**Default:** None

See [Layout System](./LAYOUT.md) for details.

---

## `category`

Tab category (assigns field to a tab).

```yaml
tabs:
  content:
    label: Content

fields:
  title:
    type: text
    category: content
```

**Values:** Must match a tab key  
**Default:** First tab

See [Tabs](./TABS.md) for details.

---

## Field-Specific Options

Some field types have additional options:

### `select`, `radio`, `checkbox`

- `options` - Object/array of options

### `file`

- `multiple` - Allow multiple files
- `max_files` - Maximum files (when multiple: true)

### `slider`

- `min` - Minimum value
- `max` - Maximum value
- `step` - Step increment

### `radio`, `checkbox`

- `layout` - `grid` or `list`
- `columns` - Number of columns

See [Field Types Reference](./FIELD_TYPES_REFERENCE.md) for field-specific options.

---

## Example: Complete Field Definition

```yaml
fields:
  title:
    type: text
    label: Page Title
    required: true
    column: 1
    span: 2
    default: Untitled Page
  
  excerpt:
    type: textarea
    label: Excerpt
    column: 1
    rows: 3
  
  featured_image:
    type: file
    label: Featured Image
    column: 2
  
  content:
    type: markdown
    label: Content
    category: content
    column: 1
    span: 2
    rows: 10
    required: true
```

---

## See Also

- [Field Types Reference](./FIELD_TYPES_REFERENCE.md)
- [Layout System](./LAYOUT.md)
- [Tabs](./TABS.md)

