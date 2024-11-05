<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/helpers/payroll_calculations.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /employee_management_system/login.php');
    exit;
}

// Fetch payroll records
$stmt = $pdo->prepare("SELECT p.*, e.full_name FROM payroll p JOIN employees e ON p.employee_id = e.id");
$stmt->execute();
$payroll_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

require '../../../views/admin/payroll/index.php';
