# The `catalogue()` Function

The main function for accessing and rendering content in templates.

## Basic Syntax

```php
<?= catalogue('field_name', 'Default Value') ?>
```

## Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `key` | String | Field name to retrieve | Required |
| `default` | String | Value if field is empty | `''` |
| `source` | String | Data source (`content`, `site`, or `file`) | `'content'` |
| `dateFormat` | String | PHP date format string (e.g., `'F j, Y'`) or separator string (e.g., `', '`) | `null` |
| `separator` | String | Separator for array values (e.g., `', '`, `' | '`) | `null` |

## Basic Usage

### Simple Field

```php
<h1><?= catalogue('title') ?></h1>
<p><?= catalogue('description', 'No description') ?></p>
```

### With Default Value

```php
<?= catalogue('subtitle', 'No subtitle') ?>
```

### Site Settings

```php
<?= catalogue('site_name', 'My Site', 'site') ?>
```

### Date Formatting

Format date fields using PHP date format strings:

```php
<?= catalogue('created_at', '', 'content', 'F j, Y') ?>
<!-- Output: November 24, 2025 -->

<?= catalogue('updated_at', '', 'content', 'D, M j') ?>
<!-- Output: Mon, Nov 27 -->

<?= catalogue('created_at', '', 'content', 'Y-m-d') ?>
<!-- Output: 2025-11-24 -->
```

**Common Date Formats:**

| Format | Example Output |
|--------|---------------|
| `'F j, Y'` | November 24, 2025 |
| `'D, M j'` | Mon, Nov 27 |
| `'Y-m-d'` | 2025-11-24 |
| `'M j, Y g:i A'` | Nov 24, 2025 3:30 PM |
| `'l, F j'` | Monday, November 24 |

**Convenience Function:**

For cleaner syntax, use `catalogueDate()`:

```php
<?= catalogueDate('created_at', 'F j, Y') ?>
<!-- Same as: catalogue('created_at', '', 'content', 'F j, Y') -->
```

See [Date Formatting](./DATE_FORMATTING.md) for complete documentation.

## Array Separator

For array fields (tags, checkboxes), you can join values with a custom separator:

```php
<?= catalogue('meta_keywords', '', 'content', ', ') ?>
<!-- Output: tag1, tag2, tag3 -->

<?= catalogue('categories', '', 'content', null, ' | ') ?>
<!-- Output: Design | Development | Marketing -->
```

**Smart Detection:**
If the 4th parameter (`dateFormat`) looks like a separator (contains `,`, `|`, `;`, `:`, or spaces but no date format characters), it will be used as a separator instead of a date format:

```php
<?= catalogue('tags', '', 'content', ', ') ?>
<!-- Automatically detected as separator, not date format -->
```

**Use Cases:**
- Meta tags (keywords, categories)
- Comma-separated lists
- Pipe-separated values
- Any array field that needs custom formatting

See [Tags](./TAGS.md) and [Select Fields](./SELECT_FIELDS.md) for more examples.

## Field Type Rendering

`catalogue()` automatically renders fields based on their blueprint type:

| Field Type | Output |
|------------|--------|
| `text` | Escaped HTML string |
| `textarea` | Escaped HTML string |
| `markdown` | Rendered HTML (markdown converted) |
| `tags` | Formatted HTML spans |
| `checkbox` | Formatted HTML spans |
| `file` | Pre-rendered image HTML |
| `select` | Option label (if options defined) |
| `radio` | Option label (if options defined) |
| `switch` | Boolean-like value (works in conditionals) |
| `slider` | Numeric value |

## Dot Notation

Access nested values using dot notation:

```php
<?= catalogue('image.src') ?>
<?= catalogue('author.name') ?>
```

## Context Awareness

`catalogue()` automatically detects context:

- **Page content** - Current page being rendered
- **Collection item** - When inside `catalogueCollection()` loop
- **File metadata** - When inside `catalogueFiles()` loop
- **Structure item** - When inside `catalogueStructure()` loop

## Examples

### Page Content

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <div class="content"><?= catalogue('content') ?></div>
</article>
```

### Collection Item (inside loop)

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <h2><?= catalogue('title') ?></h2>
    <p><?= catalogue('description') ?></p>
<?php endforeach; ?>
```

### File Metadata (inside loop)

```php
<?php foreach (catalogueFiles('files') as $file): ?>
    <?= catalogue('image') ?>
    <p><?= catalogue('caption') ?></p>
<?php endforeach; ?>
```

### Structure Item (inside loop)

```php
<?php foreach (catalogueStructure('settings') as $item): ?>
    <?= catalogue('title') ?>
    <?= catalogue('value') ?>
<?php endforeach; ?>
```

## See Also

- [Text Fields](./TEXT_FIELDS.md)
- [Markdown Content](./MARKDOWN.md)
- [Date Formatting](./DATE_FORMATTING.md)
- [Files & Images](./FILES.md)
- [Collections](./COLLECTIONS.md)

