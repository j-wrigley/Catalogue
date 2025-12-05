<?php
/**
 * Login Page
 */
$page_title = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check rate limit before attempting login
    if (!checkLoginRateLimit($username)) {
        $error = 'Too many failed login attempts. Please try again in 15 minutes.';
    } elseif (login($username, $password)) {
        header('Location: ' . CMS_URL . '/index.php?page=dashboard');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

$page = 'login';
require_once PANEL_DIR . '/partials/header.php';
?>
<div class="cms-login">
    <div class="cms-login-card">
        <div class="cms-login-header">
        <p class="cms-login-subtitle">Sign in to your account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="cms-alert cms-alert-error">
                <?php echo esc($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="cms-login-form">
            <div class="cms-form-group">
                <label for="username" class="cms-label">Username</label>
                <input type="text" id="username" name="username" class="cms-input" required autofocus>
            </div>
            
            <div class="cms-form-group">
                <label for="password" class="cms-label">Password</label>
                <input type="password" id="password" name="password" class="cms-input" required>
            </div>
            
            <button type="submit" class="cms-button cms-button-primary cms-button-full">Sign in</button>
        </form>
    </div>
</div>
<?php require_once PANEL_DIR . '/partials/footer.php'; ?>

