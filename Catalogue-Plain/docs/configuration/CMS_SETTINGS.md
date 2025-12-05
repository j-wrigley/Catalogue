# CMS Settings

Admin panel configuration and customization.

## Overview

CMS settings control the admin panel appearance and behavior:
- CMS name/branding
- Theme colors
- Traffic tracking
- System features

## Accessing

Navigate to **Settings** in the sidebar footer or visit:
```
/catalogue/index.php?page=cms-settings
```

## Settings Sections

### General

#### CMS Name

- **Purpose** - Name displayed in admin panel
- **Default** - "JSON Catalogue"
- **Usage** - Appears in header/title
- **Editable** - Yes, via text input

**Example:**
```
CMS Name: "My CMS"
```

### Theme

#### Theme Presets

Pre-configured color themes:

- **Red** - Default red theme (`#e11d48`)
- **Blue** - Blue accent (`#2563eb`)
- **Green** - Green accent (`#16a34a`)
- **Purple** - Purple accent (`#9333ea`)
- **Orange** - Orange accent (`#ea580c`)
- **Pink** - Pink accent (`#db2777`)
- **Teal** - Teal accent (`#14b8a6`)
- **Indigo** - Indigo accent (`#6366f1`)

**Usage:**
1. Click preset card
2. Theme applied instantly
3. Save to persist

#### Custom Colors

Customize theme colors:

- **Accent Color** - Primary theme color
- **Accent Hover** - Hover state color
- **Accent Text** - Text color on accent background

**Format:** Hex color (`#e11d48`)

**Usage:**
1. Use color picker
2. Or enter hex value
3. Save to apply

### Traffic Overview

#### Reset Traffic Data

- **Purpose** - Clear all traffic statistics
- **Action** - Deletes traffic log file
- **Warning** - Cannot be undone
- **Button** - "Reset Traffic Data"

#### Enable/Disable Traffic Tracking

- **Toggle** - Enable or disable tracking
- **Default** - Enabled
- **Effect** - Shows/hides dashboard traffic card
- **Storage** - Saved in `cms-settings.json`

## Settings Storage

### File Location

CMS settings stored in:
```
/catalogue/content/cms-settings.json
```

### File Structure

```json
{
  "site_name": "My CMS",
  "accent_color": "#e11d48",
  "accent_hover": "#be123c",
  "accent_text": "#ffffff",
  "traffic_enabled": true
}
```

## Editing Settings

### Steps

1. Navigate to **Settings** in sidebar footer
2. Expand accordion section
3. Modify settings
4. Click **"Save Settings"**
5. Changes applied immediately

### Accordion Interface

- **Expandable sections** - Click to expand/collapse
- **Organized** - Settings grouped logically
- **Clean** - Minimal, focused interface

## Theme Customization

### Using Presets

1. Navigate to CMS Settings
2. Expand "Theme" accordion
3. Click preset card
4. Theme applied instantly
5. Save to persist

### Custom Colors

1. Expand "Theme" accordion
2. Use color picker for accent color
3. Adjust hover color
4. Set text color
5. Save settings

### Color Format

- **Hex** - `#e11d48` format
- **Color Picker** - Visual selection
- **Text Input** - Manual entry

## CMS Name

### Changing Name

1. Expand "General" accordion
2. Edit "CMS Name" field
3. Enter new name
4. Save settings
5. Name appears in header

### Usage

CMS name appears in:
- Admin panel header
- Page titles
- Browser tab
- Branding areas

## Traffic Tracking

### Enabling/Disabling

1. Expand "Traffic Overview" accordion
2. Toggle "Enable Traffic Tracking"
3. Save settings
4. Dashboard card shows/hides

### Resetting Data

1. Expand "Traffic Overview" accordion
2. Click "Reset Traffic Data"
3. Confirm action
4. All traffic data cleared

**Warning:** This action cannot be undone!

## Site Regeneration

### Regenerate All Pages

1. Expand "Site Regeneration" accordion
2. Click "Regenerate All Pages & Collections"
3. Wait for completion
4. All HTML files updated

### When to Use

- After template changes
- After blueprint changes
- After bulk content updates
- To refresh all pages

## Examples

### Complete Settings Example

```json
{
  "site_name": "My Custom CMS",
  "accent_color": "#2563eb",
  "accent_hover": "#1d4ed8",
  "accent_text": "#ffffff",
  "traffic_enabled": true
}
```

### Theme Customization

**Red Theme:**
```json
{
  "accent_color": "#e11d48",
  "accent_hover": "#be123c",
  "accent_text": "#ffffff"
}
```

**Blue Theme:**
```json
{
  "accent_color": "#2563eb",
  "accent_hover": "#1d4ed8",
  "accent_text": "#ffffff"
}
```

## Best Practices

### Theme Colors

- **Contrast** - Ensure text is readable
- **Consistency** - Use consistent colors
- **Accessibility** - Check color contrast ratios
- **Branding** - Match your brand colors

### CMS Name

- **Clear** - Use recognizable name
- **Short** - Keep it concise
- **Branded** - Match your brand

### Traffic Tracking

- **Enable** - For analytics
- **Disable** - If not needed
- **Reset** - Periodically clear old data

## See Also

- [Admin Panel - CMS Settings](../admin-panel/CMS_SETTINGS.md) - Admin interface
- [Core Configuration](./CORE_CONFIG.md) - PHP configuration
- [Site Settings](./SITE_SETTINGS.md) - Public site settings

