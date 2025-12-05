# First-Time Setup

Configure your CMS after installation.

## Overview

After installation, you need to:
1. Create your first user
2. Configure site settings
3. Set up CMS preferences
4. Verify everything works

## Step 1: Create First User

### Access Login Page

Navigate to:
```
http://your-domain.com/catalogue/index.php?page=login
```

### Create User

**If no users exist:**
1. You'll see a user creation form
2. Enter username and password
3. Click "Create User"
4. User file created automatically

**If default user exists:**
- Username: `admin`
- Password: `admin`
- **⚠️ Change password immediately!**

### Change Default Password

**After first login:**
1. Go to **Users** in sidebar
2. Click your username
3. Enter new password
4. Save changes

## Step 2: Configure Site Settings

### Access Site Settings

Navigate to **Site** in sidebar (or `/catalogue/index.php?page=settings`)

### Basic Settings

**Fill in:**
- **Site Name** - Your website name
- **Site Tagline** - Short description
- **Site Description** - Full description

### SEO Settings

**Configure:**
- **Meta Title** - Default page title
- **Meta Description** - Default description
- **Meta Keywords** - SEO keywords
- **Open Graph Image** - Social sharing image

### Save Settings

Click **"Save Settings"** button
- Settings saved immediately
- Available in templates
- Used across site

## Step 3: Configure CMS Settings

### Access CMS Settings

Navigate to **Settings** in sidebar footer (or `/catalogue/index.php?page=cms-settings`)

### General Settings

**CMS Name:**
- Name displayed in admin panel
- Default: "JSON Catalogue"
- Customize to your brand

### Theme Settings

**Choose Theme:**
1. Expand "Theme" accordion
2. Click preset color card
3. Or customize colors manually
4. Save settings

**Theme Presets:**
- Red (default)
- Blue
- Green
- Purple
- Orange
- Pink
- Teal
- Indigo

### Traffic Tracking

**Enable/Disable:**
- Toggle traffic tracking
- Default: Enabled
- Controls dashboard card visibility

## Step 4: Verify Installation

### Check Admin Panel

**Verify:**
- Dashboard loads correctly
- All sections accessible
- No error messages
- Navigation works

### Test Content Creation

**Create test page:**
1. Go to **Pages**
2. Create a test blueprint
3. Add test content
4. Verify HTML generated

### Test File Uploads

**Upload test file:**
1. Go to **Media**
2. Click "Upload"
3. Select test image
4. Verify upload succeeds

### Check Generated HTML

**Verify:**
- HTML files created
- URLs work correctly
- Content displays properly
- No errors in logs

## Step 5: Security Checklist

### Change Default Password

**If using default:**
- Change immediately
- Use strong password
- Don't reuse passwords

### Review Security Settings

**Check:**
- DEBUG_MODE set to false (production)
- Error display disabled
- Error logging enabled
- HTTPS enabled (if available)

### File Permissions

**Verify:**
- Directories: `755`
- Files: `644`
- No `777` permissions
- Web server ownership correct

## Step 6: Create Required Pages

### Home Page

**Create home blueprint:**
1. Create `home.blueprint.yml`
2. Create `templates/home.php`
3. Add content via Pages
4. Generates `index.html`

### 404 Page

**Create 404 blueprint:**
1. Create `404.blueprint.yml`
2. Create `templates/404.php`
3. Add content via Pages
4. Generates `404.html`

**Verify handler:**
- `catalogue/404-handler.php` exists
- `.htaccess` configured
- ErrorDocument set correctly

## Common Setup Issues

### Path Detection Problems

**Symptoms:**
- URLs include `/catalogue/`
- Assets not loading
- Path errors

**Solutions:**
- Check BASE_PATH detection
- Verify .htaccess location
- Review config.php paths

### Permission Issues

**Symptoms:**
- Cannot save content
- Uploads fail
- Generation fails

**Solutions:**
- Check directory permissions
- Verify file permissions
- Check ownership

### User Creation Issues

**Symptoms:**
- Cannot create user
- Login fails
- User file not created

**Solutions:**
- Check `content/users/` permissions
- Verify directory exists
- Check error logs

## Next Steps

After setup:

1. **Create Your First Page** - See [Your First Page](./FIRST_PAGE.md)
2. **Create Your First Collection** - See [Your First Collection](./FIRST_COLLECTION.md)
3. **Customize Templates** - See [Templates Documentation](../templates/README.md)
4. **Learn Blueprints** - See [Blueprints Documentation](../blueprints/README.md)

## See Also

- [Installation](./INSTALLATION.md) - Installation steps
- [Basic Configuration](./BASIC_CONFIG.md) - Essential settings
- [Troubleshooting](../troubleshooting/README.md) - Common issues
- [Security Checklist](../security/PRODUCTION_CHECKLIST.md) - Security setup

