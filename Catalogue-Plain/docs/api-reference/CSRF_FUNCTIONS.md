# CSRF Functions

Functions for generating and validating CSRF tokens.

## `generateCsrfToken()`

Generate CSRF token for forms.

### Syntax

```php
generateCsrfToken(): string
```

### Parameters

None.

### Returns

CSRF token string.

### Examples

```php
$token = generateCsrfToken();
```

### Features

- **Random generation** - Uses `random_bytes()`
- **Session storage** - Stored in session
- **Expiration** - Tokens expire after configured time
- **Auto-regeneration** - Regenerates if expired

### Token Format

- 64-character hex string
- Generated using `bin2hex(random_bytes(32))`
- Stored in `$_SESSION['csrf_token']`

---

## `validateCsrfToken()`

Validate CSRF token.

### Syntax

```php
validateCsrfToken(string $token): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$token` | string | Token to validate |

### Returns

`true` if valid, `false` otherwise.

### Examples

```php
$token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($token)) {
    http_response_code(403);
    echo 'Invalid CSRF token';
    exit;
}
```

### Validation Rules

- Token must exist in session
- Token must not be expired
- Token must match exactly (timing-safe comparison)

---

## `csrfField()`

Get CSRF token HTML input field.

### Syntax

```php
csrfField(): string
```

### Parameters

None.

### Returns

HTML string with hidden input field.

### Examples

```php
<form method="POST">
    <?= csrfField() ?>
    <!-- Form fields -->
</form>
```

### Output

```html
<input type="hidden" name="csrf_token" value="abc123...">
```

---

## Usage Examples

### In Forms

```php
<form method="POST" action="/save">
    <?= csrfField() ?>
    <input type="text" name="title">
    <button type="submit">Save</button>
</form>
```

### Manual Token Generation

```php
$token = generateCsrfToken();
?>
<input type="hidden" name="csrf_token" value="<?= esc_attr($token) ?>">
```

### Validating on Submit

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }
    // Process form
}
```

### AJAX Requests

```php
// In form
<input type="hidden" name="csrf_token" value="<?= esc_attr(generateCsrfToken()) ?>">

// In JavaScript
const formData = new FormData(form);
fetch('/endpoint', {
    method: 'POST',
    body: formData
});
```

---

## Token Expiration

### Configuration

Token expiration configured in `config.php`:

```php
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
```

### Behavior

- Tokens expire after configured time
- Expired tokens are rejected
- New tokens generated automatically
- Expiration checked on validation

---

## Security Notes

### Why CSRF Protection?

CSRF (Cross-Site Request Forgery) protection prevents:
- Unauthorized form submissions
- Unauthorized actions
- Session hijacking attacks

### Best Practices

- **Always** include CSRF tokens in forms
- **Always** validate tokens on submission
- **Never** expose tokens in URLs (use POST)
- **Always** use `csrfField()` helper

### Timing-Safe Comparison

`validateCsrfToken()` uses `hash_equals()` for timing-safe comparison, preventing timing attacks.

---

## See Also

- [Security Documentation](../security/) - Security guide
- [Authentication Functions](./AUTHENTICATION_FUNCTIONS.md) - User management

