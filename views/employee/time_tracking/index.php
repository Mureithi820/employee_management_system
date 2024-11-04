<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Assuming user ID is stored in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

$userId = $_SESSION['user_id']; 

// Verify that the employee exists in the database using the user_id
$stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("No employee found for the logged-in user. Please contact your administrator.");
}

$employeeId = $employee['id']; // Now you have the employee_id to use in your attendance logic

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle check-in or check-out
    $action = $_POST['action']; // 'check_in' or 'check_out'
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    if ($action === 'check_in') {
        // Check if the employee has already checked in
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employee_id = ? AND check_out_time IS NULL");
        $stmt->execute([$employeeId]);
        $checkInRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($checkInRecord) {
            die("You have already checked in. Please check out before checking in again.");
        }

        // Insert check-in record
        $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, check_in_time, check_in_latitude, check_in_longitude) VALUES (?, NOW(), ?, ?)");
        $stmt->execute([$employeeId, $latitude, $longitude]);
    } elseif ($action === 'check_out') {
        // Update check-out record
        $stmt = $pdo->prepare("UPDATE attendance SET check_out_time = NOW(), check_out_latitude = ?, check_out_longitude = ? WHERE employee_id = ? AND check_out_time IS NULL");
        $stmt->execute([$latitude, $longitude, $employeeId]);
    }
}

// Fetch all attendance records for the employee
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE employee_id = ? ORDER BY check_in_time DESC");
$stmt->execute([$employeeId]);
$attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Attendance</h1>
    <form method="POST">
        <input type="hidden" name="action" id="action"> <!-- Hidden input for action -->
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <div class="form-group">
            <button type="button" class="btn btn-success" onclick="checkIn()">Check In</button>
            <button type="button" class="btn btn-danger" onclick="checkOut()">Check Out</button>
        </div>
    </form>

    <h2>Attendance Records</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Check In Time</th>
                <th>Check In Latitude</th>
                <th>Check In Longitude</th>
                <th>Check Out Time</th>
                <th>Check Out Latitude</th>
                <th>Check Out Longitude</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceRecords as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['check_in_time']); ?></td>
                    <td><?php echo htmlspecialchars($record['check_in_latitude']); ?></td>
                    <td><?php echo htmlspecialchars($record['check_in_longitude']); ?></td>
                    <td><?php echo htmlspecialchars($record['check_out_time']); ?></td>
                    <td><?php echo htmlspecialchars($record['check_out_latitude']); ?></td>
                    <td><?php echo htmlspecialchars($record['check_out_longitude']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function checkIn() {
    document.getElementById('action').value = 'check_in'; // Set action to check_in
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
        document.querySelector('form').submit();
    });
}

function checkOut() {
    document.getElementById('action').value = 'check_out'; // Set action to check_out
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
        document.querySelector('form').submit();
    });
}
</script>
