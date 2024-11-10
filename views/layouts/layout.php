<?php if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already active
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Link your custom CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Header Section -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Employee Management System</h1>
                <nav>
                    <ul class="nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($page == 'profile') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=profile">
                                        <i class="fas fa-user-shield"></i> Admin Profile
                                    </a>
                                </li>
                            <?php elseif ($_SESSION['role'] === 'employee'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($page == 'profile') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=profile">
                                        <i class="fas fa-user"></i> Employee Profile
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/employee_management_system/views/user/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'login') ? 'active' : ''; ?>" href="/employee_management_system/views/user/login.php">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'register') ? 'active' : ''; ?>" href="/employee_management_system/views/user/register.php">
                                    <i class="fas fa-user-plus"></i> Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Section -->
            <aside class="col-md-3 col-lg-2 bg-light sidebar py-4">
                <ul class="nav flex-column">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=dashboard">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'admin_time_tracking') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=admin_time_tracking">
                                    <i class="fas fa-clock"></i> Admin Time Tracking
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'admin_payroll') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=admin_payroll">
                                    <i class="fas fa-money-bill-wave"></i> Admin Payroll
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'admin_leave_management') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=admin_leave_management">
                                    <i class="fas fa-calendar-alt"></i> Admin Leave Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'employee_management') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=employee_management">
                                    <i class="fas fa-users"></i> Employee Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'user_management') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=user_management">
                                    <i class="fas fa-user-cog"></i> User Management
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'time_tracking') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=time_tracking">
                                    <i class="fas fa-clock"></i> Time Tracking
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'payroll') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=payroll">
                                    <i class="fas fa-money-bill-wave"></i> Payroll
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($page == 'leave_management') ? 'active' : ''; ?>" href="/employee_management_system/index.php?page=leave_management">
                                    <i class="fas fa-calendar-alt"></i> Leave Management
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </aside>


            <!-- Main Content Section -->
            <main class="col-md-9 col-lg-10 main-content py-4">
                <?php
                if (!empty($content) && file_exists($content)) {
                    include $content;
                } else {
                    echo '<h2>Welcome to the Employee Management System</h2>';
                    echo '<p>Use the navigation above to access different modules.</p>';
                }
                ?>
            </main>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="bg-dark text-white text-center py-2">
        <p>&copy; <?php echo date("Y"); ?> Employee Management System. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
