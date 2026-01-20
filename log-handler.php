<?php
// Log file operations handler

// Get IP location information
function get_ip_location($ip) {
    // Skip private/local IPs
    if(preg_match('/^(127\.|10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/', $ip)) {
        return 'Local/Private';
    }
    
    // Cache file for locations
    $cache_dir = __DIR__ . '/logs';
    if(!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);
    
    $cache_file = $cache_dir . '/.ip_cache.json';
    $cache = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true) : array();
    
    // Return cached result if available
    if(isset($cache[$ip])) {
        return $cache[$ip];
    }
    
    $location = 'Unknown';
    
    // Fetch from ipinfo.io API
    $url = 'https://ipinfo.io/' . $ip . '/json';
    $context = stream_context_create(array(
        'https' => array('timeout' => 5)
    ));
    
    $response = @file_get_contents($url, false, $context);
    
    if($response !== false) {
        $data = json_decode($response, true);
        
        if(isset($data['city']) || isset($data['country'])) {
            // Build location string: City, Region, Country
            $parts = array();
            if(isset($data['city']) && !empty($data['city'])) {
                $parts[] = $data['city'];
            }
            if(isset($data['region']) && !empty($data['region'])) {
                $parts[] = $data['region'];
            }
            if(isset($data['country']) && !empty($data['country'])) {
                $parts[] = $data['country'];
            }
            
            if(!empty($parts)) {
                $location = implode(', ', $parts);
            }
        }
    }
    
    // Cache the result
    $cache[$ip] = $location;
    @file_put_contents($cache_file, json_encode($cache, JSON_PRETTY_PRINT));
    
    return $location;
}

// Parse fail2ban log entry
function parse_fail2ban_log($line) {
    $log = array(
        'raw' => $line,
        'timestamp' => '',
        'jail' => '',
        'action' => '',
        'ip' => '',
        'location' => '',
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
        // Get location for this IP
        $log['location'] = get_ip_location($log['ip']);
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

// Map fail2ban actions to AbuseIPDB category IDs
function get_abuse_category($action, $jail) {
    // Category IDs: 18=Brute Force, 21=SSH, 15=Web App Attack, 22=SSH Brute Force
    $jail_lower = strtolower($jail);
    
    switch(strtolower($action)) {
        case 'ban':
            // SSH-related
            if(strpos($jail_lower, 'ssh') !== false || strpos($jail_lower, 'sshd') !== false) {
                return 22; // SSH Brute Force
            }
            // Web-related
            if(strpos($jail_lower, 'apache') !== false || strpos($jail_lower, 'nginx') !== false || strpos($jail_lower, 'http') !== false) {
                return 15; // Web Application Attack
            }
            return 18; // Default: Brute Force
        case 'found':
            return 18; // Brute Force
        case 'unban':
            return 14; // Port Scan (benign)
        default:
            return 18;
    }
}

// Format timestamp to ISO 8601
function format_iso8601($timestamp) {
    // Try to parse the timestamp
    $time = strtotime($timestamp);
    if($time === false) {
        $time = time();
    }
    return date('c', $time); // ISO 8601 format with timezone
}

// Handle export request
if(isset($_GET['export'])){
    $format = isset($_GET['format']) ? $_GET['format'] : 'logs';
    $raw_logs = file($log_file, FILE_IGNORE_NEW_LINES);
    $parsed_logs = array_map('parse_fail2ban_log', $raw_logs);
    
    if($format === 'logs'){
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="abuse_report_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header - AbuseIPDB format
        fputcsv($output, ['IP', 'Categories', 'ReportDate', 'Comment']);
        
        // CSV rows
        foreach($parsed_logs as $log){
            if(!empty($log['ip'])) {
                $category = get_abuse_category($log['action'], $log['jail']);
                $comment = $log['action'] . ' detected in ' . $log['jail'];
                if(!empty($log['extra'])) {
                    $comment .= ' - ' . $log['extra'];
                }
                if(!empty($log['location']) && $log['location'] !== 'Local/Private') {
                    $comment .= ' (' . $log['location'] . ')';
                }
                
                fputcsv($output, [
                    $log['ip'],
                    $category,
                    format_iso8601($log['timestamp']),
                    substr($comment, 0, 500) // Limit comment to 500 chars
                ]);
            }
        }
        
        fclose($output);
        exit;
    } elseif($format === 'ip_report'){
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="ip_ban_report_' . date('Y-m-d_H-i-s') . '.csv"');
        
        // Get unique banned IPs
        $banned_ips = array();
        $ip_details = array();
        
        foreach($parsed_logs as $log){
            if($log['action'] === 'Ban' && !empty($log['ip'])){
                if(!isset($banned_ips[$log['ip']])){
                    $banned_ips[$log['ip']] = 0;
                    $ip_details[$log['ip']] = array(
                        'location' => $log['location'],
                        'jail' => $log['jail'],
                        'first_seen' => $log['timestamp'],
                        'details' => array()
                    );
                }
                $banned_ips[$log['ip']]++;
                $ip_details[$log['ip']]['last_seen'] = $log['timestamp'];
                // Store event details for comment
                if(!empty($log['extra'])) {
                    $ip_details[$log['ip']]['details'][] = $log['extra'];
                }
            }
        }
        
        // Sort by ban count (descending)
        arsort($banned_ips);
        
        $output = fopen('php://output', 'w');
        
        // CSV header - AbuseIPDB format
        fputcsv($output, ['IP', 'Categories', 'ReportDate', 'Comment']);
        
        // CSV rows
        foreach($banned_ips as $ip => $count){
            $details = $ip_details[$ip];
            $categories = array();
            $jail_lower = strtolower($details['jail']);
            
            // Determine categories based on jail name
            if(strpos($jail_lower, 'ssh') !== false || strpos($jail_lower, 'sshd') !== false) {
                $categories = array(18, 22); // Brute Force + SSH
            } elseif(strpos($jail_lower, 'apache') !== false || strpos($jail_lower, 'nginx') !== false || strpos($jail_lower, 'http') !== false) {
                $categories = array(15, 18); // Web App + Brute Force
            } elseif(strpos($jail_lower, 'ftp') !== false) {
                $categories = array(18, 5); // Brute Force + FTP
            } elseif(strpos($jail_lower, 'mail') !== false || strpos($jail_lower, 'postfix') !== false) {
                $categories = array(18, 12); // Brute Force + Mail
            } else {
                $categories = array(18); // Default: Brute Force
            }
            
            $categories_str = implode(',', $categories);
            
            // Build detailed comment
            $comment_parts = array();
            $comment_parts[] = 'Fail2Ban jail: ' . $details['jail'];
            $comment_parts[] = 'Ban count: ' . $count . ' time(s)';
            
            if(!empty($details['location']) && $details['location'] !== 'Local/Private') {
                $comment_parts[] = 'Location: ' . $details['location'];
            }
            
            // Add sample event details (first few unique ones)
            if(!empty($details['details'])) {
                $unique_details = array_unique($details['details']);
                $sample_details = array_slice($unique_details, 0, 2);
                foreach($sample_details as $detail) {
                    $comment_parts[] = 'Event: ' . substr($detail, 0, 60);
                }
            }
            
            $comment_parts[] = 'First seen: ' . $details['first_seen'];
            $comment_parts[] = 'Last seen: ' . $details['last_seen'];
            
            $comment = implode('; ', $comment_parts);
            
            fputcsv($output, [
                $ip,
                $categories_str,
                format_iso8601($details['first_seen']),
                substr($comment, 0, 500) // Limit to 500 chars
            ]);
        }
        
        fclose($output);
        exit;
    }
}

// Read and parse log file
$raw_logs = file($log_file, FILE_IGNORE_NEW_LINES);
$logs = array_map('parse_fail2ban_log', $raw_logs);
