<?php
// Start the session
session_start();

// Assuming user ID is stored in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

$userId = $_SESSION['user_id'];

// Fetch employee details from the database
$stmt = $pdo->prepare("SELECT * FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee data was found
if (!$employee) {
    die("Employee not found.");
}

// Load the corresponding view
require "../../../views/employee/profile.php"; 
