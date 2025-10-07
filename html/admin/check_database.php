<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once dirname(dirname(__DIR__)) . '/includes/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /zanvarsity/html/403.php");
    exit();
}

// Set page title
$page_title = 'Database Check';

// Include header
include('includes/header.php');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Database Connection Check</h4>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        // Test database connection
                        $conn->ping();
                        echo '<div class="alert alert-success">✅ Database connection successful!</div>';
                        
                        // Check if database exists
                        $db_selected = $conn->select_db(DB_NAME);
                        if (!$db_selected) {
                            // Create database if it doesn't exist
                            $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                            if ($conn->query($sql) === TRUE) {
                                echo '<div class="alert alert-success">✅ Database "' . DB_NAME . '" created successfully</div>';
                                $conn->select_db(DB_NAME);
                            } else {
                                throw new Exception("Error creating database: " . $conn->error);
                            }
                        } else {
                            echo '<div class="alert alert-info">ℹ️ Database "' . DB_NAME . '" already exists</div>';
                        }
                        
                        // Check if background_info table exists
                        $result = $conn->query("SHOW TABLES LIKE 'background_info'");
                        if ($result->num_rows == 0) {
                            // Table doesn't exist, create it
                            $sql = "CREATE TABLE IF NOT EXISTS background_info (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                backgroundinfo LONGTEXT,
                                ownership_accredition LONGTEXT,
                                establishment_faculties LONGTEXT,
                                university_membership LONGTEXT,
                                memoranda_understanding LONGTEXT,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                is_active BOOLEAN DEFAULT 1
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                            
                            if ($conn->query($sql) === TRUE) {
                                echo '<div class="alert alert-success">✅ Table "background_info" created successfully</div>';
                                
                                // Insert default data
                                $sql = "INSERT INTO background_info (
                                    backgroundinfo, 
                                    ownership_accredition, 
                                    establishment_faculties, 
                                    university_membership, 
                                    memoranda_understanding
                                ) VALUES (
                                    'The Zanzibar University, the first University on the Isles, is a private institution sponsored by Darul Iman Charitable Association (DICA). The main campus is situated at Tunguu area, in the Central District, some 19 kilometers from Zanzibar Town.',
                                    'The Zanzibar University was founded and is owned and governed by Darul-Iman Charitable Association. It was established on the basis of the following:\n\n• The Constitution of Darul-Iman registered under the Society\'s Act No. 6, 1995 given at Zanzibar on 2nd August, 1996.\n• A letter of Interim Authority issued by the then Higher Education Accreditation Council bearing Ref. No. HEAC/SU of 1st May, 1998.\n• The Certificate of Provisional Registration No. 007 of 22nd December, 1999.\n• The Certificate of Full Registration No. 003 of 4th May, 2000.\n• The provisions of the Universities Act, 2005.\n• The Zanzibar University Charter, 2010 issued on 24th March, 2010 by the President of the United Republic of Tanzania, H.E. Dr. Jakaya Mrisho Kikwete.',
                                    '1998\n\nThe proliferation of business enterprises, Hotels, Beach resorts, and the gradual expansion of the tourism industry in the country, had convinced the development partners to begin first with a Faculty of Business Administration, with the view to satisfy the immediate needs of the business community. Five more faculties have been established as per the market demand for other professions.\n\n1999\n\nThe Faculty of Law and Shariah was established.\n\n2002\n\nThe Faculty of Arts and Social Sciences was established. Within seven or so years that followed however more but quite modern structures with larger classrooms were erected to accommodate bigger student\'s intakes.\n\n2008/2009\n\nThe Institute of Continuing Education and the Institute of Postgraduate Studies and Research were established.\n\n2012/2013\n\nThe Faculty of Engineering was established.\n\n2015/2016\n\nThe Faculty of Science was established.',
                                    'Zanzibar University is a member of various national and international academic associations and professional bodies. The university maintains active memberships in organizations that promote higher education, research, and academic excellence in the region and beyond.',
                                    'Zanzibar University has established Memoranda of Understanding with various national and international institutions to enhance academic collaboration, research, and student exchange programs. These partnerships help in sharing knowledge, resources, and best practices in higher education.'
                                )";
                                
                                if ($conn->query($sql) === TRUE) {
                                    echo '<div class="alert alert-success">✅ Default data inserted into "background_info" table</div>';
                                } else {
                                    echo '<div class="alert alert-warning">⚠️ Could not insert default data: ' . $conn->error . '</div>';
                                }
                            } else {
                                throw new Exception("Error creating table: " . $conn->error);
                            }
                        } else {
                            echo '<div class="alert alert-info">ℹ️ Table "background_info" already exists</div>';
                            
                            // Check if we need to update the table structure
                            $result = $conn->query("SHOW COLUMNS FROM background_info LIKE 'backgroundinfo'");
                            if ($result->num_rows == 0) {
                                // Table exists but doesn't have the new columns, alter it
                                $sql = "ALTER TABLE background_info 
                                        ADD COLUMN backgroundinfo LONGTEXT AFTER id,
                                        ADD COLUMN ownership_accredition LONGTEXT AFTER backgroundinfo,
                                        ADD COLUMN establishment_faculties LONGTEXT AFTER ownership_accredition,
                                        ADD COLUMN university_membership LONGTEXT AFTER establishment_faculties,
                                        ADD COLUMN memoranda_understanding LONGTEXT AFTER university_membership";
                                
                                if ($conn->query($sql) === TRUE) {
                                    echo '<div class="alert alert-success">✅ Table "background_info" updated with new columns</div>';
                                } else {
                                    echo '<div class="alert alert-warning">⚠️ Could not update table structure: ' . $conn->error . '</div>';
                                }
                            }
                        }
                        
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                    
                    <div class="mt-4">
                        <a href="manage_content.php" class="btn btn-primary">
                            <i class='bx bx-arrow-back me-1'></i> Back to Dashboard
                        </a>
                        <a href="manage_background.php" class="btn btn-success">
                            <i class='bx bx-edit me-1'></i> Manage Background Information
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include('includes/footer.php');
?>
