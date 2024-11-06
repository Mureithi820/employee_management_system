<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

// Fetch users from the database
$stmt = $pdo->prepare("SELECT * FROM users"); 
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mt-10 mb-10">
    <h1>User Management</h1>

    <!-- Wrap the buttons in a div with d-flex to align them horizontally -->
    <div class="d-flex mb-3">
        <button class="btn btn-danger" id="download-user-records">Download User Records</button>
    </div>

    <table class="table table-striped mt-3" id="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="text-center">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="update.php?page=user_management_update&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?page=user_management_delete&id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

<script>
// Function to download all user records as PDF
function downloadUserRecords() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    
    pdf.setFontSize(16);
    pdf.text('User Records', 10, 10);
    
    const columns = ["ID", "Username", "Role"];
    const rows = [];
    
    // Get the table data
    const table = document.getElementById('user-table');
    for (let i = 1; i < table.rows.length; i++) { // Skip header row
        const row = table.rows[i];
        const rowData = Array.from(row.cells).map(cell => cell.textContent);
        rows.push(rowData);
    }
    
    pdf.autoTable({
        head: [columns],
        body: rows,
    });
    
    pdf.save('user_records.pdf');
}

// Event listener for the download button
document.getElementById('download-user-records').addEventListener('click', downloadUserRecords);
</script>
