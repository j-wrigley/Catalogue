# Frequently Asked Questions

Common questions and answers about the CMS.

## General

### What is JSON Catalogue CMS?

A flat-file CMS that uses YAML blueprints, JSON content files, and PHP templates to generate static HTML sites.

### Do I need a database?

No. The CMS stores all content in JSON files. No database required.

### What are the system requirements?

- PHP 7.4 or higher
- Apache with mod_rewrite (or Nginx with URL rewriting)
- Write permissions on content directories

## Blueprints

### How do I create a new content type?

1. Create a blueprint file: `blueprints/{name}.blueprint.yml`
2. Create a template file: `templates/{name}.php`
3. Content can be created through the admin panel

### Can I use JSON instead of YAML for blueprints?

Currently, only YAML is supported. JSON support may be added in future versions.

### How many fields can I have in a blueprint?

There's no hard limit. However, keep blueprints focused and organized using tabs and groups.

### Can I nest fields?

Not directly. Use the `structure` field type for repeatable nested content.

## Templates

### Do I need to know PHP?

Basic PHP knowledge helps, but the CMS uses simple functions like `catalogue()` that are easy to learn.

### Can I use other template engines?

The CMS uses PHP templates. You can use any PHP code in templates, but the `catalogue()` functions are recommended.

### How do I access nested data?

Use dot notation: `catalogue('image.src')` or `catalogue('author.name')`.

### Can I use JavaScript frameworks?

Yes! The generated HTML is static, so you can use any frontend framework.

## Content

### What's the difference between Pages and Collections?

- **Pages**: Single content items (e.g., About, Contact)
- **Collections**: Groups of similar items (e.g., Blog posts, Projects)

### How do I change a page URL?

For pages, the URL matches the blueprint name. For collections, edit the slug in the admin panel.

### Can I have multiple collections?

Yes! Create multiple blueprints and collection folders.

### How do I delete content?

Click the delete icon in the admin panel. This removes both the JSON file and generated HTML.

## Media

### What file types can I upload?

Images (JPG, PNG, GIF, WebP, SVG), documents (PDF, DOC, DOCX), and other file types.

### Is there a file size limit?

Yes, limited by server `upload_max_filesize` setting. Default is usually 2-10MB.

### Can I organize files into folders?

Yes! Use the "New Folder" button in the media library.

### How do I add alt text to images?

Right-click an image → "Edit Metadata" → Add alt text.

## HTML Generation

### When are HTML files generated?

Automatically when you save content, or manually via "Regenerate All" in CMS Settings.

### Do I need to regenerate after template changes?

Yes. Template changes require manual regeneration using "Regenerate All".

### Where are HTML files saved?

In the root directory of your site (same level as `/catalogue/`).

### Can I customize the HTML output?

Yes! Edit the template files to change HTML structure.

## URLs

### How do URLs work?

Pages use blueprint names: `/about` → `about.html`
Collections use slugs: `/posts/my-post` → `posts/my-post.html`

### Can I remove the .html extension?

Yes! The `.htaccess` file already removes extensions automatically.

### How do I change a collection item URL?

Edit the slug in the admin panel banner when editing the item.

## Security

### Is the CMS secure?

Yes! The CMS includes CSRF protection, XSS prevention, path traversal protection, and more.

### How do I change my password?

Edit your user account in the Users section of the admin panel.

### Can I have multiple users?

Yes! Create multiple user accounts in the Users section.

### Is HTTPS required?

HTTPS is recommended for production but not required. The CMS works with HTTP.

## Troubleshooting

### Pages aren't generating HTML

Check:
1. Blueprint exists
2. Template exists
3. Content file exists
4. File permissions are correct

### Images aren't showing

Check:
1. File uploaded successfully
2. Path is correct in content
3. File permissions allow reading

### Can't log in

Check:
1. Username and password are correct
2. User file exists in `content/users/`
3. Session directory is writable

### 404 page not showing

Check:
1. `.htaccess` `ErrorDocument` path is correct
2. `404-handler.php` exists
3. `404.html` exists

## Development

### Can I extend the CMS?

Yes! The CMS is modular. Add custom functions in `/catalogue/lib/` or extend templates.

### How do I add custom field types?

Field types are defined in `/catalogue/lib/form.php`. Add new types by extending the form generation.

### Can I use Git?

Yes! The CMS is file-based, making it perfect for version control.

### How do I backup my site?

Backup the `/catalogue/content/` directory and `/catalogue/uploads/` directory.

## See Also

- [Quick Reference](./QUICK_REFERENCE.md) - Common tasks
- [Cheat Sheet](./CHEAT_SHEET.md) - Syntax reference
- [Glossary](./GLOSSARY.md) - Terms and definitions

