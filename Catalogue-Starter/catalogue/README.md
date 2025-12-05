# JSON Catalogue CMS

A modern, modular flat-file CMS built with PHP, YAML blueprints, and JSON content files.

## Features

- **Flat-file architecture** - No database required
- **YAML blueprints** - Define content structure
- **JSON content** - Simple, readable content files
- **Modular PHP backend** - Clean, maintainable code
- **Modern admin panel** - Clean, minimal design
- **Security focused** - CSRF protection, authentication

## Installation

1. Upload the `cms` directory to your web server
2. Ensure PHP 7.4+ is installed
3. Set proper permissions on directories:
   ```bash
   chmod 755 cms/content cms/data cms/uploads cms/logs
   ```
4. Access the admin panel at `/cms/index.php?page=login`
5. Default credentials: `admin` / `admin`

## Directory Structure

```
/cms
  /blueprints      - Content type definitions (YAML)
  /content         - Raw JSON content files
  /data            - Public JSON for frontend
  /uploads         - Media files
  /panel           - Admin interface
  /lib             - PHP helper functions
```

## Creating Content Types

1. Create a blueprint file in `/cms/blueprints/`:
   ```yaml
   title: My Content Type
   fields:
     title:
       type: text
       required: true
     body:
       type: textarea
   ```

2. Content files are stored in `/cms/content/[type]/[file].json`

## Usage

### Admin Panel
- Login at `/cms/index.php?page=login`
- Navigate through the admin panel to manage content
- Create, edit, and delete content items

### Frontend Integration
The CMS generates JSON files in `/cms/data/` that can be consumed by your frontend:

```javascript
fetch('/cms/data/about.json')
  .then(response => response.json())
  .then(data => {
    // Use your content data
  });
```

## Security Notes

- Change default admin password immediately
- Keep `/cms/` directory secured
- Use HTTPS in production
- Regularly update PHP version

## Development

The CMS is built modularly, making it easy to extend:

- Add new field types in `/cms/lib/blueprint.php`
- Customize admin UI in `/cms/panel/`
- Add new actions in `/cms/panel/actions/`

## License

MIT License - feel free to use and modify as needed.

