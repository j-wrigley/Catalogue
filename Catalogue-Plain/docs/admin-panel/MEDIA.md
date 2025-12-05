# Media Management

Uploading, organizing, and managing media files in the CMS.

## Overview

The media library provides a centralized location for all uploaded files. Files can be organized into folders and used throughout your content.

## Accessing

Navigate to **Media** in the sidebar or visit:
```
/catalogue/index.php?page=media
```

## Media Interface

The media interface has two main areas:

### Sidebar (Left)
- **Folders** - Folder navigation tree
- **All Media** - View all files across folders
- **Current Folder** - Shows current location
- Click folders to navigate

### Main Area (Right)
- **Files** - Grid or list view of files
- **Breadcrumbs** - Navigation path
- **View Toggle** - Switch between grid and list
- **Upload** - Upload new files

## View Modes

### Grid View
- Thumbnail previews
- File information below images
- Larger thumbnails for images
- Icons for non-image files

### List View
- Compact table layout
- File name, size, type
- Date modified
- Actions column

### Switching Views

Click the grid/list toggle buttons in the toolbar to switch between views.

## Uploading Files

### Steps

1. Click **"Upload"** button
2. Select files (multiple files supported)
3. Files upload automatically
4. Files appear in current folder

### Supported File Types

- **Images** - JPG, PNG, GIF, WebP, SVG
- **Documents** - PDF, DOC, DOCX
- **Other** - Any file type

### File Size Limits

- Limited by server `upload_max_filesize` setting
- Default: Usually 2MB-10MB
- Check server configuration for limits

## Organizing Files

### Creating Folders

1. Click **"New Folder"** button
2. Enter folder name
3. Folder created in current location
4. Navigate into folder

### Moving Files

1. Right-click file
2. Select **"Move"**
3. Choose destination folder
4. File moved to new location

### Folder Navigation

- Click folder name in sidebar to open
- Use breadcrumbs to navigate up
- "All Media" shows files from all folders

## File Actions

### Right-Click Menu

Right-click any file to access:
- **Edit Metadata** - Add alt text, captions, tags
- **Copy URL** - Copy file URL to clipboard
- **Move** - Move to another folder
- **Delete** - Delete file

### Edit Metadata

1. Right-click file
2. Select **"Edit Metadata"**
3. Fill in form:
   - Alt text
   - Caption
   - Description
   - Credit
   - Tags
4. Click **"Save"**

Metadata is stored separately and used in templates.

## Selecting Files

### Media Picker

When adding files to content:
1. Click file upload field
2. Media picker modal opens
3. Navigate folders
4. Select files
5. Click **"Select"**

### Multiple Selection

- Click files to select
- Selected files highlighted
- Select multiple files at once
- Use for multi-file fields

## File Information

### Displayed Information

- **Name** - Filename
- **Size** - File size (KB/MB)
- **Type** - File type/extension
- **Modified** - Last modified date
- **Dimensions** - Image dimensions (if image)

### Image Previews

- Thumbnails generated automatically
- Hover to see larger preview
- Click to view full size

## Folder Structure

### Default Structure

```
/uploads/
  images/
    header.jpg
    logo.png
  documents/
    guide.pdf
  file.jpg
```

### Best Practices

- **Organize by type** - Images, documents, etc.
- **Use descriptive names** - Clear folder names
- **Keep it simple** - Don't nest too deeply
- **Consistent naming** - Use consistent conventions

## Examples

### Uploading an Image

1. Navigate to Media
2. Click "Upload"
3. Select image file
4. Image appears in current folder
5. Use in content via media picker

### Creating Image Folder

1. Click "New Folder"
2. Name: "blog-images"
3. Navigate into folder
4. Upload blog images here
5. Keep blog images organized

### Adding Metadata

1. Right-click image
2. Select "Edit Metadata"
3. Add alt text: "Blog post header"
4. Add caption: "Featured image"
5. Add tags: "blog", "header"
6. Save

## See Also

- [Pages](./PAGES.md) - Using media in pages
- [Collections](./COLLECTIONS.md) - Using media in collections
- [Templates](../templates/FILES.md) - Displaying media in templates

