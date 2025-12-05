# Installation

Step-by-step guide to installing JSON Catalogue CMS.

## Download

### Option 1: Clone Repository

```bash
git clone https://github.com/your-repo/json-catalogue.git
cd json-catalogue
```

### Option 2: Download ZIP

1. Download the latest release
2. Extract ZIP file
3. Upload to your server

## Installation Steps

### Step 1: Upload Files

**Upload the `catalogue` directory to your server:**

**Root Installation:**
```
/var/www/html/
  catalogue/          ← Upload here
  assets/             ← Create this
  .htaccess           ← Upload this
```

**Subfolder Installation:**
```
/var/www/html/
  mysite/
    catalogue/        ← Upload here
    assets/           ← Create this
    .htaccess         ← Upload this
```

### Step 2: Set File Permissions

**Set directory permissions:**
```bash
chmod 755 catalogue/content
chmod 755 catalogue/uploads
chmod 755 catalogue/logs
chmod 755 catalogue/data
```

**Set file permissions:**
```bash
chmod 644 catalogue/config.php
chmod 644 .htaccess
```

**Set ownership (if needed):**
```bash
# Apache (Debian/Ubuntu)
chown -R www-data:www-data catalogue/

# Apache (CentOS/RHEL)
chown -R apache:apache catalogue/

# Nginx
chown -R nginx:nginx catalogue/
```

### Step 3: Verify .htaccess

**Check .htaccess exists in root:**
```bash
ls -la .htaccess
```

**Verify mod_rewrite enabled:**
```bash
apache2ctl -M | grep rewrite
```

**Enable if needed:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Step 4: Create Required Directories

**Directories are created automatically, but verify:**
```bash
ls -la catalogue/content/
ls -la catalogue/uploads/
ls -la catalogue/logs/
```

**If missing, create manually:**
```bash
mkdir -p catalogue/content/pages
mkdir -p catalogue/content/collections
mkdir -p catalogue/content/users
mkdir -p catalogue/content/media
mkdir -p catalogue/uploads
mkdir -p catalogue/logs
mkdir -p catalogue/data
```

### Step 5: Verify Installation

**Check PHP version:**
```bash
php -v
# Should be 7.4 or higher
```

**Test file access:**
```bash
curl http://your-domain.com/catalogue/index.php?page=login
```

**Check error logs:**
```bash
tail -f catalogue/logs/php_errors.log
```

## Installation Types

### Root Installation

**Structure:**
```
/var/www/html/
  catalogue/
  assets/
  .htaccess
  index.html (generated)
```

**URLs:**
- Admin: `http://example.com/catalogue/`
- Site: `http://example.com/`

**BASE_PATH:** Empty (`''`)

### Subfolder Installation

**Structure:**
```
/var/www/html/
  mysite/
    catalogue/
    assets/
    .htaccess
    index.html (generated)
```

**URLs:**
- Admin: `http://example.com/mysite/catalogue/`
- Site: `http://example.com/mysite/`

**BASE_PATH:** `/mysite`

## Post-Installation

### 1. Access Admin Panel

Navigate to:
```
http://your-domain.com/catalogue/index.php?page=login
```

### 2. Create First User

If no users exist, you'll be prompted to create one.

**Default (if exists):**
- Username: `admin`
- Password: `admin`

**⚠️ Change immediately after first login!**

### 3. Verify Path Detection

**Check paths are correct:**
- Admin panel loads correctly
- No path errors in logs
- URLs work as expected

### 4. Test File Uploads

**Test upload:**
1. Go to Media section
2. Click "Upload"
3. Select a test image
4. Verify upload succeeds

## Troubleshooting

### Cannot Access Admin Panel

**Check:**
1. PHP version (must be 7.4+)
2. File permissions
3. .htaccess exists
4. mod_rewrite enabled
5. Error logs for details

### Permission Errors

**Fix:**
```bash
chmod 755 catalogue/content
chmod 755 catalogue/uploads
chmod 755 catalogue/logs
```

### Path Issues

**Check:**
1. BASE_PATH detection
2. CMS_URL correct
3. .htaccess location
4. Apache configuration

## Next Steps

After installation:

1. **Create First User** - Set up admin account
2. **Configure Site** - Set site settings
3. **Create First Page** - Add your first content
4. **Customize Theme** - Set CMS appearance
5. **Review Security** - Check security settings

## See Also

- [System Requirements](./REQUIREMENTS.md) - What you need
- [First-Time Setup](./FIRST_SETUP.md) - Initial configuration
- [Troubleshooting](../troubleshooting/README.md) - Common issues
- [Environment Setup](../configuration/ENVIRONMENT.md) - Server config

