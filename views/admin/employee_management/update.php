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

$successMessage = '';
$errorMessage = '';

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE employees SET full_name = ?, position = ?, department = ?, phone = ?, email = ? WHERE id = ?");
    if ($stmt->execute([$fullName, $position, $department, $phone, $email, $id])) {
        $successMessage = "Employee record updated successfully!";
    } else {
        $errorMessage = "Error updating employee record.";
    }
}
?>

<div class="container mt-5 mb-5">
    <h1>Edit Employee Record</h1>

    <!-- Success or error message -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="update.php?page=employee_management_update&id=<?= $employee['id'] ?>">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($employee['full_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="position">Position</label>
            <input type="text" class="form-control" id="position" name="position" value="<?= htmlspecialchars($employee['position']) ?>">
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" class="form-control" id="department" name="department" value="<?= htmlspecialchars($employee['department']) ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($employee['phone']) ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($employee['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Update Employee</button>
    </form>
</div>
