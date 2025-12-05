# Authentication Functions

Functions for user and session management.

## `isLoggedIn()`

Check if user is currently logged in.

### Syntax

```php
isLoggedIn(): bool
```

### Parameters

None.

### Returns

`true` if logged in, `false` otherwise.

### Examples

```php
if (isLoggedIn()) {
    echo 'Welcome back!';
} else {
    echo 'Please log in';
}
```

---

## `requireLogin()`

Require user to be logged in (redirects if not).

### Syntax

```php
requireLogin(): void
```

### Parameters

None.

### Returns

Void (redirects to login if not authenticated).

### Examples

```php
// Protect a page
requireLogin();
// Page content here
```

### Behavior

- Checks `isLoggedIn()`
- Redirects to login page if not logged in
- Exits script execution

---

## `getAllUsers()`

Get all users from the system.

### Syntax

```php
getAllUsers(): array
```

### Parameters

None.

### Returns

Array of user arrays (without passwords).

### Examples

```php
$users = getAllUsers();
foreach ($users as $user) {
    echo $user['username'];
}
```

### User Structure

Each user array contains:
- `username` - Username
- `created` - Creation timestamp
- `updated` - Update timestamp
- `updated_by` - Who last updated
- `filename` - JSON filename

**Note:** Passwords are never included.

---

## `getUserByUsername()`

Get user by username.

### Syntax

```php
getUserByUsername(string $username): array|null
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username to lookup |

### Returns

User array (with password hash) or `null` if not found.

### Examples

```php
$user = getUserByUsername('admin');
if ($user) {
    echo 'User found: ' . $user['username'];
}
```

### Security Note

Returns password hash. Use `password_verify()` to check passwords, never compare directly.

---

## `saveUser()`

Create or update a user.

### Syntax

```php
saveUser(string $username, string $password = null, string $existing_username = null): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username |
| `$password` | string | Password (null = don't change) |
| `$existing_username` | string | Existing username (for updates) |

### Returns

`true` on success, `false` on failure.

### Examples

```php
// Create new user
saveUser('newuser', 'password123');

// Update user (change password)
saveUser('admin', 'newpassword', 'admin');

// Update user (keep password)
saveUser('admin', null, 'admin');
```

### Features

- **Password hashing** - Automatically hashes passwords
- **Username changes** - Handles username updates
- **Timestamps** - Updates created/updated timestamps
- **Metadata** - Tracks who updated

---

## `deleteUser()`

Delete a user.

### Syntax

```php
deleteUser(string $username): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username to delete |

### Returns

`true` on success, `false` on failure.

### Examples

```php
if (deleteUser('olduser')) {
    echo 'User deleted';
}
```

### Restrictions

- **Cannot delete yourself** - Returns `false` if trying to delete current user
- **Permanent** - Deletion cannot be undone

---

## `login()`

Authenticate user and create session.

### Syntax

```php
login(string $username, string $password): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username |
| `$password` | string | Password |

### Returns

`true` on success, `false` on failure.

### Examples

```php
if (login('admin', 'password123')) {
    header('Location: /dashboard');
} else {
    echo 'Invalid credentials';
}
```

### Features

- **Password verification** - Uses `password_verify()`
- **Rate limiting** - Checks login rate limits
- **Session regeneration** - Regenerates session ID
- **Failed attempt tracking** - Records failed attempts

---

## `logout()`

Sign out current user.

### Syntax

```php
logout(): void
```

### Parameters

None.

### Returns

Void.

### Examples

```php
logout();
header('Location: /login');
```

### Behavior

- Clears session data
- Deletes session cookie
- Destroys session

---

## `checkSessionTimeout()`

Check if session has timed out.

### Syntax

```php
checkSessionTimeout(): bool
```

### Parameters

None.

### Returns

`true` if session valid, `false` if timed out.

### Examples

```php
if (!checkSessionTimeout()) {
    logout();
    header('Location: /login');
}
```

---

## `checkLoginRateLimit()`

Check if login attempts are rate-limited.

### Syntax

```php
checkLoginRateLimit(string $username): bool
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username |

### Returns

`true` if allowed, `false` if rate-limited.

### Examples

```php
if (!checkLoginRateLimit($username)) {
    echo 'Too many attempts. Try again later.';
    return;
}
```

### Rate Limit Rules

- **Max attempts**: 5 failed attempts
- **Lockout period**: 15 minutes
- **Tracking**: Per IP + username

---

## `recordFailedLogin()`

Record a failed login attempt.

### Syntax

```php
recordFailedLogin(string $username): void
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username |

### Returns

Void.

---

## `clearLoginAttempts()`

Clear failed login attempts for user.

### Syntax

```php
clearLoginAttempts(string $username): void
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$username` | string | Username |

### Returns

Void.

---

## Session Variables

After login, session contains:

- `$_SESSION['user_logged_in']` - `true`
- `$_SESSION['username']` - Username
- `$_SESSION['login_time']` - Login timestamp

---

## See Also

- [Users Documentation](../users/README.md) - User management guide
- [Security Documentation](../security/) - Security best practices

