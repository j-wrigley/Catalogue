# Editing Content

How to modify existing pages and collection items.

## Overview

Editing content involves:
1. Opening content in admin panel
2. Modifying form fields
3. Saving changes
4. HTML regeneration (automatic)

## Editing Pages

### Steps

1. **Navigate to Pages** - Go to Pages in admin panel
2. **Click Page** - Click page name in table
3. **Edit Form** - Modify form fields
4. **Save** - Click "Save" button
5. **HTML Regenerated** - HTML file updated automatically

### Accessing Pages

**From Pages Table:**
- View all pages in table
- Click page name to edit
- See status, updated date
- Filter and sort

### Form Editing

- **All fields editable** - Modify any field
- **Real-time preview** - See changes as you type
- **Validation** - Required fields validated
- **Media picker** - Select files easily

### Save Changes

- **Automatic save** - Click "Save" button
- **Toast notification** - Success message shown
- **No redirect** - Stay on edit page
- **HTML regenerated** - File updated immediately

## Editing Collection Items

### Steps

1. **Navigate to Collections** - Go to Collections in admin panel
2. **Select Collection** - Click collection name in sidebar
3. **Click Item** - Click item in table
4. **Edit Form** - Modify form fields
5. **Update Slug** - Change slug if needed
6. **Save** - Click "Save" button
7. **Redirect** - Redirected back to collection table

### Accessing Items

**From Collections Table:**
- View all items in collection
- Click item to edit
- See status, featured, dates
- Filter, sort, paginate

### Slug Editing

**Location:** Banner section (top of form)

**Features:**
- Edit slug directly
- Auto-generated from title
- Validated (lowercase, hyphens)
- Updates filename automatically

**Important:** Changing slug updates filename and URL

## Form Features

### Field Types

All blueprint field types are editable:

- **Text** - Single line input
- **Textarea** - Multi-line input
- **Markdown** - Rich text editor
- **File** - Media picker
- **Select** - Dropdown
- **Radio** - Radio buttons
- **Checkbox** - Checkboxes
- **Tags** - Tag input
- **Slider** - Number slider
- **Switch** - Toggle
- **Structure** - Repeatable table

### Core Fields

**Featured:**
- Toggle switch
- Mark content as featured
- Shown in banner

**Status:**
- Dropdown selection
- Options: Draft, Published, Unlisted
- Controls visibility

### Tabs

If blueprint defines tabs:
- Fields organized into tabs
- Click tab to switch sections
- Clean organization

### Groups

If blueprint defines groups:
- Related fields grouped
- Multi-column layout
- Visual organization

## Saving Changes

### Save Process

1. **Form Submission** - Modified data sent
2. **Validation** - Fields validated
3. **JSON Update** - JSON file updated
4. **HTML Regeneration** - HTML file regenerated
5. **Success Message** - Toast notification

### File Updates

**Pages:**
- JSON file updated
- HTML file regenerated
- Metadata updated

**Collections:**
- JSON file updated
- HTML file regenerated
- Filename updated if slug changed
- Old file deleted if slug changed

### Metadata Updates

**Updated Automatically:**
```json
{
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-11-25T12:00:00+00:00",
    "author": "username"
  }
}
```

## Slug Changes

### Changing Slug

**For Collections:**
1. Edit slug in banner
2. Save content
3. Filename updated
4. Old file deleted
5. HTML regenerated

### Slug Validation

- **Lowercase only** - Auto-converted
- **Hyphens allowed** - Spaces converted
- **No special chars** - Removed automatically
- **Unique** - Within collection

### URL Updates

**Old URL:** `/posts/old-slug`
**New URL:** `/posts/new-slug`

**Important:** Old URL no longer works (404)

## Bulk Operations

### Currently Not Supported

- Bulk edit
- Bulk delete
- Bulk status change
- Bulk featured toggle

**Workaround:** Edit items individually

## Best Practices

### Editing Workflow

1. **Review content** - Check current state
2. **Make changes** - Edit fields carefully
3. **Preview** - Use "View Page" button
4. **Save** - Save changes
5. **Verify** - Check generated HTML

### Content Updates

- **Update regularly** - Keep content fresh
- **Check links** - Verify internal links
- **Test after changes** - Verify HTML generation
- **Backup before major changes** - Safety first

### Slug Management

- **Change carefully** - Affects URLs
- **Update links** - Update internal links
- **Use redirects** - If needed (manual)
- **Document changes** - Note slug changes

## Troubleshooting

### Changes Not Saving

**Problem:** Form submission fails

**Solutions:**
1. Check required fields
2. Verify CSRF token
3. Check file permissions
4. Review error logs

### HTML Not Updating

**Problem:** HTML file not regenerated

**Solutions:**
1. Check save succeeded
2. Verify template exists
3. Check file permissions
4. Manually regenerate

### Slug Not Updating

**Problem:** Filename not changing

**Solutions:**
1. Verify slug is valid
2. Check file permissions
3. Ensure old file deleted
4. Regenerate HTML

## See Also

- [Creating Content](./CREATING_CONTENT.md) - Creating new content
- [Content Structure](./CONTENT_STRUCTURE.md) - JSON format
- [Content Workflow](./CONTENT_WORKFLOW.md) - Best practices

