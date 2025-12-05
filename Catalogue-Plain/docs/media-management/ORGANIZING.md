# Organizing Files

Managing folders and organizing media files.

## Overview

The media library supports folder-based organization, allowing you to organize files logically and keep your media library tidy.

## Folder Structure

### Default Structure

```
/uploads/
  images/
    header.jpg
    logo.png
  documents/
    guide.pdf
  blog-images/
    post-1.jpg
    post-2.jpg
  file.jpg
```

### Nested Folders

Folders can be nested:
```
/uploads/
  images/
    blog/
      featured/
        image.jpg
```

## Creating Folders

### Steps

1. Navigate to desired location
2. Click **"New Folder"** button
3. Enter folder name
4. Folder created immediately
5. Navigate into folder

### Folder Naming

- Use descriptive names
- Letters, numbers, hyphens, underscores allowed
- No spaces (automatically converted)
- Case-sensitive

### Examples

- `blog-images`
- `product-photos`
- `documents-2024`
- `header_images`

## Navigating Folders

### Sidebar Navigation

- **All Media** - View all files across folders
- **Folder Tree** - Click folders to navigate
- **Nested Folders** - Indented to show hierarchy
- **Current Folder** - Highlighted in sidebar

### Breadcrumb Navigation

- Shows current folder path
- Click any part to navigate up
- Shows full path from root
- Easy navigation back

### Folder Actions

- **Click folder** - Navigate into folder
- **Click "All Media"** - View all files
- **Use breadcrumbs** - Navigate up levels

## Moving Files

### Steps

1. Right-click file
2. Select **"Move"**
3. Choose destination folder
4. File moved immediately
5. File appears in new location

### Move Restrictions

- Cannot move to same folder
- Cannot move to current folder (if already there)
- Root folder hidden if already at root

### Moving Multiple Files

Currently, files must be moved individually. Bulk move may be added in future versions.

## Folder Management

### Viewing Folder Contents

- **Grid View** - Thumbnail previews
- **List View** - Table layout
- **Folder Count** - Shows file count

### Folder Information

- **Name** - Folder name
- **File Count** - Number of files
- **Last Modified** - Last update date

## Best Practices

### Organization Strategy

- **By Type** - Images, documents, videos
- **By Content** - Blog images, product photos
- **By Date** - 2024, 2025 folders
- **By Project** - Project-specific folders

### Folder Depth

- **Keep it shallow** - 2-3 levels maximum
- **Avoid deep nesting** - Hard to navigate
- **Use descriptive names** - Clear purpose

### Naming Conventions

- **Consistent** - Use same pattern
- **Descriptive** - Clear purpose
- **Lowercase** - Avoid case issues
- **Hyphens** - Use hyphens, not spaces

## Examples

### Blog Images Organization

```
/uploads/
  blog-images/
    featured/
      post-1.jpg
      post-2.jpg
    thumbnails/
      post-1-thumb.jpg
```

### Product Photos

```
/uploads/
  products/
    category-1/
      product-a.jpg
      product-b.jpg
    category-2/
      product-c.jpg
```

### Documents

```
/uploads/
  documents/
    2024/
      reports/
        annual-report.pdf
    2025/
      guides/
        user-guide.pdf
```

## Folder Operations

### Creating Nested Folders

1. Navigate to parent folder
2. Click "New Folder"
3. Enter name
4. Folder created inside parent

### Deleting Folders

1. Right-click folder
2. Select "Delete"
3. Confirm deletion
4. Folder and contents deleted

**Warning:** Deleting a folder deletes all files inside!

### Renaming Folders

Currently, folders cannot be renamed. Delete and recreate, or move files to new folder.

## "All Media" View

### Purpose

The "All Media" view shows:
- All files from all folders
- Recursive file listing
- Easy to find files
- No folder filtering

### When to Use

- **Finding files** - Search across all folders
- **Quick access** - See all files at once
- **Overview** - Get sense of total files

### Limitations

- **No folder context** - Files shown without folder
- **Large lists** - May be slow with many files
- **Less organized** - Harder to navigate

## See Also

- [Uploading Files](./UPLOADING.md) - Uploading to folders
- [File Operations](./FILE_OPERATIONS.md) - Moving files
- [Media Picker](./MEDIA_PICKER.md) - Folder navigation in picker

