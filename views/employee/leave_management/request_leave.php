<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("Access denied. You do not have permission to access this page.");
}

$userId = $_SESSION['user_id'];

// Fetch leave types and entitled days for dropdown
$stmt = $pdo->prepare("SELECT id, name, entitled_days FROM leave_types");
$stmt->execute();
$leave_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user_id exists in employees table
$stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("Error: The user ID does not exist in the employees table.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leaveTypeId = $_POST['leave_type'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Insert leave request
    $stmt = $pdo->prepare("INSERT INTO leave_requests (user_id, leave_type_id, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$employee['id'], $leaveTypeId, $startDate, $endDate]);

    header("Location: index.php?page=leave_requests");
    exit;
}
?>

<div class="container">
    <h1>Request Leave</h1>
    <form method="post">
        <div class="mb-3">
            <label for="leave_type" class="form-label">Leave Type</label>
            <select name="leave_type" id="leave_type" class="form-select" required onchange="updateEntitledDays()">
                <option value="">Select Leave Type</option>
                <?php foreach ($leave_types as $type): ?>
                <option value="<?= $type['id'] ?>" data-entitled-days="<?= $type['entitled_days'] ?>">
                    <?= htmlspecialchars($type['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="entitled_days" class="form-label">Entitled Days</label>
            <input type="text" id="entitled_days" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>

<script>
function updateEntitledDays() {
    const leaveTypeSelect = document.getElementById('leave_type');
    const entitledDaysInput = document.getElementById('entitled_days');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];

    if (selectedOption.value) {
        // Get the entitled days from the selected option's data attribute
        const entitledDays = selectedOption.getAttribute('data-entitled-days');
        entitledDaysInput.value = entitledDays; // Display entitled days in the input field
    } else {
        entitledDaysInput.value = ''; // Clear if no selection
    }
}
</script>
