# Utility Functions

Helper functions for common operations.

## `slugify()`

Generate URL-safe slug from string.

### Syntax

```php
slugify(string $string): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$string` | string | String to convert |

### Returns

URL-safe slug string.

### Examples

```php
$slug = slugify('My Awesome Page'); // 'my-awesome-page'
$slug = slugify('Hello World!');    // 'hello-world'
$slug = slugify('Test 123');        // 'test-123'
```

### Features

- Converts to lowercase
- Removes special characters
- Replaces spaces with hyphens
- Removes multiple hyphens
- Trims hyphens from ends

---

## `getTimestamp()`

Get current timestamp in ISO 8601 format.

### Syntax

```php
getTimestamp(): string
```

### Parameters

None.

### Returns

ISO 8601 formatted timestamp string.

### Examples

```php
$now = getTimestamp(); // '2025-01-15T12:30:45+00:00'
```

### Format

Returns: `Y-m-d\TH:i:sP` (ISO 8601)

---

## `sanitizeFilename()`

Sanitize filename for safe storage.

### Syntax

```php
sanitizeFilename(string $filename): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filename` | string | Filename to sanitize |

### Returns

Sanitized filename.

### Examples

```php
$safe = sanitizeFilename('My File (1).txt'); // 'My-File-1.txt'
$safe = sanitizeFilename('test@file.json');   // 'test-file.json'
```

### Features

- Preserves file extension
- Removes special characters
- Replaces with hyphens
- Removes multiple hyphens
- Trims hyphens

---

## `getFileExtension()`

Get file extension from filename.

### Syntax

```php
getFileExtension(string $filename): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filename` | string | Filename |

### Returns

Lowercase file extension (without dot).

### Examples

```php
$ext = getFileExtension('image.jpg');  // 'jpg'
$ext = getFileExtension('file.PNG');   // 'png'
$ext = getFileExtension('document.pdf'); // 'pdf'
```

---

## `isValidJson()`

Check if string is valid JSON.

### Syntax

```php
isValidJson(string $string): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$string` | string | String to validate |

### Returns

`true` if valid JSON, `false` otherwise.

### Examples

```php
if (isValidJson($data)) {
    $array = json_decode($data, true);
}
```

---

## `getRelativePath()`

Get relative path from CMS root.

### Syntax

```php
getRelativePath(string $path): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$path` | string | Absolute path |

### Returns

Relative path from CMS root.

### Examples

```php
$relative = getRelativePath('/full/path/to/file.json');
```

---

## `formatDate()`

Format date string for display.

### Syntax

```php
formatDate(string $date_string, string $format = 'short'): string
```

### Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `$date_string` | string | ISO 8601 date string | Required |
| `$format` | string | Format (`short` or `long`) | `'short'` |

### Returns

Formatted date string.

### Examples

```php
formatDate('2025-01-15T12:30:45+00:00');           // 'Jan 15, 2025'
formatDate('2025-01-15T12:30:45+00:00', 'long');   // 'January 15, 2025 at 12:30 PM'
```

### Short Format

- **Just now** - Less than 1 minute
- **X minutes ago** - Less than 1 hour
- **Today at X:XX PM** - Today
- **Yesterday at X:XX PM** - Yesterday
- **X days ago** - Less than a week
- **Jan 15** - Current year
- **Jan 15, 2024** - Different year

### Long Format

- **January 15, 2025 at 12:30 PM**

---

## `getCmsName()`

Get CMS name from settings or fallback.

### Syntax

```php
getCmsName(): string
```

### Parameters

None.

### Returns

CMS name string.

### Examples

```php
$name = getCmsName(); // 'JSON Catalogue' or custom name
```

### Behavior

1. Checks CMS settings file
2. Falls back to `SITE_NAME` constant
3. Falls back to `'JSON Catalogue'`

---

## Examples

### Creating Slugs

```php
$title = 'My Awesome Page';
$slug = slugify($title); // 'my-awesome-page'
```

### Formatting Dates

```php
$date = '2025-01-15T12:30:45+00:00';
echo formatDate($date);        // 'Jan 15, 2025'
echo formatDate($date, 'long'); // 'January 15, 2025 at 12:30 PM'
```

### Sanitizing Filenames

```php
$filename = 'My File (1).txt';
$safe = sanitizeFilename($filename); // 'My-File-1.txt'
```

### Getting File Extensions

```php
$ext = getFileExtension('image.jpg'); // 'jpg'
if ($ext === 'jpg' || $ext === 'png') {
    // Handle image
}
```

---

## See Also

- [Storage Functions](./STORAGE_FUNCTIONS.md) - File operations
- [Render Functions](./RENDER_FUNCTIONS.md) - Escaping functions

