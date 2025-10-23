<?php
// Start session and include necessary files at the very top
if (session_status() === PHP_SESSION_NONE) {
    // Session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    session_name('zanvarsity_session');
    session_start();
}

// Include configuration and authentication functions
require_once __DIR__ . '/../includes/auth_functions.php';

// Set page title for the header
$page_title = 'Login or Register | Zanvarsity';
$page_heading = 'Welcome Back!';

// Check if user is already logged in
if (is_logged_in()) {
    header('Location: /c/zanvarsity/html/my-account.php');
    exit();
}

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
        case 'invalid_csrf':
            $error = 'Invalid security token. Please try again.';
            break;
    }
}

// Include header after setting all variables
include 'includes/about_header.php';
?>

<div class="page-title">
    <div class="container">
        <h1>Login or Register</h1>
    </div>
</div>

<div id="page-content">
    <div class="container">
        <div class="row">
            <!-- Login Form -->
            <div class="col-md-6">
                <div class="login-card">
                    <div class="login-header">
                        <h2>Login to Your Account</h2>
                        <p>Enter your credentials to access your dashboard</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form id="loginForm" method="post" action="/c/zanvarsity/login.php">
                            <?php echo csrf_token_field(); ?>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" 
                                       placeholder="Enter your email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                            </div>
                            
                            <div class="text-center">
                                <p>Or sign in with:</p>
                                <div class="social-login">
                                    <a href="#" class="btn btn-social btn-facebook">
                                        <i class="fa fa-facebook"></i> Facebook
                                    </a>
                                    <a href="#" class="btn btn-social btn-google">
                                        <i class="fa fa-google"></i> Google
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Registration Form -->
            <div class="col-md-6">
                <div class="register-card">
                    <div class="register-header">
                        <h2>Create an Account</h2>
                        <p>Join our community to get started</p>
                    </div>
                    
                    <div class="register-body">
                        <form id="registerForm" method="post" action="/c/zanvarsity/register.php">
                            <?php echo csrf_token_field(); ?>
                            <div class="form-group">
                                <label for="reg_first_name">First Name</label>
                                <input type="text" class="form-control" id="reg_first_name" name="first_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="reg_last_name">Last Name</label>
                                <input type="text" class="form-control" id="reg_last_name" name="last_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="reg_email">Email Address</label>
                                <input type="email" class="form-control" id="reg_email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="reg_password">Password</label>
                                <input type="password" class="form-control" id="reg_password" name="password" required>
                                <small class="form-text text-muted">Minimum 8 characters, at least one uppercase letter, one lowercase letter, one number and one special character</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="reg_confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="reg_confirm_password" name="confirm_password" required>
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-card, .register-card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 30px;
}

.login-header, .register-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h2, .register-header h2 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.login-header p, .register-header p {
    color: #7f8c8d;
    margin: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    height: 45px;
    border-radius: 3px;
    border: 1px solid #ddd;
    padding: 10px 15px;
}

.btn-primary {
    background-color: #3498db;
    border: none;
    padding: 12px 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

.forgot-password {
    display: block;
    text-align: right;
    font-size: 13px;
    margin-top: 5px;
    color: #7f8c8d;
}

.social-login {
    margin-top: 20px;
}

.btn-social {
    display: inline-block;
    padding: 8px 15px;
    margin: 0 5px;
    border-radius: 3px;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
}

.btn-facebook {
    background-color: #3b5998;
}

.btn-google {
    background-color: #dd4b39;
}

.btn-social i {
    margin-right: 5px;
}

.text-center {
    text-align: center;
}

/* Responsive styles */
@media (max-width: 768px) {
    .login-card, .register-card {
        padding: 20px;
    }
    
    .btn-social {
        display: block;
        margin: 10px 0;
        text-align: center;
    }
}
</style>

<?php 
// Include footer
include 'includes/about_footer.php'; 
?>
