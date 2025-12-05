# Authentication Security

Login, session management, and user authentication security.

## Overview

The CMS uses session-based authentication with secure password hashing and session management.

## Password Security

### Password Hashing

Passwords are hashed using PHP's `password_hash()`:

```php
$hash = password_hash($password, PASSWORD_DEFAULT);
// Uses bcrypt algorithm
// Cost factor: 10 (default)
```

### Password Verification

Passwords verified using `password_verify()`:

```php
if (password_verify($password, $hash)) {
    // Password correct
}
```

### Password Requirements

- **Minimum length**: 6 characters
- **No maximum length**
- **No complexity requirements** (can be enhanced)

### Password Storage

- Passwords stored as bcrypt hashes
- Never stored in plaintext
- Hashes never returned in API responses
- One-way hashing (cannot be decrypted)

## Session Security

### Session Configuration

Sessions configured with secure parameters:

```php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']), // HTTPS only if available
    'httponly' => true,                   // No JavaScript access
    'samesite' => 'Strict'                // CSRF protection
]);
```

### Session Regeneration

Session ID regenerated on login:

```php
session_regenerate_id(true);
// Prevents session fixation attacks
```

### Session Timeout

Sessions expire after configured time:

```php
define('SESSION_LIFETIME', 28800); // 8 hours
```

Timeout checked on each request.

## Login Process

### Steps

1. **Rate Limit Check** - Verify not rate-limited
2. **User Lookup** - Find user by username
3. **Password Verification** - Verify password hash
4. **Session Creation** - Create secure session
5. **Session Regeneration** - Regenerate session ID
6. **Clear Failed Attempts** - Reset rate limit

### Failed Login Handling

- Failed attempts recorded
- Rate limiting enforced
- Generic error message (no information disclosure)
- Attempts tracked per IP + username

## Rate Limiting

### Protection

Login attempts rate-limited to prevent brute force:

- **Max attempts**: 5 failed attempts
- **Lockout period**: 15 minutes
- **Tracking**: Per IP address + username
- **Auto-reset**: Clears after lockout period

### Implementation

```php
function checkLoginRateLimit($username) {
    // Check attempts
    // Return false if rate-limited
    // Return true if allowed
}
```

### Rate Limit File

Stored in:
```
/catalogue/logs/login_attempts.json
```

## User Management

### Creating Users

- Username validation (alphanumeric + underscore/hyphen)
- Password hashing automatic
- Timestamps tracked
- Metadata stored

### Updating Users

- Password optional (leave blank to keep current)
- Username changes handled
- Old files cleaned up
- Timestamps updated

### Deleting Users

- Cannot delete yourself
- Permanent deletion
- User file removed
- No content impact

## Security Features

### Secure Cookies

- **HttpOnly** - Prevents JavaScript access
- **Secure** - Only sent over HTTPS (when available)
- **SameSite** - CSRF protection

### Session Data

Session stores:
- `user_logged_in` - Boolean flag
- `username` - Current user
- `login_time` - Login timestamp

### Session Validation

- Timeout checked on each request
- Session data validated
- Invalid sessions cleared

## Best Practices

### Password Security

✅ **Do:**
- Use strong passwords (12+ characters)
- Include uppercase, lowercase, numbers, symbols
- Use unique passwords
- Change passwords regularly

❌ **Don't:**
- Use common passwords
- Share passwords
- Store passwords in plaintext
- Reuse passwords

### User Management

✅ **Do:**
- Create separate accounts for each user
- Use descriptive usernames
- Regularly review user accounts
- Delete unused accounts

❌ **Don't:**
- Share accounts
- Use generic usernames
- Leave old accounts active
- Use admin account for daily tasks

## Examples

### Secure Login

```php
if (login($username, $password)) {
    // Session created
    // User authenticated
    header('Location: /dashboard');
} else {
    // Failed attempt recorded
    // Rate limiting checked
    echo 'Invalid credentials';
}
```

### Password Reset

If password forgotten:
1. Access server directly
2. Edit user JSON file
3. Generate new password hash
4. Update file
5. User can login with new password

## See Also

- [Session Security](./SESSION_SECURITY.md) - Session management
- [CSRF Protection](./CSRF.md) - Form security
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

