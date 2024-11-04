<?php
// Start the session
session_start();

// Assuming user ID is stored in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

// Assuming user ID is stored in session
$employeeId = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle check-in or check-out
    $action = $_POST['action']; // 'check_in' or 'check_out'
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    if ($action === 'check_in') {
        // Insert check-in record
        $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, check_in_time, check_in_latitude, check_in_longitude) VALUES (?, NOW(), ?, ?)");
        $stmt->execute([$employeeId, $latitude, $longitude]);
    } elseif ($action === 'check_out') {
        // Update check-out record
        $stmt = $pdo->prepare("UPDATE attendance SET check_out_time = NOW(), check_out_latitude = ?, check_out_longitude = ? WHERE employee_id = ? AND check_out_time IS NULL");
        $stmt->execute([$latitude, $longitude, $employeeId]);
    }
}

// Load the corresponding view
require "../../../views/employee/time_tracking/index.php";
