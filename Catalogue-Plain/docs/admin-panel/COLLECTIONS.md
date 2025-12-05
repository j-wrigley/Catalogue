# Collections Management

Creating, editing, and managing collection items in the CMS.

## Overview

Collections are groups of similar content items. Examples include blog posts, projects, products, etc. Each collection item has its own page.

## Accessing

Navigate to **Collections** in the sidebar or visit:
```
/catalogue/index.php?page=collections
```

## Collections Interface

The collections interface has two views:

### Sidebar View
- Lists all collections on the left
- Click collection name to view items
- Shows item count per collection

### Main View
- Displays items for selected collection
- Table view with all items
- Search and filter options

## Collections Table

The table displays collection items with:

| Column | Description |
|--------|-------------|
| **Title** | Item title |
| **Status** | Published, Draft, or Unlisted |
| **Featured** | Featured indicator (star icon) |
| **Updated** | Last update date |
| **Created** | Creation date |
| **Actions** | Edit and delete buttons |

### Table Features

- **Sorting** - Click column headers to sort
- **Search** - Filter items by title
- **Pagination** - Navigate through pages
- **Responsive** - Scrollable on mobile

## Creating a Collection Item

### Steps

1. Select collection from sidebar
2. Click **"New Item"** button
3. Fill in form fields
4. Set slug (URL identifier)
5. Set status (Published/Draft/Unlisted)
6. Toggle featured if needed
7. Click **"Save"**

### Slug Management

- **Auto-generated** - Created from title if not provided
- **Editable** - Can be changed in banner
- **URL-safe** - Only letters, numbers, hyphens, underscores
- **Unique** - Must be unique within collection

## Editing a Collection Item

### Steps

1. Click **edit icon** (pencil) next to an item
2. Modify fields as needed
3. Update slug if needed
4. Update status or featured flag
5. Click **"Save"**

### Banner Section

When editing, a banner appears at the top with:
- **Status** - Dropdown to change publication status
- **Featured** - Toggle switch
- **Slug** - URL slug editor
- **View Page** - Link to view item on frontend
- **Save/Cancel** - Action buttons

## Deleting a Collection Item

### Steps

1. Click **delete icon** (trash) next to an item
2. Confirm deletion in dialog
3. Item is permanently deleted

### Restrictions

- Deletion cannot be undone
- Associated HTML files are also deleted
- Item removed from collection data file

## Collection Status

### Published
- Visible on frontend
- Appears in collection lists
- Accessible via URL

### Draft
- Not visible on frontend
- Hidden from collection lists
- Can be edited and published later

### Unlisted
- Accessible via direct URL
- Hidden from collection lists
- Not shown in archives

## Featured Items

- Mark items as featured
- Displayed with star icon in table
- Can be used in templates to highlight content

## Core Fields

All collection items automatically include:

- **Status** - Publication status dropdown
- **Featured** - Featured toggle switch
- **Slug** - URL identifier

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

## Pagination

### Features

- **Items per page** - Configurable (default: 10)
- **Page navigation** - Previous/Next buttons
- **Page numbers** - Click to jump to page
- **Current page** - Highlighted in navigation

### Changing Items Per Page

1. Use dropdown in table header
2. Select number of items
3. Table updates immediately
4. Preference saved in browser

## Examples

### Creating a Blog Post

1. Select "posts" collection from sidebar
2. Click "New Item"
3. Enter title: "My First Post"
4. Add content in markdown editor
5. Set slug: "my-first-post"
6. Set status to "Published"
7. Click "Save"

### Editing a Post

1. Find post in table
2. Click edit icon
3. Modify title or content
4. Update slug if needed
5. Click "Save"
6. Post updates immediately

### Changing Slug

1. Edit item
2. Modify slug in banner
3. Save
4. Old HTML file deleted, new one created
5. URL changes automatically

## See Also

- [Pages](./PAGES.md) - Managing pages
- [Blueprints](../blueprints/README.md) - Creating content types
- [Templates](../templates/COLLECTIONS.md) - Displaying collections

