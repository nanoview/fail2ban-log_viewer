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
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header-top {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logout-btn {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding: 6px 12px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            background-color: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .timestamp {
            color: #6c757d;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        
        .jail-badge {
            display: inline-block;
            background-color: #e7f3ff;
            color: #0066cc;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }
        
        .action-ban {
            display: inline-block;
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .action-unban {
            display: inline-block;
            background-color: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .action-found {
            display: inline-block;
            background-color: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .action-restore {
            display: inline-block;
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .ip-address {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #e74c3c;
        }
        
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
        }
        
        .no-logs {
            text-align: center;
            color: #6c757d;
            padding: 40px;
        }
        
        .log-count {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .export-btn {
            background-color: #27ae60;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
        }
        
        .export-btn:hover {
            background-color: #229954;
        }
        
        .export-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 150px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 4px;
            z-index: 1;
            top: 100%;
            left: 0;
            margin-top: 5px;
        }
        
        .dropdown-menu a {
            color: #333;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            transition: background-color 0.2s;
        }
        
        .dropdown-menu a:last-child {
            border-bottom: none;
        }
        
        .dropdown-menu a:hover {
            background-color: #f8f9fa;
        }
        
        .export-dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
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
            <div class="export-dropdown">
                <button class="export-btn">📥 Export Logs</button>
                <div class="dropdown-menu">
                    <a href="?export=1&format=csv">📄 Export as CSV</a>
                    <a href="?export=1&format=json">📋 Export as JSON</a>
                    <a href="?export=1&format=txt">📝 Export as TXT</a>
                </div>
            </div>
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
                    <td><span class="ip-address"><?php echo htmlspecialchars($log['ip']); ?></span></td>
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
