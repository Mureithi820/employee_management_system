<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM leave_requests WHERE id = ?");
    $stmt->execute([$deleteId]);
    // Redirect after deletion
    header('Location: index.php?page=leave_requests'); // Adjust the redirect as needed
    exit;
}

// Fetch leave requests
$stmt = $pdo->prepare("SELECT lr.*, e.full_name AS employee_full_name, lt.name AS leave_type_name
FROM leave_requests lr
JOIN employees e ON lr.user_id = e.id
JOIN leave_types lt ON lr.leave_type_id = lt.id");
$stmt->execute();
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Leave Requests</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leave_requests as $request): ?>
            <tr>
                <td><?= htmlspecialchars($request['employee_full_name']) ?></td>
                <td><?= htmlspecialchars($request['leave_type_name']) ?></td>
                <td><?= htmlspecialchars($request['start_date']) ?></td>
                <td><?= htmlspecialchars($request['end_date']) ?></td>
                <td><?= htmlspecialchars($request['status']) ?></td>
                <td>
                    <?php if ($request['status'] === 'pending'): ?>
                        <a href="approve.php?page=approve_leave_request&id=<?= $request['id'] ?>" class="btn btn-success">Approve</a>
                        <a href="reject.php?page=reject_leave_request&id=<?= $request['id'] ?>" class="btn btn-danger">Reject</a>
                    <?php elseif ($request['status'] === 'approved'): ?>
                        <span class="text-success">Approved</span>
                    <?php elseif ($request['status'] === 'rejected'): ?>
                        <span class="text-danger">Rejected</span>
                    <?php endif; ?>
                    <a href="delete_leave_request.php?page=leave_request_management_delete&id=<?= $request['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this leave type?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
