<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Fetch the employee record to get the actual user ID
$tempUserId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
$stmt->execute([$tempUserId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee exists and get the user ID
if ($employee) {
    $userId = $employee['id']; // Use the ID from the employees table
} else {
    die("Access denied. User not found.");
}

// Check if the ID is set in the query string
if (!isset($_GET['id'])) {
    die("ID not specified.");
}

// Fetch the leave type record
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM leave_types WHERE id = ?");
$stmt->execute([$id]);
$leave_type = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leave_type) {
    die("Leave type not found.");
}

$successMessage = '';
$errorMessage = '';

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description']; // New variable for description
    $entitledDays = $_POST['entitled_days'];

    // Update the leave type
    $stmt = $pdo->prepare("UPDATE leave_types SET name = ?, description = ?, entitled_days = ? WHERE id = ?");
    if ($stmt->execute([$name, $description, $entitledDays, $id])) {
        // Show success message after successful update
        $successMessage = "Leave type updated successfully!";
    } else {
        // Show error message if the update failed
        $errorMessage = "Error updating leave type.";
    }
}
?>

<div class="container mt-5 mb-5">
    <h1>Edit Leave Type</h1>

    <!-- Success or error message -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="edit_leave_type.php?page=leave_management_edit&id=<?= $leave_type['id'] ?>">
        <div class="form-group">
            <label for="name">Leave Type Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($leave_type['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($leave_type['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="entitled_days">Entitled Days</label>
            <input type="number" class="form-control" id="entitled_days" name="entitled_days" value="<?= htmlspecialchars($leave_type['entitled_days']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Update Leave Type</button>
    </form>
</div>
