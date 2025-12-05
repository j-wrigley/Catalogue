# User Files

User file structure and storage format.

## File Location

Users are stored as JSON files in:
```
/catalogue/content/users/
  admin.json
  editor.json
  author.json
  ...
```

## File Naming

- **Format**: `{username}.json`
- **Sanitization**: Username is sanitized to create filename
- **Case-sensitive**: Filenames match usernames exactly

## File Structure

Each user file contains:

```json
{
  "username": "admin",
  "password": "$2y$10$abcdefghijklmnopqrstuvwxyz1234567890",
  "created": "2025-01-01T00:00:00+00:00",
  "updated": "2025-01-15T12:30:45+00:00",
  "updated_by": "admin"
}
```

## Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `username` | String | User's login username |
| `password` | String | Bcrypt password hash |
| `created` | String | ISO 8601 timestamp of creation |
| `updated` | String | ISO 8601 timestamp of last update |
| `updated_by` | String | Username who last updated |

## Password Hash Format

Passwords are hashed using PHP's `password_hash()` with `PASSWORD_DEFAULT`:

- **Algorithm**: bcrypt (default)
- **Cost**: 10 rounds (default)
- **Format**: `$2y$10$...` (bcrypt identifier)

**Example:**
```
$2y$10$abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuv
```

## Creating Users Manually

You can create users manually by creating JSON files:

### Step 1: Create File

Create `/catalogue/content/users/newuser.json`

### Step 2: Generate Password Hash

Use PHP to generate password hash:

```php
<?php
echo password_hash('yourpassword', PASSWORD_DEFAULT);
?>
```

### Step 3: Create JSON File

```json
{
  "username": "newuser",
  "password": "$2y$10$...",
  "created": "2025-01-01T00:00:00+00:00",
  "updated": "2025-01-01T00:00:00+00:00",
  "updated_by": "system"
}
```

### Step 4: Set Permissions

Ensure file is readable:
```bash
chmod 644 /catalogue/content/users/newuser.json
```

## Updating Passwords Manually

### Method 1: Via CMS

Use the user management interface (recommended).

### Method 2: Direct File Edit

1. Open user JSON file
2. Generate new password hash:
   ```php
   <?php echo password_hash('newpassword', PASSWORD_DEFAULT); ?>
   ```
3. Replace `password` field value
4. Update `updated` timestamp
5. Save file

## File Permissions

### Recommended Permissions

- **Directory**: `755` (drwxr-xr-x)
- **Files**: `644` (-rw-r--r--)

### Security

- Files should be readable by web server
- Files should NOT be world-writable
- Directory should be writable by web server (for CMS creation)

## Examples

### Complete User File

```json
{
  "username": "admin",
  "password": "$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy",
  "created": "2025-01-01T12:00:00+00:00",
  "updated": "2025-01-15T14:30:00+00:00",
  "updated_by": "admin"
}
```

### Multiple Users

```
/catalogue/content/users/
  admin.json       → Admin user
  editor.json     → Editor user
  author.json     → Author user
  contributor.json → Contributor user
```

## File Operations

### Reading Users

Users are read via `getAllUsers()` function:
- Scans `/catalogue/content/users/` directory
- Reads all `.json` files
- Returns array of user data (without passwords)

### Writing Users

Users are written via `saveUser()` function:
- Validates username format
- Hashes password
- Writes JSON file
- Updates timestamps

### Deleting Users

Users are deleted via `deleteUser()` function:
- Validates user exists
- Prevents self-deletion
- Deletes JSON file

## See Also

- [User Management](./USER_MANAGEMENT.md) - Creating users via CMS
- [Authentication](./AUTHENTICATION.md) - Login system
- [Security](./SECURITY.md) - Security considerations

