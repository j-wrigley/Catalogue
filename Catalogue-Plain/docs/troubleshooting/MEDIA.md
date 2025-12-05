# Media Issues

Troubleshooting media library and file display problems.

## Common Issues

### Images Not Displaying

**Symptoms:**
- Images show broken link
- 404 errors for images
- Images not loading

**Solutions:**

1. **Check File Exists**
   ```bash
   ls -la catalogue/uploads/images/{filename}
   ```
   - Verify file uploaded
   - Check correct location
   - Verify filename matches

2. **Check File Path**
   ```php
   // In content JSON
   "image": "/uploads/images/file.jpg"
   ```
   - Path must be correct
   - Relative to root
   - Check BASE_PATH if subfolder

3. **Verify File Permissions**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Files must be readable (`644`)
   - Directory must be accessible (`755`)
   - Web server needs read access

4. **Check URL Generation**
   ```php
   // In template
   <img src="<?= catalogue('image') ?>">
   ```
   - Verify path correct
   - Check BASE_PATH included
   - Ensure URL valid

### Media Picker Not Working

**Symptoms:**
- Picker doesn't open
- Files not selectable
- Selection doesn't work

**Solutions:**

1. **Check JavaScript Errors**
   - Open browser console
   - Look for JavaScript errors
   - Check for missing functions

2. **Verify CSRF Token**
   - Token must be present
   - Check token valid
   - Refresh if expired

3. **Check File Permissions**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Directory must be readable
   - Files must be accessible
   - Check permissions correct

### Metadata Not Saving

**Symptoms:**
- Metadata edits don't save
- Changes revert
- Metadata missing

**Solutions:**

1. **Check Metadata Directory**
   ```bash
   ls -la catalogue/content/media/
   ```
   - Directory must exist
   - Must be writable (`755`)
   - Check permissions

2. **Verify File Write**
   ```bash
   ls -la catalogue/content/media/
   ```
   - Metadata files should exist
   - Check modification times
   - Verify write succeeded

3. **Check JSON Format**
   ```bash
   cat catalogue/content/media/{hash}.json
   ```
   - Must be valid JSON
   - Check structure correct
   - Verify encoding

### Files Not Uploading to Folder

**Symptoms:**
- Upload succeeds but wrong location
- Files in root not folder
- Folder navigation issues

**Solutions:**

1. **Check Folder Path**
   - Verify folder exists
   - Check path correct
   - Ensure writable

2. **Verify Upload Destination**
   ```bash
   ls -la catalogue/uploads/{folder}/
   ```
   - Check files uploaded
   - Verify correct folder
   - Check structure

3. **Check Folder Permissions**
   ```bash
   ls -la catalogue/uploads/{folder}/
   ```
   - Folder must be writable (`755`)
   - Check ownership correct
   - Verify web server access

### Media Library Empty

**Symptoms:**
- No files shown
- Library appears empty
- Files exist but not visible

**Solutions:**

1. **Check File Filtering**
   - Hidden files filtered
   - System files excluded
   - Check filter logic

2. **Verify File Scanning**
   ```bash
   ls -la catalogue/uploads/
   ```
   - Files must exist
   - Check file types
   - Verify readable

3. **Check Folder Navigation**
   - Verify current folder
   - Check "All Media" view
   - Verify folder structure

## File Path Issues

### Wrong Path in Content

**Symptoms:**
- Images use wrong path
- Path includes `/catalogue/`
- Absolute paths incorrect

**Solutions:**

1. **Check Path Format**
   ```json
   {
     "image": "/uploads/images/file.jpg"
   }
   ```
   - Should be relative path
   - Start with `/uploads/`
   - No `/catalogue/` prefix

2. **Verify BASE_PATH**
   ```php
   echo BASE_PATH;
   ```
   - Empty for root install
   - `/subfolder` for subfolder
   - Check template usage

3. **Update Template**
   ```php
   <img src="<?= BASE_PATH ?><?= catalogue('image') ?>">
   ```

### Path Traversal Errors

**Symptoms:**
- "Invalid path" errors
- File access denied
- Security warnings

**Solutions:**

1. **Check Path Validation**
   - Paths validated for security
   - Directory traversal prevented
   - Real paths checked

2. **Verify File Locations**
   - Files must be in uploads directory
   - Paths must be relative
   - No `../` allowed

## Metadata Issues

### Metadata Not Loading

**Symptoms:**
- Metadata form empty
- Previous data missing
- Metadata not accessible

**Solutions:**

1. **Check Metadata File**
   ```bash
   ls -la catalogue/content/media/{hash}.json
   ```
   - File must exist
   - Must be valid JSON
   - Check permissions

2. **Verify File Matching**
   - Metadata matched by hash
   - Or by filename
   - Check matching logic

3. **Check Metadata Structure**
   ```json
   {
     "file_path": "images/file.jpg",
     "alt_text": "Alt text",
     "caption": "Caption"
   }
   ```

### Metadata Not Accessible in Templates

**Symptoms:**
- Metadata functions return null
- No metadata available
- Template errors

**Solutions:**

1. **Check Function Usage**
   ```php
   $metadata = catalogueMedia('/uploads/images/file.jpg');
   ```
   - Verify function called correctly
   - Check path format
   - Ensure file exists

2. **Verify Metadata File**
   ```bash
   ls catalogue/content/media/
   ```
   - Metadata files must exist
   - Check hash matches
   - Verify structure

## Best Practices

### File Management

- **Organize files** - Use folders
- **Use descriptive names** - Clear filenames
- **Add metadata** - Include alt text
- **Regular cleanup** - Remove unused files

### Troubleshooting

- **Check file existence** - Verify files present
- **Verify permissions** - Check access
- **Test paths** - Verify URLs correct
- **Review logs** - Check for errors

## See Also

- [Media Management](../media-management/README.md) - Media guide
- [File Permissions](./FILE_PERMISSIONS.md) - Permission issues
- [Upload Problems](./UPLOADS.md) - Upload issues

