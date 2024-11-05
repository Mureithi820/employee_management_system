<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/helpers/payroll_calculations.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /employee_management_system/login.php');
    exit;
}

// Fetch employees for selection
$stmt = $pdo->prepare("SELECT id, full_name FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $basic_salary = $_POST['basic_salary'];
    
    $housing_allowance = $_POST['housing_allowance'];
    $transport_allowance = $_POST['transport_allowance'];

    // Calculate gross salary
    $gross_salary = $basic_salary + $housing_allowance + $transport_allowance;

    // Calculate taxes and deductions
    $paye_tax = calculate_paye($gross_salary);
    $nhif = calculate_nhif($gross_salary);
    $nssf = calculate_nssf($gross_salary);

    $deductions = $_POST['deductions'] ?? 0;
    $bonuses = $_POST['bonuses'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO payroll (employee_id, basic_salary, housing_allowance, transport_allowance, paye_tax, nhif, nssf, deductions, bonuses) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$employee_id, $basic_salary, $housing_allowance, $transport_allowance, $paye_tax, $nhif, $nssf, $deductions, $bonuses]);

    header('Location: index.php?page=payroll');
    exit;
}

require '../../../views/admin/payroll/create.php';
