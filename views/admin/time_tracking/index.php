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
<div class="container mt-10 mb-10">
    <h1>Attendance Records</h1>

    <!-- Wrap the buttons in a div with d-flex to align them horizontally -->
    <div class="d-flex mb-3">
        <button class="btn btn-danger" id="download-attendance-records">Download Attendance Records</button>
    </div>

    <table class="table table-striped mt-3" id="attendance-table">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

<script>
// Function to download all attendance records as PDF
function downloadAttendanceRecords() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    
    pdf.setFontSize(16);
    pdf.text('Attendance Records', 10, 10);
    
    const columns = ["Employee Name", "Check-in Time", "Check-out Time", "Check-in Location", "Check-out Location"];
    const rows = [];
    
    // Get the table data
    const table = document.getElementById('attendance-table');
    for (let i = 1; i < table.rows.length; i++) { // Skip header row
        const row = table.rows[i];
        const rowData = Array.from(row.cells).map(cell => cell.textContent);
        rows.push(rowData);
    }
    
    pdf.autoTable({
        head: [columns],
        body: rows,
    });
    
    pdf.save('attendance_records.pdf');
}

// Event listener for the download button
document.getElementById('download-attendance-records').addEventListener('click', downloadAttendanceRecords);
</script>
