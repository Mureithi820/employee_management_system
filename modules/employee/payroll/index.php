<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employee') {
    header('Location: /employee_management_system/login.php');
    exit;
}

$employeeId = $_SESSION['user_id']; 

$stmt = $pdo->prepare("SELECT * FROM payroll WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$payroll_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

require '../../../views/employee/payroll/index.php';
