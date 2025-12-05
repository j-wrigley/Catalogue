# Flat-File Architecture

Understanding the flat-file storage system and why it matters.

## Overview

The CMS uses a **flat-file architecture**, meaning all content is stored as files on the filesystem rather than in a database. This fundamental design choice affects how the CMS works and what benefits it provides.

## What is Flat-File?

### Traditional CMS (Database)

```
Content → Database → Query → Display
```

**Characteristics:**
- Content stored in database tables
- Requires database server
- SQL queries for content
- Complex data relationships

### Flat-File CMS (This CMS)

```
Content → JSON Files → Read → Display
```

**Characteristics:**
- Content stored as JSON files
- No database required
- Direct file access
- Simple file structure

## Storage Structure

### Content Files

**Location:** `content/pages/` and `content/collections/`

**Format:** JSON files

**Example:**
```
content/
  pages/
    about/
      about.json
    contact/
      contact.json
  collections/
    posts/
      my-post.json
      another-post.json
```

### File Format

**JSON Structure:**
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

## Benefits

### 1. No Database Required

**Advantages:**
- Simpler setup
- Fewer dependencies
- Lower hosting requirements
- Easier deployment

**Use Cases:**
- Shared hosting
- Static site hosting
- Simple projects
- Development environments

### 2. Human-Readable Format

**Advantages:**
- Easy to read and edit
- Direct file editing possible
- Clear content structure
- No special tools needed

**Example:**
```json
{
  "title": "About Us",
  "content": "We are a company..."
}
```

### 3. Version Control Friendly

**Advantages:**
- Track changes easily
- Git-friendly format
- See content history
- Collaborate easily

**Workflow:**
```bash
git add content/
git commit -m "Updated about page"
git push
```

### 4. Easy Backup

**Advantages:**
- Copy files to backup
- Simple restore process
- No database dumps needed
- Portable content

**Backup:**
```bash
cp -r content/ backup/
```

### 5. Performance

**Advantages:**
- Fast file reads
- No query overhead
- Direct file access
- Simple caching

**Considerations:**
- File I/O is fast
- No database connection overhead
- Suitable for most sites

## How It Works

### Content Storage

**Process:**
1. Content created in admin panel
2. Data saved as JSON file
3. File stored in appropriate directory
4. Metadata added automatically

**File Creation:**
```
Admin Panel → Save → JSON File → Filesystem
```

### Content Retrieval

**Process:**
1. Template requests content
2. JSON file read from filesystem
3. Data parsed and used
4. HTML generated

**Content Access:**
```
Template → Read JSON → Parse → Render HTML
```

## File Operations

### Reading Content

**Function:** `readJson($filepath)`

**Process:**
1. Check file exists
2. Read file contents
3. Parse JSON
4. Return data array

### Writing Content

**Function:** `writeJson($filepath, $data)`

**Process:**
1. Encode data to JSON
2. Write to temporary file
3. Rename atomically
4. Ensure directory exists

**Atomic Writes:**
- Prevents corruption
- Ensures complete writes
- Safe concurrent access

## Comparison

### Flat-File vs Database

| Feature | Flat-File | Database |
|---------|-----------|----------|
| Setup | Simple | Complex |
| Dependencies | None | Database server |
| Backup | Copy files | Database dump |
| Version Control | Native | Requires tools |
| Performance | Fast reads | Query overhead |
| Scalability | Limited | Better |
| Complexity | Low | High |

### When to Use Flat-File

**Ideal For:**
- Small to medium sites
- Simple content structures
- Static site generation
- Shared hosting
- Version control needs

**Not Ideal For:**
- Very large sites (1000s of pages)
- Complex relationships
- High-frequency updates
- Real-time data

## Limitations

### File System Limits

**Considerations:**
- File count limits
- Directory depth limits
- File size limits
- Performance at scale

### Scalability

**Challenges:**
- Many files can be slow
- Directory scanning overhead
- File system limitations

**Solutions:**
- Organize content well
- Use collections efficiently
- Consider caching
- Monitor performance

## Best Practices

### File Organization

- **Use clear structure** - Organize logically
- **Consistent naming** - Follow conventions
- **Keep it simple** - Don't over-nest
- **Regular cleanup** - Remove unused files

### Content Management

- **Edit via admin** - Use CMS interface
- **Backup regularly** - Protect content
- **Version control** - Track changes
- **Monitor file count** - Stay organized

## See Also

- [Content Storage](../content-management/CONTENT_STORAGE.md) - Storage details
- [Content Structure](../content-management/CONTENT_STRUCTURE.md) - JSON format
- [Static Site Generation](./STATIC_GENERATION.md) - HTML generation

