# Uploading Files

How to upload files to the media library.

## Overview

The CMS supports uploading various file types to the media library. Files can be uploaded individually or in bulk.

## Accessing Upload

### From Media Page

1. Navigate to **Media** in sidebar
2. Click **"Upload"** button
3. Select files
4. Files upload to current folder

### From Content Forms

1. Click file upload field
2. Media picker opens
3. Click **"Upload"** in picker
4. Select files
5. Files upload and can be selected

## Upload Process

### Steps

1. Click **"Upload"** button
2. File picker dialog opens
3. Select one or more files
4. Files upload automatically
5. Progress shown during upload
6. Files appear in current folder

### Multiple Files

- Select multiple files at once
- All files upload simultaneously
- Progress shown for each file
- Files appear when upload completes

## Supported File Types

### Images

- **JPG/JPEG** - JPEG images
- **PNG** - PNG images
- **GIF** - Animated GIFs
- **WebP** - WebP images
- **SVG** - Vector graphics

### Documents

- **PDF** - PDF documents
- **DOC/DOCX** - Word documents
- **ZIP** - Archive files

### Other Types

- Other file types may be supported
- Check server configuration for limits

## File Size Limits

### Server Limits

Files are limited by:
- **PHP `upload_max_filesize`** - Default usually 2-10MB
- **PHP `post_max_size`** - Must be larger than upload_max_filesize
- **Server configuration** - May have additional limits

### CMS Limits

The CMS attempts to increase limits:
- Sets `upload_max_filesize` to 50MB
- Sets `post_max_size` to 50MB
- May be overridden by server configuration

### Checking Limits

Check your server's PHP configuration:
```php
echo ini_get('upload_max_filesize');
echo ini_get('post_max_size');
```

## Upload Location

### Current Folder

Files upload to the currently selected folder:
- If no folder selected → Root uploads directory
- If folder selected → That folder

### Changing Location

1. Navigate to desired folder
2. Click "Upload"
3. Files upload to that folder

## Upload Validation

### File Type Validation

Files are validated by:
- **File extension** - Must match allowed extensions
- **MIME type** - Server-detected MIME type
- **Image validation** - Images verified as valid

### Validation Errors

If validation fails:
- Error message displayed
- File not uploaded
- Other files continue uploading

## Upload Progress

### Visual Feedback

- Progress bar shows upload status
- File count displayed
- Individual file progress shown
- Success/error messages displayed

### Error Handling

If upload fails:
- Error message displayed
- File not saved
- Can retry upload
- Check file size and type

## Examples

### Upload Single Image

1. Navigate to Media
2. Click "Upload"
3. Select `header.jpg`
4. Image uploads to current folder
5. Image appears in media library

### Upload Multiple Files

1. Navigate to Media
2. Click "Upload"
3. Select multiple images (Ctrl/Cmd + Click)
4. All images upload simultaneously
5. Images appear when complete

### Upload to Folder

1. Navigate to "blog-images" folder
2. Click "Upload"
3. Select images
4. Images upload to "blog-images" folder
5. Organized by folder

## Best Practices

### File Naming

- Use descriptive filenames
- Avoid spaces (use hyphens)
- Use lowercase when possible
- Include version numbers if needed

### File Organization

- Upload to appropriate folders
- Organize by content type
- Keep folder structure simple
- Don't nest too deeply

### File Size

- Optimize images before upload
- Use appropriate formats (WebP for web)
- Compress large files
- Consider CDN for very large files

## Troubleshooting

### Upload Fails

**Issue:** File doesn't upload

**Solutions:**
1. Check file size (may exceed limit)
2. Check file type (may not be allowed)
3. Check server permissions
4. Check PHP configuration

### File Size Error

**Issue:** "File size exceeds server limit"

**Solutions:**
1. Reduce file size
2. Increase server limits
3. Check `.htaccess` configuration
4. Contact hosting provider

### Wrong Folder

**Issue:** File uploaded to wrong folder

**Solutions:**
1. Use "Move" action to relocate
2. Delete and re-upload to correct folder
3. Check current folder before uploading

## See Also

- [Organizing Files](./ORGANIZING.md) - Folder management
- [File Operations](./FILE_OPERATIONS.md) - Moving files
- [Media Picker](./MEDIA_PICKER.md) - Selecting files

