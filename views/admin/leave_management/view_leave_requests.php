<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Fetch leave requests
$stmt = $pdo->prepare("SELECT lr.*, e.full_name AS employee_full_name, lt.name AS leave_type_name
FROM leave_requests lr
JOIN employees e ON lr.user_id = e.id
JOIN leave_types lt ON lr.leave_type_id = lt.id");
$stmt->execute();
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h1>Leave Requests</h1>
    <button class="btn btn-danger mb-3" id="download-leave-requests">
        <i class="fas fa-download"></i> Download All Leave Requests
    </button>

    <table class="table table-striped" id="leave-requests-table">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leave_requests as $request): ?>
            <tr>
                <td><?= htmlspecialchars($request['employee_full_name']) ?></td>
                <td><?= htmlspecialchars($request['leave_type_name']) ?></td>
                <td><?= htmlspecialchars($request['start_date']) ?></td>
                <td><?= htmlspecialchars($request['end_date']) ?></td>
                <td><?= htmlspecialchars($request['status']) ?></td>
                <td>
                    <?php if ($request['status'] === 'pending'): ?>
                        <a href="approve.php?page=approve_leave_request&id=<?= $request['id'] ?>" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Approve
                        </a>
                        <a href="reject.php?page=reject_leave_request&id=<?= $request['id'] ?>" class="btn btn-danger">
                            <i class="fas fa-times-circle"></i> Reject
                        </a>
                    <?php elseif ($request['status'] === 'approved'): ?>
                        <span class="text-success">Approved</span>
                    <?php elseif ($request['status'] === 'rejected'): ?>
                        <span class="text-danger">Rejected</span>
                    <?php endif; ?>
                    <a href="delete_leave_request.php?page=leave_request_management_delete&id=<?= $request['id'] ?>" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to delete this leave type?');">
                        <i class="fas fa-trash-alt"></i> Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
<script>
function downloadLeaveRequests() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    pdf.setFontSize(16);
    pdf.text('Leave Requests', 10, 10);

    const columns = ["Employee Name", "Leave Type", "Start Date", "End Date", "Status"];
    const rows = [];

    // Get the table data
    const table = document.getElementById('leave-requests-table');
    for (let i = 1; i < table.rows.length; i++) { // Skip header row
        const row = table.rows[i];
        const rowData = Array.from(row.cells).map(cell => cell.textContent);
        rows.push(rowData);
    }

    pdf.autoTable({
        head: [columns],
        body: rows,
    });

    pdf.save('leave_requests.pdf');
}

// Event listener for downloading leave requests
document.getElementById('download-leave-requests').addEventListener('click', downloadLeaveRequests);
</script>
