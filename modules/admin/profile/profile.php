<?php
// Start the session
session_start();

// Check if user ID and role are set
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

$userId = $_SESSION['user_id'];

// Debug: Check the user ID from session
error_log("User ID from session: " . $userId);

// Fetch employee details from the database
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
$stmt = $pdo->prepare("SELECT * FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee data was found
if (!$employee) {
    error_log("No employee found for User ID: " . htmlspecialchars($userId));
    die("Employee not found. Please check your user ID: " . htmlspecialchars($userId));
}

// Load the corresponding view
require "../../../views/admin/profile.php"; 
