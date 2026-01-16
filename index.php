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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #fff;
            color: #333;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .nav-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: #667eea;
        }
        
        .login-btn-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .login-btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 52px;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.5;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 14px 32px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }
        
        .features {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }
        
        .features-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .features-title h2 {
            font-size: 36px;
            margin-bottom: 12px;
            color: #333;
        }
        
        .features-title p {
            font-size: 16px;
            color: #666;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .feature-card:hover {
            background: white;
            border-color: #667eea;
            box-shadow: 0 10px 35px rgba(102, 126, 234, 0.15);
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #333;
        }
        
        .feature-card p {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 60px 20px;
            margin: 80px 0;
        }
        
        .stats-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }
        
        .stat-item h3 {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 8px;
            font-weight: 700;
        }
        
        .stat-item p {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        
        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .cta-section h2 {
            font-size: 36px;
            margin-bottom: 16px;
        }
        
        .cta-section p {
            font-size: 16px;
            margin-bottom: 32px;
            opacity: 0.95;
        }
        
        .footer {
            background: #2d3748;
            color: white;
            padding: 40px 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer p {
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .features-title h2 {
                font-size: 28px;
            }
            
            .nav-links {
                gap: 15px;
            }
        }
    </style>
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
