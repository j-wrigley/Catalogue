# Dashboard

Overview and statistics dashboard for the CMS.

## Overview

The dashboard provides an at-a-glance view of your CMS, including statistics, recent activity, and quick access to common tasks.

## Accessing

Navigate to **Dashboard** in the sidebar or visit:
```
/catalogue/index.php?page=dashboard
```

## Dashboard Widgets

### Statistics Cards

#### Content Status
- **Published** - Number of published items
- **Draft** - Number of draft items
- **Unlisted** - Number of unlisted items
- Visual progress bars showing content distribution

#### Traffic Overview
- **Today** - Page views today
- **This Week** - Page views this week
- **This Month** - Page views this month
- **Total** - All-time page views
- Only shown if traffic tracking is enabled

#### Recent Activity
- List of last 5 updated items
- Shows title, type (page/collection), and update date
- Click to edit item

#### Collections
- Breakdown by collection type
- Shows item count per collection
- Click to view collection

#### Storage Usage
- Visual progress bar
- Shows used/total storage
- Calculated from uploads directory

#### System Health
- PHP version
- Server information
- System status indicators

### Utility Widgets

#### Time & Date
- Current time (12-hour format)
- Current date
- Large, readable display

#### Year Progress
- Visual progress bar
- Percentage through the year
- Days remaining

#### Quick Notes
- Personal notes area
- Stored in browser localStorage
- Persistent across sessions

#### Useful Links
- Custom links list
- Stored in browser localStorage
- Quick access to external resources

## Dashboard Layout

The dashboard uses a responsive grid layout:
- Cards automatically arrange based on screen size
- Some cards span multiple columns
- Responsive breakpoints for mobile/tablet

## Interacting with Widgets

### Content Status
- Click bars to filter content by status
- Visual representation of content distribution

### Recent Activity
- Click any item to edit it
- Shows most recently updated content first

### Collections
- Click collection name to view items
- Shows count of items per collection

### Quick Notes & Links
- Click to edit
- Changes saved automatically
- Stored locally in browser

## Customization

### Hiding Widgets

Some widgets can be hidden via CMS settings:
- **Traffic Overview** - Hide if traffic tracking is disabled
- Other widgets are always visible

### Widget Order

Widgets are arranged in a logical order:
1. Statistics (Content Status, Traffic)
2. Activity (Recent Activity, Collections)
3. Utilities (Time, Date, Year Progress)
4. Tools (Quick Notes, Useful Links)
5. System (Storage, Health)

## Examples

### Viewing Statistics

1. Open Dashboard
2. View Content Status card
3. See breakdown of published/draft/unlisted content

### Checking Recent Activity

1. Open Dashboard
2. Scroll to Recent Activity card
3. See last 5 updated items
4. Click any item to edit

### Adding Quick Notes

1. Open Dashboard
2. Find Quick Notes card
3. Click to edit
4. Type your notes
5. Notes saved automatically

## See Also

- [Pages](./PAGES.md) - Managing pages
- [Collections](./COLLECTIONS.md) - Managing collections
- [CMS Settings](./CMS_SETTINGS.md) - Dashboard configuration

