# File Operations

Deleting, moving, and managing media files.

## Overview

The media library provides various operations for managing files:
- Delete files and folders
- Move files between folders
- Copy file URLs
- Edit metadata

## Deleting Files

### Steps

1. Right-click file
2. Select **"Delete"**
3. Confirm deletion in dialog
4. File deleted permanently
5. Associated metadata also deleted

### Delete Confirmation

- Confirmation dialog appears
- Shows filename
- Cannot be undone
- Metadata deleted with file

### Restrictions

- **Permanent** - Deletion cannot be undone
- **Metadata deleted** - Associated metadata removed
- **No recovery** - Files not in trash/recycle bin

## Deleting Folders

### Steps

1. Right-click folder
2. Select **"Delete"**
3. Confirm deletion
4. Folder and all contents deleted

### Warning

**Deleting a folder deletes all files inside!**

- All files in folder deleted
- All subfolders deleted
- Cannot be undone
- Confirm carefully

## Moving Files

### Steps

1. Right-click file
2. Select **"Move"**
3. Choose destination folder
4. File moved immediately
5. File appears in new location

### Move Dialog

- Shows folder tree
- Select destination folder
- Current folder hidden
- Root folder option available

### Move Restrictions

- **Cannot move to same folder** - Already there
- **Cannot move to current folder** - If already there
- **Root hidden** - If already at root

## Copying File URLs

### Steps

1. Right-click file
2. Select **"Copy URL"**
3. URL copied to clipboard
4. Paste anywhere needed

### URL Format

URLs are absolute paths:
```
/catalogue/uploads/images/header.jpg
```

Or full URL:
```
http://example.com/catalogue/uploads/images/header.jpg
```

### Usage

- Use in templates
- Share with others
- Reference in code
- Link to files

## Editing Metadata

### Steps

1. Right-click file
2. Select **"Edit Metadata"**
3. Fill in form
4. Click **"Save"**
5. Metadata updated

See [File Metadata](./METADATA.md) for details.

## File Information

### Viewing File Info

Hover or click file to see:
- **Filename** - File name
- **Size** - File size (KB/MB)
- **Type** - File type/extension
- **Modified** - Last modified date
- **Dimensions** - Image dimensions (if image)

### File Details

- **Grid view** - Info below thumbnail
- **List view** - Info in table columns
- **Hover** - Additional info on hover

## Bulk Operations

### Current Limitations

- Files must be operated on individually
- No bulk delete
- No bulk move
- No bulk metadata edit

### Future Enhancements

Bulk operations may be added:
- Select multiple files
- Bulk delete
- Bulk move
- Bulk metadata edit

## File Permissions

### Required Permissions

- **Read** - View files
- **Write** - Upload files
- **Delete** - Remove files
- **Create** - Create folders

### Server Configuration

Ensure uploads directory is writable:
```bash
chmod 755 /catalogue/uploads
```

## Examples

### Deleting Unused Files

1. Navigate to Media
2. Find unused file
3. Right-click → "Delete"
4. Confirm deletion
5. File removed

### Organizing Files

1. Navigate to Media
2. Right-click file
3. Select "Move"
4. Choose "blog-images" folder
5. File moved
6. File organized

### Copying Image URL

1. Right-click image
2. Select "Copy URL"
3. URL copied: `/catalogue/uploads/images/header.jpg`
4. Paste in template or code

### Moving Multiple Files

Currently requires individual moves:
1. Move file 1 to folder
2. Move file 2 to folder
3. Move file 3 to folder
4. All files in same folder

## Best Practices

### Before Deleting

- **Check usage** - Ensure file not used
- **Backup** - Backup important files
- **Confirm** - Double-check before deleting
- **Metadata** - Metadata deleted with file

### Organizing Files

- **Move before organizing** - Move files to folders
- **Use consistent structure** - Same organization pattern
- **Keep it simple** - Don't over-organize
- **Review regularly** - Clean up unused files

### File Management

- **Regular cleanup** - Remove unused files
- **Organize as you go** - Don't let it pile up
- **Use folders** - Keep files organized
- **Add metadata** - Make files searchable

## See Also

- [Organizing Files](./ORGANIZING.md) - Folder management
- [File Metadata](./METADATA.md) - Metadata system
- [Uploading Files](./UPLOADING.md) - Uploading files

