# User Management

Creating, editing, and deleting users in the CMS.

## Accessing User Management

Navigate to **Users** in the admin panel sidebar to access the user management interface.

## Creating a New User

### Steps

1. Click **"New User"** button in the users table
2. Enter username (letters, numbers, underscores, hyphens only)
3. Enter password (minimum 6 characters)
4. Confirm password
5. Click **"Save User"**

### Username Requirements

- Only letters, numbers, underscores (`_`), and hyphens (`-`)
- Must be unique
- Case-sensitive

### Password Requirements

- Minimum 6 characters
- No maximum length
- Passwords are hashed using PHP's `password_hash()` function

## Editing a User

### Steps

1. Click the **edit icon** (pencil) next to a user in the table
2. Modify username (if needed)
3. Enter new password (leave blank to keep current password)
4. Confirm password (if changing)
5. Click **"Save User"**

### Password Updates

- Leave password fields blank to keep the current password
- Enter a new password to change it
- Password confirmation is required when changing passwords

## Deleting a User

### Steps

1. Click the **delete icon** (trash) next to a user
2. Confirm deletion in the dialog
3. User is permanently deleted

### Restrictions

- **Cannot delete yourself** - You cannot delete your own account
- **Permanent action** - Deletion cannot be undone
- **No content impact** - Deleting a user does not delete content they created

## User Table

The users table displays:

| Column | Description |
|--------|-------------|
| **Username** | User's login username |
| **Created** | Date user was created |
| **Last Updated** | Date user was last modified |
| **Actions** | Edit and delete buttons |

### Current User Indicator

Your own account is marked with a **"You"** badge next to your username.

## Examples

### Creating Your First User

If no users exist, you'll see an empty state with a **"New User"** button. Click it to create your first user.

### Changing Your Password

1. Click edit on your own account
2. Leave username unchanged
3. Enter new password
4. Confirm password
5. Save

### Adding Team Members

1. Create a new user for each team member
2. Each user gets their own login credentials
3. All users have the same permissions (full access)

## User Data

Each user file contains:

```json
{
  "username": "admin",
  "password": "$2y$10$...",
  "created": "2025-01-01T00:00:00+00:00",
  "updated": "2025-01-01T00:00:00+00:00",
  "updated_by": "system"
}
```

**Note:** Passwords are never displayed or returned in API responses.

## See Also

- [Authentication](./AUTHENTICATION.md) - Login and logout
- [User Files](./USER_FILES.md) - File structure details
- [Security](./SECURITY.md) - Security features

