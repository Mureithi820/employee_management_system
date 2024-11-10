<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Assuming user ID is stored in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

$userId = $_SESSION['user_id']; 

// Query to fetch necessary payroll fields along with employee name
$stmt = $pdo->prepare("SELECT 
    e.full_name,
    p.gross_salary,
    p.paye_tax,
    p.nhif,
    p.nssf,
    p.deductions,
    p.bonuses,
    p.net_salary,
    p.created_at 
FROM payroll AS p 
JOIN employees AS e ON p.employee_id = e.id 
WHERE e.user_id = ?");
$stmt->execute([$userId]);
$payroll_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <h1 class="text-center">My Payroll Records</h1>
    <div class="d-flex justify-content-center justify-content-md-start">
        <button class="btn btn-danger mb-3" id="download-payroll-records">
            <i class="fas fa-download"></i> Download Payroll Records
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="payroll-table">
            <thead class="thead-dark">
                <tr>
                    <th>Employee Name</th>
                    <th>Gross Salary</th>
                    <th>PAYE Tax</th>
                    <th>NHIF</th>
                    <th>NSSF</th>
                    <th>Deductions</th>
                    <th>Bonuses</th>
                    <th>Net Salary</th>
                    <th>Date</th>
                    <th>View Payslip</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payroll_records as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['full_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['gross_salary'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['paye_tax'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['nhif'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['nssf'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['deductions'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['bonuses'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['net_salary'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($record['created_at'] ?? 'N/A') ?></td>
                    <td><button class="btn btn-info btn-sm view-payslip" data-record='<?= json_encode($record) ?>'>
                        <i class="fas fa-eye"></i> View Payslip
                    </button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Payslip -->
<div class="modal fade" id="payslipModal" tabindex="-1" role="dialog" aria-labelledby="payslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payslipModalLabel">Payslip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="payslipContent"></div>
                <div class="d-flex flex-column flex-md-row gap-2 mt-3">
                    <button id="download-payslip-csv" class="btn btn-success">
                        <i class="fas fa-file-csv"></i> Download Payslip as CSV
                    </button>
                    <button id="download-payslip-pdf" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Download Payslip as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
<script>
function downloadPayrollRecords() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    pdf.setFontSize(16);
    pdf.text('Payroll Records', 10, 10);

    const columns = ["Employee Name", "Gross Salary", "PAYE Tax", "NHIF", "NSSF", "Deductions", "Bonuses", "Net Salary", "Date"];
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

    pdf.save('payroll_records.pdf');
}

function downloadCSV() {
    const table = document.getElementById('payroll-table');
    const rows = Array.from(table.rows).map(row => Array.from(row.cells).map(cell => cell.textContent));

    // Create CSV string
    const csvContent = rows.map(e => e.join(",")).join("\n");

    // Create a Blob from the CSV string
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("download", "payslip.csv");
    a.style.visibility = 'hidden';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function downloadPayslipCSV(record) {
    const csvContent = `Employee Name, Gross Salary, Net Salary, Date\n${record.full_name}, ${record.gross_salary}, ${record.net_salary}, ${record.created_at}`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("download", "payslip.csv");
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
    pdf.save('employee_payslip.pdf');
}

function showPayslip(event) {
    const record = JSON.parse(event.currentTarget.getAttribute('data-record'));
    const payslipContent = `
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

document.getElementById('download-payroll-records').addEventListener('click', downloadPayrollRecords);
document.querySelectorAll('.view-payslip').forEach(button => {
    button.addEventListener('click', showPayslip);
});
</script>
