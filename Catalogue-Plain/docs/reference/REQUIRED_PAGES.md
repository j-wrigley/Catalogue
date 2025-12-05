# Required Pages & Blueprints

Explanation of required system pages and why some are hidden from normal CMS areas.

## Overview

The CMS includes several required pages and blueprints that serve special system functions. Some are hidden from the normal pages/collections tables because they have dedicated interfaces or special handling.

## Required System Pages

### 1. Settings (`settings`)

**Status:** ✅ Required  
**Hidden From:** Pages table  
**Access:** Via "Site" sidebar link  
**HTML Generation:** ❌ Not generated

#### Why It's Required

The `settings` blueprint stores site-wide configuration:
- Site name
- Site description
- SEO metadata
- Social media links
- Other site settings

#### Why It's Hidden

- Has its own dedicated sidebar link ("Site")
- Not a public-facing page
- Admin-only configuration
- Doesn't generate HTML (settings are used in templates, not displayed as a page)

#### File Structure

```
/catalogue/blueprints/settings.blueprint.yml
/catalogue/templates/settings.php (optional, for admin panel)
/catalogue/content/pages/settings/settings.json
```

#### Usage in Templates

```php
<?= catalogue('site_name', 'Site', 'site') ?>
<?= catalogue('site_description', '', 'site') ?>
```

---

### 2. 404 Error Page (`404`)

**Status:** ✅ Required  
**Hidden From:** Pages table  
**Access:** Via Pages table (if you know it exists) or direct URL  
**HTML Generation:** ✅ Generates `404.html`

#### Why It's Required

The 404 page is shown when visitors access non-existent URLs. It's a core part of the site's error handling.

#### Why It's Hidden

- Managed separately from regular pages
- Has special Apache configuration
- Uses PHP handler for proper HTTP status codes
- Auto-generated and handled specially

#### File Structure

```
/catalogue/blueprints/404.blueprint.yml
/catalogue/templates/404.php
/catalogue/content/pages/404/404.json
/catalogue/404-handler.php
/404.html (generated)
```

#### Special Handling

- Generates `404.html` in root (not `404.html` in a subdirectory)
- Uses `404-handler.php` to serve with proper HTTP 404 status
- Configured in `.htaccess` with `ErrorDocument 404`

#### Editing

Can be edited through Pages table, but it's recommended to access it directly via the admin panel when needed.

---

### 3. Media (`media`)

**Status:** ✅ Required (if using media features)  
**Hidden From:** Pages table, Collections table  
**Access:** Via "Media" sidebar link  
**HTML Generation:** ❌ Not generated

#### Why It's Required

The `media` blueprint stores metadata for uploaded files:
- Alt text
- Captions
- Descriptions
- Tags
- Photo credits

#### Why It's Hidden

- Has its own dedicated sidebar link ("Media")
- Not a content page
- Used for file metadata only
- Doesn't generate HTML (metadata accessed via `catalogueMedia()`)

#### File Structure

```
/catalogue/blueprints/media.blueprint.yml
/catalogue/content/media-metadata/{hash}.json (metadata files)
```

#### Usage

Metadata is accessed via `catalogueMedia()` function in templates, not as a regular page.

---

### 4. Home Page (`home`)

**Status:** ✅ Optional but recommended  
**Hidden From:** Not hidden (appears in Pages table)  
**Access:** Via Pages table  
**HTML Generation:** ✅ Generates `index.html` (special)

#### Why It's Special

The home page is special because:
- Generates `index.html` instead of `home.html`
- Accessible at root URL (`/`)
- Often the first page visitors see

#### File Structure

```
/catalogue/blueprints/home.blueprint.yml
/catalogue/templates/home.php
/catalogue/content/pages/home/home.json
/index.html (generated)
```

#### Special Handling

- Template name must be `home.php`
- Blueprint name must be `home.blueprint.yml`
- Generates to `index.html` (not `home.html`)
- URL is `/` (root)

---

### 5. Users (`users`)

**Status:** ✅ Required (system)  
**Hidden From:** Pages table, Collections table  
**Access:** Via "Users" sidebar link  
**HTML Generation:** ❌ Not generated

#### Why It's Required

The users system stores user accounts:
- Usernames
- Password hashes
- User metadata

#### Why It's Hidden

- Has its own dedicated sidebar link ("Users")
- Not content, it's system data
- Admin-only access
- Doesn't generate HTML

#### File Structure

```
/catalogue/content/users/{username}.json
```

#### Usage

Managed through the Users section of the admin panel, not as regular content.

---

## Summary Table

| Page | Required | Hidden From | Access Via | HTML Generated | Reason |
|------|----------|-------------|------------|----------------|--------|
| `settings` | ✅ Yes | Pages table | "Site" sidebar | ❌ No | Site configuration |
| `404` | ✅ Yes | Pages table | Pages (special) | ✅ Yes (`404.html`) | Error handling |
| `media` | ✅ Yes* | Pages & Collections | "Media" sidebar | ❌ No | File metadata |
| `home` | ⚠️ Recommended | No | Pages table | ✅ Yes (`index.html`) | Home page |
| `users` | ✅ Yes | Pages & Collections | "Users" sidebar | ❌ No | User accounts |

*Required if using media metadata features

## Why Pages Are Hidden

### Dedicated Interfaces

Some pages have dedicated admin interfaces:
- **Settings** → "Site" sidebar link
- **Media** → "Media" sidebar link  
- **Users** → "Users" sidebar link

These provide better UX than showing them in generic tables.

### System Functions

Some pages serve system functions:
- **404** → Error handling
- **Settings** → Site configuration
- **Users** → Authentication system

They're not regular content pages.

### No HTML Generation

Some pages don't generate HTML:
- **Settings** → Used in templates, not displayed
- **Media** → Metadata only, accessed via functions
- **Users** → System data, not public

## Creating Required Pages

### Settings

1. Create `blueprints/settings.blueprint.yml`
2. Access via "Site" sidebar link
3. Content saved to `content/pages/settings/settings.json`

### 404 Page

1. Create `blueprints/404.blueprint.yml`
2. Create `templates/404.php`
3. Create `content/pages/404/404.json`
4. HTML generates automatically to `404.html`

### Media Metadata

1. Create `blueprints/media.blueprint.yml`
2. Metadata managed through Media library
3. Files stored in `content/media-metadata/`

### Home Page

1. Create `blueprints/home.blueprint.yml`
2. Create `templates/home.php`
3. Create `content/pages/home/home.json`
4. HTML generates automatically to `index.html`

## Editing Required Pages

### Settings

- Navigate to **Site** in sidebar
- Edit fields
- Save
- Settings available in templates immediately

### 404 Page

- Navigate to **Pages** in sidebar
- Find **404** page (may need to search)
- Edit fields
- Save
- `404.html` regenerates automatically

### Media Metadata

- Navigate to **Media** in sidebar
- Right-click file → **Edit Metadata**
- Edit fields
- Save
- Metadata available in templates

### Home Page

- Navigate to **Pages** in sidebar
- Find **Home** page
- Edit fields
- Save
- `index.html` regenerates automatically

## Best Practices

### Don't Delete Required Pages

- **Settings** - Required for site configuration
- **404** - Required for error handling
- **Media** - Required if using metadata
- **Users** - Required for authentication

### Don't Modify System Behavior

- Don't change how `settings` is accessed
- Don't modify `404-handler.php` unnecessarily
- Don't change user file structure
- Don't rename `home` blueprint (must be `home`)

### Use Appropriate Access Methods

- Use sidebar links for dedicated pages
- Use Pages table for regular pages
- Use Collections table for collection items

## See Also

- [Site Generation - 404 Page](../site-generation/404_PAGE.md) - 404 page details
- [Site Generation - Home Page](../site-generation/HOME_PAGE.md) - Home page details
- [Admin Panel - Site Settings](../admin-panel/SITE_SETTINGS.md) - Settings management
- [Media Management](../admin-panel/MEDIA.md) - Media library

