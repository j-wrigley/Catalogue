# CMS Settings

Configuring the CMS system itself (not site content).

## Overview

CMS settings control the admin panel appearance and behavior, including theme colors, CMS name, and system features.

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

### Theme

#### Theme Presets
- **Red** - Default red theme
- **Blue** - Blue accent color
- **Green** - Green accent color
- **Purple** - Purple accent color
- **Orange** - Orange accent color
- **Pink** - Pink accent color
- **Teal** - Teal accent color
- **Indigo** - Indigo accent color

Click preset card to apply theme instantly.

#### Custom Colors

- **Accent Color** - Primary theme color
- **Accent Hover** - Hover state color
- **Accent Text** - Text color on accent background

Use color picker or enter hex values.

### Traffic Overview

#### Reset Traffic Data
- **Purpose** - Clear all traffic statistics
- **Action** - Deletes traffic log file
- **Warning** - Cannot be undone

#### Enable/Disable Traffic Tracking
- **Toggle** - Enable or disable tracking
- **Default** - Enabled
- **Effect** - Shows/hides dashboard traffic card

## Editing Settings

### Steps

1. Navigate to CMS Settings
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

- **Hex** - #e11d48 format
- **RGB** - Converted automatically
- **Validation** - Invalid colors rejected

## Settings Storage

CMS settings are stored in:
```
/catalogue/content/cms-settings.json
```

## Examples

### Changing Theme

1. Navigate to CMS Settings
2. Expand "Theme" accordion
3. Click "Blue" preset
4. Admin panel updates immediately
5. Click "Save Settings" to persist

### Customizing Colors

1. Navigate to CMS Settings
2. Expand "Theme" accordion
3. Click accent color picker
4. Choose custom color: #9333ea
5. Adjust hover color
6. Save settings

### Disabling Traffic Tracking

1. Navigate to CMS Settings
2. Expand "Traffic Overview" accordion
3. Toggle "Enable Traffic Tracking" off
4. Save settings
5. Traffic card hidden from dashboard

## See Also

- [Site Settings](./SITE_SETTINGS.md) - Site content settings
- [Dashboard](./DASHBOARD.md) - Dashboard overview

