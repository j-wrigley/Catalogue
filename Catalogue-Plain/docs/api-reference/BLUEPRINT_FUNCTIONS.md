# Blueprint Functions

Functions for parsing and accessing blueprint files.

## `getBlueprint()`

Get blueprint by name.

### Syntax

```php
getBlueprint(string $name): array|null
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | string | Blueprint name (without .blueprint.yml) |

### Returns

Blueprint array on success, `null` if not found.

### Examples

```php
$blueprint = getBlueprint('about');
$postsBlueprint = getBlueprint('posts');
$settingsBlueprint = getBlueprint('settings');
```

### Blueprint Structure

Returns array with:
- `title` - Blueprint title
- `fields` - Field definitions
- `tabs` - Tab configuration (if defined)

### Usage

```php
$blueprint = getBlueprint('about');
if ($blueprint) {
    echo $blueprint['title'];
    foreach ($blueprint['fields'] as $name => $field) {
        echo $name . ': ' . $field['type'];
    }
}
```

---

## `getAllBlueprints()`

Get all available blueprints.

### Syntax

```php
getAllBlueprints(): array
```

### Parameters

None.

### Returns

Associative array of blueprints: `['name' => blueprint_array, ...]`

### Examples

```php
$blueprints = getAllBlueprints();
foreach ($blueprints as $name => $blueprint) {
    echo $name . ': ' . $blueprint['title'];
}
```

### Usage

```php
// List all content types
$blueprints = getAllBlueprints();
foreach ($blueprints as $name => $blueprint) {
    echo '<option value="' . $name . '">' . $blueprint['title'] . '</option>';
}
```

---

## `parseBlueprint()`

Parse YAML blueprint file.

### Syntax

```php
parseBlueprint(string $filepath): array|null
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filepath` | string | Path to blueprint file |

### Returns

Parsed blueprint array, `null` on failure.

### Examples

```php
$blueprint = parseBlueprint(BLUEPRINTS_DIR . '/about.blueprint.yml');
```

### Features

- **YAML parsing** - Converts YAML to PHP array
- **Error handling** - Returns null on failure
- **Validation** - Validates blueprint structure

### Usage

```php
$filepath = BLUEPRINTS_DIR . '/about.blueprint.yml';
$blueprint = parseBlueprint($filepath);
if ($blueprint) {
    // Use blueprint
}
```

---

## `parseSimpleYaml()`

Parse simple YAML content (internal function).

### Syntax

```php
parseSimpleYaml(string $content): array
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$content` | string | YAML content string |

### Returns

Parsed array.

### Notes

Internal function used by `parseBlueprint()`. Generally not called directly.

---

## Blueprint Directory

Blueprints are stored in:
```
/catalogue/blueprints/
  about.blueprint.yml
  posts.blueprint.yml
  settings.blueprint.yml
  ...
```

### Blueprint File Naming

- Format: `{name}.blueprint.yml`
- Name used in `getBlueprint('name')`
- Case-sensitive

---

## Blueprint Structure

### Basic Blueprint

```yaml
title: About Page
fields:
  title:
    type: text
    label: Title
  content:
    type: markdown
    label: Content
```

### With Tabs

```yaml
title: Page
tabs:
  content:
    label: Content
fields:
  title:
    type: text
    category: content
```

### With Layout

```yaml
title: Page
fields:
  title:
    type: text
    column: 1
    span: 1
  image:
    type: file
    column: 2
    span: 1
```

---

## Examples

### Get Blueprint Fields

```php
$blueprint = getBlueprint('about');
if ($blueprint && isset($blueprint['fields'])) {
    foreach ($blueprint['fields'] as $name => $field) {
        echo $name . ' (' . $field['type'] . ')';
    }
}
```

### List All Content Types

```php
$blueprints = getAllBlueprints();
foreach ($blueprints as $name => $blueprint) {
    echo '<li>' . $blueprint['title'] . '</li>';
}
```

### Check Blueprint Exists

```php
$blueprint = getBlueprint('posts');
if ($blueprint) {
    echo 'Posts blueprint found';
} else {
    echo 'Posts blueprint not found';
}
```

---

## See Also

- [Blueprints Documentation](../blueprints/README.md) - Blueprint guide
- [Storage Functions](./STORAGE_FUNCTIONS.md) - File operations

