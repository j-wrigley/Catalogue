# Blueprint Layout System

Organize fields into columns and control their size using layout attributes.

## Layout Attributes

| Attribute | Purpose | Values | Default |
|-----------|---------|--------|---------|
| `column` | Column position | `1`, `2`, `3`, `4`... | `1` |
| `span` | Columns to span | `1`, `2`, `3`, `4`... | `1` |
| `rows` | Height in rows | `1`, `2`, `3`, `4`... | `1` |
| `group` | Group name for alignment | String | None |

---

## `column`

Specifies which column a field appears in.

**Example:**
```yaml
fields:
  title:
    type: text
    column: 1
  featured_image:
    type: file
    column: 2
```

**Result:**
- `title` appears in column 1 (left)
- `featured_image` appears in column 2 (right)

---

## `span`

Controls how many columns a field spans across.

**Example:**
```yaml
fields:
  title:
    type: text
    column: 1
    span: 2  # Spans both columns
  description:
    type: textarea
    column: 1
    span: 1  # Single column
```

**Result:**
- `title` spans full width (2 columns)
- `description` appears in column 1 only

---

## `rows`

Sets the height of textarea and markdown fields.

**Example:**
```yaml
fields:
  excerpt:
    type: textarea
    rows: 3
  content:
    type: markdown
    rows: 10
```

**Result:**
- `excerpt` is 3 rows tall
- `content` is 10 rows tall

---

## `group`

Groups fields horizontally in the same row.

**Example:**
```yaml
fields:
  first_name:
    type: text
    column: 1
    group: name-group
  last_name:
    type: text
    column: 2
    group: name-group
  email:
    type: text
    column: 1
    group: contact-group
  phone:
    type: text
    column: 2
    group: contact-group
```

**Result:**
- `first_name` and `last_name` appear on the same row
- `email` and `phone` appear on the same row (below)

**Note:** Fields without a `group` attribute start a new row.

---

## Layout Examples

### Two-Column Layout

```yaml
fields:
  title:
    type: text
    column: 1
  featured_image:
    type: file
    column: 2
  content:
    type: markdown
    column: 1
    span: 2  # Full width
```

### Three-Column Layout

```yaml
fields:
  title:
    type: text
    column: 1
    span: 3  # Full width
  col1:
    type: textarea
    column: 1
  col2:
    type: textarea
    column: 2
  col3:
    type: textarea
    column: 3
```

### Mixed Layout with Groups

```yaml
fields:
  title:
    type: text
    column: 1
    span: 2
  name:
    type: text
    column: 1
    group: contact
  email:
    type: text
    column: 2
    group: contact
  bio:
    type: textarea
    column: 1
    span: 2
    rows: 5
```

---

## Best Practices

1. **Use `span` for full-width fields** - Titles, descriptions, content
2. **Use `column` for side-by-side fields** - Image + text, name + email
3. **Use `group` to keep related fields together** - Contact info, dates
4. **Use `rows` for text areas** - Match content needs (3-10 rows typical)

---

## See Also

- [Field Types Reference](./FIELD_TYPES_REFERENCE.md)
- [Field Options](./FIELD_OPTIONS.md)

