# Markdown Content

Rendering markdown fields in templates.

## Field Type

- `markdown` - Visual markdown editor with formatting toolbar

## Basic Usage

```php
<div class="content">
    <?= catalogue('content') ?>
</div>
```

## How It Works

Markdown fields are automatically converted to HTML when rendered:

- **Input:** Markdown text (stored in JSON)
- **Output:** Rendered HTML (displayed in template)

## Markdown Features

The markdown editor supports:

- **Bold** (`**text**`)
- *Italic* (`*text*`)
- ~~Strikethrough~~ (`~~text~~`)
- Headings (`# H1`, `## H2`, `### H3`)
- Lists (ordered and unordered)
- Links (`[text](url)`)
- Code blocks

## Examples

### Blog Post

```php
<article class="post">
    <h1><?= catalogue('title') ?></h1>
    <div class="post-content">
        <?= catalogue('content') ?>
    </div>
</article>
```

### With Excerpt

```php
<article>
    <h1><?= catalogue('title') ?></h1>
    <?php if (catalogue('excerpt')): ?>
        <div class="excerpt">
            <?= catalogue('excerpt') ?>
        </div>
    <?php endif; ?>
    <div class="content">
        <?= catalogue('content') ?>
    </div>
</article>
```

### Page Content

```php
<main>
    <header>
        <h1><?= catalogue('title') ?></h1>
        <?php if (catalogue('description')): ?>
            <p class="lead"><?= catalogue('description') ?></p>
        <?php endif; ?>
    </header>
    <div class="page-content">
        <?= catalogue('content', 'No content available') ?>
    </div>
</main>
```

## Styling

Markdown content is rendered as standard HTML. Style it with CSS:

```css
.content h1 { }
.content p { }
.content ul { }
.content a { }
```

## See Also

- [Catalogue Function](./CATALOGUE_FUNCTION.md)
- [Text Fields](./TEXT_FIELDS.md)

