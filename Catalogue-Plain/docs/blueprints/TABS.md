# Blueprint Tabs

Organize fields into tabbed sections for better organization.

## Basic Structure

```yaml
title: Content Type

tabs:
  tab-name:
    label: Tab Label

fields:
  field-name:
    type: text
    category: tab-name
```

## How Tabs Work

1. Define tabs in the `tabs` section
2. Assign fields to tabs using `category`
3. Fields without `category` appear in the first tab

## Example

```yaml
title: Blog Post

tabs:
  content:
    label: Content
  metadata:
    label: Metadata
  settings:
    label: Settings

fields:
  # Content Tab
  title:
    type: text
    category: content
    label: Title
  
  content:
    type: markdown
    category: content
    label: Content
  
  # Metadata Tab
  tags:
    type: tags
    category: metadata
    label: Tags
  
  author:
    type: text
    category: metadata
    label: Author
  
  # Settings Tab
  featured:
    type: switch
    category: settings
    label: Featured
```

## Tab Definition

Each tab requires:
- **Key** - Internal name (used in `category`)
- **label** - Display name

```yaml
tabs:
  content:        # Key (used in category)
    label: Content  # Display label
```

## Field Assignment

Assign fields to tabs using `category`:

```yaml
fields:
  title:
    type: text
    category: content  # Assigns to "content" tab
```

## Fields Without Category

Fields without `category` appear in the first tab:

```yaml
fields:
  title:
    type: text
    # No category - appears in first tab
  content:
    type: markdown
    category: content  # Appears in "content" tab
```

## Tab Order

Tabs appear in the order defined in `tabs`:

```yaml
tabs:
  content:      # First tab
    label: Content
  metadata:    # Second tab
    label: Metadata
  settings:     # Third tab
    label: Settings
```

## Complete Example

```yaml
title: Product

tabs:
  details:
    label: Product Details
  pricing:
    label: Pricing
  media:
    label: Media

fields:
  # Details Tab
  name:
    type: text
    category: details
    label: Product Name
    required: true
  
  description:
    type: textarea
    category: details
    label: Description
    rows: 5
  
  # Pricing Tab
  price:
    type: text
    category: pricing
    label: Price
  
  currency:
    type: select
    category: pricing
    label: Currency
    options:
      usd: USD
      eur: EUR
      gbp: GBP
  
  # Media Tab
  images:
    type: file
    category: media
    label: Product Images
    multiple: true
    max_files: 5
```

## Best Practices

1. **Group related fields** - Content, metadata, settings
2. **Use clear tab names** - "Content", "Settings", "SEO"
3. **Keep tabs focused** - Don't mix unrelated fields
4. **Limit tab count** - 3-5 tabs maximum for usability

---

## See Also

- [Basic Structure](./BASIC_STRUCTURE.md)
- [Field Types Reference](./FIELD_TYPES_REFERENCE.md)

