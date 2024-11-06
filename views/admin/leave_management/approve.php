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

    // Get the leave request details
    $stmt = $pdo->prepare("SELECT user_id, leave_type_id, start_date, end_date FROM leave_requests WHERE id = ?");
    $stmt->execute([$id]);
    $leaveRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($leaveRequest) {
        // Calculate the number of days for the leave request
        $startDate = new DateTime($leaveRequest['start_date']);
        $endDate = new DateTime($leaveRequest['end_date']);
        $usedDays = $startDate->diff($endDate)->days + 1; // Include the start day

        // Update the leave request status to approved and set used days
        $stmt = $pdo->prepare("
            UPDATE leave_requests 
            SET status = 'approved', used_days = ? 
            WHERE id = ?
        ");
        $stmt->execute([$usedDays, $id]);

        // Update the total used days for the specific employee and leave type
        $stmt = $pdo->prepare("
            INSERT INTO employee_leave_balances (user_id, leave_type_id, used_days) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE used_days = used_days + ?
        ");
        $stmt->execute([$leaveRequest['user_id'], $leaveRequest['leave_type_id'], $usedDays, $usedDays]);
    }

    // Redirect to view_leave_requests.php or the page displaying leave requests
    header("Location: view_leave_requests.php"); 
    exit;
}
?>
