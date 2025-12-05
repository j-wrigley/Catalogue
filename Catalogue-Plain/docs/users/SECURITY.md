# User Security

Security features and best practices for user management.

## Security Features

### Password Hashing

- **Algorithm**: bcrypt via PHP's `password_hash()`
- **Cost factor**: 10 rounds (default)
- **One-way**: Passwords cannot be decrypted
- **Verification**: Uses `password_verify()` for login

### Rate Limiting

- **Max attempts**: 5 failed login attempts
- **Lockout period**: 15 minutes
- **Tracking**: Per IP address and username
- **Auto-reset**: Clears after lockout period

### Session Security

- **ID regeneration**: Session ID regenerated on login
- **Secure cookies**: HttpOnly, Secure, SameSite attributes
- **Timeout**: Configurable session lifetime
- **Validation**: Session timeout checked on each request

### CSRF Protection

- **Token-based**: All forms use CSRF tokens
- **Validation**: Tokens validated on submission
- **Expiration**: Tokens expire with session

## Best Practices

### Password Security

✅ **Do:**
- Use strong passwords (12+ characters)
- Include uppercase, lowercase, numbers, symbols
- Use unique passwords for each account
- Change passwords regularly

❌ **Don't:**
- Use common passwords (password123, admin, etc.)
- Share passwords between users
- Store passwords in plain text
- Reuse passwords from other services

### User Management

✅ **Do:**
- Create separate accounts for each user
- Use descriptive usernames
- Regularly review user accounts
- Delete unused accounts

❌ **Don't:**
- Share accounts between users
- Use generic usernames (user1, test, etc.)
- Leave old accounts active
- Use admin account for daily tasks

### Access Control

✅ **Do:**
- Limit user access to necessary features
- Use strong passwords for admin accounts
- Monitor login attempts
- Log out when finished

❌ **Don't:**
- Share login credentials
- Leave sessions open on shared computers
- Use admin account for content editing
- Ignore failed login attempts

## Security Considerations

### File Permissions

Ensure proper file permissions:

```bash
# Directory
chmod 755 /catalogue/content/users/

# Files
chmod 644 /catalogue/content/users/*.json
```

### Server Security

- **HTTPS**: Use HTTPS in production
- **Firewall**: Restrict access to admin panel
- **Updates**: Keep PHP and server software updated
- **Backups**: Regularly backup user files

### Password Reset

If password is forgotten:

1. Access server directly
2. Edit user JSON file
3. Generate new password hash
4. Update file
5. User can login with new password

**Generate hash:**
```php
<?php
echo password_hash('newpassword', PASSWORD_DEFAULT);
?>
```

## Rate Limiting Details

### How It Works

1. Failed login attempt recorded
2. Count incremented for IP + username
3. After 5 attempts, login blocked
4. After 15 minutes, attempts reset

### Rate Limit File

Stored in:
```
/catalogue/logs/login_attempts.json
```

**Format:**
```json
{
  "192.168.1.1_admin": {
    "count": 3,
    "last_attempt": 1704067200
  }
}
```

### Clearing Rate Limits

Rate limits clear automatically after 15 minutes. To manually clear:

1. Delete `/catalogue/logs/login_attempts.json`
2. Or wait for automatic expiration

## Session Security

### Session Configuration

Configured in `config.php`:

```php
define('SESSION_LIFETIME', 86400); // 24 hours
```

### Session Cookies

- **HttpOnly**: Prevents JavaScript access
- **Secure**: Only sent over HTTPS (if enabled)
- **SameSite**: CSRF protection

### Session Timeout

- Sessions expire after configured lifetime
- Timeout checked on each page load
- User redirected to login on expiration

## Common Security Issues

### Issue: Weak Passwords

**Problem**: Users choose weak passwords

**Solution**: Enforce password requirements (minimum length, complexity)

### Issue: Shared Accounts

**Problem**: Multiple users share one account

**Solution**: Create separate accounts for each user

### Issue: Session Hijacking

**Problem**: Sessions can be hijacked

**Solution**: Use HTTPS, secure cookies, session regeneration

### Issue: Brute Force Attacks

**Problem**: Attackers try many passwords

**Solution**: Rate limiting (already implemented)

## Security Checklist

- [ ] All users have strong passwords
- [ ] HTTPS enabled in production
- [ ] File permissions set correctly
- [ ] Rate limiting enabled
- [ ] Session timeout configured
- [ ] CSRF protection enabled
- [ ] Regular backups of user files
- [ ] Unused accounts deleted
- [ ] Server software updated
- [ ] Firewall configured

## See Also

- [User Management](./USER_MANAGEMENT.md) - Creating users
- [Authentication](./AUTHENTICATION.md) - Login system
- [User Files](./USER_FILES.md) - File structure

