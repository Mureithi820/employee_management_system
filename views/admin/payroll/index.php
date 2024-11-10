<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/helpers/payroll_calculations.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

//Delete Payroll

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure an ID is provided
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $payrollId = $_POST['id'];

        // Prepare the delete statement
        $stmt = $pdo->prepare("DELETE FROM payroll WHERE id = :id");
        $stmt->bindParam(':id', $payrollId, PDO::PARAM_INT);

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

// Fetch payroll records
$stmt = $pdo->prepare("SELECT p.*, e.full_name FROM payroll p JOIN employees e ON p.employee_id = e.id");
$stmt->execute();
$payroll_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-10 mb-10">
    <h1 class="text-center">Payroll Management</h1>
    <div class="d-flex flex-column flex-md-row justify-content-between mb-3">
        <!-- Add Payroll Record Button -->
        <a href="create.php?page=payroll_create" class="btn btn-success mb-2 mb-md-0 btn-md w-100">
            <i class="fas fa-plus-circle"></i> Add Payroll Record
        </a>
        <!-- Download Payroll Records Button -->
        <button class="btn btn-danger btn-md w-100" id="download-payroll-records-admin">
            <i class="fas fa-download"></i> Download All Payroll Records
        </button>
    </div>


    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="payroll-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Employee Name</th>
                    <th>Basic Salary</th>
                    <th>Gross Salary</th>
                    <th>PAYE Tax</th>
                    <th>NHIF</th>
                    <th>NSSF</th>
                    <th>Deductions</th>
                    <th>Bonuses</th>
                    <th>Net Salary</th>
                    <th>Created At</th>
                    <th>View Payslip</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payroll_records as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['id']) ?></td>
                    <td><?= htmlspecialchars($record['full_name']) ?></td>
                    <td><?= htmlspecialchars($record['basic_salary']) ?></td>
                    <td><?= htmlspecialchars($record['gross_salary']) ?></td>
                    <td><?= htmlspecialchars($record['paye_tax']) ?></td>
                    <td><?= htmlspecialchars($record['nhif']) ?></td>
                    <td><?= htmlspecialchars($record['nssf']) ?></td>
                    <td><?= htmlspecialchars($record['deductions']) ?></td>
                    <td><?= htmlspecialchars($record['bonuses']) ?></td>
                    <td><?= htmlspecialchars($record['net_salary']) ?></td>
                    <td><?= htmlspecialchars($record['created_at']) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm view-payslip" data-record='<?= json_encode($record) ?>'>
                            <i class="fas fa-eye"></i> View Payslip
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-payroll" data-id="<?= htmlspecialchars($record['id']) ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Payslip -->
<div class="modal fade" id="payslipModal" tabindex="-1" role="dialog" aria-labelledby="payslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payslipModalLabel">Payslip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="payslipContent"></div>
                <!-- Download Payslip as CSV Button with icon -->
                <button id="download-payslip-csv" class="btn btn-success btn-block mt-3">
                    <i class="fas fa-file-csv"></i> Download Payslip as CSV
                </button>
                <!-- Download Payslip as PDF Button with icon -->
                <button id="download-payslip-pdf" class="btn btn-danger btn-block mt-3">
                    <i class="fas fa-file-pdf"></i> Download Payslip as PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome CDN -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
<script>
function downloadPayrollRecordsAdmin() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    pdf.setFontSize(16);
    pdf.text('Payroll Records', 10, 10);

    const columns = ["ID", "Employee Name", "Basic Salary", "Gross Salary", "PAYE Tax", "NHIF", "NSSF", "Deductions", "Bonuses", "Net Salary", "Created At"];
    const rows = [];

    // Get the table data
    const table = document.getElementById('payroll-table');
    for (let i = 1; i < table.rows.length; i++) { // Skip header row
        const row = table.rows[i];
        const rowData = Array.from(row.cells).map(cell => cell.textContent);
        rows.push(rowData);
    }

    pdf.autoTable({
        head: [columns],
        body: rows,
    });

    pdf.save('payroll_records_admin.pdf');
}
document.querySelectorAll('.delete-payroll').forEach(button => {
    button.addEventListener('click', function() {
        const recordId = this.getAttribute('data-id');
        
        if (confirm('Are you sure you want to delete this payroll record?')) {
            // AJAX request to delete payroll record
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_payroll.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // If the delete is successful, remove the row from the table
                    const row = button.closest('tr');
                    row.parentNode.removeChild(row);
                    alert('Payroll record deleted successfully.');
                } else {
                    alert('Failed to delete payroll record. Please try again.');
                }
            };
            xhr.send('id=' + recordId); // Send the record ID to delete
        }
    });
});

function downloadCSVAdmin() {
    const table = document.getElementById('payroll-table');
    const rows = Array.from(table.rows).map(row => Array.from(row.cells).map(cell => cell.textContent));

    // Create CSV string
    const csvContent = rows.map(e => e.join(",")).join("\n");

    // Create a Blob from the CSV string
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("download", "payroll_records_admin.csv");
    a.style.visibility = 'hidden';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPayslipCSV(record) {
    const csvContent = `ID, Employee Name, Gross Salary, Net Salary, Date\n${record.id}, ${record.full_name}, ${record.gross_salary}, ${record.net_salary}, ${record.created_at}`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("download", "payslip_admin.csv");
    a.style.visibility = 'hidden';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPayslipPDF(record) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    
    // Set document title
    pdf.setFontSize(18);
    pdf.text('Payslip', 10, 10);
    
    // Set font for body text
    pdf.setFontSize(12);
    
    // Add Employee Information
    pdf.text(`Employee ID: ${record.id}`, 10, 20);
    pdf.text(`Employee Name: ${record.full_name}`, 10, 30);
    pdf.text(`Gross Salary: ${record.gross_salary}`, 10, 40);
    pdf.text(`Net Salary: ${record.net_salary}`, 10, 50);
    pdf.text(`Date: ${record.created_at}`, 10, 60);
    
    // Add a separator line
    pdf.setLineWidth(0.5);
    pdf.line(10, 70, 200, 70);
    
    // Add Payroll Details in Table Format
    const tableColumn = ["Description", "Amount"];
    const tableRows = [
        ["Gross Salary", record.gross_salary],
        ["PAYE Tax", record.paye_tax],
        ["NHIF", record.nhif],
        ["NSSF", record.nssf],
        ["Deductions", record.deductions],
        ["Bonuses", record.bonuses],
        ["Net Salary", record.net_salary],
    ];
    
    // Add table to PDF
    pdf.autoTable({
        startY: 80,
        head: [tableColumn],
        body: tableRows,
        theme: 'grid',
        headStyles: {
            fillColor: [45, 85, 180],
            textColor: [255, 255, 255],
            fontSize: 10,
        },
        bodyStyles: {
            fontSize: 10,
            cellPadding: 3,
        }
    });
    
    // Save the PDF
    pdf.save('payslip_admin.pdf');
}

function showPayslip(event) {
    const record = JSON.parse(event.currentTarget.getAttribute('data-record'));
    const payslipContent = `
        <strong>Employee ID:</strong> ${record.id} <br>
        <strong>Employee Name:</strong> ${record.full_name} <br>
        <strong>Gross Salary:</strong> ${record.gross_salary} <br>
        <strong>Net Salary:</strong> ${record.net_salary} <br>
        <strong>Date:</strong> ${record.created_at} <br>
    `;

    document.getElementById('payslipContent').innerHTML = payslipContent;
    
    // Attach event listeners for download buttons
    document.getElementById('download-payslip-csv').onclick = function() {
        downloadPayslipCSV(record);
    };
    document.getElementById('download-payslip-pdf').onclick = function() {
        downloadPayslipPDF(record);
    };

    $('#payslipModal').modal('show');
}

// Event listeners
document.getElementById('download-payroll-records-admin').addEventListener('click', downloadPayrollRecordsAdmin);
document.querySelectorAll('.view-payslip').forEach(button => {
    button.addEventListener('click', showPayslip);
});
</script>

