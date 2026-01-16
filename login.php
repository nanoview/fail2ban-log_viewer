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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            padding: 50px 40px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 40px;
        }
        
        .login-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .login-header p {
            font-size: 14px;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #c3e6cb;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            text-align: center;
        }
        
        .icon {
            font-size: 40px;
        }
    </style>
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
