<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Simulate logged in user
$_SESSION['loggedin'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'Admin';

// Simulate GET request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'get_profile';

echo "=== Testing Profile Endpoint ===\n";
echo "Session User ID: " . $_SESSION['user_id'] . "\n";
echo "Action: " . $_GET['action'] . "\n\n";

include 'handler_users.php';
