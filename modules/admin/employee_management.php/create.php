<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Fetch users for the select dropdown
$stmt = $pdo->prepare("SELECT id, username FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $fullName = $_POST['full_name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $userId = $_POST['user_id']; 

    try {
        // Insert employee record
        $stmt = $pdo->prepare("INSERT INTO employees (user_id, full_name, position, department, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $fullName, $position, $department, $phone, $email]);

        // Redirect after successful creation
        header('Location: index.php?page=employee_management');
        exit;
    } catch (PDOException $e) {
        // Handle any errors during the insert
        echo "Error: " . $e->getMessage();
        exit;
    }
}

// Load the corresponding view
require "../../../views/admin/employee_management/create.php";
?>
