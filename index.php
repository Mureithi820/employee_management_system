<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already active
}

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Determine which page to load based on URL parameter
    $page = $_GET['page'] ?? 'dashboard';
    $id = $_GET['id'] ?? null;
    switch ($page) {
        case 'dashboard':
            $content = ($_SESSION['role'] === 'admin') ? 'views/admin/dashboard_content.php' : 'views/employee/dashboard_content.php';
            break;
        case 'employee_management_create':
            $content = 'views/admin/employee_management/create.php';
            break;
        case 'employee_management_update':
            $content = 'views/admin/employee_management/update.php'; 
            break;
        case 'employee_management_delete':
            include 'views/admin/employee_management/delete.php';
            break;
        case 'admin_time_tracking':
            $content = 'views/admin/time_tracking/index.php';
            break;
        case 'admin_payroll':
            $content = 'views/admin/payroll/index.php';
            break;
        case 'payroll_create':
            $content = 'views/admin/payroll/create.php';
            break;
        case 'admin_leave_management':
            $content = 'views/admin/leave_management/index.php';
            break;
        case 'admin_leave_type':
            $content = 'views/admin/leave_management/add_leave_type.php';
            break;
        case 'approve_leave_request':
            include 'views/admin/leave_management/approve.php'; 
            break;
        case 'reject_leave_request':
            include 'views/admin/leave_management/reject.php'; 
            break;
        case 'view_leave_requests':
            $content = 'views/admin/leave_management/view_leave_requests.php';
            break;
        case 'leave_management_edit':
            $content = 'views/admin/leave_management/edit_leave_type.php'; 
            break;
        case 'leave_management_delete':
            include 'views/admin/leave_management/delete_leave_type.php';
            break;
        case 'leave_request_management_delete':
            include 'views/admin/leave_management/delete_leave_request.php';
            break;
        case 'employee_management':
            $content = 'views/admin/employee_management/index.php';
            break;
        case 'user_management':
            $content = 'views/admin/user_management/index.php';
            break;
        case 'user_management_update':
            $content = 'views/admin/user_management/update.php'; 
            break;
        case 'user_management_delete':
            include 'views/admin/user_management/delete.php';
            break;
        case 'time_tracking':
            $content = 'views/employee/time_tracking/index.php';
            break;
        case 'payroll':
            $content = 'views/employee/payroll/index.php';
            break;
        case 'leave_management':
            $content = 'views/employee/leave_management/index.php';
            break;
        case 'request_leave':
            $content = 'views/employee/leave_management/request_leave.php';
            break;
        case 'profile': 
            $content = ($_SESSION['role'] === 'admin') ? 'views/admin/profile.php' : 'views/employee/profile.php';
            break;
        default:
            $content = ($_SESSION['role'] === 'admin') ? 'views/admin/dashboard_content.php' : 'views/employee/dashboard_content.php';
            break;
    }

    // Include the layout which contains the header, main, and footer
    include 'views/layouts/layout.php';
} else {
    // User is not logged in, so don't include the header or footer
    $content = ''; // No specific content for unauthenticated users
    include 'views/user/login.php'; // Directly include the login page
}
