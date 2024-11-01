<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already active
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /views/user/login.php"); // Redirect to login if not authorized
    exit();
}

// Set the content variable to the dashboard content
$content = 'views/admin/dashboard_content.php'; 
