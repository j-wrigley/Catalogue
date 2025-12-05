# Security Model

How the CMS protects content, users, and data.

## Overview

Security is a core principle of the CMS. Multiple layers of protection ensure content, users, and data are safe from common web vulnerabilities.

## Security Layers

### 1. Authentication

**Purpose:** Verify user identity

**Mechanisms:**
- Username/password login
- Session management
- Password hashing
- Rate limiting

**Implementation:**
- File-based user storage
- Secure password hashing
- Session cookies
- Login attempt limits

### 2. Authorization

**Purpose:** Control access to resources

**Mechanisms:**
- Login required for admin
- Protected routes
- File access control
- Directory protection

**Implementation:**
- Session checks
- Route protection
- File permissions
- Directory restrictions

### 3. CSRF Protection

**Purpose:** Prevent cross-site request forgery

**Mechanisms:**
- CSRF tokens
- Token validation
- Token expiry
- Form protection

**Implementation:**
- Token generation
- Token validation
- Token expiry (1 hour)
- All forms protected

### 4. Input Validation

**Purpose:** Ensure safe input

**Mechanisms:**
- Input sanitization
- Type validation
- Path validation
- File validation

**Implementation:**
- Path sanitization
- Filename validation
- File type checking
- Size limits

### 5. Output Escaping

**Purpose:** Prevent XSS attacks

**Mechanisms:**
- HTML escaping
- Attribute escaping
- URL escaping
- JavaScript escaping

**Implementation:**
- `esc()` function
- `esc_attr()` function
- `esc_url()` function
- Automatic escaping

## Authentication System

### User Storage

**Location:** `content/users/{username}.json`

**Format:**
```json
{
  "username": "admin",
  "password_hash": "$2y$10$...",
  "created": "2024-01-01T00:00:00+00:00"
}
```

### Password Security

**Hashing:** `password_hash()` with `PASSWORD_DEFAULT`

**Verification:** `password_verify()`

**Requirements:**
- Strong passwords recommended
- No password storage in plain text
- Secure hashing algorithm

### Session Management

**Session Configuration:**
- Secure cookies (HTTPS)
- HttpOnly cookies
- SameSite protection
- Session timeout (8 hours)

**Session Security:**
- Regenerated on login
- Timeout after inactivity
- Secure cookie parameters
- Session fixation prevention

## CSRF Protection

### Token Generation

**Process:**
1. Generate random token
2. Store in session
3. Add to form
4. Validate on submission

**Token Format:**
- Random string
- Stored in session
- Expires after 1 hour
- Regenerated per request

### Token Validation

**Process:**
1. Check token exists
2. Verify token matches session
3. Check token not expired
4. Reject if invalid

**Protection:**
- All POST requests protected
- Forms include tokens
- AJAX requests validated
- Token expiry enforced

## Input Validation

### Path Sanitization

**Process:**
- Remove special characters
- Validate path structure
- Check directory traversal
- Ensure safe paths

**Example:**
```php
$path = preg_replace('/[^a-z0-9_\/-]/i', '', $path);
```

### Filename Validation

**Process:**
- Sanitize filename
- Remove dangerous characters
- Validate extension
- Check file type

**Example:**
```php
$filename = sanitizeFilename($filename);
```

### File Upload Validation

**Checks:**
- File type validation
- MIME type checking
- File size limits
- Extension validation

**Allowed Types:**
- Images (jpg, png, gif, webp, svg)
- Documents (pdf, zip)
- Validated before upload

## Output Escaping

### HTML Escaping

**Function:** `esc($text)`

**Purpose:** Prevent XSS attacks

**Usage:**
```php
<?= esc($user_input) ?>
```

**Output:** HTML entities encoded

### Attribute Escaping

**Function:** `esc_attr($text)`

**Purpose:** Safe HTML attributes

**Usage:**
```php
<div class="<?= esc_attr($class) ?>">
```

**Output:** Attribute-safe encoding

### URL Escaping

**Function:** `esc_url($url)`

**Purpose:** Safe URLs

**Usage:**
```php
<a href="<?= esc_url($url) ?>">Link</a>
```

**Output:** URL-safe encoding

## File Security

### Directory Protection

**Methods:**
- `.htaccess` rules
- File access restrictions
- Directory listing disabled
- Sensitive files protected

**Protected Files:**
- Blueprint files (`.yml`)
- JSON configs (`.json`)
- Log files (`.log`)
- Markdown files (`.md`)

### Path Traversal Protection

**Validation:**
- Real path checking
- Directory traversal prevention
- Path normalization
- Security checks

**Example:**
```php
$real_path = realpath($path);
if (strpos($real_path, $base_dir) !== 0) {
    // Reject - path traversal attempt
}
```

## Security Headers

### HTTP Headers

**Headers Set:**
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Content-Security-Policy: ...`

### Purpose

**Protection Against:**
- MIME type sniffing
- Clickjacking
- XSS attacks
- Referrer leakage

## Best Practices

### Authentication

- **Strong passwords** - Use complex passwords
- **Change defaults** - Don't use default credentials
- **Regular updates** - Update passwords regularly
- **Limit attempts** - Rate limiting prevents brute force

### Input Handling

- **Validate all input** - Never trust user input
- **Sanitize paths** - Prevent directory traversal
- **Check file types** - Validate uploads
- **Limit sizes** - Prevent large uploads

### Output Handling

- **Escape all output** - Prevent XSS
- **Use functions** - Use escaping functions
- **Don't trust data** - Always escape
- **Test output** - Verify escaping works

## See Also

- [Security Documentation](../security/README.md) - Complete security guide
- [Authentication](../users/AUTHENTICATION.md) - Login system
- [CSRF Protection](../security/CSRF.md) - CSRF details

