<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if user ID is in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

$userId = $_SESSION['user_id'];

// Verify that the employee exists in the database using the user_id
$stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("Access denied. Employee not found.");
}

// Fetch employee-specific data using employee ID
$employeeId = $employee['id'];

$attendanceStmt = $pdo->prepare("
    SELECT DATE_FORMAT(check_in_time, '%Y-%m') AS month, COUNT(*) AS total
    FROM attendance 
    WHERE employee_id = ?
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 6
");
$attendanceStmt->execute([$employeeId]);
$attendanceData = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);

$recentActivityStmt = $pdo->prepare("
    SELECT check_in_time, check_out_time 
    FROM attendance 
    WHERE employee_id = ?
    ORDER BY check_in_time DESC 
    LIMIT 5
");
$recentActivityStmt->execute([$employeeId]);
$recentActivities = $recentActivityStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Container styling */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Grid layout for cards */
        .dashboard-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        /* Chart styling for responsiveness */
        .card-body canvas {
            max-width: 100%;
            height: auto;
        }

        /* Adjust table to be more compact */
        .table th, .table td {
            font-size: 0.9rem;
            padding: 0.5rem;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Dashboard grid layout -->
    <div class="dashboard-grid">

        <!-- Attendance Summary Section -->
        <div class="card">
            <div class="card-header">
                <h3>Attendance Summary (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Activity</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentActivities as $activity): ?>
                        <tr>
                            <td><?= htmlspecialchars($activity['check_in_time']) ?></td>
                            <td><?= htmlspecialchars($activity['check_out_time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Chart
const attendanceChartCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceChartCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($attendanceData, 'month')); ?>,
        datasets: [{
            label: 'Monthly Attendance',
            data: <?php echo json_encode(array_column($attendanceData, 'total')); ?>,
            backgroundColor: '#4e73df',
        }]
    }
});
</script>
</body>
</html>
