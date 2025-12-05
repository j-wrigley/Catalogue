<?php
/**
 * Panel Navigation
 * Main navigation menu - Collapsible sidebar
 */
$current_page = $_GET['page'] ?? 'dashboard';

// Get site URL from settings, fallback to BASE_PATH
// Note: save.php forces filename to {content_type}.json for pages, so it's settings.json
$site_settings = readJson(PAGES_DIR . '/settings/settings.json');
$site_url = $site_settings['site_url'] ?? '';

// If site_url is default or empty, use BASE_PATH
if (empty($site_url) || $site_url === 'https://example.com') {
    $site_url = defined('BASE_PATH') ? BASE_PATH : '/';
    if (empty($site_url)) {
        $site_url = '/';
    }
}

// Ensure site_url is absolute (starts with / or http)
if (!preg_match('/^(https?:\/\/|\/)/', $site_url)) {
    $site_url = '/' . ltrim($site_url, '/');
}
?>
<nav class="cms-nav" id="cms-nav">
    <button class="cms-nav-toggle" id="cms-nav-toggle" aria-label="Toggle sidebar">
        <?php echo icon('chevron-left', 'cms-icon'); ?>
    </button>
    <ul class="cms-nav-list">
        <li>
            <a href="<?php echo CMS_URL; ?>/index.php?page=dashboard" class="cms-nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" title="Dashboard">
                <?php echo icon('dashboard', 'cms-icon'); ?>
                <span class="cms-nav-link-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="<?php echo CMS_URL; ?>/index.php?page=pages" class="cms-nav-link <?php echo $current_page === 'pages' ? 'active' : ''; ?>" title="Pages">
                <?php echo icon('card-stack', 'cms-icon'); ?>
                <span class="cms-nav-link-text">Pages</span>
            </a>
        </li>
        <li>
            <a href="<?php echo CMS_URL; ?>/index.php?page=collections" class="cms-nav-link <?php echo $current_page === 'collections' ? 'active' : ''; ?>" title="Collections">
                <?php echo icon('grid', 'cms-icon'); ?>
                <span class="cms-nav-link-text">Collections</span>
            </a>
        </li>
        <li>
            <a href="<?php echo CMS_URL; ?>/index.php?page=media" class="cms-nav-link <?php echo $current_page === 'media' ? 'active' : ''; ?>" title="Media">
                <?php echo icon('image', 'cms-icon'); ?>
                <span class="cms-nav-link-text">Media</span>
            </a>
        </li>
        <li>
            <a href="<?php echo CMS_URL; ?>/index.php?page=settings" class="cms-nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" title="Site">
                <?php echo icon('globe', 'cms-icon'); ?>
                <span class="cms-nav-link-text">Site</span>
            </a>
        </li>
        <li>
            <a href="<?php echo CMS_URL; ?>/index.php?page=users" class="cms-nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>" title="Users">
                <?php echo icon('person', 'cms-icon'); ?>
                <span class="cms-nav-link-text">Users</span>
            </a>
        </li>
    </ul>
    <div class="cms-nav-footer">
        <a href="<?php echo htmlspecialchars($site_url); ?>" class="cms-nav-link cms-nav-link-external" target="_blank" title="View Site">
            <?php echo icon('external-link', 'cms-icon'); ?>
            <span class="cms-nav-link-text">View Site</span>
        </a>
        <a href="<?php echo CMS_URL; ?>/index.php?page=cms-settings" class="cms-nav-link <?php echo $current_page === 'cms-settings' ? 'active' : ''; ?>" title="Settings">
            <?php echo icon('gear', 'cms-icon'); ?>
            <span class="cms-nav-link-text">Settings</span>
        </a>
        <a href="<?php echo CMS_URL; ?>/panel/actions/logout.php" class="cms-nav-link cms-nav-link-logout" title="Logout">
            <?php echo icon('exit', 'cms-icon'); ?>
            <span class="cms-nav-link-text">Logout</span>
        </a>
    </div>
</nav>
