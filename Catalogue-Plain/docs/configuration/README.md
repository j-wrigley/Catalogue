# Configuration Documentation

Complete guide to configuring the CMS system.

## Quick Links

- **[Core Configuration](./CORE_CONFIG.md)** - `config.php` settings
- **[Site Settings](./SITE_SETTINGS.md)** - Public site configuration
- **[CMS Settings](./CMS_SETTINGS.md)** - Admin panel configuration
- **[Path Configuration](./PATHS.md)** - Directory and URL paths
- **[Security Configuration](./SECURITY.md)** - Security settings
- **[Environment Setup](./ENVIRONMENT.md)** - Server and environment configuration

## Overview

The CMS uses multiple configuration layers:
- **Core Configuration** - PHP constants in `config.php`
- **Site Settings** - Public-facing settings (editable in admin)
- **CMS Settings** - Admin panel settings (editable in admin)
- **Server Configuration** - Apache/PHP settings

## Configuration Types

### Core Configuration (`config.php`)

- **Path constants** - Directory and URL paths
- **Security settings** - Session, CSRF, headers
- **Debug mode** - Development/production toggle
- **Directory structure** - Required folders

### Site Settings

- **Site information** - Name, description, tagline
- **SEO settings** - Meta tags, Open Graph
- **Social media** - Social links
- **Editable** - Via admin panel "Site" section

### CMS Settings

- **CMS name** - Admin panel branding
- **Theme colors** - Admin panel appearance
- **Traffic tracking** - Enable/disable analytics
- **Editable** - Via admin panel "Settings" section

## Quick Start

1. **Review Core Config** - Check `config.php` for your setup
2. **Configure Paths** - Ensure paths match your installation
3. **Set Site Settings** - Configure via admin panel
4. **Customize CMS** - Set theme and CMS name
5. **Security** - Review security settings

## Next Steps

1. Read [Core Configuration](./CORE_CONFIG.md) for PHP constants
2. Check [Site Settings](./SITE_SETTINGS.md) for public settings
3. Review [CMS Settings](./CMS_SETTINGS.md) for admin panel
4. See [Path Configuration](./PATHS.md) for directory structure
5. Review [Security Configuration](./SECURITY.md) for security

