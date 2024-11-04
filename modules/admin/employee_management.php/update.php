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

// Fetch the employee record
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("Employee not found.");
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE employees SET full_name = ?, position = ?, department = ?, phone = ?, email = ? WHERE id = ?");
    $stmt->execute([$fullName, $position, $department, $phone, $email, $id]);

    // Redirect after successful update
    header('Location: index.php?page=employee_management');
    exit;
}

// Load the corresponding view
require "../../../views/admin/employee_management/update.php";

