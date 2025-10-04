<?php

require_once 'session-manager.php';

require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EDUMA is a modern online learning platform offering a wide range of courses to help you achieve your goals and unlock your potential.">

    <title><?php echo htmlspecialchars($pageTitle ?? "Welcome to EDUMA"); ?></title>

    <link href="https:
    <link rel=" stylesheet" href="https:
    <link rel=" stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="main-header py-3 shadow-sm sticky-top bg-white">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <a class="navbar-brand logo" href="index.php">EDUMA</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto nav-links">
                            <li class="nav-item">
                                <a class="nav-link <?php echo is_active('index.php'); ?>" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo is_active('courses.php'); ?>" href="courses.php">Courses</a>
                            </li>

                            <?php if (isset($_SESSION['user_id'])) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-1"></i> Hello, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if ($_SESSION['role'] == 'student') : ?>
                                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                        <?php elseif ($_SESSION['role'] == 'instructor') : ?>
                                            <li><a class="dropdown-item" href="instructor/dashboard.php">Instructor Dashboard</a></li>
                                        <?php elseif ($_SESSION['role'] == 'admin') : ?>
                                            <li><a class="dropdown-item" href="admin/dashboard.php">Admin Dashboard</a></li>
                                        <?php endif; ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                                    </ul>
                                </li>
                            <?php else : ?>
                                <li class="nav-item mx-lg-2">
                                    <a class="nav-link <?php echo is_active('login.php'); ?>" href="login.php">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link btn btn-primary px-3 regButton <?php echo is_active('register.php'); ?>" href="register.php">Register</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>