# Media Picker

Selecting files from the media library when adding content.

## Overview

The media picker is a modal interface that appears when you click file upload fields in content forms. It provides an easy way to select existing files or upload new ones.

## When Media Picker Appears

### File Fields

The media picker opens when:
- Clicking a `file` field in a form
- Clicking "Select Media" button
- Adding files to content

### Multiple File Fields

For fields with `multiple: true`:
- Media picker supports multiple selection
- Select multiple files at once
- All selected files added to field

## Media Picker Interface

### Layout

- **Large Modal** - Full-screen feel
- **Sidebar** - Folder navigation
- **Main Area** - File grid/list
- **Toolbar** - Upload, view toggle, actions

### View Modes

- **Grid View** - Thumbnail previews
- **List View** - Compact table
- **Toggle** - Switch between views

### Folder Navigation

- **Sidebar** - Folder tree (same as main media page)
- **Breadcrumbs** - Current folder path
- **"All Media"** - View all files

## Selecting Files

### Single Selection

1. Click file in picker
2. File highlighted
3. Click **"Select"** button
4. File added to form field

### Multiple Selection

1. Click files to select (multiple)
2. Selected files highlighted
3. Click **"Select"** button
4. All selected files added

### Selection Limits

- **max_files** - Blueprint can set limit
- **Visual indicator** - Shows selection count
- **Prevents over-selection** - Blocks if limit reached

## Uploading from Picker

### Steps

1. Open media picker
2. Click **"Upload"** button
3. Select files
4. Files upload to current folder
5. Files appear in picker
6. Select uploaded files

### Upload Location

Files upload to:
- Current folder in picker
- Can navigate folders before uploading
- Files appear immediately after upload

## File Information

### Displayed Information

- **Thumbnail** - Image preview
- **Filename** - File name
- **Size** - File size
- **Type** - File type/extension

### Image Previews

- Thumbnails for images
- Icons for non-images
- Hover for larger preview
- Click to view full size

## Closing Picker

### Methods

- Click **"Cancel"** button
- Click outside modal (backdrop)
- Press Escape key
- Click X button

### Behavior

- **No selection** - Closes without changes
- **Selection made** - Files added to form
- **Unsaved changes** - Lost on close

## Examples

### Selecting Featured Image

1. Edit page/collection item
2. Click "Featured Image" field
3. Media picker opens
4. Navigate to "images" folder
5. Click image
6. Click "Select"
7. Image added to field

### Selecting Multiple Files

1. Edit content with `files` field (multiple)
2. Click "Files" field
3. Media picker opens
4. Select multiple images
5. Click "Select"
6. All images added to field

### Upload and Select

1. Open media picker
2. Navigate to desired folder
3. Click "Upload"
4. Select files
5. Files upload
6. Select uploaded files
7. Click "Select"
8. Files added to content

## Media Picker Features

### Search

- Search files by name
- Filters results in real-time
- Works across all folders
- Case-insensitive

### Filtering

- Filter by file type (if implemented)
- Filter by folder
- Filter by tags (if implemented)

### Sorting

- Sort by name
- Sort by date
- Sort by size
- Sort by type

## Best Practices

### Organization

- Organize files into folders
- Use descriptive filenames
- Keep folder structure simple
- Upload to correct folder first

### Selection

- Select files before uploading
- Use appropriate file types
- Consider file sizes
- Check image dimensions

### Workflow

1. Upload files to organized folders
2. Add metadata to files
3. Use media picker to select
4. Files automatically linked

## See Also

- [Uploading Files](./UPLOADING.md) - Uploading files
- [Organizing Files](./ORGANIZING.md) - Folder management
- [File Metadata](./METADATA.md) - Adding metadata

