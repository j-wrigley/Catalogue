<?php
/**
 * Traffic Logging Endpoint
 * Called via JavaScript from static HTML pages to log page views
 */

// Prevent direct access (only allow POST requests)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Set JSON response header
header('Content-Type: application/json');

// Get CMS root (go up one directory from data/)
define('CMS_ROOT', dirname(__DIR__));

// Load required files
require_once CMS_ROOT . '/lib/storage.php';

// Get page identifier from POST
$page = $_POST['page'] ?? null;
if (empty($page)) {
    // Try to auto-detect from referrer
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (!empty($referer)) {
        $parsed = parse_url($referer);
        $page = $parsed['path'] ?? '/';
        $page = ltrim($page, '/');
        $page = preg_replace('/\.html$/', '', $page);
        if (empty($page)) {
            $page = 'home';
        }
    } else {
        $page = 'home';
    }
}

// Sanitize page name
$page = preg_replace('/[^a-z0-9_-]/i', '', $page);
if (empty($page)) {
    $page = 'home';
}

// Determine traffic data directory
$trafficDir = CMS_ROOT . '/data/traffic';
if (!is_dir($trafficDir)) {
    mkdir($trafficDir, 0755, true);
}

// Get current date for daily logs
$date = date('Y-m-d');
$trafficFile = $trafficDir . '/' . $date . '.json';

// Load existing traffic data for today
$trafficData = [];
if (file_exists($trafficFile)) {
    $trafficData = readJson($trafficFile);
    if (!is_array($trafficData)) {
        $trafficData = [];
    }
}

// Get visitor identifier (hash IP for privacy)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$visitorId = hash('sha256', $ip . $userAgent);

// Initialize page entry if not exists
if (!isset($trafficData[$page])) {
    $trafficData[$page] = [
        'views' => 0,
        'visitors' => [],
        'last_view' => null
    ];
}

// Increment views
$trafficData[$page]['views']++;

// Track unique visitors (store first 8 chars of hash for privacy)
$visitorShort = substr($visitorId, 0, 8);
if (!in_array($visitorShort, $trafficData[$page]['visitors'])) {
    $trafficData[$page]['visitors'][] = $visitorShort;
    // Keep only last 1000 unique visitors per page per day
    if (count($trafficData[$page]['visitors']) > 1000) {
        $trafficData[$page]['visitors'] = array_slice($trafficData[$page]['visitors'], -1000);
    }
}

// Update last view timestamp
$trafficData[$page]['last_view'] = date('c');

// Save traffic data
writeJson($trafficFile, $trafficData);

// Return success response
echo json_encode(['success' => true, 'page' => $page]);
exit;

