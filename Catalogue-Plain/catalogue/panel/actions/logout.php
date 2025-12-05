<?php
/**
 * Logout Action
 */
define('CMS_ROOT', dirname(__FILE__) . '/../..');
require_once CMS_ROOT . '/config.php';

logout();

// Use CMS_URL which is already correctly calculated
header('Location: ' . CMS_URL . '/index.php?page=login');
exit;

