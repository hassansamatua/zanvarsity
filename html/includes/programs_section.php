<?php
// Check if user has permission
if (!($is_admin || $is_dean)) {
    echo '<div class="alert alert-danger">You do not have permission to access this section.</div>';
    return;
}

// Process form submission for programs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Add CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    $action = $_POST['action'];
    
    if ($action === 'add_program' || $action === 'update_program') {
        $title = trim($_POST['title'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $degree = trim($_POST['degree'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $id = $_POST['id'] ?? 0;
        
        // Basic validation
        if (empty($title) || empty($code) || empty($department) || empty($duration) || empty($degree)) {
            $_SESSION['error'] = 'All fields are required except description';
        } else {
            try {
                if ($action === 'add_program') {
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO programs (title, code, department, duration, degree, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssi", $title, $code, $department, $duration, $degree, $description, $user_id);
                    $stmt->execute();
                    $program_id = $conn->insert_id;
                    
                    // Handle brochure upload if provided
                    if (isset($_FILES['brochure']) && $_FILES['brochure']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/programs/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['brochure']['name'], PATHINFO_EXTENSION);
                        $file_name = 'program_' . $program_id . '_' . time() . '.' . $file_extension;
                        $target_file = $upload_dir . $file_name;
                        
                        // Check if file is a PDF
                        $file_type = mime_content_type($_FILES['brochure']['tmp_name']);
                        if ($file_type !== 'application/pdf') {
                            throw new Exception('Only PDF files are allowed for brochures.');
                        }
                        
                        // Check file size (max 5MB)
                        if ($_FILES['brochure']['size'] > 5000000) {
                            throw new Exception('Sorry, your file is too large. Maximum size is 5MB.');
                        }
                        
                        if (move_uploaded_file($_FILES['brochure']['tmp_name'], $target_file)) {
                            $brochure_path = 'uploads/programs/' . $file_name;
                            $conn->query("UPDATE programs SET brochure_path = '$brochure_path' WHERE id = $program_id");
                        } else {
                            throw new Exception('Failed to upload brochure');
                        }
                    }
                    
                    $_SESSION['success'] = 'Program added successfully';
                } else {
                    // Update existing program
                    $update_sql = "UPDATE programs SET title = ?, code = ?, department = ?, duration = ?, degree = ?, description = ?";
                    $params = [$title, $code, $department, $duration, $degree, $description];
                    $types = "ssssss";
                    
                    // Handle brochure update if new file is uploaded
                    if (isset($_FILES['brochure']) && $_FILES['brochure']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/programs/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['brochure']['name'], PATHINFO_EXTENSION);
                        $file_name = 'program_' . $id . '_' . time() . '.' . $file_extension;
                        $target_file = $upload_dir . $file_name;
                        
                        // Check if file is a PDF
                        $file_type = mime_content_type($_FILES['brochure']['tmp_name']);
                        if ($file_type !== 'application/pdf') {
                            throw new Exception('Only PDF files are allowed for brochures.');
                        }
                        
                        // Check file size (max 5MB)
                        if ($_FILES['brochure']['size'] > 5000000) {
                            throw new Exception('Sorry, your file is too large. Maximum size is 5MB.');
                        }
                        
                        if (move_uploaded_file($_FILES['brochure']['tmp_name'], $target_file)) {
                            $brochure_path = 'uploads/programs/' . $file_name;
                            $update_sql .= ", brochure_path = ?";
                            $params[] = $brochure_path;
                            $types .= "s";
                            
                            // Delete old brochure if exists
                            $old_brochure = $conn->query("SELECT brochure_path FROM programs WHERE id = $id")->fetch_assoc()['brochure_path'] ?? '';
                            if ($old_brochure && file_exists(__DIR__ . '/../' . $old_brochure)) {
                                unlink(__DIR__ . '/../' . $old_brochure);
                            }
                        } else {
                            throw new Exception('Failed to upload brochure');
                        }
                    }
                    
                    $update_sql .= " WHERE id = ?";
                    $params[] = $id;
                    $types .= "i";
                    
                    $stmt = $conn->prepare($update_sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    
                    $_SESSION['success'] = 'Program updated successfully';
                }
                
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete_program') {
        $id = $_POST['id'] ?? 0;
        
        try {
            // Get brochure path before deleting
            $brochure_path = $conn->query("SELECT brochure_path FROM programs WHERE id = $id")->fetch_assoc()['brochure_path'] ?? '';
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM programs WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Delete brochure if exists
            if ($brochure_path && file_exists(__DIR__ . '/../' . $brochure_path)) {
                unlink(__DIR__ . '/../' . $brochure_path);
            }
            
            $_SESSION['success'] = 'Program deleted successfully';
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting program: ' . $e->getMessage();
        }
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Fetch departments
$departments = [];
$result = $conn->query("SELECT DISTINCT department FROM programs WHERE department IS NOT NULL AND department != '' ORDER BY department");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['department'];
    }
}

// Fetch degree types
$degree_types = [
    'Certificate',
    'Diploma',
    'Associate Degree',
    'Bachelor\'s Degree',
    'Master\'s Degree',
    'Doctorate',
    'Professional Certificate',
    'Other'
];

// Handle department filter
$department_filter = $_GET['department'] ?? '';
$where_clause = $department_filter ? "WHERE department = '" . $conn->real_escape_string($department_filter) . "'" : '';

// Fetch programs
$programs = [];
$result = $conn->query("SELECT * FROM programs $where_clause ORDER BY title ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
}
?>

<div class="manage-programs-section">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class='bx bxs-graduation me-2'></i>Programs & Courses</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#programModal">
                <i class='bx bx-plus'></i> Add Program
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
                <input type="hidden" name="tab" value="manage-programs">
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
                        <a href="?tab=manage-programs" class="btn btn-outline-secondary">Clear Filter</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Programs List -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($programs)): ?>
                <div class="text-center py-5">
                    <i class='bx bx-book-open' style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                    <h4>No programs found</h4>
                    <p>Get started by adding your first program.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Degree</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($programs as $program): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($program['code']); ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <i class='bx bx-book text-primary'></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($program['title']); ?></h6>
                                                <?php if (!empty($program['description'])): ?>
                                                    <small class="text-muted">
                                                        <?php echo nl2br(htmlspecialchars(substr($program['description'], 0, 50) . (strlen($program['description']) > 50 ? '...' : ''))); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($program['department']); ?></td>
                                    <td><?php echo htmlspecialchars($program['degree']); ?></td>
                                    <td><?php echo htmlspecialchars($program['duration']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="viewProgram(<?php echo htmlspecialchars(json_encode($program)); ?>)">
                                                <i class='bx bx-show'></i> View
                                            </button>
                                            <button class="btn btn-outline-warning" onclick="editProgram(<?php echo htmlspecialchars(json_encode($program)); ?>)">
                                                <i class='bx bx-edit'></i> Edit
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="confirmDelete(<?php echo $program['id']; ?>, '<?php echo addslashes($program['title']); ?>')">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Program Details Modal -->
<div class="modal fade" id="viewProgramModal" tabindex="-1" aria-labelledby="viewProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewProgramModalLabel">Program Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h3 id="viewProgramTitle" class="mb-1"></h3>
                        <p class="text-muted mb-2">
                            <i class='bx bx-code-alt'></i> <span id="viewProgramCode"></span>
                            <span class="mx-2">•</span>
                            <i class='bx bx-buildings'></i> <span id="viewProgramDepartment"></span>
                        </p>
                        <p class="mb-3">
                            <span class="badge bg-primary" id="viewProgramDegree"></span>
                            <span class="ms-2"><i class='bx bx-time'></i> <span id="viewProgramDuration"></span></span>
                        </p>
                        <div class="mb-3">
                            <h6>Description:</h6>
                            <p id="viewProgramDescription" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <i class='bx bxs-graduation display-4 text-primary mb-3'></i>
                                <h5>Program Brochure</h5>
                                <p class="text-muted">Download the program brochure for more details</p>
                                <a href="#" id="downloadBrochure" class="btn btn-primary mt-auto" download>
                                    <i class='bx bx-download'></i> Download Brochure
                                </a>
                                <small class="text-muted mt-2" id="brochureSize"></small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional program details can be added here -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class='bx bx-list-ul me-2'></i>Curriculum</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-0">Curriculum details will be displayed here.</p>
                                <!-- You can add a dynamic curriculum list here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class='bx bx-calendar me-2'></i>Important Dates</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-0">Important dates will be displayed here.</p>
                                <!-- You can add important dates here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editProgramBtn">
                    <i class='bx bx-edit'></i> Edit Program
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Program Modal -->
<div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="programForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="programAction" value="add_program">
                <input type="hidden" name="id" id="programId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="programModalLabel">Add New Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Program Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Program Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" required>
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
                                <label for="degree" class="form-label">Degree/Certificate <span class="text-danger">*</span></label>
                                <select class="form-select" id="degree" name="degree" required>
                                    <option value="">Select Degree</option>
                                    <?php foreach ($degree_types as $type): ?>
                                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 4 years, 2 semesters" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brochure" class="form-label">Program Brochure (PDF) <span class="text-danger" id="brochureRequired">*</span></label>
                                <input class="form-control" type="file" id="brochure" name="brochure" accept=".pdf">
                                <div id="currentBrochure" class="mt-2"></div>
                                <div class="form-text">Maximum file size: 5MB</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Program Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Program</button>
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
                    <input type="hidden" name="action" value="delete_program">
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
// Function to view program details
function viewProgram(program) {
    const modal = new bootstrap.Modal(document.getElementById('viewProgramModal'));
    
    // Fill the modal with program data
    document.getElementById('viewProgramTitle').textContent = program.title;
    document.getElementById('viewProgramCode').textContent = program.code;
    document.getElementById('viewProgramDepartment').textContent = program.department;
    document.getElementById('viewProgramDegree').textContent = program.degree;
    document.getElementById('viewProgramDuration').textContent = program.duration;
    document.getElementById('viewProgramDescription').textContent = program.description || 'No description available.';
    
    // Handle brochure download
    const downloadBtn = document.getElementById('downloadBrochure');
    const brochureSize = document.getElementById('brochureSize');
    
    if (program.brochure_path) {
        downloadBtn.href = program.brochure_path;
        downloadBtn.classList.remove('disabled');
        
        // Get file size
        fetch(program.brochure_path, { method: 'HEAD' })
            .then(response => {
                const size = response.headers.get('content-length');
                if (size) {
                    const sizeInMB = (size / (1024 * 1024)).toFixed(2);
                    brochureSize.textContent = `PDF, ${sizeInMB} MB`;
                }
            })
            .catch(() => {
                brochureSize.textContent = 'PDF';
            });
    } else {
        downloadBtn.href = '#';
        downloadBtn.classList.add('disabled');
        downloadBtn.setAttribute('onclick', 'return false;');
        brochureSize.textContent = 'No brochure available';
    }
    
    // Set up edit button
    document.getElementById('editProgramBtn').onclick = function() {
        modal.hide();
        editProgram(program);
    };
    
    // Show the modal
    modal.show();
}

// Function to handle edit program
function editProgram(program) {
    const modal = new bootstrap.Modal(document.getElementById('programModal'));
    const form = document.getElementById('programForm');
    
    // Set form action and title
    document.getElementById('programModalLabel').textContent = 'Edit Program';
    document.getElementById('programAction').value = 'update_program';
    document.getElementById('programId').value = program.id;
    
    // Fill the form
    document.getElementById('title').value = program.title || '';
    document.getElementById('code').value = program.code || '';
    document.getElementById('department').value = program.department || '';
    document.getElementById('degree').value = program.degree || '';
    document.getElementById('duration').value = program.duration || '';
    document.getElementById('description').value = program.description || '';
    
    // Handle brochure input
    const brochureInput = document.getElementById('brochure');
    const brochureRequired = document.getElementById('brochureRequired');
    const currentBrochure = document.getElementById('currentBrochure');
    
    if (program.brochure_path) {
        const fileName = program.brochure_path.split('/').pop();
        currentBrochure.innerHTML = `
            <div class="alert alert-info p-2">
                <i class='bx bx-file'></i> Current: 
                <a href="${program.brochure_path}" target="_blank">${fileName}</a>
                <br>
                <small>Upload a new file only if you want to replace it.</small>
            </div>
        `;
        brochureRequired.textContent = ''; // Make brochure not required for updates
    } else {
        currentBrochure.innerHTML = '';
        brochureRequired.textContent = '*';
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
    deleteForm.action = '?tab=manage-programs';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Reset form when modal is closed
document.getElementById('programModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('programForm');
    form.reset();
    document.getElementById('currentBrochure').innerHTML = '';
    document.getElementById('programModalLabel').textContent = 'Add New Program';
    document.getElementById('programAction').value = 'add_program';
    document.getElementById('brochureRequired').textContent = '*';
});

// Form validation
document.getElementById('programForm').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const code = document.getElementById('code').value.trim();
    const department = document.getElementById('department').value.trim();
    const degree = document.getElementById('degree').value.trim();
    const duration = document.getElementById('duration').value.trim();
    const brochureInput = document.getElementById('brochure');
    const action = document.getElementById('programAction').value;
    
    if (!title || !code || !department || !degree || !duration) {
        e.preventDefault();
        alert('Please fill in all required fields');
        return false;
    }
    
    // For new programs, brochure is required
    if (action === 'add_program' && brochureInput.files.length === 0) {
        e.preventDefault();
        alert('Please upload a program brochure (PDF)');
        return false;
    }
    
    // If a file is selected, validate it
    if (brochureInput.files.length > 0) {
        const file = brochureInput.files[0];
        const fileSize = file.size / 1024 / 1024; // in MB
        
        if (file.type !== 'application/pdf') {
            e.preventDefault();
            alert('Only PDF files are allowed for brochures');
            return false;
        }
        
        if (fileSize > 5) {
            e.preventDefault();
            alert('File size must be less than 5MB');
            return false;
        }
    }
    
    // If we got here, form is valid
    return true;
});
</script>

<style>
/* Custom styles for programs management */
.manage-programs-section {
    padding: 20px 0;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

/* Card styles for program details */
.program-card {
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom: 20px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    height: 100%;
}

.program-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.program-card .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.program-card .card-text {
    flex-grow: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        flex: 1 0 auto;
        min-width: 70px;
    }
    
    .table-responsive {
        border: 0;
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

/* Animation for view modal */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

#viewProgramModal .modal-content {
    animation: fadeIn 0.3s ease-out;
}
</style>
