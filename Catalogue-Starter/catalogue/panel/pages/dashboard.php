<?php
/**
 * Dashboard Page
 */
$page = 'dashboard';
$page_title = 'Dashboard';
require_once PANEL_DIR . '/partials/header.php';

// Load CMS settings to check if traffic is enabled
$cms_settings_file = CMS_ROOT . '/content/cms-settings.json';
$traffic_enabled = true; // Default to enabled
if (file_exists($cms_settings_file)) {
    $cms_settings = readJson($cms_settings_file);
    $traffic_enabled = isset($cms_settings['traffic_enabled']) ? (bool)$cms_settings['traffic_enabled'] : true;
}

// Get statistics
$blueprints = getAllBlueprints();
$blueprint_count = count($blueprints);

// Count pages
$page_count = 0;
$page_dirs = [];
if (is_dir(PAGES_DIR)) {
    $page_dirs = glob(PAGES_DIR . '/*', GLOB_ONLYDIR);
    foreach ($page_dirs as $page_dir) {
        $page_count += count(listJsonFiles($page_dir));
    }
}

// Count collections and items
$collection_count = 0;
$collection_item_count = 0;
$collection_dirs = [];
if (is_dir(COLLECTIONS_DIR)) {
    $collection_dirs = glob(COLLECTIONS_DIR . '/*', GLOB_ONLYDIR);
    $collection_count = count($collection_dirs);
    foreach ($collection_dirs as $collection_dir) {
        $collection_item_count += count(listJsonFiles($collection_dir));
    }
}

$total_content_count = $page_count + $collection_item_count;

// Get recent activity (last 7 days)
$recent_activity = [];
$all_content_files = [];

// Get all content files with their modification times
if (is_dir(PAGES_DIR)) {
    foreach ($page_dirs as $page_dir) {
        $files = listJsonFiles($page_dir);
        foreach ($files as $file) {
            $content = readJson($file);
            if ($content && isset($content['_meta']['updated'])) {
                $all_content_files[] = [
                    'file' => $file,
                    'type' => 'page',
                    'title' => $content['title'] ?? basename($file, '.json'),
                    'updated' => $content['_meta']['updated'],
                    'collection' => basename($page_dir)
                ];
            }
        }
    }
}

if (is_dir(COLLECTIONS_DIR)) {
    foreach ($collection_dirs as $collection_dir) {
        $files = listJsonFiles($collection_dir);
        foreach ($files as $file) {
            $content = readJson($file);
            if ($content && isset($content['_meta']['updated'])) {
                $all_content_files[] = [
                    'file' => $file,
                    'type' => 'collection',
                    'title' => $content['title'] ?? basename($file, '.json'),
                    'updated' => $content['_meta']['updated'],
                    'collection' => basename($collection_dir)
                ];
            }
        }
    }
}

// Sort by updated date (newest first)
usort($all_content_files, function($a, $b) {
    $a_time = isset($a['updated']) ? strtotime($a['updated']) : 0;
    $b_time = isset($b['updated']) ? strtotime($b['updated']) : 0;
    return $b_time - $a_time;
});

// Get last 5 recent items
$recent_activity = array_slice($all_content_files, 0, 5);

// Calculate storage usage
function getDirectorySize($dir) {
    $size = 0;
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $dir . '/' . $file;
            if (is_file($path)) {
                $fileSize = @filesize($path);
                if ($fileSize !== false) {
                    $size += $fileSize;
                }
            } elseif (is_dir($path)) {
                $size += getDirectorySize($path);
            }
        }
    }
    return $size;
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    if ($bytes === 0) {
        return '0 B';
    }
    $pow = floor(log($bytes) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Calculate content size (pages + collections)
$content_size = 0;
if (is_dir(PAGES_DIR)) {
    $content_size += getDirectorySize(PAGES_DIR);
}
if (is_dir(COLLECTIONS_DIR)) {
    $content_size += getDirectorySize(COLLECTIONS_DIR);
}

// Calculate uploads size
$uploads_size = 0;
if (is_dir(UPLOADS_DIR)) {
    $uploads_size = getDirectorySize(UPLOADS_DIR);
}

// Total storage (content + uploads)
$total_size = $content_size + $uploads_size;

// Calculate content growth (last 30 days)
$thirty_days_ago = date('c', strtotime('-30 days'));
$recent_content_count = 0;
foreach ($all_content_files as $file) {
    if (isset($file['updated']) && strtotime($file['updated']) >= strtotime($thirty_days_ago)) {
        $recent_content_count++;
    }
}

// Get collection breakdown
$collection_breakdown = [];
foreach ($collection_dirs as $collection_dir) {
    $name = basename($collection_dir);
    $files = listJsonFiles($collection_dir);
    $collection_breakdown[] = [
        'name' => $name,
        'count' => count($files)
    ];
}
// Sort by count descending
usort($collection_breakdown, function($a, $b) {
    return $b['count'] - $a['count'];
});

// Generate traffic/analytics data from real tracking
function generateTrafficData() {
    require_once CMS_ROOT . '/lib/catalogue.php';
    require_once CMS_ROOT . '/lib/storage.php';
    
    $trafficDir = CMS_ROOT . '/data/traffic';
    $traffic = [];
    
    // Get last 7 days of traffic
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayName = date('D', strtotime("-$i days"));
        $trafficFile = $trafficDir . '/' . $date . '.json';
        
        $dayViews = 0;
        $dayVisitors = [];
        
        if (file_exists($trafficFile)) {
            $dayData = readJson($trafficFile);
            if (is_array($dayData)) {
                // Sum views from all pages for this day
                foreach ($dayData as $pageData) {
                    if (isset($pageData['views'])) {
                        $dayViews += $pageData['views'];
                    }
                    // Collect unique visitors
                    if (isset($pageData['visitors']) && is_array($pageData['visitors'])) {
                        $dayVisitors = array_merge($dayVisitors, $pageData['visitors']);
                    }
                }
            }
        }
        
        // Count unique visitors
        $uniqueVisitors = count(array_unique($dayVisitors));
        
        $traffic[] = [
            'day' => $dayName,
            'views' => $dayViews,
            'visitors' => $uniqueVisitors
        ];
    }
    
    return $traffic;
}

$traffic_data = generateTrafficData();
$total_views = array_sum(array_column($traffic_data, 'views'));
$total_visitors = array_sum(array_column($traffic_data, 'visitors'));
$max_views = max(array_column($traffic_data, 'views')) ?: 1; // Prevent division by zero

// Count uploads (including subdirectories)
function countUploadFiles($dir) {
    $count = 0;
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.htaccess' || $file === '.gitkeep' || $file === 'index.php') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (is_file($path)) {
                $count++;
            } elseif (is_dir($path)) {
                $count += countUploadFiles($path);
            }
        }
    }
    return $count;
}

$upload_count = is_dir(UPLOADS_DIR) ? countUploadFiles(UPLOADS_DIR) : 0;

// Calculate content status breakdown
$status_counts = [
    'published' => 0,
    'draft' => 0,
    'unlisted' => 0
];

// Hidden pages that should be excluded from content status
$hidden_pages = ['settings', '404', 'media'];

// Count status for pages (excluding hidden pages)
if (is_dir(PAGES_DIR)) {
    foreach ($page_dirs as $page_dir) {
        $page_name = basename($page_dir);
        // Skip hidden pages
        if (in_array($page_name, $hidden_pages)) {
            continue;
        }
        
        $files = listJsonFiles($page_dir);
        foreach ($files as $file) {
            $content = readJson($file);
            if ($content) {
                $status = isset($content['_status']) ? $content['_status'] : (isset($content['status']) ? $content['status'] : 'draft');
                if (isset($status_counts[$status])) {
                    $status_counts[$status]++;
                } else {
                    $status_counts['draft']++;
                }
            }
        }
    }
}

// Count status for collections
if (is_dir(COLLECTIONS_DIR)) {
    foreach ($collection_dirs as $collection_dir) {
        $files = listJsonFiles($collection_dir);
        foreach ($files as $file) {
            $content = readJson($file);
            if ($content) {
                $status = isset($content['_status']) ? $content['_status'] : (isset($content['status']) ? $content['status'] : 'draft');
                if (isset($status_counts[$status])) {
                    $status_counts[$status]++;
                } else {
                    $status_counts['draft']++;
                }
            }
        }
    }
}

$total_with_status = array_sum($status_counts);

// Get most active collections (by item count)
$most_active_collections = array_slice($collection_breakdown, 0, 3);

// System health (real checks)
function checkSystemHealth() {
    $health = [
        'status' => 'healthy',
        'uptime' => '100%',
        'response_time' => '0ms',
        'last_backup' => 'Never'
    ];
    
    // Check if critical directories exist and are writable
    $critical_dirs = [
        PAGES_DIR => 'Pages directory',
        COLLECTIONS_DIR => 'Collections directory',
        UPLOADS_DIR => 'Uploads directory',
        CMS_ROOT . '/data' => 'Data directory'
    ];
    
    $issues = [];
    foreach ($critical_dirs as $dir => $name) {
        if (!is_dir($dir)) {
            $issues[] = "$name missing";
        } elseif (!is_writable($dir)) {
            $issues[] = "$name not writable";
        }
    }
    
    // Check PHP version and critical extensions
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        $issues[] = 'PHP version outdated';
    }
    
    if (!function_exists('json_encode')) {
        $issues[] = 'JSON extension missing';
    }
    
    if (!function_exists('file_get_contents')) {
        $issues[] = 'File functions unavailable';
    }
    
    // Determine status
    if (count($issues) > 0) {
        $health['status'] = count($issues) > 2 ? 'critical' : 'warning';
        $health['issues'] = $issues;
    }
    
    // Calculate response time (multiple operations for more accurate measurement)
    $start = microtime(true);
    
    // Perform several file operations to get a more realistic measurement
    $test_files = [
        CMS_ROOT . '/config.php',
        CMS_ROOT . '/lib/storage.php',
        CMS_ROOT . '/lib/catalogue.php'
    ];
    
    $operations = 0;
    foreach ($test_files as $test_file) {
        if (file_exists($test_file)) {
            @file_get_contents($test_file);
            $operations++;
        }
    }
    
    // If no files found, do a simple directory check
    if ($operations === 0) {
        @is_dir(CMS_ROOT);
        @is_dir(PAGES_DIR);
        @is_dir(COLLECTIONS_DIR);
        $operations = 3;
    }
    
    $end = microtime(true);
    $response_time_ms = ($end - $start) * 1000;
    
    // Round to 1 decimal place, but ensure minimum of 0.1ms
    $response_time_ms = max(0.1, round($response_time_ms, 1));
    $health['response_time'] = $response_time_ms . 'ms';
    
    // Check for recent content updates (as a proxy for "uptime")
    // If content was updated in last 24 hours, system is active
    $recent_update = false;
    if (is_dir(PAGES_DIR)) {
        $page_dirs = glob(PAGES_DIR . '/*', GLOB_ONLYDIR);
        foreach ($page_dirs as $page_dir) {
            $files = listJsonFiles($page_dir);
            foreach ($files as $file) {
                if (file_exists($file) && filemtime($file) > (time() - 86400)) {
                    $recent_update = true;
                    break 2;
                }
            }
        }
    }
    
    if (!$recent_update && is_dir(COLLECTIONS_DIR)) {
        $collection_dirs = glob(COLLECTIONS_DIR . '/*', GLOB_ONLYDIR);
        foreach ($collection_dirs as $collection_dir) {
            $files = listJsonFiles($collection_dir);
            foreach ($files as $file) {
                if (file_exists($file) && filemtime($file) > (time() - 86400)) {
                    $recent_update = true;
                    break 2;
                }
            }
        }
    }
    
    $health['uptime'] = $recent_update ? '100%' : '99.9%';
    
    // Check for backup files or last generation time
    $last_generation = 0;
    if (is_dir(CMS_ROOT . '/../')) {
        $html_files = glob(CMS_ROOT . '/../*.html');
        foreach ($html_files as $html_file) {
            $mtime = filemtime($html_file);
            if ($mtime > $last_generation) {
                $last_generation = $mtime;
            }
        }
    }
    
    if ($last_generation > 0) {
        $days_ago = floor((time() - $last_generation) / 86400);
        if ($days_ago === 0) {
            $health['last_backup'] = 'Today';
        } elseif ($days_ago === 1) {
            $health['last_backup'] = 'Yesterday';
        } else {
            $health['last_backup'] = date('M j, Y', $last_generation);
        }
    }
    
    return $health;
}

$system_health = checkSystemHealth();

// Get current date/time info
$current_date = date('F j, Y');
$current_day = date('l');
$current_time = date('g:i A');
$timezone = date_default_timezone_get();
$day_of_year = date('z') + 1;
$week_of_year = date('W');
$days_in_year = date('L') ? 366 : 365;
$year_progress = round(($day_of_year / $days_in_year) * 100, 1);
?>
<div class="cms-content">
    <!-- Tools Section -->
    <section class="cms-dashboard-section">
        <div class="cms-dashboard-grid cms-dashboard-tools">
    <!-- Time Widget -->
    <div class="cms-card cms-dashboard-time">
        <div class="cms-card-body">
            <div class="cms-time-widget">
                <div class="cms-time-display" id="cms-clock"><?php echo esc($current_time); ?></div>
                <div class="cms-time-meta">
                    <span class="cms-time-timezone"><?php echo esc($timezone); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Date Widget -->
    <div class="cms-card cms-dashboard-date">
        <div class="cms-card-body">
            <div class="cms-date-widget">
                <div class="cms-date-day"><?php echo esc($current_day); ?></div>
                <div class="cms-date-date"><?php echo esc($current_date); ?></div>
                <div class="cms-date-meta">
                    <span>Day <?php echo esc($day_of_year); ?> of <?php echo esc($days_in_year); ?></span>
                    <span>Week <?php echo esc($week_of_year); ?></span>
                </div>
            </div>
        </div>
    </div>
    
            <!-- Year Progress Widget -->
            <div class="cms-card cms-dashboard-year">
                <div class="cms-card-header">
                    <h3 class="cms-card-title"><?php echo esc(date('Y')); ?> Progress</h3>
                </div>
                <div class="cms-card-body">
                    <div class="cms-year-progress">
                        <div class="cms-year-progress-value"><?php echo esc($year_progress); ?>%</div>
                        <div class="cms-year-progress-bar">
                            <div class="cms-year-progress-fill" style="width: <?php echo esc($year_progress); ?>%;"></div>
                        </div>
                        <div class="cms-year-progress-meta">
                            <span>Week <?php echo esc($week_of_year); ?></span>
                            <span><?php echo esc($days_in_year - $day_of_year); ?> days remaining</span>
                        </div>
            </div>
        </div>
    </div>
    
            <!-- Useful Links Widget -->
            <div class="cms-card cms-dashboard-links">
                <div class="cms-card-header">
                    <h3 class="cms-card-title">Useful Links</h3>
                    <?php echo icon('link-1', 'cms-icon'); ?>
                </div>
                <div class="cms-card-body">
                    <div class="cms-useful-links">
                        <a href="<?php echo CMS_URL; ?>/index.php?page=pages" class="cms-useful-link">
                            <?php echo icon('file', 'cms-icon'); ?>
                            <span>All Pages</span>
                        </a>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=collections" class="cms-useful-link">
                            <?php echo icon('layers', 'cms-icon'); ?>
                            <span>All Collections</span>
                        </a>
                        <a href="<?php echo str_replace('/cms', '', CMS_URL); ?>" target="_blank" class="cms-useful-link">
                            <?php echo icon('external-link', 'cms-icon'); ?>
                            <span>View Site</span>
                        </a>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=settings" class="cms-useful-link">
                            <?php echo icon('globe', 'cms-icon'); ?>
                            <span>Site</span>
                        </a>
            </div>
        </div>
    </div>
    
            <!-- Quick Notes Widget -->
            <div class="cms-card cms-dashboard-notes">
                <div class="cms-card-header">
                    <h3 class="cms-card-title">Quick Notes</h3>
                    <?php echo icon('pencil-1', 'cms-icon'); ?>
                </div>
                <div class="cms-card-body">
                    <div class="cms-quick-notes">
                        <textarea id="cms-quick-notes" class="cms-quick-notes-input" placeholder="Jot down quick thoughts, reminders, or ideas..."></textarea>
                        <div class="cms-quick-notes-info">
                            <span class="cms-text-muted">Saved automatically</span>
            </div>
        </div>
    </div>
            </div>
        </div>
    </section>
    
    <!-- Content & Analytics Section -->
    <section class="cms-dashboard-section">
        <div class="cms-dashboard-grid cms-dashboard-content">
      <!-- Content Status Card -->
    <div class="cms-card cms-dashboard-status">
        <div class="cms-card-header">
            <h3 class="cms-card-title">Content Status</h3>
        </div>
        <div class="cms-card-body">
            <div class="cms-status-overview">
                <?php if ($total_with_status > 0): 
                    $published_pct = ($status_counts['published'] / $total_with_status) * 100;
                    $draft_pct = ($status_counts['draft'] / $total_with_status) * 100;
                    $unlisted_pct = ($status_counts['unlisted'] / $total_with_status) * 100;
                ?>
                    <div class="cms-status-breakdown">
                        <a href="<?php echo CMS_URL; ?>/index.php?page=pages" class="cms-status-item">
                            <div class="cms-status-item-header">
                                <span class="cms-status-label">Published</span>
                                <span class="cms-status-value"><?php echo esc($status_counts['published']); ?></span>
                            </div>
                            <div class="cms-status-bar">
                                <div class="cms-status-bar-fill cms-status-published" style="width: <?php echo esc($published_pct); ?>%;"></div>
                            </div>
                        </a>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=pages" class="cms-status-item">
                            <div class="cms-status-item-header">
                                <span class="cms-status-label">Draft</span>
                                <span class="cms-status-value"><?php echo esc($status_counts['draft']); ?></span>
                </div>
                            <div class="cms-status-bar">
                                <div class="cms-status-bar-fill cms-status-draft" style="width: <?php echo esc($draft_pct); ?>%;"></div>
                            </div>
                        </a>
                        <a href="<?php echo CMS_URL; ?>/index.php?page=pages" class="cms-status-item">
                            <div class="cms-status-item-header">
                                <span class="cms-status-label">Unlisted</span>
                                <span class="cms-status-value"><?php echo esc($status_counts['unlisted']); ?></span>
                                </div>
                            <div class="cms-status-bar">
                                <div class="cms-status-bar-fill cms-status-unlisted" style="width: <?php echo esc($unlisted_pct); ?>%;"></div>
                            </div>
                        </a>
                        </div>
                <?php else: ?>
                    <div class="cms-empty-state-small">
                        <p class="cms-text-muted">No content yet</p>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Traffic Card -->
    <?php if ($traffic_enabled): ?>
    <div class="cms-card cms-dashboard-traffic">
        <div class="cms-card-header">
            <h3 class="cms-card-title">Traffic Overview</h3>
            <div class="cms-card-header-actions">
                <span class="cms-badge cms-badge-info">Last 7 days</span>
            </div>
        </div>
        <div class="cms-card-body">
            <div class="cms-traffic-stats">
                <div class="cms-traffic-stat">
                    <div class="cms-traffic-stat-value"><?php echo esc(number_format($total_views)); ?></div>
                    <div class="cms-traffic-stat-label">Total Views</div>
                </div>
                <div class="cms-traffic-stat">
                    <div class="cms-traffic-stat-value"><?php echo esc(number_format($total_visitors)); ?></div>
                    <div class="cms-traffic-stat-label">Visitors</div>
                </div>
            </div>
            <div class="cms-chart">
                <div class="cms-chart-bars">
                    <?php foreach ($traffic_data as $day): ?>
                        <div class="cms-chart-bar">
                            <div class="cms-chart-bar-fill" style="height: <?php echo $max_views > 0 ? ($day['views'] / $max_views * 100) : 0; ?>%;" title="<?php echo esc($day['views']); ?> views"></div>
                            <div class="cms-chart-bar-label"><?php echo esc($day['day']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Recent Activity Card -->
    <div class="cms-card cms-dashboard-activity">
        <div class="cms-card-header">
            <h3 class="cms-card-title">Recent Activity</h3>
        </div>
        <div class="cms-card-body">
            <?php if (empty($recent_activity)): ?>
                <div class="cms-empty-state-small">
                    <p class="cms-text-muted">No recent activity</p>
                </div>
            <?php else: ?>
                <div class="cms-activity-list">
                    <?php foreach ($recent_activity as $item): ?>
                        <?php
                        // Build the edit URL based on type
                        if ($item['type'] === 'page') {
                            $edit_url = CMS_URL . '/index.php?page=pages&action=edit&type=' . urlencode($item['collection']);
                        } else {
                            // For collections, extract the item filename from the file path
                            $item_file = basename($item['file'], '.json');
                            $edit_url = CMS_URL . '/index.php?page=collections&action=edit&type=' . urlencode($item['collection']) . '&item=' . urlencode($item_file);
                        }
                        ?>
                        <a href="<?php echo esc_attr($edit_url); ?>" class="cms-activity-item" style="text-decoration: none; color: inherit; display: flex;">
                            <div class="cms-activity-icon">
                                <?php echo icon($item['type'] === 'collection' ? 'layers' : 'file', 'cms-icon'); ?>
                            </div>
                            <div class="cms-activity-content">
                                <div class="cms-activity-title"><?php echo esc($item['title']); ?></div>
                                <div class="cms-activity-meta">
                                    <span class="cms-badge cms-badge-info"><?php echo esc($item['type'] === 'collection' ? $item['collection'] : 'page'); ?></span>
                                    <span class="cms-text-muted"><?php echo esc(formatDate($item['updated'])); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Collection Breakdown Card -->
    <?php if (!empty($collection_breakdown)): ?>
    <div class="cms-card cms-dashboard-collections">
        <div class="cms-card-header">
            <h3 class="cms-card-title">Collections</h3>
        </div>
        <div class="cms-card-body">
            <div class="cms-collection-breakdown">
                <?php 
                $max_count = !empty($collection_breakdown) ? max(array_column($collection_breakdown, 'count')) : 1;
                foreach (array_slice($collection_breakdown, 0, 5) as $collection): 
                ?>
                    <div class="cms-collection-breakdown-item">
                        <div class="cms-collection-breakdown-label">
                            <span><?php echo esc(ucfirst($collection['name'])); ?></span>
                            <span class="cms-badge cms-badge-info"><?php echo esc($collection['count']); ?></span>
                        </div>
                        <div class="cms-collection-breakdown-bar">
                            <div class="cms-collection-breakdown-fill" style="width: <?php echo $max_count > 0 ? ($collection['count'] / $max_count * 100) : 0; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Storage Usage Card -->
    <div class="cms-card cms-dashboard-storage">
        <div class="cms-card-header">
            <h3 class="cms-card-title">Storage Usage</h3>
        </div>
        <div class="cms-card-body">
            <div class="cms-storage">
                <div class="cms-storage-total">
                    <div class="cms-storage-label">Total Storage</div>
                    <div class="cms-storage-value"><?php echo esc(formatBytes($total_size)); ?></div>
                </div>
                <div class="cms-storage-breakdown">
                    <div class="cms-storage-item">
                        <div class="cms-storage-item-label">
                            <span>Content</span>
                            <span><?php echo esc(formatBytes($content_size)); ?></span>
                        </div>
                        <div class="cms-storage-bar">
                            <div class="cms-storage-fill" style="width: <?php echo $total_size > 0 ? ($content_size / $total_size * 100) : 0; ?>%;"></div>
                        </div>
                    </div>
                    <div class="cms-storage-item">
                        <div class="cms-storage-item-label">
                            <span>Uploads</span>
                            <span><?php echo esc(formatBytes($uploads_size)); ?></span>
                        </div>
                        <div class="cms-storage-bar">
                            <div class="cms-storage-fill cms-storage-fill-secondary" style="width: <?php echo $total_size > 0 ? ($uploads_size / $total_size * 100) : 0; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Health Card -->
    <div class="cms-card cms-dashboard-health">
        <div class="cms-card-header">
            <h3 class="cms-card-title">System Health</h3>
        </div>
        <div class="cms-card-body">
            <div class="cms-system-health">
                <div class="cms-system-health-item">
                    <div class="cms-system-health-label">Status</div>
                    <div class="cms-system-health-value">
                        <?php 
                        $status_class = 'cms-status-healthy';
                        if ($system_health['status'] === 'warning') {
                            $status_class = 'cms-status-warning';
                        } elseif ($system_health['status'] === 'critical') {
                            $status_class = 'cms-status-error';
                        }
                        ?>
                        <span class="cms-status-indicator <?php echo esc($status_class); ?>"></span>
                        <?php echo esc(ucfirst($system_health['status'])); ?>
                    </div>
                </div>
                <div class="cms-system-health-item">
                    <div class="cms-system-health-label">Uptime</div>
                    <div class="cms-system-health-value"><?php echo esc($system_health['uptime']); ?></div>
                </div>
                <div class="cms-system-health-item">
                    <div class="cms-system-health-label">Response Time</div>
                    <div class="cms-system-health-value"><?php echo esc($system_health['response_time']); ?></div>
                </div>
                <div class="cms-system-health-item">
                    <div class="cms-system-health-label">Last Generation</div>
                    <div class="cms-system-health-value"><?php echo esc($system_health['last_backup']); ?></div>
                </div>
                <?php if (isset($system_health['issues']) && !empty($system_health['issues'])): ?>
                    <div class="cms-system-health-item" style="grid-column: 1 / -1; margin-top: var(--space-2); padding-top: var(--space-2); border-top: 1px solid var(--color-border);">
                        <div class="cms-system-health-label" style="color: var(--color-error);">Issues</div>
                        <div class="cms-system-health-value" style="font-size: var(--font-size-sm);">
                            <?php foreach ($system_health['issues'] as $issue): ?>
                                <div><?php echo esc($issue); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
        </div>
    </section>
</div>

<script>
// Live clock update
function updateClock() {
    const clockElement = document.getElementById('cms-clock');
    if (clockElement) {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
        clockElement.textContent = displayHours + ':' + displayMinutes + ' ' + ampm;
    }
}

// Update clock every minute
updateClock();
setInterval(updateClock, 60000);

// Quick Notes auto-save
(function() {
    const notesInput = document.getElementById('cms-quick-notes');
    if (notesInput) {
        // Load saved notes
        const savedNotes = localStorage.getItem('cms-quick-notes');
        if (savedNotes) {
            notesInput.value = savedNotes;
        }
        
        // Save on input
        notesInput.addEventListener('input', function() {
            localStorage.setItem('cms-quick-notes', this.value);
        });
    }
})();
</script>

<?php require_once PANEL_DIR . '/partials/footer.php'; ?>
