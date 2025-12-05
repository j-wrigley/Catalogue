# Content Storage

How content files are stored and organized.

## Overview

Content is stored as JSON files:
- **Flat-file system** - No database
- **JSON format** - Human-readable
- **Organized structure** - Pages and collections
- **Automatic management** - Created/updated by CMS

## Storage Structure

### Root Directory

```
content/
  pages/          # Page content
  collections/    # Collection content
  media/          # Media metadata
  users/          # User accounts
```

### Pages Directory

```
content/pages/
  {page-name}/
    {page-name}.json
```

**Example:**
```
content/pages/
  about/
    about.json
  contact/
    contact.json
  home/
    home.json
```

### Collections Directory

```
content/collections/
  {collection-name}/
    {slug}.json
```

**Example:**
```
content/collections/
  posts/
    my-first-post.json
    another-post.json
  projects/
    project-1.json
    project-2.json
```

## File Naming

### Pages

**Pattern:** `{page-name}.json`

**Location:** `content/pages/{page-name}/`

**Examples:**
- `about.json` → `content/pages/about/about.json`
- `contact.json` → `content/pages/contact/contact.json`

### Collections

**Pattern:** `{slug}.json`

**Location:** `content/collections/{collection-name}/`

**Examples:**
- `my-post.json` → `content/collections/posts/my-post.json`
- `project-1.json` → `content/collections/projects/project-1.json`

### Slug Sanitization

**Process:**
1. Convert to lowercase
2. Replace spaces with hyphens
3. Remove special characters
4. Keep only alphanumeric and hyphens

**Examples:**
- "My First Post" → `my-first-post`
- "Project #1" → `project-1`
- "Hello World!" → `hello-world`

## File Format

### JSON Structure

**Pretty-printed:** Human-readable format

**Encoding:** UTF-8

**Indentation:** 2 spaces

**Example:**
```json
{
  "title": "My Page",
  "content": "Page content...",
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-11-25T12:00:00+00:00"
  }
}
```

### Atomic Writes

**Process:**
1. Write to temporary file (`{filename}.tmp`)
2. Validate write succeeded
3. Rename temp file to final name
4. Delete temp file if rename fails

**Benefits:**
- Prevents corruption
- Ensures complete writes
- Safe concurrent access

## File Permissions

### Default Permissions

**Directories:** `755` (drwxr-xr-x)

**Files:** `644` (-rw-r--r--)

### Required Permissions

**Writable:** Web server must write files

**Readable:** Web server must read files

**Not executable:** Files should not be executable

## File Operations

### Creating Files

**Process:**
1. Validate directory exists
2. Create directory if needed
3. Write JSON file
4. Set permissions

**Location:** Determined by content type

### Updating Files

**Process:**
1. Read existing file
2. Merge new data
3. Update metadata
4. Write file atomically

**Preserves:** Existing data not in update

### Deleting Files

**Process:**
1. Validate file exists
2. Delete file
3. Delete directory if empty
4. Clean up related files

**Collections:** Delete individual items

**Pages:** Delete entire page directory

## Metadata Storage

### _meta Object

Stored in each content file:

```json
{
  "_meta": {
    "created": "2024-01-01T00:00:00+00:00",
    "updated": "2024-11-25T12:00:00+00:00",
    "author": "username"
  }
}
```

### Media Metadata

**Location:** `content/media/`

**Format:** `{hash}.json`

**Example:**
```
content/media/
  a1b2c3d4e5f6.json
  7g8h9i0j1k2l.json
```

## Backup & Version Control

### Backup Strategy

**Regular Backups:**
- Backup `content/` directory
- Include all JSON files
- Preserve directory structure

**Before Changes:**
- Backup before major edits
- Backup before bulk operations
- Keep multiple backups

### Version Control

**Git-Friendly:**
- Text-based JSON files
- Human-readable format
- Easy to diff
- Track changes

**Best Practices:**
- Commit regularly
- Use descriptive messages
- Don't commit sensitive data
- Use `.gitignore` appropriately

## File Size Considerations

### JSON File Size

**Typical Size:**
- Small pages: 1-5 KB
- Large pages: 10-50 KB
- Collections: 1-20 KB per item

**Limitations:**
- No hard limit
- PHP memory limits apply
- Consider for very large content

### Performance

**Reading:**
- Fast for small files
- Slower for large files
- Consider caching for large sites

**Writing:**
- Atomic writes ensure safety
- Slower than database
- Acceptable for CMS use

## Security

### File Access

**Protected:** Files not directly accessible via web

**Location:** Outside web root or protected

**Access:** Only via CMS admin panel

### Path Traversal Protection

**Validation:**
- Paths validated
- Directory traversal prevented
- Real paths checked

**Security:**
- Sanitized filenames
- Validated paths
- Restricted access

## Best Practices

### Organization

- **Use consistent naming** - Clear, descriptive names
- **Keep structure simple** - Don't over-nest
- **Group related content** - Logical organization
- **Regular cleanup** - Remove unused files

### File Management

- **Backup regularly** - Protect your content
- **Use version control** - Track changes
- **Monitor file sizes** - Keep reasonable sizes
- **Clean up old files** - Remove unused content

## See Also

- [Content Structure](./CONTENT_STRUCTURE.md) - JSON format
- [Content Workflow](./CONTENT_WORKFLOW.md) - Best practices
- [Creating Content](./CREATING_CONTENT.md) - Creating content

