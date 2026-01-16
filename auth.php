<?php
// Authentication handler with improved security

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Handle logout
if(isset($_GET['logout']) && $_GET['logout'] === '1'){
    session_destroy();
    header('Location: login.php?logged_out=1');
    exit;
}

// Redirect to login if not authenticated
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header('Location: login.php');
    exit;
}

// Function to check authentication
function is_authenticated(){
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to get logout URL
function get_logout_url(){
    return 'index.php?logout=1';
}
