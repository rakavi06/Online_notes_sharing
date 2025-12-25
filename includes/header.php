<?php
/**
 * Common Header and Navigation
 * Online Notes Sharing System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

$isLoggedIn = isLoggedIn();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Online Notes Sharing System'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <!-- Logged In Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="dashboard.php">
                    <i class="fas fa-book"></i> # ONSS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars(getCurrentUserName()); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php else: ?>
        <!-- Public Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="index.php">
                    <i class="fas fa-book"></i> Online Notes Sharing System
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'notes.php' ? 'active' : ''; ?>" href="notes.php">Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning me-2" href="signup.php">Join</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light" href="login.php">Log In</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

