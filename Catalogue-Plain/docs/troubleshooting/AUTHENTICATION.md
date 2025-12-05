# Authentication Issues

Troubleshooting login and authentication problems.

## Common Issues

### Cannot Log In

**Symptoms:**
- Login fails
- "Invalid credentials" error
- Redirects back to login

**Solutions:**

1. **Check Username/Password**
   - Verify username correct
   - Check password correct
   - Ensure no typos

2. **Check User File Exists**
   ```bash
   ls catalogue/content/users/{username}.json
   ```
   - User file must exist
   - Filename must match username
   - Case-sensitive

3. **Verify Password Hash**
   ```php
   // Check password hash format
   $user = readJson('catalogue/content/users/admin.json');
   var_dump($user['password_hash']);
   ```
   - Should be bcrypt hash
   - Format: `$2y$10$...`
   - Verify hash valid

4. **Reset Password**
   - Use password reset function
   - Or manually update hash
   - Use `password_hash()` function

### Session Not Starting

**Symptoms:**
- Login succeeds but session lost
- Redirected to login immediately
- Session timeout too short

**Solutions:**

1. **Check Session Directory**
   ```bash
   ls -la /var/lib/php/sessions/
   # or
   echo session_save_path();
   ```
   - Directory must be writable
   - Check permissions
   - Verify exists

2. **Check Session Configuration**
   ```php
   // config.php
   define('SESSION_LIFETIME', 3600 * 8); // 8 hours
   ```
   - Verify lifetime set
   - Check cookie parameters
   - Ensure secure settings

3. **Check Cookie Settings**
   ```php
   // Verify cookie parameters
   print_r(session_get_cookie_params());
   ```
   - HttpOnly should be true
   - Secure if HTTPS
   - SameSite should be Strict

### "Invalid CSRF Token" on Login

**Symptoms:**
- CSRF error on login
- Token validation fails
- Cannot submit login form

**Solutions:**

1. **Refresh Login Page**
   - Token may have expired
   - Refresh to get new token
   - Try login again

2. **Check Session Started**
   - Session must be active
   - Verify session_start() called
   - Check session configuration

3. **Clear Browser Cookies**
   - Old cookies may interfere
   - Clear site cookies
   - Try login again

### Rate Limiting Issues

**Symptoms:**
- "Too many login attempts" error
- Account locked temporarily
- Cannot log in

**Solutions:**

1. **Wait for Lockout**
   - Default: 15 minutes
   - Wait for lockout to expire
   - Try again after timeout

2. **Check Lockout Settings**
   ```php
   // lib/auth.php
   define('LOGIN_MAX_ATTEMPTS', 5);
   define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
   ```

3. **Reset Lockout**
   - Clear session data
   - Remove lockout records
   - Or wait for timeout

## User File Issues

### User File Not Found

**Symptoms:**
- "User not found" error
- Login fails
- User doesn't exist

**Solutions:**

1. **Check File Exists**
   ```bash
   ls catalogue/content/users/
   ```
   - Verify user file exists
   - Check filename matches username
   - Case-sensitive

2. **Verify File Format**
   ```bash
   cat catalogue/content/users/admin.json
   ```
   - Must be valid JSON
   - Must contain username
   - Must have password_hash

3. **Create User File**
   ```json
   {
     "username": "admin",
     "password_hash": "$2y$10$..."
   }
   ```

### Invalid Password Hash

**Symptoms:**
- Login fails
- Password verification fails
- Hash format incorrect

**Solutions:**

1. **Check Hash Format**
   ```php
   $hash = '$2y$10$...';
   echo password_verify('password', $hash) ? 'Valid' : 'Invalid';
   ```
   - Must be bcrypt hash
   - Format: `$2y$10$...`
   - Verify with password_verify()

2. **Regenerate Hash**
   ```php
   $hash = password_hash('newpassword', PASSWORD_DEFAULT);
   echo $hash;
   ```

3. **Update User File**
   ```json
   {
     "username": "admin",
     "password_hash": "$2y$10$newhash..."
   }
   ```

## Session Issues

### Session Expires Too Quickly

**Symptoms:**
- Logged out frequently
- Session timeout too short
- Inactivity timeout

**Solutions:**

1. **Increase Session Lifetime**
   ```php
   // config.php
   define('SESSION_LIFETIME', 3600 * 24); // 24 hours
   ```

2. **Check Session Configuration**
   ```php
   session_set_cookie_params([
       'lifetime' => SESSION_LIFETIME,
       'httponly' => true,
       'samesite' => 'Strict'
   ]);
   ```

### Session Not Persisting

**Symptoms:**
- Session lost on page reload
- Login doesn't persist
- Cookies not set

**Solutions:**

1. **Check Cookie Settings**
   - HttpOnly must be true
   - Secure if HTTPS
   - SameSite must be set

2. **Verify Session Save Path**
   ```php
   echo session_save_path();
   ```
   - Must be writable
   - Check permissions
   - Verify exists

3. **Check Browser Settings**
   - Cookies must be enabled
   - Third-party cookies allowed
   - Check privacy settings

## Debugging Steps

### Step 1: Check User File

```bash
cat catalogue/content/users/admin.json
```

### Step 2: Test Password

```php
<?php
require_once 'catalogue/config.php';
require_once 'catalogue/lib/storage.php';

$user = readJson('catalogue/content/users/admin.json');
$password = 'yourpassword';
$valid = password_verify($password, $user['password_hash']);
var_dump($valid);
?>
```

### Step 3: Check Session

```php
<?php
session_start();
print_r($_SESSION);
print_r(session_get_cookie_params());
?>
```

## Best Practices

### Security

- **Strong passwords** - Use complex passwords
- **Change defaults** - Don't use default credentials
- **Regular updates** - Change passwords regularly
- **Monitor attempts** - Watch for brute force

### Troubleshooting

- **Check logs** - Review error logs
- **Verify files** - Check user files exist
- **Test manually** - Isolate problem
- **Clear sessions** - Reset if needed

## See Also

- [Authentication](../users/AUTHENTICATION.md) - Authentication system
- [User Management](../users/USER_MANAGEMENT.md) - User administration
- [Security](../security/AUTHENTICATION.md) - Security details

