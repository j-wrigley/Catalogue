# Glossary

Terms and definitions used in the CMS.

## A

### Admin Panel
The web interface for managing CMS content, accessible at `/catalogue/index.php`.

### Authentication
The process of verifying user identity through login credentials.

### Authorization
The process of determining what actions a user can perform.

## B

### Blueprint
A YAML file that defines the structure and fields for a content type. Stored in `/catalogue/blueprints/`.

### Blueprint Mapping
The 1:1 relationship between blueprints and templates (e.g., `about.blueprint.yml` → `about.php`).

## C

### Catalogue Function
The main template function `catalogue()` used to access content data.

### Collection
A group of similar content items (e.g., blog posts, projects). Each item has its own page.

### Content Type
A type of content defined by a blueprint (e.g., "about", "posts", "projects").

### CSRF (Cross-Site Request Forgery)
An attack where unauthorized commands are transmitted from a user. Prevented with CSRF tokens.

## D

### Data Directory
Public JSON files directory (`/catalogue/data/`) used by frontend JavaScript.

### Draft
Content status indicating the item is not published and not visible on the frontend.

## F

### Featured
A flag that marks content as featured. Can be used to highlight content in templates.

### Field
A single input in a blueprint (e.g., title, content, image).

### Field Type
The type of field (text, markdown, file, tags, etc.) defined in a blueprint.

### Flat-File CMS
A CMS that stores content in files (JSON) rather than a database.

## G

### Group
A blueprint attribute that groups fields horizontally, maintaining alignment across columns.

## H

### Home Page
The main page of the site, generated as `index.html` from `home.blueprint.yml`.

### HTML Generation
The process of converting PHP templates and JSON content into static HTML files.

## I

### Index.html
The home page file generated from the `home` blueprint and template.

## J

### JSON
JavaScript Object Notation - the format used for storing content data.

## M

### Markdown
A lightweight markup language for formatting text. Used in markdown fields.

### Media Library
The file management system for uploading and organizing images and files.

### Metadata
Additional data stored with content (e.g., created date, updated date, author).

## P

### Page
An individual content item with its own URL (e.g., "About", "Contact").

### Path Traversal
A security attack attempting to access files outside allowed directories. Prevented with path validation.

### Published
Content status indicating the item is visible on the frontend.

## R

### Regeneration
The process of regenerating HTML files from templates and content.

### Render
The process of converting content data into HTML output.

## S

### Session
A server-side storage mechanism for maintaining user login state.

### Slug
A URL-friendly identifier for content (e.g., "my-awesome-post" from "My Awesome Post!").

### Snippet
A reusable template component stored in `/catalogue/templates/snippets/`.

### Static Site Generation (SSG)
The process of generating static HTML files from templates and content.

## T

### Template
A PHP file that defines the HTML structure for a content type. Stored in `/catalogue/templates/`.

### Template Mapping
The relationship between blueprints and templates (1:1 mapping).

## U

### Unlisted
Content status indicating the item is accessible via direct URL but hidden from lists/navigation.

### Uploads Directory
The directory for uploaded media files (`/catalogue/uploads/`).

## X

### XSS (Cross-Site Scripting)
An attack where malicious scripts are injected into web pages. Prevented with output escaping.

## Y

### YAML
YAML Ain't Markup Language - the format used for blueprint files.

## See Also

- [FAQ](./FAQ.md) - Common questions
- [Cheat Sheet](./CHEAT_SHEET.md) - Syntax reference

