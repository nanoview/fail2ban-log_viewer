<?php
// Load configuration and dependencies
require_once 'config.php';
require_once 'auth.php';
require_once 'log-handler.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Fail2Ban Log Viewer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/dashboard.css">
</head>
<body>
<div class="container">
    <div class="header-top">
        <div>
            🔐 Logged in as <strong>Administrator</strong>
        </div>
        <div class="user-info">
            <a href="dashboard.php?logout=1" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    <div class="header">
        <h1>⚠️ Fail2Ban Log Viewer</h1>
        <p>Security Event Monitoring Dashboard</p>
    </div>
    
    <div class="content">
        <?php if(isset($_GET['deleted'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            ✓ Log entry deleted successfully
        </div>
        <?php endif; ?>
        
        <div class="log-count">
            📋 Showing <strong><?php echo count($logs); ?></strong> log entries
        </div>
        
        <div class="toolbar">
            <a href="?export=1&format=logs" class="export-btn">📄 Export Logs (CSV)</a>
            <a href="?export=1&format=ip_report" class="export-btn">🚨 IP Ban Report (CSV)</a>
        </div>
        <?php if(empty($logs)): ?>
            <div class="no-logs">
                <p>No logs found. Check your log file path.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 160px;">Timestamp</th>
                    <th style="width: 100px;">Jail</th>
                    <th style="width: 80px;">Action</th>
                    <th style="width: 130px;">IP Address</th>
                    <th style="width: 150px;">Location</th>
                    <th>Details</th>
                    <th style="width: 80px;">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $i => $log): ?>
                <tr>
                    <td class="timestamp"><?php echo htmlspecialchars($log['timestamp']); ?></td>
                    <td><span class="jail-badge"><?php echo htmlspecialchars($log['jail']); ?></span></td>
                    <td>
                        <?php 
                        $action = strtolower($log['action']);
                        $action_class = 'action-' . $action;
                        ?>
                        <span class="<?php echo $action_class; ?>"><?php echo htmlspecialchars($log['action']); ?></span>
                    </td>
                    <td><span class="ip-address"><a href="https://www.abuseipdb.com/check/<?php echo htmlspecialchars($log['ip']); ?>" target="_blank"><?php echo htmlspecialchars($log['ip']); ?></a></span></td>
                    <td><span class="location"><?php echo htmlspecialchars($log['location']); ?></span></td>
                    <td><?php echo htmlspecialchars($log['extra']); ?></td>
                    <td>
                        <a class="delete-btn" href="?delete=<?php echo $i; ?>">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
