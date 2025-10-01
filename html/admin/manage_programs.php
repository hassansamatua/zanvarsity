<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth_functions.php';

// Check if user is logged in and has admin privileges
check_admin_auth();

// Database configuration
$db_host = DB_HOST;
$db_name = DB_NAME;
$db_user = DB_USER;
$db_pass = DB_PASS;

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$page_title = 'Manage Programs';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Add new program
                $stmt = $pdo->prepare("INSERT INTO programs (program_name, abbreviation, program_description, entry_requirements, vision, mission, program_objectives, study_level, duration_years) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['program_name'],
                    $_POST['abbreviation'],
                    $_POST['program_description'],
                    $_POST['entry_requirements'],
                    $_POST['vision'],
                    $_POST['mission'],
                    $_POST['program_objectives'],
                    $_POST['study_level'],
                    $_POST['duration_years']
                ]);
                $_SESSION['success'] = 'Program added successfully';
                break;
                
            case 'edit':
                // Update program
                $stmt = $pdo->prepare("UPDATE programs SET 
                    program_name = ?, 
                    abbreviation = ?, 
                    program_description = ?, 
                    entry_requirements = ?, 
                    vision = ?, 
                    mission = ?, 
                    program_objectives = ?,
                    study_level = ?,
                    duration_years = ?
                    WHERE id = ?");
                $stmt->execute([
                    $_POST['program_name'],
                    $_POST['abbreviation'],
                    $_POST['program_description'],
                    $_POST['entry_requirements'],
                    $_POST['vision'],
                    $_POST['mission'],
                    $_POST['program_objectives'],
                    $_POST['study_level'],
                    $_POST['duration_years'],
                    $_POST['program_id']
                ]);
                $_SESSION['success'] = 'Program updated successfully';
                break;
                
            case 'delete':
                // Delete program
                $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
                $stmt->execute([$_POST['program_id']]);
                $_SESSION['success'] = 'Program deleted successfully';
                break;
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$study_level = isset($_GET['study_level']) ? $_GET['study_level'] : '';

// Build query
$query = "SELECT * FROM programs WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (program_name LIKE ? OR abbreviation LIKE ? OR program_description LIKE ?)";
    $searchParam = "%$search%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
}

if (!empty($study_level)) {
    $query .= " AND study_level = ?";
    $params[] = $study_level;
}

$query .= " ORDER BY study_level, program_name";

// Get programs
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Zanvarsity Admin</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .program-card {
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .program-level {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .program-level-certificate { background-color: #3498db; color: white; }
        .program-level-diploma { background-color: #2ecc71; color: white; }
        .program-level-bachelor { background-color: #9b59b6; color: white; }
        .program-level-masters { background-color: #e74c3c; color: white; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $page_title; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProgramModal">
                            <i class="fas fa-plus"></i> Add New Program
                        </button>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" placeholder="Search programs..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-4">
                                <select name="study_level" class="form-select">
                                    <option value="">All Study Levels</option>
                                    <option value="Certificate" <?php echo $study_level === 'Certificate' ? 'selected' : ''; ?>>Certificate</option>
                                    <option value="Diploma" <?php echo $study_level === 'Diploma' ? 'selected' : ''; ?>>Diploma</option>
                                    <option value="Bachelor" <?php echo $study_level === 'Bachelor' ? 'selected' : ''; ?>>Bachelor</option>
                                    <option value="Masters" <?php echo $study_level === 'Masters' ? 'selected' : ''; ?>>Masters</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Programs List -->
                <div class="row">
                    <?php if (empty($programs)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">No programs found.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($programs as $program): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card program-card">
                                    <div class="card-body">
                                        <span class="program-level program-level-<?php echo strtolower($program['study_level']); ?>">
                                            <?php echo $program['study_level']; ?>
                                        </span>
                                        <h5 class="card-title"><?php echo htmlspecialchars($program['program_name']); ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($program['abbreviation']); ?></h6>
                                        <p class="card-text">
                                            <?php echo substr(strip_tags($program['program_description']), 0, 100); ?>...
                                        </p>
                                        <div class="d-flex justify-content-between">
                                            <a href="program.php?id=<?php echo $program['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary edit-program" 
                                                        data-id="<?php echo $program['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($program['program_name']); ?>"
                                                        data-abbr="<?php echo htmlspecialchars($program['abbreviation']); ?>"
                                                        data-desc="<?php echo htmlspecialchars($program['program_description']); ?>"
                                                        data-requirements="<?php echo htmlspecialchars($program['entry_requirements']); ?>"
                                                        data-vision="<?php echo htmlspecialchars($program['vision']); ?>"
                                                        data-mission="<?php echo htmlspecialchars($program['mission']); ?>"
                                                        data-objectives="<?php echo htmlspecialchars($program['program_objectives']); ?>"
                                                        data-level="<?php echo $program['study_level']; ?>"
                                                        data-duration="<?php echo $program['duration_years']; ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Program Modal -->
    <div class="modal fade" id="addProgramModal" tabindex="-1" aria-labelledby="addProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="programForm">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProgramModalLabel">Add New Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program_name" class="form-label">Program Name *</label>
                                    <input type="text" class="form-control" id="program_name" name="program_name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="abbreviation" class="form-label">Abbreviation *</label>
                                    <input type="text" class="form-control" id="abbreviation" name="abbreviation" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="study_level" class="form-label">Study Level *</label>
                                    <select class="form-select" id="study_level" name="study_level" required>
                                        <option value="">Select Level</option>
                                        <option value="Certificate">Certificate</option>
                                        <option value="Diploma">Diploma</option>
                                        <option value="Bachelor">Bachelor</option>
                                        <option value="Masters">Masters</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_years" class="form-label">Duration (Years) *</label>
                                    <input type="number" class="form-control" id="duration_years" name="duration_years" min="1" max="6" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="program_description" class="form-label">Program Description *</label>
                            <textarea class="form-control" id="program_description" name="program_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="entry_requirements" class="form-label">Entry Requirements *</label>
                            <textarea class="form-control" id="entry_requirements" name="entry_requirements" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="vision" class="form-label">Vision *</label>
                            <textarea class="form-control" id="vision" name="vision" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="mission" class="form-label">Mission *</label>
                            <textarea class="form-control" id="mission" name="mission" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="program_objectives" class="form-label">Program Objectives *</label>
                            <textarea class="form-control" id="program_objectives" name="program_objectives" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="editProgramForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="program_id" id="edit_program_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_program_name" class="form-label">Program Name *</label>
                                    <input type="text" class="form-control" id="edit_program_name" name="program_name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_abbreviation" class="form-label">Abbreviation *</label>
                                    <input type="text" class="form-control" id="edit_abbreviation" name="abbreviation" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_study_level" class="form-label">Study Level *</label>
                                    <select class="form-select" id="edit_study_level" name="study_level" required>
                                        <option value="Certificate">Certificate</option>
                                        <option value="Diploma">Diploma</option>
                                        <option value="Bachelor">Bachelor</option>
                                        <option value="Masters">Masters</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_duration_years" class="form-label">Duration (Years) *</label>
                                    <input type="number" class="form-control" id="edit_duration_years" name="duration_years" min="1" max="6" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_program_description" class="form-label">Program Description *</label>
                            <textarea class="form-control" id="edit_program_description" name="program_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_entry_requirements" class="form-label">Entry Requirements *</label>
                            <textarea class="form-control" id="edit_entry_requirements" name="entry_requirements" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_vision" class="form-label">Vision *</label>
                            <textarea class="form-control" id="edit_vision" name="vision" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_mission" class="form-label">Mission *</label>
                            <textarea class="form-control" id="edit_mission" name="mission" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_program_objectives" class="form-label">Program Objectives *</label>
                            <textarea class="form-control" id="edit_program_objectives" name="program_objectives" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Initialize edit program modal
        document.querySelectorAll('.edit-program').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const abbr = this.getAttribute('data-abbr');
                const desc = this.getAttribute('data-desc');
                const requirements = this.getAttribute('data-requirements');
                const vision = this.getAttribute('data-vision');
                const mission = this.getAttribute('data-mission');
                const objectives = this.getAttribute('data-objectives');
                const level = this.getAttribute('data-level');
                const duration = this.getAttribute('data-duration');
                
                document.getElementById('edit_program_id').value = id;
                document.getElementById('edit_program_name').value = name;
                document.getElementById('edit_abbreviation').value = abbr;
                document.getElementById('edit_program_description').value = desc;
                document.getElementById('edit_entry_requirements').value = requirements;
                document.getElementById('edit_vision').value = vision;
                document.getElementById('edit_mission').value = mission;
                document.getElementById('edit_program_objectives').value = objectives;
                document.getElementById('edit_study_level').value = level;
                document.getElementById('edit_duration_years').value = duration;
                
                // Initialize CKEditor for textareas if needed
                if (typeof CKEDITOR !== 'undefined') {
                    CKEDITOR.instances['edit_program_description']?.setData(desc);
                    CKEDITOR.instances['edit_entry_requirements']?.setData(requirements);
                    CKEDITOR.instances['edit_vision']?.setData(vision);
                    CKEDITOR.instances['edit_mission']?.setData(mission);
                    CKEDITOR.instances['edit_program_objectives']?.setData(objectives);
                }
                
                const modal = new bootstrap.Modal(document.getElementById('editProgramModal'));
                modal.show();
            });
        });
        
        // Initialize form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CKEditor if available
            if (typeof CKEDITOR !== 'undefined') {
                const textareas = [
                    'program_description', 'entry_requirements', 'vision', 'mission', 'program_objectives',
                    'edit_program_description', 'edit_entry_requirements', 'edit_vision', 'edit_mission', 'edit_program_objectives'
                ];
                
                textareas.forEach(id => {
                    if (document.getElementById(id)) {
                        CKEDITOR.replace(id, {
                            toolbar: [
                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
                                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                                { name: 'links', items: ['Link', 'Unlink'] },
                                { name: 'document', items: ['Source'] }
                            ],
                            removeButtons: 'Subscript,Superscript,Image,Table,HorizontalRule,SpecialChar,PageBreak,Iframe',
                            removePlugins: 'elementspath',
                            resize_enabled: false,
                            height: 150
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>
