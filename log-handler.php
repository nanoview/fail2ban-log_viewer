<?php
// Log file operations handler

// Parse fail2ban log entry
function parse_fail2ban_log($line) {
    $log = array(
        'raw' => $line,
        'timestamp' => '',
        'jail' => '',
        'action' => '',
        'ip' => '',
        'extra' => ''
    );
    
    // Typical fail2ban log format:
    // 2024-01-15 10:30:45,123 fail2ban.actions [1234]: INFO [sshd] Ban 192.168.1.100
    // 2024-01-15 10:30:45,123 fail2ban.filter [1234]: INFO [sshd] Found 192.168.1.100
    
    // Extract timestamp
    if(preg_match('/^(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $line, $m)) {
        $log['timestamp'] = $m[1];
    }
    
    // Extract jail name
    if(preg_match('/\[([^\]]+)\]/', $line, $m)) {
        $log['jail'] = $m[1];
    }
    
    // Extract action (Ban, Unban, Found)
    if(preg_match('/(Ban|Unban|Found|Restore)\s+/', $line, $m)) {
        $log['action'] = $m[1];
    }
    
    // Extract IP address
    if(preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $line, $m)) {
        $log['ip'] = $m[1];
    }
    
    // Extract extra information (after IP)
    if(preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\s+(.+)$/', $line, $m)) {
        $log['extra'] = trim($m[1]);
    }
    
    return $log;
}

// Handle deletion request
if(isset($_GET['delete'])){
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    $index = intval($_GET['delete']);
    if(isset($lines[$index])){
        unset($lines[$index]);
        file_put_contents($log_file, implode(PHP_EOL, $lines) . PHP_EOL);
        header("Location: index.php?deleted=1");
        exit;
    }
}

// Handle export request
if(isset($_GET['export'])){
    $format = isset($_GET['format']) ? $_GET['format'] : 'csv';
    $raw_logs = file($log_file, FILE_IGNORE_NEW_LINES);
    $parsed_logs = array_map('parse_fail2ban_log', $raw_logs);
    
    if($format === 'json'){
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="fail2ban_logs_' . date('Y-m-d_H-i-s') . '.json"');
        echo json_encode($parsed_logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    } elseif($format === 'csv'){
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="fail2ban_logs_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, ['Timestamp', 'Jail', 'Action', 'IP Address', 'Details']);
        
        // CSV rows
        foreach($parsed_logs as $log){
            fputcsv($output, [
                $log['timestamp'],
                $log['jail'],
                $log['action'],
                $log['ip'],
                $log['extra']
            ]);
        }
        
        fclose($output);
        exit;
    } elseif($format === 'txt'){
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="fail2ban_logs_' . date('Y-m-d_H-i-s') . '.txt"');
        
        echo "FAIL2BAN LOG EXPORT\n";
        echo "Generated: " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("=", 100) . "\n\n";
        
        foreach($parsed_logs as $log){
            echo "[" . $log['timestamp'] . "] [" . $log['jail'] . "] " . $log['action'] . " " . $log['ip'];
            if(!empty($log['extra'])){
                echo " - " . $log['extra'];
            }
            echo "\n";
        }
        exit;
    }
}

// Read and parse log file
$raw_logs = file($log_file, FILE_IGNORE_NEW_LINES);
$logs = array_map('parse_fail2ban_log', $raw_logs);
