<?php
/**
 * Users Page
 */
$page = 'users';
$page_title = 'Users';
require_once PANEL_DIR . '/partials/header.php';

// Get all users
$users = getAllUsers();
$current_user = $_SESSION['username'] ?? '';
?>
<div class="cms-content">
    <?php if (empty($users)): ?>
        <div class="cms-empty-state">
            <?php echo icon('person', 'cms-icon cms-empty-state-icon'); ?>
            <h3 class="cms-empty-state-title">No users found</h3>
            <p class="cms-empty-state-description">Create your first user to get started.</p>
            <button class="cms-button cms-button-primary" onclick="openUserModal()" style="margin-top: var(--space-4);">
                <?php echo icon('plus', 'cms-icon'); ?>
                <span>New User</span>
            </button>
        </div>
    <?php else: ?>
        <div class="cms-table-wrapper">
            <div class="cms-table-header-actions">
                <button class="cms-button cms-button-outline" onclick="openUserModal()">
                    <?php echo icon('plus', 'cms-icon'); ?>
                    <span>New User</span>
                </button>
            </div>
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: var(--space-2);">
                                    <?php 
                                    $display_username = $user['username'];
                                    $truncated_username = mb_strlen($display_username) > 30 ? mb_substr($display_username, 0, 30) . '...' : $display_username;
                                    ?>
                                    <strong><?php echo esc($truncated_username); ?></strong>
                                <?php if ($user['username'] === $current_user): ?>
                                    <span class="cms-badge cms-badge-info">You</span>
                                <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo esc(formatDate($user['created'] ?? null)); ?></td>
                            <td><?php echo esc(formatDate($user['updated'] ?? null)); ?></td>
                            <td>
                                <div class="cms-table-actions">
                                    <button class="cms-button cms-button-ghost cms-button-sm" onclick="editUser('<?php echo esc_attr($user['username']); ?>')" title="Edit">
                                        <?php echo icon('pencil-1', 'cms-icon'); ?>
                                    </button>
                                    <?php if ($user['username'] !== $current_user): ?>
                                        <button class="cms-button cms-button-ghost cms-button-sm cms-button-danger" onclick="deleteUser('<?php echo esc_attr($user['username']); ?>')" title="Delete">
                                            <?php echo icon('trash', 'cms-icon'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- User Modal -->
<div id="user-modal" class="cms-modal" style="display: none;">
    <div class="cms-modal-backdrop" onclick="closeUserModal()"></div>
    <div class="cms-modal-content">
        <div class="cms-modal-header">
            <h3 class="cms-modal-title" id="user-modal-title">New User</h3>
            <button class="cms-modal-close" onclick="closeUserModal()" aria-label="Close">&times;</button>
        </div>
        <form id="user-form" onsubmit="saveUser(event)">
            <div class="cms-modal-body">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <input type="hidden" name="existing_username" id="existing-username">
            
            <div class="cms-form-group">
                <label for="username" class="cms-label">Username</label>
                <input type="text" id="username" name="username" class="cms-input" required>
            </div>
            
            <div class="cms-form-group">
                <label for="password" class="cms-label">
                    Password
                </label>
                <span id="password-hint" class="cms-text-muted" style="display: none; font-size: var(--font-size-xs); margin-top: var(--space-1); margin-bottom: var(--space-2);">(leave blank to keep current)</span>
                <input type="password" id="password" name="password" class="cms-input">
            </div>
            
            <div class="cms-form-group">
                <label for="password-confirm" class="cms-label">Confirm Password</label>
                <input type="password" id="password-confirm" name="password_confirm" class="cms-input">
                </div>
            </div>
            
            <div class="cms-modal-footer">
                <button type="button" class="cms-button cms-button-ghost" onclick="closeUserModal()">Cancel</button>
                <button type="submit" class="cms-button cms-button-primary">Save User</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUserModal(username = null) {
    const modal = document.getElementById('user-modal');
    const form = document.getElementById('user-form');
    const title = document.getElementById('user-modal-title');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password-confirm');
    const existingUsernameInput = document.getElementById('existing-username');
    const passwordHint = document.getElementById('password-hint');
    
    if (username) {
        // Edit mode
        title.textContent = 'Edit User';
        existingUsernameInput.value = username;
        usernameInput.value = username;
        passwordInput.required = false;
        passwordConfirmInput.required = false;
        if (passwordHint) passwordHint.style.display = 'block';
    } else {
        // New user mode
        title.textContent = 'New User';
        form.reset();
        existingUsernameInput.value = '';
        passwordInput.required = true;
        passwordConfirmInput.required = true;
        if (passwordHint) passwordHint.style.display = 'none';
    }
    
    modal.style.display = 'flex';
}

function closeUserModal() {
    const modal = document.getElementById('user-modal');
    modal.style.display = 'none';
    document.getElementById('user-form').reset();
}

function editUser(username) {
    openUserModal(username);
}

function deleteUser(username) {
    dialog.confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`, 'Delete User')
        .then(confirmed => {
            if (!confirmed) return;
            
            const formData = new FormData();
            formData.append('csrf_token', '<?php echo generateCsrfToken(); ?>');
            formData.append('username', username);
            
            fetch('<?php echo CMS_URL; ?>/panel/actions/user-delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    dialog.error(data.error || 'Failed to delete user', 'Error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dialog.error('An error occurred while deleting the user', 'Error');
            });
        });
}

function saveUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const password = formData.get('password');
    const passwordConfirm = formData.get('password_confirm');
    
    // Validate password confirmation
    if (password && password !== passwordConfirm) {
        dialog.error('Passwords do not match', 'Validation Error');
        return;
    }
    
    // Remove password_confirm from form data
    formData.delete('password_confirm');
    
    fetch('<?php echo CMS_URL; ?>/panel/actions/user-save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeUserModal();
            location.reload();
        } else {
            dialog.error(data.error || 'Failed to save user', 'Error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        dialog.error('An error occurred while saving the user', 'Error');
    });
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('user-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('cms-modal-backdrop')) {
        closeUserModal();
            }
        });
    }
});
</script>

<?php require_once PANEL_DIR . '/partials/footer.php'; ?>
