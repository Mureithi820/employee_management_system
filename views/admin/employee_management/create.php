<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $fullName = trim($_POST['full_name']);
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $userId = $_POST['user_id']; 

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO employees (user_id, full_name, position, department, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Execute the statement and check for success
    if ($stmt->execute([$userId, $fullName, $position, $department, $phone, $email])) {
        $successMessage = "Employee created successfully!";
    } else {
        // Handle insertion error
        $errorMessage = "Error creating employee record.";
    }
}

// Fetch users for the select dropdown
$stmt = $pdo->prepare("SELECT id, username FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h4>Create New Employee</h4>
        </div>
        <div class="card-body">
            <!-- Success or error message -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            
            <!-- Form -->
            <form method="POST" action="create.php?page=employee_management_create">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                    <input type="text" class="form-control" id="position" name="position">
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="user_id">Select User</label>
                    <select class="form-control" id="user_id" name="user_id" required>
                        <option value="">Select a user</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No users found</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Employee</button>
            </form>
        </div>
    </div>
</div>
