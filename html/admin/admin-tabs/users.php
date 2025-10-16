<?php
// Check if this is a POST request for user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (isset($_POST['user_id'])) {
                    $user_id = intval($_POST['user_id']);
                    // Prevent deleting yourself
                    if ($user_id != $_SESSION['user_id']) {
                        $sql = "DELETE FROM users WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                        if ($stmt->execute()) {
                            $_SESSION['success'] = "User deleted successfully!";
                        } else {
                            $_SESSION['error'] = "Error deleting user: " . $conn->error;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['error'] = "You cannot delete your own account!";
                    }
                }
                break;
                
            case 'update_role':
                if (isset($_POST['user_id'], $_POST['new_role'])) {
                    $user_id = intval($_POST['user_id']);
                    $new_role = $_POST['new_role'];
                    
                    // Only allow valid roles
                    $valid_roles = ['student', 'lecturer', 'admin'];
                    if (in_array($new_role, $valid_roles)) {
                        $sql = "UPDATE users SET role = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("si", $new_role, $user_id);
                        if ($stmt->execute()) {
                            $_SESSION['success'] = "User role updated successfully!";
                        } else {
                            $_SESSION['error'] = "Error updating user role: " . $conn->error;
                        }
                        $stmt->close();
                    }
                }
                break;
        }
        header("Location: ?tab=users");
        exit();
    }
}
?>

<div class="admin-users">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa fa-users-cog"></i> Manage Users</h2>
        <a href="?tab=users&action=add" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New User
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, first_name, last_name, email, role, is_active FROM users ORDER BY created_at DESC";
                        $result = $conn->query($sql);
                        
                        if ($result && $result->num_rows > 0) {
                            while ($user = $result->fetch_assoc()) {
                                $full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                $email = htmlspecialchars($user['email']);
                                $status = $user['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                                $is_current_user = ($user['id'] == $_SESSION['user_id']);
                                ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td><?php echo $full_name; ?></td>
                                    <td><?php echo $email; ?></td>
                                    <td>
                                        <?php if ($is_current_user): ?>
                                            <?php echo ucfirst($user['role']); ?>
                                        <?php else: ?>
                                            <form method="post" class="d-inline" onchange="this.submit()">
                                                <input type="hidden" name="action" value="update_role">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <select name="new_role" class="form-select form-select-sm" style="width: auto;">
                                                    <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                                                    <option value="lecturer" <?php echo $user['role'] === 'lecturer' ? 'selected' : ''; ?>>Lecturer</option>
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $status; ?></td>
                                    <td>
                                        <a href="?tab=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <?php if (!$is_current_user): ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center">No users found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="user_id" id="userId">
                    
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="isActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize the user modal
var userModal = new bootstrap.Modal(document.getElementById('userModal'));

// Handle add user button click
document.querySelector('.btn-primary[href*="action=add"]')?.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('userModalLabel').textContent = 'Add New User';
    document.getElementById('formAction').value = 'add';
    document.getElementById('userForm').reset();
    userModal.show();
});

// Handle edit user button clicks
document.querySelectorAll('.btn-info').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (this.getAttribute('href').includes('action=edit')) {
            e.preventDefault();
            // In a real implementation, you would fetch the user data via AJAX
            // and populate the form before showing the modal
            document.getElementById('userModalLabel').textContent = 'Edit User';
            document.getElementById('formAction').value = 'edit';
            // Set user ID from the URL or data attribute
            const userId = new URLSearchParams(this.getAttribute('href').split('?')[1]).get('id');
            document.getElementById('userId').value = userId;
            userModal.show();
        }
    });
});
</script>
