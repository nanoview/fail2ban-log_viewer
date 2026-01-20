<?php
// Configuration file for Fail2Ban Log Viewer

// Base URL for assets
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

// Authentication password
$password = 'My_secret_mY'; // change this

// Log file path
$log_file = __DIR__ . '/logs/fail2ban.log';
