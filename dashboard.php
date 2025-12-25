<?php
/**
 * User Dashboard
 * Online Notes Sharing System
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$user_name = getCurrentUserName();
$user_email = getCurrentUserEmail();

// Get statistics
$conn = getDBConnection();

// Get total unique subjects
$stmt = $conn->prepare("SELECT COUNT(DISTINCT subject) as total_subjects FROM notes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$total_subjects = $stats['total_subjects'] ?? 0;
$stmt->close();

// Get total notes files
$stmt = $conn->prepare("SELECT COUNT(*) as total_files FROM note_files nf 
                        INNER JOIN notes n ON nf.note_id = n.id 
                        WHERE n.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$total_files = $stats['total_files'] ?? 0;
$stmt->close();

closeDBConnection($conn);

$pageTitle = 'User Dashboard';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 bg-light sidebar p-0">
            <div class="sidebar-sticky">
                <div class="p-3 bg-primary text-white">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                    <h6 class="mb-1"><?php echo htmlspecialchars($user_name); ?></h6>
                    <small><?php echo htmlspecialchars($user_email); ?></small>
                </div>
                
                <nav class="nav flex-column p-3">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a class="nav-link" href="notes.php">
                        <i class="fas fa-sticky-note"></i> Notes
                    </a>
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">User Dashboard</h2>
            </div>

            <div class="welcome-message mb-4">
                <h4 class="fw-bold text-dark">Hello, <?php echo htmlspecialchars($user_name); ?> Welcome to your panel</h4>
            </div>

            <div class="row g-4 mb-4">
                <!-- Total Uploaded Subject Notes Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-book fa-4x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-4">
                                    <h5 class="card-title text-muted mb-2">Total Uploaded Subject Notes</h5>
                                    <h2 class="text-primary fw-bold mb-2"><?php echo $total_subjects; ?></h2>
                                    <a href="notes.php" class="text-primary text-decoration-none">
                                        View Detail <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Uploaded Notes File Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file fa-4x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-4">
                                    <h5 class="card-title text-muted mb-2">Total Uploaded Notes File</h5>
                                    <h2 class="text-primary fw-bold mb-2"><?php echo $total_files; ?></h2>
                                    <a href="notes.php" class="text-primary text-decoration-none">
                                        View Detail <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

