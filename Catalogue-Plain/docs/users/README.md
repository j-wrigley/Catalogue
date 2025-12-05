# Users Documentation

User management and authentication system for the CMS.

## Quick Links

- **[User Management](./USER_MANAGEMENT.md)** - Creating, editing, and deleting users
- **[Authentication](./AUTHENTICATION.md)** - Login, logout, and session management
- **[User Files](./USER_FILES.md)** - User file structure and storage
- **[Security](./SECURITY.md)** - Security features and best practices

## Overview

The CMS uses a file-based user system where each user is stored as a JSON file. Users can log in to access the admin panel and manage content.

## Key Features

- **File-based storage** - Users stored as JSON files (no database)
- **Password hashing** - Secure password storage using PHP's `password_hash()`
- **Rate limiting** - Protection against brute force attacks
- **Session management** - Secure session handling with timeout
- **Multi-user support** - Multiple users can access the CMS
- **Self-service** - Users can change their own passwords

## User Storage Location

Users are stored in:
```
/catalogue/content/users/
  admin.json
  editor.json
  ...
```

## Quick Start

1. **First User**: Create your first user through the CMS interface
2. **Login**: Access `/catalogue/index.php?page=login`
3. **Manage Users**: Navigate to "Users" in the admin panel sidebar

## Next Steps

1. Read [User Management](./USER_MANAGEMENT.md) to learn how to create and manage users
2. Check [Authentication](./AUTHENTICATION.md) for login/logout details
3. Review [Security](./SECURITY.md) for security best practices

