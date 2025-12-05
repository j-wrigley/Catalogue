# API Reference

Complete reference for all PHP functions available in the CMS.

## Quick Links

- **[Template Functions](./TEMPLATE_FUNCTIONS.md)** - Content access and rendering
- **[Storage Functions](./STORAGE_FUNCTIONS.md)** - File and JSON operations
- **[Blueprint Functions](./BLUEPRINT_FUNCTIONS.md)** - Blueprint parsing and access
- **[Authentication Functions](./AUTHENTICATION_FUNCTIONS.md)** - User and session management
- **[Utility Functions](./UTILITY_FUNCTIONS.md)** - Helper functions
- **[Render Functions](./RENDER_FUNCTIONS.md)** - Escaping and sanitization
- **[CSRF Functions](./CSRF_FUNCTIONS.md)** - Security tokens

## Overview

The CMS provides a comprehensive set of PHP functions organized into logical modules:

- **Template Functions** - Access content in templates
- **Storage Functions** - Read/write JSON files
- **Blueprint Functions** - Parse and access blueprints
- **Authentication Functions** - User management
- **Utility Functions** - Common operations
- **Render Functions** - Security and escaping
- **CSRF Functions** - Form security

## Function Categories

### Template Functions
Functions for accessing and rendering content in templates:
- `catalogue()` - Get and render field values
- `catalogueRaw()` - Get raw field values
- `catalogueFiles()` - Iterate through files
- `catalogueCollection()` - Iterate through collections
- `catalogueNav()` - Get navigation data
- `snippet()` - Include reusable components
- `traffic()` - Track page views

### Storage Functions
Functions for file and JSON operations:
- `readJson()` - Read JSON file
- `writeJson()` - Write JSON file
- `listJsonFiles()` - List JSON files in directory
- `deleteJson()` - Delete JSON file

### Blueprint Functions
Functions for blueprint management:
- `getBlueprint()` - Get blueprint by name
- `getAllBlueprints()` - Get all blueprints
- `parseBlueprint()` - Parse YAML blueprint file

### Authentication Functions
Functions for user and session management:
- `isLoggedIn()` - Check login status
- `requireLogin()` - Require authentication
- `getAllUsers()` - Get all users
- `getUserByUsername()` - Get user by username
- `saveUser()` - Create/update user
- `deleteUser()` - Delete user
- `login()` - Authenticate user
- `logout()` - Sign out user

### Utility Functions
Helper functions for common operations:
- `slugify()` - Create URL-safe slug
- `getTimestamp()` - Get current timestamp
- `sanitizeFilename()` - Sanitize filename
- `formatDate()` - Format date string
- `getCmsName()` - Get CMS name

### Render Functions
Security and escaping functions:
- `esc()` - Escape HTML
- `esc_attr()` - Escape HTML attribute
- `esc_url()` - Escape URL
- `esc_js()` - Escape JavaScript

### CSRF Functions
Security token functions:
- `generateCsrfToken()` - Generate CSRF token
- `validateCsrfToken()` - Validate token
- `csrfField()` - Get CSRF input field

## Usage

### In Templates

```php
<?= catalogue('title') ?>
<?php foreach (catalogueCollection('posts') as $post): ?>
    <?= catalogue('title') ?>
<?php endforeach; ?>
```

### In PHP Code

```php
$content = readJson('/path/to/file.json');
$blueprint = getBlueprint('about');
$users = getAllUsers();
```

## Next Steps

1. Read [Template Functions](./TEMPLATE_FUNCTIONS.md) for content access
2. Check [Storage Functions](./STORAGE_FUNCTIONS.md) for file operations
3. Review [Utility Functions](./UTILITY_FUNCTIONS.md) for helpers

