# Render Functions

Functions for escaping and sanitizing output.

## `esc()`

Escape HTML output.

### Syntax

```php
esc(string $string): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$string` | string | String to escape |

### Returns

HTML-escaped string.

### Examples

```php
echo esc($user_input); // Safe HTML output
echo esc($title);      // Escaped title
```

### Features

- Escapes HTML special characters
- Converts `&`, `<`, `>`, `"`, `'`
- Uses UTF-8 encoding
- Handles null values

---

## `esc_attr()`

Escape HTML attribute value.

### Syntax

```php
esc_attr(string $string): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$string` | string | String to escape |

### Returns

HTML attribute-escaped string.

### Examples

```php
<input value="<?= esc_attr($value) ?>">
<div class="<?= esc_attr($class) ?>">
```

### Usage

Use for HTML attribute values:
```php
<a href="<?= esc_url($url) ?>" title="<?= esc_attr($title) ?>">
    <?= esc($text) ?>
</a>
```

---

## `esc_url()`

Escape URL for safe output.

### Syntax

```php
esc_url(string $url): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$url` | string | URL to escape |

### Returns

HTML-escaped URL string.

### Examples

```php
<a href="<?= esc_url($link) ?>">Link</a>
<img src="<?= esc_url($image_url) ?>">
```

### Usage

Use for URLs in HTML:
```php
<a href="<?= esc_url($page_url) ?>">Page</a>
```

---

## `esc_js()`

Escape string for JavaScript.

### Syntax

```php
esc_js(string $string): string
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$string` | string | String to escape |

### Returns

JavaScript-escaped string (JSON encoded).

### Examples

```php
<script>
var title = <?= esc_js($title) ?>;
var data = <?= esc_js(json_encode($array)) ?>;
</script>
```

### Features

- JSON encodes string
- Escapes HTML special characters
- Safe for JavaScript strings

### Usage

```php
<script>
var config = {
    title: <?= esc_js($title) ?>,
    url: <?= esc_js($url) ?>
};
</script>
```

---

## Best Practices

### Always Escape Output

```php
// ✅ Good
<?= esc($user_input) ?>

// ❌ Bad
<?= $user_input ?>
```

### Use Appropriate Function

```php
// HTML content
<?= esc($content) ?>

// HTML attribute
<div class="<?= esc_attr($class) ?>">

// URL
<a href="<?= esc_url($url) ?>">

// JavaScript
<script>var x = <?= esc_js($value) ?>;</script>
```

### Template Examples

```php
<!-- HTML content -->
<h1><?= esc($title) ?></h1>
<p><?= esc($description) ?></p>

<!-- Attributes -->
<input type="text" value="<?= esc_attr($value) ?>" class="<?= esc_attr($class) ?>">

<!-- URLs -->
<a href="<?= esc_url($link) ?>">Link</a>
<img src="<?= esc_url($image) ?>" alt="<?= esc_attr($alt) ?>">

<!-- JavaScript -->
<script>
var config = {
    title: <?= esc_js($title) ?>,
    items: <?= esc_js(json_encode($items)) ?>
};
</script>
```

---

## Security Notes

### XSS Prevention

These functions prevent Cross-Site Scripting (XSS) attacks by escaping user input.

### When to Use

- **Always** escape user-generated content
- **Always** escape content from database/files
- **Never** trust external input
- **Always** use appropriate escape function

### Common Mistakes

```php
// ❌ Wrong - unescaped
<?= $user_input ?>

// ✅ Correct - escaped
<?= esc($user_input) ?>
```

---

## See Also

- [Template Functions](./TEMPLATE_FUNCTIONS.md) - Content access
- [Security Documentation](../security/) - Security guide

