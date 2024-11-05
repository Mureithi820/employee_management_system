<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Update the leave request status to rejected
    $stmt = $pdo->prepare("UPDATE leave_requests SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: view_leave_requests.php"); // Redirect back to leave requests
    exit;
}
