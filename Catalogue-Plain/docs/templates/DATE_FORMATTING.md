# Date Formatting

Format date fields using PHP date format strings.

## Basic Usage

Use the `dateFormat` parameter in `catalogue()`:

```php
<?= catalogue('created_at', '', 'content', 'F j, Y') ?>
<!-- Output: November 24, 2025 -->
```

Or use the convenience function `catalogueDate()`:

```php
<?= catalogueDate('created_at', 'F j, Y') ?>
<!-- Same as: catalogue('created_at', '', 'content', 'F j, Y') -->
```

## Date Format Strings

Date formatting uses PHP's `date()` format strings. Common formats:

| Format | Example Output | Description |
|--------|---------------|-------------|
| `'F j, Y'` | November 24, 2025 | Full month name, day, year |
| `'M j, Y'` | Nov 24, 2025 | Short month name, day, year |
| `'D, M j'` | Mon, Nov 27 | Day of week, short month, day |
| `'l, F j'` | Monday, November 24 | Full day name, full month, day |
| `'Y-m-d'` | 2025-11-24 | ISO date format |
| `'m/d/Y'` | 11/24/2025 | US date format |
| `'d/m/Y'` | 24/11/2025 | European date format |
| `'F j, Y g:i A'` | November 24, 2025 3:30 PM | Date with time |
| `'M j, Y \a\t g:i A'` | Nov 24, 2025 at 3:30 PM | Date with "at" separator |

## Common Format Characters

| Character | Description | Example |
|-----------|-------------|---------|
| `Y` | 4-digit year | 2025 |
| `y` | 2-digit year | 25 |
| `F` | Full month name | November |
| `M` | Short month name | Nov |
| `m` | Numeric month (01-12) | 11 |
| `j` | Day of month (1-31) | 24 |
| `d` | Day of month (01-31) | 24 |
| `l` | Full day name | Monday |
| `D` | Short day name | Mon |
| `g` | 12-hour format (1-12) | 3 |
| `G` | 24-hour format (0-23) | 15 |
| `i` | Minutes (00-59) | 30 |
| `A` | AM/PM | PM |
| `a` | am/pm | pm |

## Examples

### Collection Item Dates

```php
<?php foreach (catalogueCollection('posts') as $post): ?>
    <article>
        <h2><?= catalogue('title') ?></h2>
        <time datetime="<?= catalogue('created_at') ?>">
            <?= catalogueDate('created_at', 'F j, Y') ?>
        </time>
        <p><?= catalogue('description') ?></p>
    </article>
<?php endforeach; ?>
```

### Metadata Timestamps

```php
<p>Created: <?= catalogueDate('created_at', 'F j, Y') ?></p>
<p>Last updated: <?= catalogueDate('updated_at', 'M j, Y \a\t g:i A') ?></p>
```

### Conditional Date Display

```php
<?php if (catalogue('created_at')): ?>
    <p>Published on <?= catalogueDate('created_at', 'l, F j, Y') ?></p>
<?php endif; ?>
```

### Different Formats for Same Date

```php
<!-- Human-readable -->
<p><?= catalogueDate('created_at', 'F j, Y') ?></p>
<!-- Output: November 24, 2025 -->

<!-- Machine-readable (for datetime attribute) -->
<time datetime="<?= catalogue('created_at') ?>">
    <?= catalogueDate('created_at', 'F j, Y') ?>
</time>
```

## Date Sources

Date formatting works with any date field:

- **Collection metadata**: `created_at`, `updated_at`
- **Page metadata**: `created_at`, `updated_at`
- **Custom date fields**: Any field containing ISO 8601 dates

## ISO 8601 Format

Dates are stored in ISO 8601 format (`2025-11-24T21:05:02+00:00`). The formatting function automatically detects and converts these dates.

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md) - Main function documentation
- [Collections](./COLLECTIONS.md) - Working with collection items
- [PHP Date Format Reference](https://www.php.net/manual/en/datetime.format.php) - Complete format reference

