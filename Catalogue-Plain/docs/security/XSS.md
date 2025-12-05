# XSS Protection

Cross-Site Scripting (XSS) protection through output escaping.

## Overview

XSS protection prevents malicious scripts from being executed by escaping all user-generated content before output.

## Escaping Functions

### `esc()` - HTML Escaping

Escapes HTML special characters:

```php
echo esc($user_input);
// <script> → &lt;script&gt;
```

### `esc_attr()` - Attribute Escaping

Escapes HTML attribute values:

```php
<input value="<?= esc_attr($value) ?>">
```

### `esc_url()` - URL Escaping

Escapes URLs:

```php
<a href="<?= esc_url($url) ?>">Link</a>
```

### `esc_js()` - JavaScript Escaping

Escapes JavaScript strings:

```php
<script>
var title = <?= esc_js($title) ?>;
</script>
```

## Implementation

### HTML Output

```php
// ✅ Safe
<?= esc($user_input) ?>

// ❌ Unsafe
<?= $user_input ?>
```

### HTML Attributes

```php
// ✅ Safe
<div class="<?= esc_attr($class) ?>">

// ❌ Unsafe
<div class="<?= $class ?>">
```

### URLs

```php
// ✅ Safe
<a href="<?= esc_url($link) ?>">

// ❌ Unsafe
<a href="<?= $link ?>">
```

### JavaScript

```php
// ✅ Safe
<script>
var data = <?= esc_js(json_encode($data)) ?>;
</script>

// ❌ Unsafe
<script>
var data = <?= json_encode($data) ?>;
</script>
```

## Automatic Escaping

### Template Functions

`catalogue()` function automatically escapes:

```php
<?= catalogue('title') ?>
// Automatically escaped based on field type
```

### Field Type Handling

- **Text fields** - Escaped as HTML
- **Markdown fields** - Rendered as HTML (safe)
- **Tags** - Formatted as HTML spans
- **URLs** - Escaped appropriately

## Content Security Policy

### CSP Headers

CSP headers provide additional XSS protection:

```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
```

### Current Configuration

- Allows inline scripts/styles (for CMS functionality)
- Restricts external resources
- Admin panel only (requires authentication)

## Best Practices

### Always Escape Output

✅ **Do:**
- Escape all user-generated content
- Use appropriate escape function
- Escape in templates
- Escape in API responses

❌ **Don't:**
- Output unescaped user input
- Trust content from files
- Skip escaping "safe" content
- Mix escaped and unescaped content

### Context-Aware Escaping

```php
// HTML content
<?= esc($content) ?>

// HTML attribute
<div class="<?= esc_attr($class) ?>">

// URL
<a href="<?= esc_url($link) ?>">

// JavaScript
<script>var x = <?= esc_js($value) ?>;</script>
```

## Examples

### Safe Template

```php
<h1><?= esc($title) ?></h1>
<p><?= esc($description) ?></p>
<a href="<?= esc_url($link) ?>" class="<?= esc_attr($class) ?>">
    <?= esc($text) ?>
</a>
```

### Safe JSON Output

```php
header('Content-Type: application/json');
echo json_encode([
    'title' => $title, // Safe - JSON encoding escapes
    'content' => $content
]);
```

### Safe JavaScript

```php
<script>
var config = {
    title: <?= esc_js($title) ?>,
    items: <?= esc_js(json_encode($items)) ?>
};
</script>
```

## Attack Prevention

### Stored XSS

Prevented by:
- Escaping on output
- Validating on input
- Sanitizing content

### Reflected XSS

Prevented by:
- Escaping all output
- Validating input
- Using prepared output

### DOM-Based XSS

Prevented by:
- Escaping JavaScript output
- Validating client-side input
- Using safe DOM methods

## See Also

- [Input Validation](./INPUT_VALIDATION.md) - Input sanitization
- [Render Functions](../api-reference/RENDER_FUNCTIONS.md) - Escaping functions
- [Best Practices](./BEST_PRACTICES.md) - Security guidelines

