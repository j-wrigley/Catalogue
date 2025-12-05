# File Security

File upload security and path traversal protection.

## Overview

File operations are secured through path validation, upload restrictions, and directory isolation.

## Path Traversal Protection

### Real Path Validation

All file paths validated using `realpath()`:

```php
$real_path = realpath($filepath);
$real_base = realpath($base_dir);

if (strpos($real_path, $real_base) !== 0) {
    // Path traversal attempt - blocked
    return false;
}
```

### Path Component Stripping

Filenames sanitized to remove path components:

```php
$filename = basename($filename); // Removes ../../
```

### Directory Validation

Directories validated before access:

```php
$real_dir = realpath($directory);
if ($real_dir === false) {
    // Invalid directory
    return false;
}
```

## File Upload Security

### MIME Type Validation

Files validated using MIME type detection:

```php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mime_type, $allowed_types)) {
    // Invalid file type - rejected
}
```

### File Extension Whitelist

Only safe extensions allowed:

```php
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowed_extensions)) {
    // Invalid extension - rejected
}
```

### Image Validation

Images validated using `getimagesize()`:

```php
$image_info = @getimagesize($file['tmp_name']);
if ($image_info === false) {
    // Not a valid image - rejected
}
```

### File Size Limits

File size restricted:

```php
$max_size = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $max_size) {
    // File too large - rejected
}
```

### Filename Sanitization

Filenames sanitized:

```php
function sanitizeFilename($filename) {
    // Removes special characters
    // Preserves extension
    // Replaces spaces with hyphens
}
```

### Unique Filenames

Filenames made unique to prevent overwrites:

```php
$filename = $base . '-' . time() . '.' . $extension;
```

## Directory Isolation

### Upload Directory

Uploads stored in isolated directory:

```
/catalogue/uploads/
```

### Access Control

`.htaccess` restricts access:

```apache
# Allow only images and safe files
<FilesMatch "\.(jpg|jpeg|png|gif|webp|pdf)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
```

### Path Restrictions

Upload directory isolated from web root:

- Uploads cannot access parent directories
- Path validation prevents traversal
- Files cannot execute PHP

## File Operations

### Atomic Writes

Files written atomically:

```php
// Write to temp file
$temp_file = $filepath . '.tmp';
file_put_contents($temp_file, $data);

// Rename (atomic)
rename($temp_file, $filepath);
```

### Error Handling

File operations handle errors:

```php
if (!file_put_contents($filepath, $data)) {
    error_log("Failed to write file: $filepath");
    return false;
}
```

## Security Measures

### Path Validation Checklist

- ✅ All paths use `realpath()`
- ✅ Paths validated against base directories
- ✅ `basename()` strips path components
- ✅ Directory traversal attempts logged

### Upload Validation Checklist

- ✅ MIME type validation
- ✅ File extension whitelist
- ✅ Image validation
- ✅ File size limits
- ✅ Filename sanitization
- ✅ Unique filenames

## Examples

### Safe File Read

```php
$filepath = BASE_DIR . '/' . $filename;
$real_filepath = realpath($filepath);
$real_base = realpath(BASE_DIR);

if ($real_filepath === false || strpos($real_filepath, $real_base) !== 0) {
    // Path traversal attempt - blocked
    return false;
}

$content = file_get_contents($real_filepath);
```

### Safe File Upload

```php
// Validate MIME type
$mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);
if (!in_array($mime_type, $allowed_types)) {
    return false;
}

// Validate extension
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, $allowed_extensions)) {
    return false;
}

// Validate size
if ($file['size'] > $max_size) {
    return false;
}

// Sanitize filename
$filename = sanitizeFilename($file['name']);

// Move to upload directory
move_uploaded_file($file['tmp_name'], UPLOADS_DIR . '/' . $filename);
```

## Best Practices

### File Operations

✅ **Do:**
- Validate all file paths
- Use `realpath()` for validation
- Sanitize filenames
- Use atomic writes
- Validate file types

❌ **Don't:**
- Trust user-provided paths
- Skip path validation
- Allow arbitrary file types
- Store files in web root
- Execute uploaded files

### Upload Security

✅ **Do:**
- Validate MIME types
- Use extension whitelist
- Validate images
- Limit file sizes
- Sanitize filenames
- Isolate upload directory

❌ **Don't:**
- Trust file extensions
- Allow executable files
- Store in web root
- Skip validation
- Allow large files

## See Also

- [Input Validation](./INPUT_VALIDATION.md) - Input sanitization
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

