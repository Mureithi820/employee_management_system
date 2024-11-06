<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Fetch various statistics for dashboard display
$employeeCountStmt = $pdo->query("SELECT COUNT(*) AS total, role FROM users GROUP BY role");
$employeeCounts = $employeeCountStmt->fetchAll(PDO::FETCH_ASSOC);

$attendanceStmt = $pdo->query("
    SELECT DATE_FORMAT(check_in_time, '%Y-%m') AS month, COUNT(*) AS total
    FROM attendance 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 6
");
$attendanceData = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);

$payrollStmt = $pdo->query("
    SELECT COUNT(*) AS total, SUM(net_salary) AS total_salary, AVG(net_salary) AS avg_salary 
    FROM payroll
");
$payrollSummary = $payrollStmt->fetch(PDO::FETCH_ASSOC);

$recentActivityStmt = $pdo->query("
    SELECT a.employee_id, e.full_name, a.check_in_time, a.check_out_time 
    FROM attendance a 
    JOIN employees e ON a.employee_id = e.id 
    ORDER BY a.check_in_time DESC 
    LIMIT 5
");
$recentActivities = $recentActivityStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        <!-- User Statistics Section -->
        <div class="card">
            <div class="card-header">
                <h3>User Statistics</h3>
            </div>
            <div class="card-body">
                <?php foreach ($employeeCounts as $count): ?>
                    <p><?= htmlspecialchars(ucfirst($count['role'])) ?>s: <?= $count['total'] ?></p>
                <?php endforeach; ?>
                <canvas id="userRoleChart"></canvas>
            </div>
        </div>

        <!-- Attendance Summary Section -->
        <div class="card">
            <div class="card-header">
                <h3>Attendance Summary (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Payroll Overview Section -->
        <div class="card">
            <div class="card-header">
                <h3>Payroll Overview</h3>
            </div>
            <div class="card-body">
                <p>Total Payroll Expenses: Ksh<?= number_format($payrollSummary['total_salary'], 2) ?></p>
                <p>Average Salary: Ksh.<?= number_format($payrollSummary['avg_salary'], 2) ?></p>
                <canvas id="payrollChart"></canvas>
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
                        <th>Employee Name</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentActivities as $activity): ?>
                        <tr>
                            <td><?= htmlspecialchars($activity['full_name']) ?></td>
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
// User Role Chart
const userRoleChartCtx = document.getElementById('userRoleChart').getContext('2d');
new Chart(userRoleChartCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($employeeCounts, 'role')); ?>,
        datasets: [{
            label: 'User Roles',
            data: <?php echo json_encode(array_column($employeeCounts, 'total')); ?>,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
        }]
    }
});

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

// Payroll Chart
const payrollChartCtx = document.getElementById('payrollChart').getContext('2d');
new Chart(payrollChartCtx, {
    type: 'doughnut',
    data: {
        labels: ['Total Salary', 'Average Salary'],
        datasets: [{
            data: [<?php echo $payrollSummary['total_salary'] ?? 0; ?>, <?php echo $payrollSummary['avg_salary'] ?? 0; ?>],
            backgroundColor: ['#36b9cc', '#1cc88a'],
        }]
    }
});
</script>
</body>
</html>
