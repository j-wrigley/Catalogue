# Your First Page

Create your first content page in 5 minutes.

## Overview

Creating a page involves:
1. Creating a blueprint (defines structure)
2. Creating a template (defines HTML)
3. Adding content (via admin panel)
4. HTML generated automatically

## Step 1: Create Blueprint

### Create Blueprint File

**Location:** `catalogue/blueprints/about.blueprint.yml`

**Content:**
```yaml
title: About Page
fields:
  title:
    type: text
    label: Title
    required: true
  content:
    type: markdown
    label: Content
    rows: 10
  featured_image:
    type: file
    label: Featured Image
```

**Save the file** in `catalogue/blueprints/`

## Step 2: Create Template

### Create Template File

**Location:** `catalogue/templates/about.php`

**Content:**
```php
<?= snippet('header') ?>
<main>
    <h1><?= catalogue('title') ?></h1>
    
    <?php if (catalogue('featured_image')): ?>
        <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
    <?php endif; ?>
    
    <div class="content">
        <?= catalogue('content') ?>
    </div>
</main>
<?= snippet('footer') ?>
```

**Save the file** in `catalogue/templates/`

### Create Snippets (Optional)

**Header:** `catalogue/templates/snippets/header.php`
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= catalogue('site_name', 'Site', 'site') ?></title>
</head>
<body>
```

**Footer:** `catalogue/templates/snippets/footer.php`
```php
<footer>
    <p>&copy; <?= date('Y') ?> <?= catalogue('site_name', 'Site', 'site') ?></p>
</footer>
</body>
</html>
```

## Step 3: Add Content

### Access Pages Section

1. Login to admin panel
2. Navigate to **Pages** in sidebar
3. Click **"About"** in the table

### Fill Form

**Enter content:**
- **Title:** "About Us"
- **Content:** Your about page content (markdown)
- **Featured Image:** Upload or select image (optional)

### Save Content

1. Click **"Save"** button
2. Toast notification appears
3. HTML file generated automatically
4. Page available at `/about`

## Step 4: View Your Page

### Check Generated HTML

**File created:**
```
about.html
```

**URL:**
```
http://your-domain.com/about
```

### Verify Content

**Check:**
- Page loads correctly
- Content displays properly
- Images load (if added)
- Markdown rendered correctly

## Complete Example

### Blueprint: `about.blueprint.yml`

```yaml
title: About Page
fields:
  title:
    type: text
    label: Title
    required: true
  subtitle:
    type: text
    label: Subtitle
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

### Template: `about.php`

```php
<?= snippet('header') ?>
<main class="page">
    <header class="page-header">
        <h1><?= catalogue('title') ?></h1>
        <?php if (catalogue('subtitle')): ?>
            <p class="subtitle"><?= catalogue('subtitle') ?></p>
        <?php endif; ?>
    </header>
    
    <?php if (catalogue('featured_image')): ?>
        <figure class="featured-image">
            <img src="<?= catalogue('featured_image') ?>" alt="<?= catalogue('title') ?>">
        </figure>
    <?php endif; ?>
    
    <div class="page-content">
        <?= catalogue('content') ?>
    </div>
    
    <?php if (catalogue('tags')): ?>
        <div class="page-tags">
            <?= catalogue('tags') ?>
        </div>
    <?php endif; ?>
</main>
<?= snippet('footer') ?>
```

### Content Added via Admin

**Result:**
- JSON file: `content/pages/about/about.json`
- HTML file: `about.html`
- URL: `/about`

## Common Issues

### Page Not Generating

**Check:**
1. Blueprint exists and valid YAML
2. Template exists and valid PHP
3. Content file created
4. File permissions correct

### Template Errors

**Check:**
1. PHP syntax correct
2. Functions available (`catalogue()`)
3. Snippets exist (if used)
4. Error logs for details

### Content Not Displaying

**Check:**
1. Field names match blueprint
2. Content saved correctly
3. HTML regenerated
4. Template uses correct fields

## Next Steps

After creating your first page:

1. **Add More Pages** - Create additional pages
2. **Customize Templates** - Style your pages
3. **Add Navigation** - Link pages together
4. **Create Collections** - See [Your First Collection](./FIRST_COLLECTION.md)

## See Also

- [Blueprints Documentation](../blueprints/README.md) - Blueprint reference
- [Templates Documentation](../templates/README.md) - Template guide
- [Creating Content](../content-management/CREATING_CONTENT.md) - Content creation
- [Template Examples](../templates/EXAMPLES.md) - More examples

