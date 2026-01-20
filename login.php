<?php
// Login page
require_once 'config.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$error_message = '';
$success_message = '';

// Check if user is already logged in
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
    header('Location: dashboard.php');
    exit;
}

// Check if logged out
if(isset($_GET['logged_out']) && $_GET['logged_out'] === '1'){
    $success_message = 'You have been logged out successfully.';
}

// Handle login form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['password']) && !empty($_POST['password'])){
        if($_POST['password'] === $password){
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'Invalid password. Please try again.';
        }
    } else {
        $error_message = 'Please enter a password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Fail2Ban Log Viewer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><span class="icon">⚠️</span> Fail2Ban Log Viewer</h1>
            <p>Security Event Monitoring Dashboard</p>
        </div>
        
        <?php if(!empty($error_message)): ?>
        <div class="error-message">
            <span>✕</span>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($success_message)): ?>
        <div class="success-message">
            <span>✓</span>
            <span><?php echo htmlspecialchars($success_message); ?></span>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password"
                    name="password" 
                    placeholder="Enter your password" 
                    required
                    autofocus
                >
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <div class="info-text">
            <p>🔐 Please enter your password to access the dashboard</p>
        </div>
    </div>
</body>
</html>
