<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/helpers/payroll_calculations.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You do not have permission to access this page.");
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

    header('Location: index.php?page=admin_payroll');
    exit;
}

?>
<div class="container">
    <h1>Add Payroll Record</h1>
    <form method="POST" action="create.php?page=payroll_create" class="border p-4 rounded">
        <div class="form-group">
            <label for="employee_id">Employee</label>
            <select class="form-control" id="employee_id" name="employee_id" required>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= htmlspecialchars($employee['id']) ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="basic_salary">Basic Salary</label>
            <input type="number" class="form-control" id="basic_salary" name="basic_salary" required>
        </div>
        <div class="form-group">
            <label for="housing_allowance">Housing Allowance</label>
            <input type="number" class="form-control" id="housing_allowance" name="housing_allowance">
        </div>
        <div class="form-group">
            <label for="transport_allowance">Transport Allowance</label>
            <input type="number" class="form-control" id="transport_allowance" name="transport_allowance">
        </div>
        <div class="form-group">
            <label for="deductions">Deductions</label>
            <input type="number" class="form-control" id="deductions" name="deductions">
        </div>
        <div class="form-group">
            <label for="bonuses">Bonuses</label>
            <input type="number" class="form-control" id="bonuses" name="bonuses">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add Payroll</button>
    </form>
</div>
