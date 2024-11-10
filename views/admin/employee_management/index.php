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

<div class="container mt-4 mb-4">
    <h1 class="text-center">Employee Management</h1>

    <!-- Responsive button group -->
    <div class="d-flex flex-column flex-sm-row justify-content-center mb-3">
        
        <a href="create.php?page=employee_management_create" class="btn btn-success mb-2 mb-sm-0 mr-sm-2">
            <i class="fas fa-user-plus"></i> Create New Employee
        </a>
        
        <button class="btn btn-danger" id="download-employee-records">
            <i class="fas fa-download"></i> Download Employee Records
        </button>
    </div>

    <!-- Responsive table -->
    <div class="table-responsive">
        <table class="table table-striped mt-3" id="employee-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
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
                        <td>
                            <!-- Edit button -->
                            <a href="update.php?page=employee_management_update&id=<?= $employee['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <!-- Delete button with confirmation -->
                            <a href="delete.php?page=employee_management_delete&id=<?= $employee['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?');">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
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
