# Authentication

Login, logout, and session management.

## Login

### Accessing Login

Navigate to:
```
/catalogue/index.php?page=login
```

Or access any protected page and you'll be redirected to login.

### Login Form

Enter your:
- **Username** - Your user account username
- **Password** - Your account password

Click **"Sign in"** to log in.

### Successful Login

After successful login, you'll be redirected to the dashboard.

### Failed Login

If login fails:
- Error message: "Invalid username or password"
- Failed attempts are recorded for rate limiting

## Logout

### How to Logout

1. Click your username in the sidebar footer
2. Click **"Logout"**
3. You'll be redirected to the login page

### Session Cleanup

On logout:
- Session data is cleared
- Session cookie is deleted
- Session is destroyed

## Session Management

### Session Lifetime

- Sessions expire after a configured timeout period
- Default timeout: 24 hours (configurable in `config.php`)
- Session timeout is checked on each page load

### Session Security

- **Session ID regeneration** - Session ID is regenerated on login
- **Secure cookies** - Session cookies use secure settings
- **HttpOnly** - Cookies are not accessible via JavaScript
- **SameSite** - CSRF protection via SameSite cookie attribute

## Rate Limiting

### Protection

Login attempts are rate-limited to prevent brute force attacks:

- **Max attempts**: 5 failed attempts
- **Lockout period**: 15 minutes
- **Tracking**: Per IP address and username combination

### Rate Limit Behavior

1. After 5 failed attempts, login is blocked
2. Error message: "Too many failed login attempts. Please try again in 15 minutes."
3. After 15 minutes, attempts reset automatically

### Successful Login

Successful login clears failed attempt records for that user/IP.

## Examples

### First-Time Login

1. Navigate to login page
2. Enter username and password
3. Click "Sign in"
4. Redirected to dashboard

### Forgot Password

If you forget your password:
1. Access user file directly: `/catalogue/content/users/{username}.json`
2. Replace password hash with new hash
3. Use PHP's `password_hash()` function to generate hash

**Example:**
```php
<?php
echo password_hash('newpassword', PASSWORD_DEFAULT);
?>
```

### Multiple Users

Each user logs in with their own credentials:
- User A: `admin` / `password123`
- User B: `editor` / `password456`

Both can access the CMS simultaneously from different browsers/devices.

## Security Features

- **Password hashing** - Passwords stored as bcrypt hashes
- **Rate limiting** - Brute force protection
- **Session security** - Secure session handling
- **CSRF protection** - Token-based form protection
- **Path validation** - Prevents directory traversal

## See Also

- [User Management](./USER_MANAGEMENT.md) - Creating and managing users
- [Security](./SECURITY.md) - Security best practices

