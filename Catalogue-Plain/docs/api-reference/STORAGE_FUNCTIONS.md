# Storage Functions

Functions for reading, writing, and managing JSON files.

## `readJson()`

Read JSON file and return decoded data.

### Syntax

```php
readJson(string $filepath): array|null
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filepath` | string | Path to JSON file |

### Returns

Decoded array on success, `null` on failure.

### Examples

```php
$content = readJson('/path/to/file.json');
$settings = readJson(PAGES_DIR . '/settings/settings.json');
$user = readJson(CONTENT_DIR . '/users/admin.json');
```

### Error Handling

Returns `null` if:
- File doesn't exist
- File is not readable
- JSON is invalid
- File is empty

### Usage

```php
$data = readJson('/path/to/content.json');
if ($data) {
    echo $data['title'];
} else {
    echo 'File not found';
}
```

---

## `writeJson()`

Write data to JSON file.

### Syntax

```php
writeJson(string $filepath, array $data): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filepath` | string | Path to JSON file |
| `$data` | array | Data to write |

### Returns

`true` on success, `false` on failure.

### Examples

```php
$success = writeJson('/path/to/file.json', ['title' => 'Page']);
writeJson(PAGES_DIR . '/about/about.json', $content);
```

### Features

- **Atomic writes** - Writes to temp file then renames
- **Auto-creates directories** - Creates parent directories if needed
- **Pretty formatting** - JSON formatted with indentation
- **Error handling** - Returns false on failure

### Usage

```php
$data = [
    'title' => 'My Page',
    'content' => 'Page content'
];

if (writeJson('/path/to/page.json', $data)) {
    echo 'Saved successfully';
} else {
    echo 'Failed to save';
}
```

---

## `listJsonFiles()`

List all JSON files in a directory.

### Syntax

```php
listJsonFiles(string $directory): array
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$directory` | string | Directory path |

### Returns

Array of file paths.

### Examples

```php
$files = listJsonFiles(PAGES_DIR . '/about');
$items = listJsonFiles(COLLECTIONS_DIR . '/posts');
$users = listJsonFiles(CONTENT_DIR . '/users');
```

### Features

- **Recursive** - Scans subdirectories
- **Filtered** - Only returns `.json` files
- **Sorted** - Files sorted alphabetically

### Usage

```php
$jsonFiles = listJsonFiles('/path/to/directory');
foreach ($jsonFiles as $file) {
    $content = readJson($file);
    echo $content['title'];
}
```

---

## `deleteJson()`

Delete JSON file.

### Syntax

```php
deleteJson(string $filepath): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filepath` | string | Path to JSON file |

### Returns

`true` on success, `false` on failure.

### Examples

```php
deleteJson('/path/to/file.json');
deleteJson(CONTENT_DIR . '/users/olduser.json');
```

### Error Handling

Returns `false` if:
- File doesn't exist
- File is not writable
- Deletion fails

### Usage

```php
if (deleteJson('/path/to/file.json')) {
    echo 'Deleted successfully';
} else {
    echo 'Failed to delete';
}
```

---

## File Path Constants

Common path constants used with storage functions:

| Constant | Description |
|----------|-------------|
| `CMS_ROOT` | CMS root directory |
| `CONTENT_DIR` | Content directory |
| `PAGES_DIR` | Pages directory |
| `COLLECTIONS_DIR` | Collections directory |
| `DATA_DIR` | Public data directory |
| `UPLOADS_DIR` | Uploads directory |

### Examples

```php
// Read page content
$page = readJson(PAGES_DIR . '/about/about.json');

// Write collection item
writeJson(COLLECTIONS_DIR . '/posts/item.json', $data);

// List users
$users = listJsonFiles(CONTENT_DIR . '/users');
```

---

## Best Practices

### Error Handling

Always check return values:

```php
$data = readJson($filepath);
if (!$data) {
    // Handle error
    return;
}
// Use $data
```

### Atomic Writes

`writeJson()` uses atomic writes:
- Writes to temp file first
- Renames on success
- Prevents corruption on failure

### File Permissions

Ensure directories are writable:

```php
chmod 755 /path/to/directory
chmod 644 /path/to/file.json
```

---

## See Also

- [Template Functions](./TEMPLATE_FUNCTIONS.md) - Content access
- [Utility Functions](./UTILITY_FUNCTIONS.md) - Helper functions

