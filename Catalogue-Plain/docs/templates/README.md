# Templates Documentation

Templates are PHP files that generate static HTML from your content. They use simple functions to access content data.

## Quick Links

- **[Catalogue Function](./CATALOGUE_FUNCTION.md)** - Main function for accessing content
- **[Text Fields](./TEXT_FIELDS.md)** - Displaying text and textarea fields
- **[Markdown Content](./MARKDOWN.md)** - Rendering markdown/content fields
- **[Date Formatting](./DATE_FORMATTING.md)** - Formatting date fields
- **[Tags](./TAGS.md)** - Displaying tags
- **[Select, Radio & Checkbox](./SELECT_FIELDS.md)** - Displaying selection fields
- **[Slider & Switch](./SLIDER_SWITCH.md)** - Displaying numeric and boolean fields
- **[Structure](./STRUCTURE.md)** - Displaying repeatable table items
- **[Files & Images](./FILES.md)** - Working with file fields and media
- **[Collections](./COLLECTIONS.md)** - Iterating through collection items
- **[Pagination](./PAGINATION.md)** - Paginating collection items
- **[Navigation](./NAVIGATION.md)** - Building navigation menus
- **[Snippets](./SNIPPETS.md)** - Reusable template components
- **[Site Settings](./SITE_SETTINGS.md)** - Accessing site configuration
- **[Conditionals](./CONDITIONALS.md)** - Conditional rendering
- **[Examples](./EXAMPLES.md)** - Complete template examples

## Template Basics

Templates are PHP files stored in `/catalogue/templates/` that generate static HTML.

**File Naming:**
- `{content-type}.php` → Template for that content type
- `home.php` → Generates `index.html`
- `404.php` → Generates `404.html`

**Basic Template Structure:**
```php
<?= snippet('header') ?>

<main>
    <h1><?= catalogue('title') ?></h1>
    <div><?= catalogue('content') ?></div>
</main>

<?= snippet('footer') ?>
```

## Core Functions

| Function | Purpose |
|----------|---------|
| `catalogue()` | Get and render field values |
| `catalogueRaw()` | Get raw field values (no rendering) |
| `catalogueFiles()` | Iterate through file fields |
| `catalogueCollection()` | Iterate through collection items |
| `catalogueStructure()` | Iterate through structure items |
| `catalogueNav()` | Get navigation data |
| `catalogueMedia()` | Get media metadata |
| `snippet()` | Include reusable components |
| `traffic()` | Track page views |

## Next Steps

1. Read [Catalogue Function](./CATALOGUE_FUNCTION.md) to understand the main function
2. Check specific field type docs for detailed examples
3. See [Examples](./EXAMPLES.md) for complete templates

