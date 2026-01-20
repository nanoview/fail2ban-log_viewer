<?php
// Load configuration
require_once 'config.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fail2Ban Log Viewer - Security Event Monitoring</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/index.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo">⚠️ Fail2Ban Log Viewer</div>
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="login.php" class="login-btn-nav">Login</a>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Security Event Monitoring Made Simple</h1>
            <p>Monitor and analyze Fail2Ban security events in real-time with an intuitive, professional dashboard.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary">Get Started</a>
                <a href="#features" class="btn btn-secondary">Learn More</a>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-title">
            <h2>Powerful Features</h2>
            <p>Everything you need to manage and monitor security events</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Real-Time Monitoring</h3>
                <p>Monitor security events as they happen with our live dashboard displaying all ban, unban, and detection activities.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Detailed Log Analysis</h3>
                <p>View comprehensive information about each security event including IP addresses, jails, actions, and timestamps.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📥</div>
                <h3>Multi-Format Export</h3>
                <p>Export your security logs in multiple formats including CSV, JSON, and TXT for analysis and reporting.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Secure Access</h3>
                <p>Password-protected dashboard ensures that only authorized administrators can view sensitive security information.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Fast & Responsive</h3>
                <p>Optimized performance with a clean, intuitive interface that works seamlessly on desktop and mobile devices.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Easy Management</h3>
                <p>Manage log entries with simple delete options and view all details in an organized, easy-to-read table format.</p>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-content">
            <div class="stat-item">
                <h3>100%</h3>
                <p>Uptime Monitoring</p>
            </div>
            <div class="stat-item">
                <h3>Real-Time</h3>
                <p>Event Tracking</p>
            </div>
            <div class="stat-item">
                <h3>Secure</h3>
                <p>Password Protected</p>
            </div>
            <div class="stat-item">
                <h3>Easy</h3>
                <p>to Use</p>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Ready to Monitor Your Security?</h2>
            <p>Start tracking Fail2Ban events and secure your system today with our comprehensive log viewer.</p>
            <a href="login.php" class="btn btn-primary">Login to Dashboard</a>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2026 Fail2Ban Log Viewer. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
