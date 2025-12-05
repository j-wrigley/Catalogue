# Input Validation

Input sanitization and validation security.

## Overview

All user input is validated and sanitized before use to prevent injection attacks and ensure data integrity.

## Validation Rules

### Username Validation

```php
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    // Invalid username
}
```

**Allowed:** Letters, numbers, underscores, hyphens  
**Rejected:** Special characters, spaces

### Content Type Validation

```php
$content_type = preg_replace('/[^a-z0-9_-]/i', '', $content_type);
```

**Allowed:** Letters, numbers, underscores, hyphens  
**Rejected:** Special characters, spaces

### Filename Sanitization

```php
function sanitizeFilename($filename) {
    // Preserves extension
    // Removes special characters
    // Replaces with hyphens
}
```

**Allowed:** Letters, numbers, dots, hyphens, underscores  
**Rejected:** Path separators, special characters

### Slug Validation

```php
$slug = preg_replace('/[^a-z0-9-]/i', '-', $slug);
$slug = preg_replace('/-+/', '-', $slug);
$slug = trim($slug, '-');
```

**Allowed:** Letters, numbers, hyphens  
**Rejected:** Special characters, spaces

## Input Sanitization

### Query Parameters

```php
$page = $_GET['page'] ?? 'dashboard';
$page = preg_replace('/[^a-z0-9_-]/i', '', $page);
```

### Form Data

```php
$username = trim($_POST['username'] ?? '');
$username = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
```

### File Paths

```php
$filename = basename($filename); // Remove path components
$filename = sanitizeFilename($filename);
```

## JSON Validation

### Validating JSON Strings

```php
function isValidJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}
```

### Parsing JSON Safely

```php
$data = json_decode($json_string, true);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    // Invalid JSON
    return false;
}
```

## Type Validation

### String Validation

```php
if (!is_string($value)) {
    // Invalid type
}
```

### Array Validation

```php
if (!is_array($data)) {
    // Invalid type
}
```

### Integer Validation

```php
$id = filter_var($id, FILTER_VALIDATE_INT);
if ($id === false) {
    // Invalid integer
}
```

## Whitelist Approach

### Content Types

Only allow known content types:

```php
$allowed_types = ['about', 'contact', 'home'];
if (!in_array($content_type, $allowed_types)) {
    // Reject
}
```

### File Extensions

Only allow safe extensions:

```php
$allowed_extensions = ['jpg', 'png', 'gif', 'pdf'];
if (!in_array($ext, $allowed_extensions)) {
    // Reject
}
```

## Examples

### Validating Username

```php
$username = trim($_POST['username'] ?? '');

if (empty($username)) {
    echo 'Username required';
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    echo 'Invalid username format';
    exit;
}

// Username is safe to use
```

### Validating Content Type

```php
$content_type = $_POST['content_type'] ?? '';
$content_type = preg_replace('/[^a-z0-9_-]/i', '', $content_type);

if (empty($content_type)) {
    echo 'Content type required';
    exit;
}

// Content type is safe
```

### Validating JSON Input

```php
$json_data = $_POST['content_data'] ?? '';

if (!isValidJson($json_data)) {
    echo 'Invalid JSON';
    exit;
}

$data = json_decode($json_data, true);
// Data is safe to use
```

## Best Practices

### Validate Early

✅ **Do:**
- Validate input immediately
- Reject invalid input early
- Use whitelist approach
- Validate type and format

❌ **Don't:**
- Trust user input
- Validate only on output
- Use blacklist approach
- Skip validation

### Sanitize Consistently

```php
// ✅ Consistent sanitization
$input = sanitizeInput($raw_input);

// ❌ Inconsistent sanitization
$input = $raw_input; // Sometimes sanitized, sometimes not
```

### Type Checking

```php
// ✅ Type checking
if (!is_string($value)) {
    return false;
}

// ❌ No type checking
$value = $_POST['value']; // Could be anything
```

## Security Benefits

### Protection Against

- **Injection Attacks** - Invalid characters rejected
- **Path Traversal** - Path components removed
- **Type Confusion** - Types validated
- **Data Corruption** - Invalid data rejected

## See Also

- [XSS Protection](./XSS.md) - Output escaping
- [File Security](./FILE_SECURITY.md) - File validation
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

