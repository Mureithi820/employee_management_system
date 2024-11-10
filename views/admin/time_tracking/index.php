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

//Delete Payroll

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure an ID is provided
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $attendanceId = $_POST['id'];

        // Prepare the delete statement
        $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = :id");
        $stmt->bindParam(':id', $attendanceId, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            echo 'Record deleted successfully';
        } else {
            echo 'Failed to delete the record';
        }
    } else {
        echo 'Invalid request';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-10 mb-10">
        <h1>Attendance Records</h1>

        <!-- Wrap the buttons in a div with d-flex to align them horizontally -->
        <div class="d-flex mb-3">
            <!-- Added Font Awesome icon to the button -->
            <button class="btn btn-danger" id="download-attendance-records">
                <i class="fas fa-download"></i> Download Attendance Records
            </button>
        </div>

        <table class="table table-striped mt-3" id="attendance-table">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Check-in Location</th>
                    <th>Check-out Location</th>
                    <th>Actions</th>
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
                    <td>
                        <button class="btn btn-danger btn-sm delete-attendance" data-id="<?= htmlspecialchars($record['id']) ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
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

document.querySelectorAll('.delete-attendance').forEach(button => {
    button.addEventListener('click', function() {
        const recordId = this.getAttribute('data-id');
        
        if (confirm('Are you sure you want to delete this attendance record?')) {
            // AJAX request to delete payroll record
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_attendance.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // If the delete is successful, remove the row from the table
                    const row = button.closest('tr');
                    row.parentNode.removeChild(row);
                    alert('Attendance record deleted successfully.');
                } else {
                    alert('Failed to delete Attendance record. Please try again.');
                }
            };
            xhr.send('id=' + recordId); // Send the record ID to delete
        }
    });
})
</script>
