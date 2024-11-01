<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already active
}
// Check if user is logged in and is an employee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: /views/user/login.php"); // Redirect to login if not authorized
    exit();
}


// Set the content variable to the dashboard content
$content = 'views/employee/dashboard_content.php'; 
