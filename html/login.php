<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clean up any existing session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

// Set session parameters
$session_name = 'zanvarsity_session';
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$httponly = true;
$samesite = 'Lax';

// Ensure cookies are only sent over HTTPS in production
if ($secure) {
    ini_set('session.cookie_secure', 1);
}

// Prevent JavaScript access to session cookie
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', $samesite);

// Set session garbage collection
ini_set('session.gc_maxlifetime', 86400); // 1 day
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => $samesite
]);

// Set session name
session_name($session_name);

// Set custom session name
session_name($session_name);

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => $samesite
]);

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID to prevent session fixation
if (!isset($_SESSION['CREATED'])) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// Debug: Log session status
error_log('Session started. ID: ' . session_id() . ', Name: ' . session_name());

// Include database connection
$db_path = __DIR__ . '/includes/db.php';
if (!file_exists($db_path)) {
    die('Database configuration file not found at: ' . $db_path);
}
require_once $db_path;

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    // Simple validation
    if (empty($_POST['email']) || empty($_POST['password'])) {
        header('Location: /c/zanvarsity/html/login.php?error=empty_fields');
        exit();
    }
    
    // Basic CSRF protection (simplified for debugging)
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log('CSRF token validation failed');
        header('Location: /c/zanvarsity/html/login.php?error=invalid_token');
        exit();
    }

    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        header('Location: /c/zanvarsity/html/login.php?error=empty');
        exit();
    }

    try {
        // Prepare and execute query
        $stmt = $conn->prepare('SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                try {
                    // Debug: Log before setting session
                    error_log('Setting session variables for user ID: ' . $user['id']);
                    
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Clear any existing session data to prevent conflicts
                    $_SESSION = array();
                    
                    // Clear any existing session data
                    $_SESSION = array();
                    
                    // Set session creation time
                    $_SESSION['CREATED'] = time();
                    $_SESSION['LAST_ACTIVITY'] = time();
                    
                    // Set user session variables
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
                    $_SESSION['email'] = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                    $_SESSION['last_activity'] = time();
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    
                    // Debug: Log session data
                    error_log('Session data set: ' . print_r($_SESSION, true));
                    
                    // Verify session variables were set
                    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
                        error_log('Session variables not set correctly: ' . print_r($_SESSION, true));
                        throw new Exception('Failed to set session variables');
                    }
                    
                    // Regenerate session ID for security
                    error_log('Regenerating session ID');
                    if (!session_regenerate_id(true)) {
                        throw new Exception('Failed to regenerate session ID');
                    }
                    
                    // Debug: Log successful session creation
                    error_log('Session created successfully for user ID: ' . $user['id']);
                    
                    // Set default redirect URL
                    $redirectUrl = '/c/zanvarsity/html/my-account.php';
                    
                    // Check for redirect in this order of priority:
                    // 1. POST parameter (from hidden form field)
                    // 2. GET parameter (from URL)
                    // 3. Session variable
                    
                    $sources = [
                        'POST' => $_POST['redirect'] ?? null,
                        'GET' => $_GET['redirect'] ?? null,
                        'SESSION' => $_SESSION['redirect_after_login'] ?? null
                    ];
                    
                    foreach ($sources as $source => $url) {
                        if (!empty($url)) {
                            $url = filter_var($url, FILTER_SANITIZE_URL);
                            // Only allow relative URLs for security
                            if (strpos($url, '/') === 0) {
                                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $url;
                                // Verify the target page exists and is accessible
                                if (file_exists($fullPath)) {
                                    $redirectUrl = $url;
                                    error_log("Using redirect URL from $source: " . $redirectUrl);
                                    // Clear the session redirect after using it
                                    if ($source === 'SESSION') {
                                        unset($_SESSION['redirect_after_login']);
                                    }
                                    break;
                                } else {
                                    error_log("Requested redirect page not found (source: $source): " . $fullPath);
                                }
                            }
                        }
                    }
                    // Check for any output before header
                    if (headers_sent($filename, $linenum)) {
                        error_log("Headers already sent in $filename on line $linenum");
                        throw new Exception('Output started before header() call in ' . $filename . ' on line ' . $linenum);
                    }
                    
                    // Final debug before redirect
                    error_log('Initiating redirect to: ' . $redirectUrl);
                    
                    // Set a debug cookie
                    setcookie('debug_login', 'success', [
                        'expires' => time() + 60,
                        'path' => '/',
                        'domain' => $_SERVER['HTTP_HOST'],
                        'secure' => isset($_SERVER['HTTPS']),
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                    
                    // Debug: Log session data
                    error_log('Final session data: ' . print_r($_SESSION, true));
                    
                    // Ensure no output before header
                    if (headers_sent($file, $line)) {
                        throw new Exception("Headers already sent in $file on line $line");
                    }
                    
                    // Determine redirect URL
                    $redirect_url = 'my-account.php';
                    
                    // Check for return URL in query string
                    if (!empty($_GET['redirect'])) {
                        $redirect_url = $_GET['redirect'];
                    }
                    // Clear any stored redirect URL to prevent future redirects
                    unset($_SESSION['redirect_after_login']);
                    
                    // Ensure the redirect URL is absolute
                    if (strpos($redirectUrl, 'http') !== 0) {
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                        $host = $_SERVER['HTTP_HOST'];
                        $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                        $redirectUrl = rtrim($protocol . $host . $basePath, '/') . '/' . ltrim($redirectUrl, '/');
                    } elseif (isset($_SESSION['redirect_after_login'])) {
                        $redirect_url = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                    }
                    
                    // Ensure the redirect URL is safe
                    $redirect_url = filter_var($redirect_url, FILTER_SANITIZE_URL);
                    
                    // Prevent open redirects by ensuring the URL is relative
                    if (strpos($redirect_url, 'http') === 0) {
                        $redirect_url = 'my-account.php';
                    }
                    
                    // Debug: Log redirect URL
                    error_log('Preparing to redirect to: ' . $redirect_url);
                    
                    // Clear output buffer
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    
                    // Set headers
                    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: no-cache');
                    header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
                    
                    // Perform the redirect
                    header('Location: ' . $redirect_url, true, 302);
                    exit();
                    
                } catch (Exception $e) {
                    error_log('Session error during login: ' . $e->getMessage());
                    throw new Exception('Error processing your login. Please try again.');
                }
            } else {
                error_log('Login failed: Invalid password for email: ' . $email);
            }
        } else {
            error_log('Login failed: No user found with email: ' . $email);
        }
        
        // If we get here, login failed
        header('Location: /c/zanvarsity/html/login.php?error=invalid');
        exit();
        
    } catch (Exception $e) {
        error_log('Login system error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        header('Location: /c/zanvarsity/html/login.php?error=server&msg=' . urlencode($e->getMessage()));
        exit();
    }
}

// Set page title for the header
$pageTitle = 'Login';
$bodyClass = 'page-register-sign-in';

// Set base URL for assets
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_path = trim(dirname($_SERVER['PHP_SELF']), '/\\');

// Construct base URL without double slashes
$base_url = "$protocol://$host";
if (!empty($base_path)) {
    $base_url .= '/' . $base_path;
}

// Ensure base_url ends with a slash
$base_url = rtrim($base_url, '/') . '/';

// Debug: Log the constructed URL
error_log('Constructed Base URL: ' . $base_url);

// Store base URL in session for header
$_SESSION['base_url'] = $base_url;

// Debug: Log the base URL
error_log('Base URL set to: ' . $base_url);

// Check if user is already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: my-account.php');
    exit();
}

// Set default redirect URL after login
if (!isset($_GET['error'])) {
    if (isset($_GET['redirect'])) {
        // Sanitize and store the redirect URL from the query string
        $redirect = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
        // Only allow relative URLs for security
        if (strpos($redirect, '/') === 0) {
            $_SESSION['redirect_after_login'] = $redirect;
        }
    } elseif (!isset($_SESSION['redirect_after_login'])) {
        $_SESSION['redirect_after_login'] = 'my-account.php';
    }
}

// Include the header
include 'new-header.php';
?>

<!-- Page Content -->
<div id="page-content">
    <div class="container" style="padding-top: 15px;">
        <div class="row">
            <!-- Breadcrumb and Sign In Header -->
            <div class="col-md-12" style="margin-bottom: 1px; padding-bottom: 2px; border-bottom: 1px solid #eee;">
                <div class="row">
                    <div class="col-md-6">
                        <ol class="breadcrumb" style="margin: 0; padding: 0; background: none;">
                            <li><a href="index.php">Home</a></li>
                            <li class="active">Login</li>
                        </ol>
                    </div>
                   
                </div>
                   <!-- Main Content -->
                <div class="col-md-6 col-md-offset-3" style="padding-top: 20px;">
                     <div class="col-md-6 text-center">
                        <h1 style="margin: 0; font-size: 24px; color: #333;">Sign In to Your Account</h1>
                    </div>
                <section class="account-block">
                    
                    <?php
                    // Display error message if login fails
                    if (isset($_GET['error'])) {
                        echo '<div class="alert alert-danger">';
                        switch ($_GET['error']) {
                            case 'invalid':
                                echo 'Invalid email or password.';
                                break;
                            case 'empty':
                                echo 'Please fill in all fields.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        echo '</div>';
                    }
                    ?>
                    
                    <?php
                    // Generate CSRF token if not exists
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }
                    ?>
                    <?php
                    // Preserve the redirect URL if it exists
                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'my-account.php');
                    ?>
                    <form action="" method="post" class="form-vertical">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="form-group">
                            <label for="email" class="control-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="control-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <p class="help-block">
                                <a href="forgot-password.php">Forgot your password?</a>
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> Remember me
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        
                        <div class="form-group text-center">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                    </form>
                </section>
            </div><!-- /.col-md
            </div>
            </div> /.row -->
        </div><!-- /.container -->
    </div><!-- /.container -->
    
</div><!-- /#page-content -->
<?php
    // Include the new footer
    include 'new-footer.php';
    ?>


