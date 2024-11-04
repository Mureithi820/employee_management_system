 <?php
 require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}
// Assuming user ID is stored in session
if (!isset($_SESSION['user_id'])) {
    die("Access denied. You must be logged in to view this page.");
}

$userId = $_SESSION['user_id'];

// Fetch employee details from the database
$stmt = $pdo->prepare("SELECT * FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee data was found
if (!$employee) {
    die("Employee not found.");
}

?>
<div class="container">
    <h1>Your Profile</h1>
    <?php if ($employee): ?>
        <table class="table table-striped">
            <tr>
                <th>Full Name</th>
                <td><?= htmlspecialchars($employee['full_name']) ?></td>
            </tr>
            <tr>
                <th>Position</th>
                <td><?= htmlspecialchars($employee['position']) ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?= htmlspecialchars($employee['department']) ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?= htmlspecialchars($employee['phone']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($employee['email']) ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p>No employee data found.</p>
    <?php endif; ?>
</div>
