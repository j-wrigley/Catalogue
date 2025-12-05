# Session Security

Secure session management and configuration.

## Overview

Sessions are configured with secure parameters to prevent session hijacking and ensure secure authentication.

## Session Configuration

### Cookie Parameters

Sessions use secure cookie settings:

```php
session_set_cookie_params([
    'lifetime' => 0,                    // Session cookie
    'path' => '/',                      // Available site-wide
    'domain' => '',                     // Current domain
    'secure' => isset($_SERVER['HTTPS']), // HTTPS only if available
    'httponly' => true,                 // No JavaScript access
    'samesite' => 'Strict'              // CSRF protection
]);
```

### Session Name

Custom session name:

```php
session_name('cms_session');
```

### Session Lifetime

Configurable session timeout:

```php
define('SESSION_LIFETIME', 28800); // 8 hours
```

## Security Features

### HttpOnly Flag

Prevents JavaScript access to session cookie:

```php
'httponly' => true
```

**Benefit:** Prevents XSS attacks from stealing session cookies

### Secure Flag

Only sends cookie over HTTPS:

```php
'secure' => isset($_SERVER['HTTPS'])
```

**Benefit:** Prevents cookie theft over unencrypted connections

### SameSite Flag

CSRF protection:

```php
'samesite' => 'Strict'
```

**Benefit:** Prevents cross-site request forgery

## Session Regeneration

### On Login

Session ID regenerated on successful login:

```php
session_regenerate_id(true);
```

**Benefit:** Prevents session fixation attacks

### Process

1. User logs in
2. Session ID regenerated
3. Old session invalidated
4. New session created

## Session Timeout

### Timeout Check

Session timeout checked on each request:

```php
function checkSessionTimeout() {
    if (isset($_SESSION['login_time'])) {
        $elapsed = time() - $_SESSION['login_time'];
        if ($elapsed > SESSION_LIFETIME) {
            logout();
            return false;
        }
    }
    return true;
}
```

### Automatic Logout

If session expired:
- User logged out automatically
- Redirected to login page
- Session data cleared

## Session Data

### Stored Data

Session stores minimal data:

```php
$_SESSION['user_logged_in'] = true;
$_SESSION['username'] = $username;
$_SESSION['login_time'] = time();
```

### Security Notes

- No sensitive data in session
- No passwords stored
- No file paths stored
- Only authentication flags

## Session Validation

### Login Check

```php
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && 
           $_SESSION['user_logged_in'] === true;
}
```

### Require Login

```php
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}
```

## Best Practices

### Session Management

✅ **Do:**
- Regenerate session on login
- Set secure cookie parameters
- Check session timeout
- Store minimal data
- Clear session on logout

❌ **Don't:**
- Store sensitive data
- Skip session regeneration
- Use insecure cookies
- Extend session indefinitely
- Share sessions between users

### Cookie Security

✅ **Do:**
- Use HttpOnly flag
- Use Secure flag (HTTPS)
- Use SameSite flag
- Set appropriate path
- Use custom session name

❌ **Don't:**
- Allow JavaScript access
- Send over HTTP (if HTTPS available)
- Use default session name
- Set overly broad domain

## Examples

### Secure Session Start

```php
session_name('cms_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
```

### Session Regeneration

```php
if (login($username, $password)) {
    session_regenerate_id(true);
    $_SESSION['user_logged_in'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
}
```

### Session Timeout Check

```php
if (!checkSessionTimeout()) {
    logout();
    header('Location: /login');
    exit;
}
```

## See Also

- [Authentication](./AUTHENTICATION.md) - Login security
- [CSRF Protection](./CSRF.md) - Form security
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

