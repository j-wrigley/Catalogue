# Modular Architecture

How the CMS is organized into modular, reusable components.

## Overview

The CMS uses a **modular architecture** where code is organized into separate, focused modules. This approach makes the system easier to understand, maintain, and extend.

## What is Modular Architecture?

### Definition

**Modular architecture** means:
- Code organized into separate modules
- Each module has a specific purpose
- Modules can work independently
- Easy to understand and maintain

### Benefits

- **Separation of concerns** - Each module has one job
- **Reusability** - Modules can be reused
- **Maintainability** - Easy to find and fix code
- **Extensibility** - Easy to add new features

## Module Structure

### Directory Organization

```
catalogue/
  lib/              # Core modules
  panel/            # Admin panel modules
  blueprints/       # Content definitions
  templates/        # Frontend templates
  content/          # Content storage
```

### Core Modules (`lib/`)

**Purpose:** Core functionality

**Modules:**
- `storage.php` - File operations
- `blueprint.php` - Blueprint parsing
- `auth.php` - Authentication
- `csrf.php` - Security tokens
- `render.php` - Output escaping
- `form.php` - Form generation
- `catalogue.php` - Template functions

### Admin Panel Modules (`panel/`)

**Purpose:** Admin interface

**Structure:**
```
panel/
  pages/           # Admin pages
  partials/        # Reusable components
  actions/         # AJAX handlers
  assets/          # CSS/JS
```

### Content Modules

**Purpose:** Content management

**Modules:**
- `blueprints/` - Content definitions
- `content/` - Content storage
- `templates/` - Frontend templates

## Module Responsibilities

### Storage Module

**File:** `lib/storage.php`

**Responsibilities:**
- Read JSON files
- Write JSON files
- Validate JSON
- Atomic file operations

**Functions:**
- `readJson($filepath)`
- `writeJson($filepath, $data)`
- `listJsonFiles($directory)`

### Blueprint Module

**File:** `lib/blueprint.php`

**Responsibilities:**
- Parse YAML blueprints
- Validate blueprint structure
- Get blueprint data
- List all blueprints

**Functions:**
- `getBlueprint($name)`
- `getAllBlueprints()`
- `parseBlueprint($filepath)`

### Authentication Module

**File:** `lib/auth.php`

**Responsibilities:**
- User login
- User logout
- Session management
- Password verification

**Functions:**
- `login($username, $password)`
- `logout()`
- `isLoggedIn()`
- `requireLogin()`

### Form Module

**File:** `lib/form.php`

**Responsibilities:**
- Generate forms from blueprints
- Handle field rendering
- Organize layout
- Validate input

**Functions:**
- `generateFormFromBlueprint($blueprint, $content)`
- `renderFormField($field, $value, $column, $span)`

### Catalogue Module

**File:** `lib/catalogue.php`

**Responsibilities:**
- Template functions
- Content access
- Field rendering
- Context management

**Functions:**
- `catalogue($field, $default, $context)`
- `catalogueCollection($collection, $filter)`
- `catalogueFiles($field)`

## Separation of Concerns

### Backend vs Frontend

**Backend (PHP):**
- Admin panel
- Content management
- File operations
- Security

**Frontend (HTML/JS):**
- Template rendering
- Static HTML generation
- User interface
- Content display

### Content vs Presentation

**Content (JSON):**
- Data storage
- Content structure
- Field values

**Presentation (Templates):**
- HTML structure
- Styling
- Layout

## Module Dependencies

### Dependency Flow

```
config.php
  ↓
lib/storage.php
lib/blueprint.php
lib/auth.php
  ↓
panel/pages/*.php
panel/actions/*.php
  ↓
templates/*.php
```

### Module Loading

**Order:**
1. `config.php` - Configuration
2. Core modules - Storage, auth, etc.
3. Panel modules - Admin interface
4. Templates - Frontend rendering

## Extensibility

### Adding New Modules

**Process:**
1. Create new module file
2. Define module functions
3. Include in appropriate place
4. Use module functions

**Example:**
```php
// lib/custom.php
function customFunction() {
    // Module code
}

// Use in other files
require_once LIB_DIR . '/custom.php';
customFunction();
```

### Extending Existing Modules

**Process:**
1. Identify module to extend
2. Add new functions
3. Maintain compatibility
4. Document changes

## Best Practices

### Module Design

- **Single responsibility** - One purpose per module
- **Clear interfaces** - Well-defined functions
- **Documentation** - Document purpose and usage
- **Testing** - Test modules independently

### Code Organization

- **Logical grouping** - Related code together
- **Consistent naming** - Clear, descriptive names
- **Minimal dependencies** - Reduce coupling
- **Reusable code** - Share common functionality

### Maintenance

- **Keep modules focused** - Don't mix concerns
- **Update carefully** - Maintain compatibility
- **Document changes** - Note modifications
- **Test thoroughly** - Verify functionality

## Examples

### Storage Module Usage

```php
require_once LIB_DIR . '/storage.php';

// Read content
$content = readJson('content/pages/about/about.json');

// Write content
writeJson('content/pages/about/about.json', $data);
```

### Blueprint Module Usage

```php
require_once LIB_DIR . '/blueprint.php';

// Get blueprint
$blueprint = getBlueprint('about');

// Parse blueprint
$parsed = parseBlueprint('blueprints/about.blueprint.yml');
```

### Form Module Usage

```php
require_once LIB_DIR . '/form.php';

// Generate form
$form = generateFormFromBlueprint($blueprint, $content);
```

## See Also

- [API Reference](../api-reference/README.md) - Module function reference
- [Configuration](../configuration/CORE_CONFIG.md) - Configuration structure
- [Security Model](./SECURITY.md) - Security modules

