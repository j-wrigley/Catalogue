# Security Headers

HTTP security headers for additional protection.

## Overview

Security headers provide additional layers of protection against various attacks.

## Implemented Headers

### X-Content-Type-Options

Prevents MIME type sniffing:

```php
header('X-Content-Type-Options: nosniff');
```

**Protection:** Prevents browsers from interpreting files as different MIME types

### X-Frame-Options

Prevents clickjacking:

```php
header('X-Frame-Options: DENY');
```

**Protection:** Prevents site from being embedded in iframes

### X-XSS-Protection

Enables browser XSS filter:

```php
header('X-XSS-Protection: 1; mode=block');
```

**Protection:** Additional XSS protection (legacy browsers)

### Referrer-Policy

Controls referrer information:

```php
header('Referrer-Policy: strict-origin-when-cross-origin');
```

**Protection:** Limits referrer information leakage

### Content-Security-Policy

Controls resource loading:

```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
```

**Protection:** Prevents XSS and injection attacks

## Header Configuration

### Location

Headers set in `config.php`:

```php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
```

### When Set

Headers set on:
- All admin panel pages
- All API endpoints
- All generated HTML files (via PHP handler)

## Content Security Policy

### Current Configuration

```
default-src 'self'
script-src 'self' 'unsafe-inline'
style-src 'self' 'unsafe-inline'
```

### Why `unsafe-inline`?

Required for:
- Inline scripts in admin panel
- Inline styles in admin panel
- Dynamic form generation

### Future Enhancement

Consider using nonces:

```php
$nonce = bin2hex(random_bytes(16));
header("Content-Security-Policy: script-src 'self' 'nonce-$nonce'");
```

## Header Benefits

### X-Content-Type-Options

Prevents:
- MIME type confusion attacks
- File interpretation attacks

### X-Frame-Options

Prevents:
- Clickjacking attacks
- UI redressing attacks

### X-XSS-Protection

Provides:
- Additional XSS filtering
- Legacy browser support

### Referrer-Policy

Limits:
- Referrer information leakage
- Privacy concerns

### Content-Security-Policy

Prevents:
- XSS attacks
- Injection attacks
- Unauthorized resource loading

## Testing Headers

### Check Headers

```bash
curl -I http://yoursite.com/admin
```

### Verify CSP

Browser console shows CSP violations:
- Open browser dev tools
- Check console for CSP warnings
- Verify resources load correctly

## Best Practices

### Header Configuration

✅ **Do:**
- Set all security headers
- Use strict CSP when possible
- Test headers in production
- Monitor CSP violations

❌ **Don't:**
- Skip security headers
- Use permissive CSP
- Ignore CSP violations
- Disable headers for convenience

### CSP Configuration

✅ **Do:**
- Use `'self'` for same-origin
- Specify allowed sources explicitly
- Use nonces when possible
- Monitor violations

❌ **Don't:**
- Use `*` wildcard
- Allow `'unsafe-eval'`
- Ignore violations
- Disable CSP

## See Also

- [XSS Protection](./XSS.md) - Output escaping
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

