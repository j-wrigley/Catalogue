# Creating Content

How to create pages and collection items.

## Overview

Creating content involves:
1. Creating a blueprint (if not exists)
2. Adding content via admin panel
3. Saving content
4. HTML generation (automatic)

## Creating Pages

### Steps

1. **Create Blueprint** - Create `{page-name}.blueprint.yml`
2. **Navigate to Pages** - Go to Pages in admin panel
3. **Click Page** - Click on page name in table
4. **Fill Form** - Enter content in form fields
5. **Save** - Click "Save" button
6. **HTML Generated** - HTML file created automatically

### Blueprint First

**Required:** Blueprint must exist before creating content

**Location:** `catalogue/blueprints/{page-name}.blueprint.yml`

**Example:** `about.blueprint.yml`
```yaml
title: About Page
fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
```

### Content Creation

**Location:** `content/pages/{page-name}/{page-name}.json`

**Created:** Automatically when saving

**Structure:** Defined by blueprint

### Example: Creating About Page

1. **Create Blueprint:**
   ```yaml
   # catalogue/blueprints/about.blueprint.yml
   title: About Page
   fields:
     title:
       type: text
     content:
       type: markdown
   ```

2. **Create Content:**
   - Navigate to Pages
   - Click "About" in table
   - Fill form:
     - Title: "About Us"
     - Content: "We are a company..."
   - Click "Save"

3. **Result:**
   - File: `content/pages/about/about.json`
   - HTML: `about.html`
   - URL: `/about`

## Creating Collection Items

### Steps

1. **Create Blueprint** - Create `{collection-name}.blueprint.yml`
2. **Navigate to Collections** - Go to Collections in admin panel
3. **Select Collection** - Click collection name in sidebar
4. **Click "New Item"** - Create new item
5. **Fill Form** - Enter content in form fields
6. **Set Slug** - Enter URL slug
7. **Save** - Click "Save" button
8. **HTML Generated** - HTML file created automatically

### Blueprint First

**Required:** Blueprint must exist before creating items

**Location:** `catalogue/blueprints/{collection-name}.blueprint.yml`

**Example:** `posts.blueprint.yml`
```yaml
title: Blog Post
fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
```

### Content Creation

**Location:** `content/collections/{collection-name}/{slug}.json`

**Created:** Automatically when saving

**Filename:** Based on slug (sanitized)

### Slug Management

**Slug:** URL-friendly identifier

**Auto-generated:** From title if not provided

**Editable:** Can be changed in banner

**Format:** Lowercase, hyphens, no spaces

**Examples:**
- "My First Post" → `my-first-post`
- "Project #1" → `project-1`

### Example: Creating Blog Post

1. **Create Blueprint:**
   ```yaml
   # catalogue/blueprints/posts.blueprint.yml
   title: Blog Post
   fields:
     title:
       type: text
     content:
       type: markdown
   ```

2. **Create Content:**
   - Navigate to Collections
   - Click "Posts" in sidebar
   - Click "New Item"
   - Fill form:
     - Title: "My First Post"
     - Content: "This is my first post..."
   - Set slug: `my-first-post`
   - Click "Save"

3. **Result:**
   - File: `content/collections/posts/my-first-post.json`
   - HTML: `posts/my-first-post.html`
   - URL: `/posts/my-first-post`

## Form Fields

### Field Types

Fields are defined in blueprint:

- **text** - Single line text
- **textarea** - Multi-line text
- **markdown** - Rich text editor
- **file** - File upload
- **select** - Dropdown selection
- **radio** - Radio buttons
- **checkbox** - Checkboxes
- **tags** - Tag input
- **slider** - Number slider
- **switch** - Toggle switch
- **structure** - Repeatable table

### Core Fields

Automatically added to all content:

- **Featured** - Switch to feature content
- **Status** - Dropdown (draft, published, unlisted)

### Required Fields

Marked with `required: true` in blueprint:

- Must be filled before saving
- Validation on form submission
- Error message if empty

## Saving Content

### Save Process

1. **Form Submission** - Form data sent to server
2. **Validation** - Fields validated
3. **JSON Creation** - Data converted to JSON
4. **File Write** - JSON saved to file
5. **HTML Generation** - HTML file generated
6. **Success Message** - Toast notification shown

### Save Location

**Pages:**
```
content/pages/{page-name}/{page-name}.json
```

**Collections:**
```
content/collections/{collection-name}/{slug}.json
```

### Metadata

Automatically added to content:

```json
{
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-01-01T00:00:00+00:00",
    "author": "username"
  }
}
```

## HTML Generation

### Automatic Generation

HTML files generated automatically:
- On content save
- On content update
- On collection item save

### Generation Process

1. **Load Content** - Read JSON file
2. **Load Blueprint** - Get blueprint definition
3. **Load Template** - Get PHP template
4. **Render** - Execute template with content
5. **Save HTML** - Write HTML file

### HTML Location

**Pages:**
```
{page-name}.html
```

**Collections:**
```
{collection-name}/{slug}.html
```

**Special:**
- `home` → `index.html`
- `404` → `404.html`

## Best Practices

### Content Creation

- **Create blueprint first** - Define structure before content
- **Use descriptive slugs** - Clear, URL-friendly slugs
- **Fill required fields** - Complete all required fields
- **Add metadata** - Include alt text, descriptions
- **Test after creation** - Verify HTML generation

### Naming

- **Pages** - Use lowercase, descriptive names
- **Collections** - Use plural, descriptive names
- **Slugs** - Use lowercase, hyphens, no spaces

### Organization

- **Group related content** - Use consistent naming
- **Use folders** - Organize media files
- **Keep structure simple** - Don't over-complicate

## Troubleshooting

### Content Not Saving

**Problem:** Form submission fails

**Solutions:**
1. Check required fields are filled
2. Verify CSRF token is valid
3. Check file permissions
4. Review error logs

### HTML Not Generating

**Problem:** HTML file not created

**Solutions:**
1. Check template exists
2. Verify blueprint exists
3. Check file permissions
4. Review error logs

### Slug Issues

**Problem:** Slug not working correctly

**Solutions:**
1. Verify slug is valid (lowercase, hyphens)
2. Check for duplicate slugs
3. Ensure slug matches filename
4. Regenerate HTML files

## See Also

- [Pages vs Collections](./PAGES_VS_COLLECTIONS.md) - Content types
- [Editing Content](./EDITING_CONTENT.md) - Modifying content
- [Content Structure](./CONTENT_STRUCTURE.md) - JSON format
- [Blueprints & Content](./BLUEPRINTS_CONTENT.md) - Blueprint relationship

