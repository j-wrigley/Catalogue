# Content Workflow

Best practices and workflows for managing content.

## Overview

Content workflow covers:
- **Content planning** - Planning content structure
- **Content creation** - Creating pages and collections
- **Content editing** - Modifying existing content
- **Content organization** - Organizing and managing content
- **Content maintenance** - Keeping content up-to-date

## Planning Workflow

### 1. Define Structure

**Steps:**
1. Identify content types needed
2. Determine pages vs collections
3. Plan blueprint structure
4. Design field organization

**Questions:**
- What content types do I need?
- Which are single pages?
- Which are collections?
- What fields does each need?

### 2. Create Blueprints

**Steps:**
1. Create blueprint files
2. Define fields and types
3. Set up tabs/groups
4. Configure options

**Best Practices:**
- Start simple, add complexity later
- Use appropriate field types
- Organize with tabs/groups
- Add validation (required fields)

### 3. Plan Content

**Steps:**
1. List all pages needed
2. List all collections needed
3. Plan content for each
4. Gather media assets

## Creation Workflow

### Pages

**Workflow:**
1. Create blueprint
2. Navigate to Pages
3. Click page name
4. Fill form fields
5. Save content
6. Verify HTML generation

**Checklist:**
- [ ] Blueprint created
- [ ] Content added
- [ ] Required fields filled
- [ ] HTML generated
- [ ] Page accessible

### Collections

**Workflow:**
1. Create blueprint
2. Navigate to Collections
3. Select collection
4. Click "New Item"
5. Fill form fields
6. Set slug
7. Save content
8. Verify HTML generation

**Checklist:**
- [ ] Blueprint created
- [ ] Item added
- [ ] Slug set
- [ ] Required fields filled
- [ ] HTML generated
- [ ] Item accessible

## Editing Workflow

### Regular Updates

**Workflow:**
1. Navigate to content
2. Review current state
3. Make changes
4. Preview if needed
5. Save changes
6. Verify updates

### Major Revisions

**Workflow:**
1. Backup content
2. Review current state
3. Plan changes
4. Make changes incrementally
5. Test after each change
6. Save and verify

### Bulk Updates

**Current Limitation:** No bulk operations

**Workaround:**
1. Edit items individually
2. Use consistent changes
3. Document changes
4. Verify all updates

## Organization Workflow

### Content Organization

**Pages:**
- Group related pages
- Use consistent naming
- Keep structure simple

**Collections:**
- Organize by collection type
- Use descriptive slugs
- Maintain consistent structure

### Media Organization

**Workflow:**
1. Create folders by type
2. Upload files to folders
3. Add metadata to files
4. Use in content

**Best Practices:**
- Organize by content type
- Use descriptive filenames
- Add alt text and captions
- Keep folder structure simple

## Maintenance Workflow

### Regular Maintenance

**Tasks:**
1. Review content regularly
2. Update outdated content
3. Check for broken links
4. Verify media files exist
5. Clean up unused content

### Content Audit

**Checklist:**
- [ ] All pages accessible
- [ ] All collection items accessible
- [ ] Media files exist
- [ ] Links work correctly
- [ ] Content is current
- [ ] No broken references

### Cleanup

**Tasks:**
1. Remove unused content
2. Delete unused media
3. Clean up old files
4. Organize remaining content

## Publishing Workflow

### Status Management

**Draft:**
- Work in progress
- Not publicly visible
- Can be edited freely

**Published:**
- Publicly visible
- HTML generated
- Accessible via URL

**Unlisted:**
- Not in navigation
- Accessible via direct URL
- Hidden from lists

### Publishing Process

**Workflow:**
1. Create content as draft
2. Edit and refine
3. Set status to published
4. Verify public access
5. Monitor for issues

## Best Practices

### Content Creation

- **Plan first** - Define structure before creating
- **Start simple** - Add complexity gradually
- **Use templates** - Reuse structures
- **Test regularly** - Verify as you go

### Content Editing

- **Backup before major changes** - Safety first
- **Edit incrementally** - Small changes at a time
- **Test after changes** - Verify updates
- **Document changes** - Note what changed

### Content Organization

- **Use consistent naming** - Clear, descriptive
- **Organize logically** - Group related content
- **Keep it simple** - Don't over-complicate
- **Regular cleanup** - Remove unused content

### Content Maintenance

- **Review regularly** - Keep content current
- **Update outdated content** - Maintain accuracy
- **Check links** - Verify they work
- **Monitor performance** - Track issues

## Workflow Examples

### Creating a Blog

**Workflow:**
1. Create `posts.blueprint.yml`
2. Define fields (title, content, tags)
3. Create first post
4. Set up archive page template
5. Add navigation link
6. Publish posts

### Creating a Portfolio

**Workflow:**
1. Create `projects.blueprint.yml`
2. Define fields (title, description, images)
3. Create project items
4. Set up project list template
5. Add individual project pages
6. Publish projects

### Updating Site Content

**Workflow:**
1. Review all pages
2. Identify outdated content
3. Update content incrementally
4. Verify changes
5. Regenerate HTML if needed
6. Test site functionality

## Troubleshooting

### Content Not Appearing

**Check:**
1. Status is "published"
2. HTML file generated
3. Template exists
4. File permissions correct

### Changes Not Saving

**Check:**
1. Required fields filled
2. CSRF token valid
3. File permissions correct
4. Error logs for issues

### HTML Not Updating

**Check:**
1. Save succeeded
2. Template exists
3. Regenerate manually
4. Check error logs

## See Also

- [Creating Content](./CREATING_CONTENT.md) - Creating content
- [Editing Content](./EDITING_CONTENT.md) - Editing content
- [Content Structure](./CONTENT_STRUCTURE.md) - JSON structure

