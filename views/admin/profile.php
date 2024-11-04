<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if user ID is set
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}
$userId = $_SESSION['user_id'];

// Debug: Check the user ID from session
error_log("User ID from session in profile view: " . $userId);

// Fetch employee details from the database
$stmt = $pdo->prepare("SELECT * FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee data was found
if (!$employee) {
    error_log("No employee found for User ID: " . htmlspecialchars($userId));
    die("Employee not found. Please check your user ID: " . htmlspecialchars($userId));
}
?>
<div class="container mt-5 mb-5">
    <h1>Your Profile</h1>
    <table class="table table-striped">
        <tr>
            <th>Full Name</th>
            <td><?= htmlspecialchars($employee['full_name']) ?></td>
        </tr>
        <tr>
            <th>Position</th>
            <td><?= htmlspecialchars($employee['position']) ?></td>
        </tr>
        <tr>
            <th>Department</th>
            <td><?= htmlspecialchars($employee['department']) ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= htmlspecialchars($employee['phone']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($employee['email']) ?></td>
        </tr>
    </table>
</div>
