# Pages Management

Creating, editing, and managing pages in the CMS.

## Overview

Pages are individual content items that each have their own URL. Examples include "About", "Contact", "Home", etc.

## Accessing

Navigate to **Pages** in the sidebar or visit:
```
/catalogue/index.php?page=pages
```

## Pages Table

The pages table displays all pages with:

| Column | Description |
|--------|-------------|
| **Title** | Page title |
| **Status** | Published, Draft, or Unlisted |
| **Featured** | Featured indicator (star icon) |
| **Updated** | Last update date |
| **Created** | Creation date |
| **Actions** | Edit and delete buttons |

### Table Features

- **Sorting** - Click column headers to sort
- **Search** - Filter pages by title
- **Responsive** - Scrollable on mobile devices
- **Sticky Actions** - Actions column stays visible while scrolling

## Creating a Page

### Steps

1. Click **"New Page"** button
2. Select content type (blueprint) from dropdown
3. Fill in form fields
4. Set status (Published/Draft/Unlisted)
5. Toggle featured if needed
6. Click **"Save"**

### Content Types

Pages are created from blueprints. Available blueprints appear in the "New Page" dropdown:
- Each blueprint defines the fields available
- Blueprints can have tabs, columns, and groups
- Fields are rendered based on blueprint configuration

## Editing a Page

### Steps

1. Click **edit icon** (pencil) next to a page
2. Modify fields as needed
3. Update status or featured flag
4. Click **"Save"**

### Banner Section

When editing, a banner appears at the top with:
- **Status** - Dropdown to change publication status
- **Featured** - Toggle switch
- **Slug** - URL slug editor (for collections)
- **View Page** - Link to view page on frontend
- **Save/Cancel** - Action buttons

## Deleting a Page

### Steps

1. Click **delete icon** (trash) next to a page
2. Confirm deletion in dialog
3. Page is permanently deleted

### Restrictions

- Deletion cannot be undone
- Associated HTML files are also deleted
- Data files are removed

## Page Status

### Published
- Visible on frontend
- Appears in navigation (if configured)
- Accessible via URL

### Draft
- Not visible on frontend
- Hidden from navigation
- Can be edited and published later

### Unlisted
- Accessible via direct URL
- Hidden from navigation
- Not shown in lists

## Featured Pages

- Mark pages as featured
- Displayed with star icon in table
- Can be used in templates to highlight content

## Core Fields

All pages automatically include:

- **Status** - Publication status dropdown
- **Featured** - Featured toggle switch

These fields appear in the banner when editing.

## Form Features

### Tabs

If blueprint defines tabs:
- Fields organized into tabbed sections
- Click tabs to switch between sections
- Only active tab content is visible

### Columns

If blueprint defines columns:
- Fields arranged in grid layout
- Multiple columns supported
- Responsive (single column on mobile)

### Groups

If blueprint defines groups:
- Fields in same group align horizontally
- Groups maintain alignment across columns
- Useful for related fields

## Examples

### Creating an About Page

1. Click "New Page"
2. Select "about" blueprint
3. Enter title: "About Us"
4. Add content in markdown editor
5. Set status to "Published"
6. Click "Save"

### Editing a Page

1. Find page in table
2. Click edit icon
3. Modify title or content
4. Click "Save"
5. Page updates immediately

### Changing Status

1. Edit page
2. Use Status dropdown in banner
3. Select new status
4. Save
5. Status updates in table

## See Also

- [Collections](./COLLECTIONS.md) - Managing collections
- [Blueprints](../blueprints/README.md) - Creating content types
- [Site Settings](./SITE_SETTINGS.md) - Site configuration

