# Theme Customization

Customizing the CMS appearance, colors, and visual design.

## Overview

The CMS includes a built-in theme customization system that allows you to change the appearance of the admin panel without modifying code. You can customize accent colors, hover states, and text colors.

## Accessing Theme Settings

Theme customization is available in **Settings** → **CMS Settings** → **Theme** section.

## Theme Presets

The CMS includes 8 predefined color themes:

- **Red** (default) - `#e11d48`
- **Blue** - `#2563eb`
- **Green** - `#16a34a`
- **Purple** - `#9333ea`
- **Orange** - `#ea580c`
- **Pink** - `#db2777`
- **Teal** - `#14b8a6`
- **Indigo** - `#6366f1`

### Using Presets

1. Go to **Settings** → **CMS Settings**
2. Open the **Theme** accordion
3. Click on a preset card to apply it
4. Click **Save** to persist the changes

## Custom Colors

You can create custom color themes by manually setting colors:

### Color Options

- **Accent Color** - Primary color used for buttons, links, and highlights
- **Hover Color** - Color shown on hover states
- **Text Color** - Text color used on accent backgrounds

### Setting Custom Colors

1. Go to **Settings** → **CMS Settings** → **Theme**
2. Use the color picker or enter hex values
3. Preview changes in real-time
4. Click **Save** to apply

### Color Format

Colors must be in hex format: `#RRGGBB`

Examples:
- `#e11d48` - Red
- `#2563eb` - Blue
- `#ffffff` - White
- `#000000` - Black

## How Theme Colors Work

Theme colors are applied via CSS variables:

```css
--color-accent: #e11d48;
--color-accent-hover: #be123c;
--color-accent-text: #ffffff;
--color-accent-subtle: rgba(225, 29, 72, 0.08);
```

These variables are used throughout the CMS interface for:
- Buttons and interactive elements
- Links and navigation
- Badges and status indicators
- Focus states
- Active states

## Real-Time Preview

The theme settings page includes a real-time preview:
- Changes are visible immediately as you adjust colors
- No need to save to see the effect
- Preview updates automatically

## Saving Theme Changes

1. Adjust colors using presets or custom values
2. Preview changes in real-time
3. Click **Save** at the bottom of the form
4. Changes are applied immediately

## Storage

Theme settings are stored in:
```
/catalogue/content/cms-settings.json
```

The file contains:
```json
{
  "site_name": "JSON Catalogue",
  "accent_color": "#e11d48",
  "accent_hover": "#be123c",
  "accent_text": "#ffffff",
  "traffic_enabled": true
}
```

## CSS Variables

Theme colors are exposed as CSS variables that can be used in custom CSS:

```css
.my-custom-element {
    background-color: var(--color-accent);
    color: var(--color-accent-text);
}

.my-custom-element:hover {
    background-color: var(--color-accent-hover);
}
```

## Best Practices

1. **Choose Accessible Colors**: Ensure sufficient contrast between text and background colors
2. **Test Hover States**: Verify hover colors provide clear visual feedback
3. **Consistent Branding**: Match theme colors to your brand identity
4. **Preview Before Saving**: Use the real-time preview to test combinations

## Troubleshooting

### Colors Not Applying

1. **Clear Browser Cache**: Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)
2. **Check File Permissions**: Ensure `/catalogue/content/cms-settings.json` is writable
3. **Verify Format**: Colors must be valid hex codes (`#RRGGBB`)
4. **Check Console**: Look for JavaScript errors in browser console

### Preset Not Working

- Ensure you click **Save** after selecting a preset
- Check that the preset card shows as "active" (highlighted)
- Verify the save was successful (toast notification)

### Custom Colors Not Saving

- Verify hex format is correct (`#` followed by 6 hex digits)
- Check file permissions on `cms-settings.json`
- Ensure CSRF token is valid (try refreshing the page)

## Advanced Customization

### Modifying CSS Directly

For advanced customization beyond theme colors, you can modify:
- `/catalogue/panel/assets/css/panel.css` - Main admin panel styles
- CSS variables in `:root` selector
- Component-specific styles

**Note**: Direct CSS modifications will be overwritten on CMS updates. Consider creating a custom CSS file that loads after the main stylesheet.

### Custom CSS Variables

You can add custom CSS variables in `panel.css`:

```css
:root {
    --color-accent: #e11d48;
    --color-accent-hover: #be123c;
    --color-accent-text: #ffffff;
    /* Add your custom variables */
    --custom-spacing: 24px;
    --custom-radius: 8px;
}
```

## See Also

- [CMS Settings](../admin-panel/CMS_SETTINGS.md) - CMS configuration
- [Configuration](../configuration/README.md) - System configuration
- [Troubleshooting](../troubleshooting/README.md) - Common issues

