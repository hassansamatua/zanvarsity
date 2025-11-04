<?php
// Check if user has permission
if (!($is_admin || $is_dean)) {
    echo '<div class="alert alert-danger">You do not have permission to access this section.</div>';
    return;
}

// Process form submission for staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Add CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    $action = $_POST['action'];
    
    if ($action === 'add_staff' || $action === 'update_staff') {
        $name = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $id = $_POST['id'] ?? 0;
        
        // Basic validation
        if (empty($name) || empty($position) || empty($department)) {
            $_SESSION['error'] = 'Name, position, and department are required';
        } else {
            try {
                if ($action === 'add_staff') {
                    // Handle image upload
                    $image_path = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/staff/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $file_name = 'staff_' . time() . '.' . $file_extension;
                        $target_file = $upload_dir . $file_name;
                        
                        // Check if image file is an actual image
                        $check = getimagesize($_FILES['image']['tmp_name']);
                        if ($check === false) {
                            throw new Exception('File is not an image.');
                        }
                        
                        // Check file size (max 2MB)
                        if ($_FILES['image']['size'] > 2000000) {
                            throw new Exception('Sorry, your file is too large. Maximum size is 2MB.');
                        }
                        
                        // Allow certain file formats
                        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                        if (!in_array(strtolower($file_extension), $allowed_types)) {
                            throw new Exception('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
                        }
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                            $image_path = 'uploads/staff/' . $file_name;
                        } else {
                            throw new Exception('Failed to upload image');
                        }
                    } else {
                        throw new Exception('Please select a profile image');
                    }
                    
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO staff (name, position, department, email, phone, bio, image_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssssi", $name, $position, $department, $email, $phone, $bio, $image_path, $user_id);
                    $stmt->execute();
                    $_SESSION['success'] = 'Staff member added successfully';
                } else {
                    // Update existing staff
                    $update_sql = "UPDATE staff SET name = ?, position = ?, department = ?, email = ?, phone = ?, bio = ?";
                    $params = [$name, $position, $department, $email, $phone, $bio];
                    $types = "ssssss";
                    
                    // Handle image update if new image is uploaded
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/staff/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $file_name = 'staff_' . time() . '.' . $file_extension;
                        $target_file = $upload_dir . $file_name;
                        
                        // Check if image file is an actual image
                        $check = getimagesize($_FILES['image']['tmp_name']);
                        if ($check === false) {
                            throw new Exception('File is not an image.');
                        }
                        
                        // Check file size (max 2MB)
                        if ($_FILES['image']['size'] > 2000000) {
                            throw new Exception('Sorry, your file is too large. Maximum size is 2MB.');
                        }
                        
                        // Allow certain file formats
                        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                        if (!in_array(strtolower($file_extension), $allowed_types)) {
                            throw new Exception('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
                        }
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                            $image_path = 'uploads/staff/' . $file_name;
                            $update_sql .= ", image_path = ?";
                            $params[] = $image_path;
                            $types .= "s";
                            
                            // Delete old image if exists
                            $old_image = $conn->query("SELECT image_path FROM staff WHERE id = $id")->fetch_assoc()['image_path'] ?? '';
                            if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
                                unlink(__DIR__ . '/../' . $old_image);
                            }
                        } else {
                            throw new Exception('Failed to upload image');
                        }
                    }
                    
                    $update_sql .= " WHERE id = ?";
                    $params[] = $id;
                    $types .= "i";
                    
                    $stmt = $conn->prepare($update_sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $_SESSION['success'] = 'Staff member updated successfully';
                }
                
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete_staff') {
        $id = $_POST['id'] ?? 0;
        
        try {
            // Get image path before deleting
            $image_path = $conn->query("SELECT image_path FROM staff WHERE id = $id")->fetch_assoc()['image_path'] ?? '';
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Delete image if exists
            if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                unlink(__DIR__ . '/../' . $image_path);
            }
            
            $_SESSION['success'] = 'Staff member deleted successfully';
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting staff member: ' . $e->getMessage();
        }
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Fetch departments
$departments = [];
$result = $conn->query("SELECT DISTINCT department FROM staff WHERE department IS NOT NULL AND department != '' ORDER BY department");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['department'];
    }
}

// Handle department filter
$department_filter = $_GET['department'] ?? '';
$where_clause = $department_filter ? "WHERE department = '" . $conn->real_escape_string($department_filter) . "'" : '';

// Fetch staff members
$staff_members = [];
$result = $conn->query("SELECT * FROM staff $where_clause ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $staff_members[] = $row;
    }
}
?>

<div class="manage-staff-section">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class='bx bxs-user-detail me-2'></i>Staff Management</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staffModal">
                <i class='bx bx-plus'></i> Add Staff Member
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Department Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-center">
                <input type="hidden" name="tab" value="manage-staff">
                <div class="col-md-4">
                    <label for="department" class="form-label">Filter by Department:</label>
                    <select name="department" id="department" class="form-select" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $department_filter === $dept ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($department_filter): ?>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="?tab=manage-staff" class="btn btn-outline-secondary">Clear Filter</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Staff Grid -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($staff_members)): ?>
                <div class="text-center py-5">
                    <i class='bx bx-user-x' style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                    <h4>No staff members found</h4>
                    <p>Get started by adding your first staff member.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($staff_members as $staff): ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="<?php echo !empty($staff['image_path']) ? htmlspecialchars($staff['image_path']) : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNTAgMjUwIj48c3R5bGU+LmJhY2tncm91bmQge2ZpbGw6I2VlZTt9PC9zdHlsZT48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBjbGFzcz0iYmFja2dyb3VuZCIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM5OTkiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='; ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($staff['name']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($staff['name']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($staff['position']); ?></h6>
                                    <p class="card-text text-muted">
                                        <i class='bx bx-building-house'></i> <?php echo htmlspecialchars($staff['department']); ?><br>
                                        <?php if (!empty($staff['email'])): ?>
                                            <i class='bx bx-envelope'></i> <?php echo htmlspecialchars($staff['email']); ?><br>
                                        <?php endif; ?>
                                        <?php if (!empty($staff['phone'])): ?>
                                            <i class='bx bx-phone'></i> <?php echo htmlspecialchars($staff['phone']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if (!empty($staff['bio'])): ?>
                                        <p class="card-text">
                                            <?php echo nl2br(htmlspecialchars(substr($staff['bio'], 0, 100) . (strlen($staff['bio']) > 100 ? '...' : ''))); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="editStaff(<?php echo htmlspecialchars(json_encode($staff)); ?>)">
                                            <i class='bx bx-edit'></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $staff['id']; ?>, '<?php echo addslashes($staff['name']); ?>')">
                                            <i class='bx bx-trash'></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Staff Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="staffForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="staffAction" value="add_staff">
                <input type="hidden" name="id" id="staffId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="staffModalLabel">Add New Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3 text-center">
                                <div class="mb-3">
                                    <img id="imagePreview" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNTAgMjUwIiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZWVlO2JvcmRlcjoxcHggc29saWQgI2RkZDtib3JkZXItcmFkaXVzOjRweDsiPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZhaWxlZD0iIzk5OSI+UHJvZmlsZSBJbWFnZTwvdGV4dD48L3N2Zz4=" 
                                         class="img-thumbnail mb-2" 
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Profile Image <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text">Recommended size: 400x400px, max 2MB</div>
                                    <div id="currentImage" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="position" name="position" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="department" name="department" list="departments" required>
                                        <datalist id="departments">
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo htmlspecialchars($dept); ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"></textarea>
                            </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteItemName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    <input type="hidden" name="action" value="delete_staff">
                    <input type="hidden" name="id" id="deleteItemId">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Function to handle edit staff
function editStaff(staff) {
    const modal = new bootstrap.Modal(document.getElementById('staffModal'));
    const form = document.getElementById('staffForm');
    
    // Set form action and title
    document.getElementById('staffModalLabel').textContent = 'Edit Staff Member';
    document.getElementById('staffAction').value = 'update_staff';
    document.getElementById('staffId').value = staff.id;
    
    // Fill the form
    document.getElementById('name').value = staff.name || '';
    document.getElementById('position').value = staff.position || '';
    document.getElementById('department').value = staff.department || '';
    document.getElementById('email').value = staff.email || '';
    document.getElementById('phone').value = staff.phone || '';
    document.getElementById('bio').value = staff.bio || '';
    
    // Handle image preview
    const imagePreview = document.getElementById('imagePreview');
    const currentImage = document.getElementById('currentImage');
    
    if (staff.image_path) {
        imagePreview.src = staff.image_path;
        currentImage.innerHTML = `
            <div class="alert alert-info p-2">
                <i class='bx bx-image'></i> Current image will be kept if no new image is selected.
            </div>
        `;
    } else {
        imagePreview.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNTAgMjUwIiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZWVlO2JvcmRlcjoxcHggc29saWQgI2RkZDtib3JkZXItcmFkaXVzOjRweDsiPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZhaWxlZD0iIzk5OSI+UHJvZmlsZSBJbWFnZTwvdGV4dD48L3N2Zz4=';
        currentImage.innerHTML = '';
    }
    
    // Show the modal
    modal.show();
}

// Function to confirm delete
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteItemId').value = id;
    
    // Update form action
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '?tab=manage-staff';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Preview image before upload
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.objectFit = 'cover';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Reset form when modal is closed
document.getElementById('staffModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('staffForm');
    form.reset();
    document.getElementById('currentImage').innerHTML = '';
    document.getElementById('staffModalLabel').textContent = 'Add New Staff Member';
    document.getElementById('staffAction').value = 'add_staff';
    document.getElementById('imagePreview').src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNTAgMjUwIiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZWVlO2JvcmRlcjoxcHggc29saWQgI2RkZDtib3JkZXItcmFkaXVzOjRweDsiPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZhaWxlZD0iIzk5OSI+UHJvZmlsZSBJbWFnZTwvdGV4dD48L3N2Zz4=';
    document.getElementById('imagePreview').style.objectFit = 'contain';
});

// Form validation
document.getElementById('staffForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const position = document.getElementById('position').value.trim();
    const department = document.getElementById('department').value.trim();
    const imageInput = document.getElementById('image');
    const action = document.getElementById('staffAction').value;
    
    if (!name || !position || !department) {
        e.preventDefault();
        alert('Please fill in all required fields');
        return false;
    }
    
    // For new staff, image is required
    if (action === 'add_staff' && imageInput.files.length === 0) {
        e.preventDefault();
        alert('Please select a profile image');
        return false;
    }
    
    // If we got here, form is valid
    return true;
});
</script>

<style>
/* Custom styles for staff management */
.manage-staff-section {
    padding: 20px 0;
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom: 20px;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.card-subtitle {
    font-size: 0.9rem;
    color: #7f8c8d !important;
}

.card-text {
    font-size: 0.9rem;
    color: #555;
}

.card-footer {
    background-color: rgba(0, 0, 0, 0.03);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.bx {
    vertical-align: middle;
    margin-right: 3px;
}

/* Image preview styling */
#imagePreview {
    border: 2px dashed #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: border-color 0.3s;
}

#imagePreview:hover {
    border-color: #999;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row-cols-md-2 > * {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .modal-body .row > [class*="col-"] {
        margin-bottom: 1rem;
    }
    
    .modal-body .row > [class*="col-"]:last-child {
        margin-bottom: 0;
    }
}

/* Custom scrollbar for modal body */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Custom scrollbar for WebKit browsers */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
