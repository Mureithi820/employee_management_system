<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

// Fetch attendance records from the database
$stmt = $pdo->prepare("
    SELECT a.*, e.full_name 
    FROM attendance a 
    JOIN employees e ON a.employee_id = e.id 
    ORDER BY a.check_in_time DESC
");
$stmt->execute();
$attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load the corresponding view
require "../../../views/admin/time_tracking/index.php";

