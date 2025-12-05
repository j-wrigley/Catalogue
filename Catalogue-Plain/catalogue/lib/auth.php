<?php
/**
 * Authentication Functions
 * Login/logout logic
 */

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Require login (redirect if not logged in)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . CMS_URL . '/index.php?page=login');
        exit;
    }
}

/**
 * Get all users
 */
function getAllUsers() {
    $users = [];
    $users_dir = CONTENT_DIR . '/users';
    
    if (!is_dir($users_dir)) {
        return $users;
    }
    
    $files = listJsonFiles($users_dir);
    foreach ($files as $file) {
        $user = readJson($file);
        if ($user && isset($user['username'])) {
            // Don't include password in user list
            unset($user['password']);
            $user['filename'] = basename($file);
            $users[] = $user;
        }
    }
    
    return $users;
}

/**
 * Get user by username
 */
function getUserByUsername($username) {
    $users_dir = CONTENT_DIR . '/users';
    $user_file = $users_dir . '/' . sanitizeFilename($username) . '.json';
    
    if (!file_exists($user_file)) {
        return null;
    }
    
    return readJson($user_file);
}

/**
 * Create or update user
 */
function saveUser($username, $password = null, $existing_username = null) {
    $users_dir = CONTENT_DIR . '/users';
    
    if (!is_dir($users_dir)) {
        mkdir($users_dir, 0755, true);
    }
    
    // If updating existing user, delete old file if username changed
    if ($existing_username && $existing_username !== $username) {
        $old_file = $users_dir . '/' . sanitizeFilename($existing_username) . '.json';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
    }
    
    $user_file = $users_dir . '/' . sanitizeFilename($username) . '.json';
    
    // Load existing user data if updating
    $user_data = [];
    if ($existing_username && file_exists($user_file)) {
        $user_data = readJson($user_file);
    }
    
    // Update user data
    $user_data['username'] = $username;
    if ($password !== null && !empty($password)) {
        $user_data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    
    if (!isset($user_data['created'])) {
        $user_data['created'] = getTimestamp();
    }
    
    $user_data['updated'] = getTimestamp();
    $user_data['updated_by'] = $_SESSION['username'] ?? 'system';
    
    return writeJson($user_file, $user_data);
}

/**
 * Delete user
 */
function deleteUser($username) {
    // Prevent deleting yourself
    if (isset($_SESSION['username']) && $_SESSION['username'] === $username) {
        return false;
    }
    
    $users_dir = CONTENT_DIR . '/users';
    $user_file = $users_dir . '/' . sanitizeFilename($username) . '.json';
    
    if (!file_exists($user_file)) {
        return false;
    }
    
    return deleteJson($user_file);
}

/**
 * Check login rate limit
 * Prevents brute force attacks
 */
function checkLoginRateLimit($username) {
    $rate_limit_file = LOGS_DIR . '/login_attempts.json';
    $max_attempts = 5;
    $lockout_time = 900; // 15 minutes
    
    if (!file_exists($rate_limit_file)) {
        return true; // No previous attempts
    }
    
    $attempts = readJson($rate_limit_file) ?: [];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = $ip . '_' . $username;
    
    if (isset($attempts[$key])) {
        $attempt_data = $attempts[$key];
        $last_attempt = $attempt_data['last_attempt'] ?? 0;
        $count = $attempt_data['count'] ?? 0;
        
        // Reset if lockout period has passed
        if (time() - $last_attempt > $lockout_time) {
            unset($attempts[$key]);
            if (!empty($attempts)) {
                writeJson($rate_limit_file, $attempts);
            } else {
                @unlink($rate_limit_file);
            }
            return true;
        }
        
        // Check if exceeded max attempts
        if ($count >= $max_attempts) {
            return false; // Rate limited
        }
    }
    
    return true;
}

/**
 * Record failed login attempt
 */
function recordFailedLogin($username) {
    $rate_limit_file = LOGS_DIR . '/login_attempts.json';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = $ip . '_' . $username;
    
    $attempts = file_exists($rate_limit_file) ? (readJson($rate_limit_file) ?: []) : [];
    
    if (!isset($attempts[$key])) {
        $attempts[$key] = ['count' => 0, 'last_attempt' => 0];
    }
    
    $attempts[$key]['count']++;
    $attempts[$key]['last_attempt'] = time();
    
    writeJson($rate_limit_file, $attempts);
}

/**
 * Clear login attempts on successful login
 */
function clearLoginAttempts($username) {
    $rate_limit_file = LOGS_DIR . '/login_attempts.json';
    if (!file_exists($rate_limit_file)) {
        return;
    }
    
    $attempts = readJson($rate_limit_file) ?: [];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = $ip . '_' . $username;
    
    if (isset($attempts[$key])) {
        unset($attempts[$key]);
        if (!empty($attempts)) {
            writeJson($rate_limit_file, $attempts);
        } else {
            @unlink($rate_limit_file);
        }
    }
}

/**
 * Login user
 */
function login($username, $password) {
    // Check rate limit
    if (!checkLoginRateLimit($username)) {
        return false;
    }
    
    $user = getUserByUsername($username);
    
    if (!$user || !isset($user['username'])) {
        recordFailedLogin($username);
        return false;
    }
    
    // Verify password
    if ($user['username'] === $username && password_verify($password, $user['password'])) {
        // Clear failed attempts on successful login
        clearLoginAttempts($username);
        
        // Security: Regenerate session ID on login to prevent session fixation
        session_regenerate_id(true);
        
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        return true;
    }
    
    // Record failed attempt
    recordFailedLogin($username);
    return false;
}

/**
 * Logout user
 */
function logout() {
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['login_time'])) {
        $elapsed = time() - $_SESSION['login_time'];
        if ($elapsed > SESSION_LIFETIME) {
            logout();
            return false;
        }
    }
    return true;
}

// SECURITY: Default admin user creation disabled
// Manually create admin user through the CMS interface or by creating:
// cms/content/users/admin.json with hashed password using password_hash()
// Example: {"username":"admin","password":"$2y$10$..."}

