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
$stmt = $pdo->prepare("SELECT * FROM employees"); 
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-10 mb-10">
    <h1>Employee Management</h1>

    <!-- Wrap the buttons in a div with d-flex to align them horizontally -->
    <div class="d-flex mb-3">
        <a href="create.php?page=employee_management_create" class="btn btn-success mr-2">Create New Employee</a>
        <button class="btn btn-danger" id="download-employee-records">Download Employee Records</button>
    </div>

    <table class="table table-striped mt-3" id="employee-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Position</th>
                <th>Department</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($employees)): ?>
                <tr>
                    <td colspan="6" class="text-center">No employees found.</td>
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
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

<script>
// Function to download all employee records as PDF
function downloadEmployeeRecords() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    
    pdf.setFontSize(16);
    pdf.text('Employee Records', 10, 10);
    
    const columns = ["ID", "Full Name", "Position", "Department", "Phone", "Email"];
    const rows = [];
    
    // Get the table data
    const table = document.getElementById('employee-table');
    for (let i = 1; i < table.rows.length; i++) { // Skip header row
        const row = table.rows[i];
        const rowData = Array.from(row.cells).map(cell => cell.textContent);
        rows.push(rowData);
    }
    
    pdf.autoTable({
        head: [columns],
        body: rows,
    });
    
    pdf.save('employee_records.pdf');
}

// Event listener for the download button
document.getElementById('download-employee-records').addEventListener('click', downloadEmployeeRecords);
</script>
