<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user_id exists in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

// Retrieve user_id from the session
$tempUserId = $_SESSION['user_id'];

// Fetch the employee record to get the actual user ID
$stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
$stmt->execute([$tempUserId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee exists and get the user ID
if ($employee) {
    $userId = $employee['id']; // Use the ID from the employees table
} else {
    die("Access denied. User not found.");
}

// Fetch leave requests for the current user
$stmt = $pdo->prepare("
    SELECT lr.*, lt.name AS leave_type_name 
    FROM leave_requests lr 
    JOIN leave_types lt ON lr.leave_type_id = lt.id 
    WHERE lr.user_id = ?
");

if (!$stmt->execute([$userId])) {
    // Debugging: Output any SQL errors if needed, but not shown in UI
    // var_dump($stmt->errorInfo());
}

$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Now fetch entitled days for the leave types applied
$entitled_days = [];
foreach ($leave_requests as $request) {
    if (!isset($entitled_days[$request['leave_type_id']])) {
        $stmt = $pdo->prepare("SELECT entitled_days FROM leave_types WHERE id = ?");
        $stmt->execute([$request['leave_type_id']]);
        $entitled_days_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($entitled_days_data) {
            $entitled_days[$request['leave_type_id']] = $entitled_days_data['entitled_days'];
        } else {
            $entitled_days[$request['leave_type_id']] = 0; // Default to 0 if not found
        }
    }
}

?>

<div class="container">
    <h1 class="text-center">Your Leave Requests</h1>

    <!-- Responsive button group for mobile screens -->
    <div class="d-flex flex-column flex-md-row justify-content-between mb-3">
        <a href="request_leave.php?page=request_leave" class="btn btn-secondary mb-2 mb-md-0">Request Leave</a>
        <button class="btn btn-danger" id="download-leave-records">Download Leave Requests as PDF</button>
    </div>

    <!-- Responsive table container for smaller screens -->
    <div class="table-responsive">
        <table id="leaveTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Used Days</th>
                    <th>Remaining Days</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($leave_requests) > 0): ?>
                    <?php foreach ($leave_requests as $request): ?>
                    <tr data-leave-id="<?= htmlspecialchars($request['id']) ?>">
                        <td><?= htmlspecialchars($request['leave_type_name']) ?></td>
                        <td><?= htmlspecialchars($request['start_date']) ?></td>
                        <td><?= htmlspecialchars($request['end_date']) ?></td>
                        <td><?= htmlspecialchars($request['status']) ?></td>
                        <td><?= htmlspecialchars($request['used_days']) ?></td>
                        <td>
                            <?php 
                            $remainingDays = isset($entitled_days[$request['leave_type_id']]) ? 
                                $entitled_days[$request['leave_type_id']] - $request['used_days'] : 0;
                            echo htmlspecialchars($remainingDays);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($request['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No leave requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
<script>
function downloadLeaveRecordsPDF() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    pdf.setFontSize(16);
    pdf.text('Leave Request Records', 10, 10);

    const columns = ["Leave Type", "Start Date", "End Date", "Status", "Used Days", "Remaining Days", "Created At"];
    const rows = [];

    // Get the table data
    const table = document.getElementById('leaveTable');
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

// Event listener
document.getElementById('download-leave-records').addEventListener('click', downloadLeaveRecordsPDF);
</script>
