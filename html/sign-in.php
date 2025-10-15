<?php
// Start session and include necessary files at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration and authentication functions
require_once __DIR__ . '/../includes/auth_functions.php';

// Set page title for the header
$page_title = 'Sign In | Zanvarsity';
$page_heading = 'Welcome Back!';

// Include header
include 'includes/about_header.php';

// Redirect if already logged in
redirect_if_logged_in();

// Display error messages if any
$error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty_fields':
            $error = 'Please fill in all fields';
            break;
        case 'invalid_email':
            $error = 'Invalid email format';
            break;
        case 'invalid_credentials':
            $error = 'Invalid email or password';
            break;
        case 'login_required':
            $error = 'Please log in to access that page';
            break;
    }
}
?>

<!-- Add this style block in the head -->
<style>
/* Modern Login Form Styling */
body {
    background: #f5f7fa;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
}

#page-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
}

.login-container {
    width: 100%;
    max-width: 450px;
    margin: 0 auto;
    padding: 0 20px;
}

.login-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin: -100px 0;
}

.login-header {
    background: linear-gradient(135deg, #004225 0%, #006633 100%);
    color: white;
    padding: 30px 20px;
    text-align: center;
}

.login-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.login-body {
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.form-control:focus {
    border-color: #004225;
    box-shadow: 0 0 0 0.2rem rgba(0, 66, 37, 0.25);
    outline: none;
}

.btn-login {
    background: linear-gradient(135deg, #004225 0%, #006633 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.forgot-password {
    color: #666;
    text-align: right;
    display: block;
    margin-top: 10px;
    font-size: 14px;
}

.forgot-password:hover {
    color: #004225;
    text-decoration: none;
}

.register-link {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

.register-link a {
    color: #004225;
    font-weight: 500;
}

.register-link a:hover {
    text-decoration: underline;
}

.alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .login-card {
        margin: 10px;
    }
    
    .login-header {
        padding: 20px 15px;
    }
    
    .login-body {
        padding: 20px;
    }
}
</style>

<!-- Main Content -->
<div id="page-content">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><?php echo $page_heading; ?></h2>
                
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form id="loginForm" method="post" action="/c/zanvarsity/login.php" onsubmit="return handleLogin(event)">
    <input type="hidden" name="csrf_token" value="<?php 
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        echo $_SESSION['csrf_token']; 
    ?>"> 
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-login">Sign In</button>
                    </div>
                    
                    <div class="register-link">
                        Don't have an account? <a href="register.php">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/about_footer.php'; ?>

<!-- JavaScript -->
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
function handleLogin(event) {
    // Basic client-side validation
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        showAlert('Please fill in all fields', 'danger');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('Please enter a valid email address', 'danger');
        return false;
    }
    
    // If all validations pass
    return true;
}

function showAlert(message, type = 'info') {
    // Remove any existing alerts
    const existingAlert = document.querySelector('.alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${message}`;
    
    // Add to DOM
    const loginBody = document.querySelector('.login-body');
    loginBody.insertBefore(alertDiv, loginBody.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
</body>
</html>