<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Fetch leave types
$stmt = $pdo->prepare("SELECT * FROM leave_types");
$stmt->execute();
$leave_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Leave Types</h1>
    <a href="add_leave_type.php?page=admin_leave_type" class="btn btn-primary mb-3">
        <i class="fas fa-plus-circle"></i> Add Leave Type
    </a>
    <a href="view_leave_requests.php?page=view_leave_requests" class="btn btn-secondary mb-3">
        <i class="fas fa-eye"></i> View Leave Requests
    </a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Leave Type</th>
                <th>Description</th>
                <th>Entitled Days</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leave_types as $type): ?>
            <tr>
                <td><?= htmlspecialchars($type['name']) ?></td>
                <td><?= htmlspecialchars($type['description']) ?></td>
                <td><?= htmlspecialchars($type['entitled_days']) ?></td>
                <td>
                    <a href="edit_leave_type.php?page=leave_management_edit&id=<?= $type['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete_leave_type.php?page=leave_management_delete&id=<?= $type['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this leave type?');">
                        <i class="fas fa-trash-alt"></i> Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>