<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}
// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Fetch employees from the database
$stmt = $pdo->prepare("SELECT * FROM employees"); // Adjust table name if necessary
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container">
    <h1>Employee Management</h1>
    <a href="create.php?page=employee_management_create" class="btn btn-success">Create New Employee</a>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Position</th>
                <th>Department</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($employees)): ?>
                <tr>
                    <td colspan="7" class="text-center">No employees found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?= htmlspecialchars($employee['id']) ?></td>
                    <td><?= htmlspecialchars($employee['full_name']) ?></td>
                    <td><?= htmlspecialchars($employee['position']) ?></td>
                    <td><?= htmlspecialchars($employee['department']) ?></td>
                    <td><?= htmlspecialchars($employee['phone']) ?></td>
                    <td><?= htmlspecialchars($employee['email']) ?></td>
                    <td>
                        <a href="update.php?page=employee_management_update&id=<?= $employee['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        
                        <a href="delete.php?page=employee_management_delete&id=<?= $employee['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                    </td>

                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

