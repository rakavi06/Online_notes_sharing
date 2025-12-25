<?php
/**
 * User Profile Page
 * Online Notes Sharing System
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$user_name = getCurrentUserName();
$user_email = getCurrentUserEmail();

$error = '';
$success = '';

// Get current user data
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT name, mobile, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $mobile = sanitizeInput($_POST['mobile'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // Validation
    if (empty($name) || empty($mobile) || empty($email)) {
        $error = 'All fields are required!';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format!';
    } elseif (!validateMobile($mobile)) {
        $error = 'Invalid mobile number!';
    } else {
        $conn = getDBConnection();
        
        // Check if email is changed and already exists
        if ($email != $user['email']) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Email already registered by another user!';
                $stmt->close();
            } else {
                $stmt->close();
                
                // Update user
                $stmt = $conn->prepare("UPDATE users SET name = ?, mobile = ?, email = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $mobile, $email, $user_id);
                
                if ($stmt->execute()) {
                    // Update session
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $user_name = $name;
                    $user_email = $email;
                    
                    $success = 'Profile updated successfully!';
                    $user['name'] = $name;
                    $user['mobile'] = $mobile;
                    $user['email'] = $email;
                } else {
                    $error = 'Error updating profile! Please try again.';
                }
                $stmt->close();
            }
        } else {
            // Update user (email not changed)
            $stmt = $conn->prepare("UPDATE users SET name = ?, mobile = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $mobile, $user_id);
            
            if ($stmt->execute()) {
                // Update session
                $_SESSION['user_name'] = $name;
                $user_name = $name;
                
                $success = 'Profile updated successfully!';
                $user['name'] = $name;
                $user['mobile'] = $mobile;
            } else {
                $error = 'Error updating profile! Please try again.';
            }
            $stmt->close();
        }
        
        closeDBConnection($conn);
    }
}

$pageTitle = 'My Profile';
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
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a class="nav-link" href="notes.php">
                        <i class="fas fa-sticky-note"></i> Notes
                    </a>
                    <a class="nav-link active" href="profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">My Profile</h2>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="profileForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" 
                                   value="<?php echo htmlspecialchars($user['mobile']); ?>" 
                                   pattern="[0-9]{10,15}" required>
                            <small class="text-muted">Enter 10-15 digits</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

