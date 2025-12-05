# Your First Collection

Create your first collection (like blog posts) in 10 minutes.

## Overview

Collections are groups of similar content items. Examples:
- Blog posts
- Portfolio projects
- Product catalog
- Team members

**Difference from Pages:**
- **Pages:** One item per type (About, Contact)
- **Collections:** Many items per type (Posts, Projects)

## Step 1: Create Blueprint

### Create Blueprint File

**Location:** `catalogue/blueprints/posts.blueprint.yml`

**Content:**
```yaml
title: Blog Post
fields:
  title:
    type: text
    label: Title
    required: true
  content:
    type: markdown
    label: Content
    rows: 15
  featured_image:
    type: file
    label: Featured Image
  tags:
    type: tags
    label: Tags
```

**Save the file** in `catalogue/blueprints/`

## Step 2: Create Template

### Create Template File

**Location:** `catalogue/templates/posts.php`

**Content:**
```php
<?= snippet('header') ?>
<main>
    <article class="post">
        <header>
            <h1><?= catalogue('title') ?></h1>
        </header>
        
        <?php if (catalogue('featured_image')): ?>
            <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
        <?php endif; ?>
        
        <div class="post-content">
            <?= catalogue('content') ?>
        </div>
        
        <?php if (catalogue('tags')): ?>
            <footer class="post-tags">
                <?= catalogue('tags') ?>
            </footer>
        <?php endif; ?>
    </article>
</main>
<?= snippet('footer') ?>
```

**Save the file** in `catalogue/templates/`

### Create Archive Template (Optional)

**Location:** `catalogue/templates/archive.php`

**Content:**
```php
<?= snippet('header') ?>
<main>
    <h1>Blog Posts</h1>
    
    <ul class="posts-list">
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <li>
                <a href="<?= catalogue('url') ?>">
                    <h2><?= catalogue('title') ?></h2>
                    <?php if (catalogue('description')): ?>
                        <p><?= catalogue('description') ?></p>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</main>
<?= snippet('footer') ?>
```

## Step 3: Add First Item

### Access Collections

1. Login to admin panel
2. Navigate to **Collections** in sidebar
3. Click **"Posts"** in sidebar (or create if needed)
4. Click **"New Item"** button

### Fill Form

**Enter content:**
- **Title:** "My First Post"
- **Content:** Your blog post content (markdown)
- **Featured Image:** Upload or select image (optional)
- **Tags:** Add tags (optional)
- **Slug:** `my-first-post` (auto-generated from title)

### Set Status

**In banner:**
- **Status:** Published (dropdown)
- **Featured:** Toggle if needed

### Save Content

1. Click **"Save"** button
2. Redirected back to collections table
3. HTML file generated automatically
4. Page available at `/posts/my-first-post`

## Step 4: View Your Collection Item

### Check Generated HTML

**File created:**
```
posts/my-first-post.html
```

**URL:**
```
http://your-domain.com/posts/my-first-post
```

### Verify Content

**Check:**
- Page loads correctly
- Content displays properly
- Slug-based URL works
- Item appears in collection

## Step 5: Add More Items

### Create Additional Posts

**Repeat process:**
1. Click "New Item"
2. Fill form
3. Set slug
4. Save

**Each item:**
- Has its own JSON file
- Generates its own HTML
- Has unique slug/URL

## Complete Example

### Blueprint: `posts.blueprint.yml`

```yaml
title: Blog Post
fields:
  title:
    type: text
    label: Title
    required: true
  description:
    type: textarea
    label: Description
    rows: 3
  content:
    type: markdown
    label: Content
    rows: 20
  featured_image:
    type: file
    label: Featured Image
  tags:
    type: tags
    label: Tags
```

### Template: `posts.php`

```php
<?= snippet('header') ?>
<main class="post-page">
    <article class="post">
        <header class="post-header">
            <h1><?= catalogue('title') ?></h1>
            <?php if (catalogue('description')): ?>
                <p class="post-description"><?= catalogue('description') ?></p>
            <?php endif; ?>
        </header>
        
        <?php if (catalogue('featured_image')): ?>
            <figure class="post-image">
                <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
            </figure>
        <?php endif; ?>
        
        <div class="post-content">
            <?= catalogue('content') ?>
        </div>
        
        <?php if (catalogue('tags')): ?>
            <footer class="post-footer">
                <div class="post-tags">
                    <?= catalogue('tags') ?>
                </div>
            </footer>
        <?php endif; ?>
    </article>
</main>
<?= snippet('footer') ?>
```

### Archive Template: `archive.php`

```php
<?= snippet('header') ?>
<main class="archive">
    <h1>Blog Posts</h1>
    
    <div class="posts-grid">
        <?php foreach (catalogueCollection('posts', ['status' => 'published']) as $post): ?>
            <article class="post-card">
                <a href="<?= catalogue('url') ?>">
                    <?php if (catalogue('featured_image')): ?>
                        <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
                    <?php endif; ?>
                    <h2><?= catalogue('title') ?></h2>
                    <?php if (catalogue('description')): ?>
                        <p><?= catalogue('description') ?></p>
                    <?php endif; ?>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?= snippet('footer') ?>
```

## Understanding Collections

### File Structure

**Collection items stored as:**
```
content/collections/posts/
  my-first-post.json
  another-post.json
  third-post.json
```

### URL Structure

**Each item has unique URL:**
- `/posts/my-first-post`
- `/posts/another-post`
- `/posts/third-post`

### Slug Management

**Slug determines:**
- Filename (`{slug}.json`)
- HTML filename (`{slug}.html`)
- URL (`/posts/{slug}`)

**Important:** Changing slug updates filename and URL

## Common Issues

### Items Not Generating

**Check:**
1. Blueprint exists
2. Template exists
3. Content files created
4. Slugs are valid

### Slug Issues

**Check:**
1. Slug is lowercase
2. No special characters
3. Unique within collection
4. Matches filename

### Archive Not Showing Items

**Check:**
1. Items have `status: published`
2. Template uses `catalogueCollection()`
3. Filter correct
4. Content files exist

## Next Steps

After creating your first collection:

1. **Add More Items** - Create additional posts
2. **Create Archive Page** - List all items
3. **Add Navigation** - Link to collection
4. **Customize Templates** - Style your collection

## See Also

- [Pages vs Collections](../content-management/PAGES_VS_COLLECTIONS.md) - Understanding types
- [Creating Content](../content-management/CREATING_CONTENT.md) - Content creation
- [Collections Documentation](../templates/COLLECTIONS.md) - Collection templates
- [Your First Page](./FIRST_PAGE.md) - Create pages

