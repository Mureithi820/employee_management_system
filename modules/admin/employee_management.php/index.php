<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/employee_management_system/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Fetch employee records from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM employees");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if the fetch was successful
    if ($employees === false) {
        throw new Exception("Error fetching employees.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("General error: " . $e->getMessage());
}

// Load the corresponding view
require "../../../views/admin/employee_management/index.php";
