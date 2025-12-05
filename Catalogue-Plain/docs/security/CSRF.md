# CSRF Protection

Cross-Site Request Forgery (CSRF) protection implementation.

## Overview

CSRF protection prevents unauthorized form submissions and actions by requiring a valid token on all POST requests.

## How It Works

### Token Generation

CSRF tokens are generated using cryptographically secure random bytes:

```php
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// 64-character hex string
```

### Token Storage

- Stored in session
- Expires after 1 hour
- Regenerated if expired
- One token per session

### Token Validation

Tokens validated on all POST requests:

```php
if (!validateCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}
```

## Implementation

### Generating Tokens

```php
$token = generateCsrfToken();
// Returns: "abc123def456..."
```

### Including in Forms

```php
<form method="POST">
    <?= csrfField() ?>
    <!-- Form fields -->
</form>
```

Outputs:
```html
<input type="hidden" name="csrf_token" value="abc123...">
```

### Validating Tokens

```php
$token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($token)) {
    // Invalid token - reject request
}
```

## Token Expiration

### Expiry Time

Tokens expire after 1 hour:

```php
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
```

### Auto-Regeneration

If token expired:
- New token generated automatically
- Old token invalidated
- New token stored in session

## Timing-Safe Comparison

### Implementation

Uses `hash_equals()` for timing-safe comparison:

```php
return hash_equals($_SESSION['csrf_token'], $token);
```

### Why Important

Prevents timing attacks:
- Comparison takes same time regardless of token
- Attackers cannot determine token characters
- More secure than `===` comparison

## Coverage

### Protected Endpoints

All POST endpoints require CSRF tokens:

- Content save (`save.php`)
- User management (`user-save.php`, `user-delete.php`)
- Media upload (`upload.php`, `media.php`)
- Item deletion (`item-delete.php`)
- Regeneration (`regenerate-all.php`)
- Traffic reset (`reset-traffic.php`)
- CMS settings (`save-cms-settings.php`)

### Unprotected Endpoints

- GET requests (read-only)
- Login page (uses session-based protection)

## Usage Examples

### Form with CSRF Token

```php
<form method="POST" action="/save">
    <?= csrfField() ?>
    <input type="text" name="title">
    <button type="submit">Save</button>
</form>
```

### AJAX Request

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

### Manual Validation

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

## Security Benefits

### Protection Against

- **Unauthorized Actions** - Prevents actions without valid token
- **Cross-Site Requests** - Blocks requests from other sites
- **Session Hijacking** - Token tied to session
- **Replay Attacks** - Tokens expire

### Attack Scenario Prevented

1. Attacker creates malicious form
2. Form submits to your site
3. **CSRF protection blocks** - Token missing or invalid
4. Action rejected

## Best Practices

### Always Include Tokens

✅ **Do:**
- Include CSRF token in all forms
- Validate tokens on all POST requests
- Use `csrfField()` helper function
- Regenerate tokens on expiration

❌ **Don't:**
- Skip CSRF tokens
- Expose tokens in URLs (use POST)
- Reuse tokens across sessions
- Store tokens in cookies (use sessions)

## Troubleshooting

### Invalid Token Error

**Issue:** "Invalid CSRF token" error

**Solutions:**
1. Ensure token included in form
2. Check session is active
3. Verify token not expired
4. Check token matches session

### Token Expired

**Issue:** Token expires during form completion

**Solutions:**
1. Increase token expiry time
2. Regenerate token on page load
3. Handle expiry gracefully

## See Also

- [Session Security](./SESSION_SECURITY.md) - Session management
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

