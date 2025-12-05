# Security Best Practices

Guidelines for maintaining security in the CMS.

## General Principles

### Defense in Depth

Use multiple layers of security:
- Input validation
- Output escaping
- Authentication
- Authorization
- Security headers

### Principle of Least Privilege

Grant minimum necessary access:
- Users only access needed features
- Files only accessible when needed
- Permissions set appropriately

### Fail Securely

Default to secure state:
- Reject invalid input
- Deny access by default
- Log security events

## Input Handling

### Validate All Input

✅ **Do:**
- Validate immediately on receipt
- Use whitelist approach
- Validate type and format
- Reject invalid input

❌ **Don't:**
- Trust user input
- Validate only on output
- Use blacklist approach
- Accept invalid data

### Sanitize Consistently

```php
// ✅ Consistent
$input = sanitizeInput($raw_input);

// ❌ Inconsistent
$input = $raw_input; // Sometimes sanitized
```

## Output Handling

### Escape All Output

✅ **Do:**
- Escape HTML content
- Escape HTML attributes
- Escape URLs
- Escape JavaScript

❌ **Don't:**
- Output unescaped content
- Trust content from files
- Skip escaping "safe" content

### Context-Aware Escaping

```php
// HTML content
<?= esc($content) ?>

// HTML attribute
<div class="<?= esc_attr($class) ?>">

// URL
<a href="<?= esc_url($url) ?>">
```

## Authentication

### Strong Passwords

✅ **Do:**
- Enforce minimum length (12+)
- Require complexity
- Use unique passwords
- Change regularly

❌ **Don't:**
- Use common passwords
- Share passwords
- Reuse passwords
- Store in plaintext

### Session Management

✅ **Do:**
- Regenerate on login
- Set secure cookies
- Check timeout
- Clear on logout

❌ **Don't:**
- Skip regeneration
- Use insecure cookies
- Extend indefinitely
- Share sessions

## File Operations

### Path Validation

✅ **Do:**
- Use `realpath()` validation
- Check against base directories
- Strip path components
- Log suspicious attempts

❌ **Don't:**
- Trust user paths
- Skip validation
- Allow traversal
- Expose errors

### File Uploads

✅ **Do:**
- Validate MIME types
- Use extension whitelist
- Validate images
- Limit file sizes
- Sanitize filenames

❌ **Don't:**
- Trust extensions
- Allow executables
- Skip validation
- Store in web root

## Error Handling

### Secure Error Messages

✅ **Do:**
- Log detailed errors
- Show generic messages
- Hide sensitive info
- Monitor error logs

❌ **Don't:**
- Expose errors to users
- Show file paths
- Reveal system info
- Ignore errors

## Configuration

### Server Settings

✅ **Do:**
- Disable `display_errors` in production
- Enable `log_errors`
- Set appropriate limits
- Use HTTPS

❌ **Don't:**
- Show errors in production
- Ignore error logs
- Use default settings
- Skip HTTPS

### File Permissions

✅ **Do:**
- Directories: `755`
- Files: `644`
- Restrict write access
- Review regularly

❌ **Don't:**
- Use `777` permissions
- Allow world-write
- Skip permission checks
- Ignore warnings

## Monitoring

### Security Monitoring

✅ **Do:**
- Monitor login attempts
- Review error logs
- Check file changes
- Track suspicious activity

❌ **Don't:**
- Ignore failed logins
- Skip log reviews
- Disable logging
- Ignore warnings

## Updates

### Keep Updated

✅ **Do:**
- Update PHP version
- Update server software
- Review security patches
- Test updates

❌ **Don't:**
- Use outdated PHP
- Skip updates
- Ignore patches
- Deploy untested updates

## Code Security

### Secure Coding

✅ **Do:**
- Validate input
- Escape output
- Use prepared statements (if database)
- Follow best practices

❌ **Don't:**
- Trust user input
- Skip validation
- Use dangerous functions
- Ignore warnings

## See Also

- [Authentication](./AUTHENTICATION.md) - Login security
- [CSRF Protection](./CSRF.md) - Form security
- [XSS Protection](./XSS.md) - Output escaping
- [Production Checklist](./PRODUCTION_CHECKLIST.md) - Deployment guide

