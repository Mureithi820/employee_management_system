<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $entitled_days = $_POST['entitled_days'];

    // Insert leave type
    $stmt = $pdo->prepare("INSERT INTO leave_types (name, description, entitled_days) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $entitled_days]);

    // Redirect to leave types index page
    header("Location: index.php?page=leave_types");
    exit;
}
?>

<div class="container">
    <h1>Add Leave Type</h1>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Leave Type Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="entitled_days" class="form-label">Entitled Days</label>
            <input type="number" name="entitled_days" id="entitled_days" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Leave Type</button>
    </form>
</div>