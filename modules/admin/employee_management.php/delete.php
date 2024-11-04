<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Check if the ID is set in the query string
if (!isset($_GET['id'])) {
    die("ID not specified.");
}

// Delete the employee record
$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
$stmt->execute([$id]);

// Redirect after successful deletion
header('Location: index.php?page=employee_management');
exit;
?>
