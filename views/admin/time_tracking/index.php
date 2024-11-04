<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

// Fetch attendance records from the database
$stmt = $pdo->prepare("
    SELECT a.*, e.full_name 
    FROM attendance a 
    JOIN employees e ON a.employee_id = e.id 
    ORDER BY a.check_in_time DESC
");
$stmt->execute();
$attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container">
    <h1>Attendance Records</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Check-in Time</th>
                <th>Check-out Time</th>
                <th>Check-in Location</th>
                <th>Check-out Location</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceRecords as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['full_name']) ?></td>
                <td><?= htmlspecialchars($record['check_in_time']) ?></td>
                <td><?= htmlspecialchars($record['check_out_time']) ?></td>
                <td><?= htmlspecialchars($record['check_in_latitude'] . ', ' . $record['check_in_longitude']) ?></td>
                <td><?= htmlspecialchars($record['check_out_latitude'] . ', ' . $record['check_out_longitude']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
