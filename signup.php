<?php
/**
 * User Registration Page
 * Online Notes Sharing System
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $mobile = sanitizeInput($_POST['mobile'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($name) || empty($mobile) || empty($email) || empty($password)) {
        $error = 'All fields are required!';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format!';
    } elseif (!validateMobile($mobile)) {
        $error = 'Invalid mobile number!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long!';
    } else {
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered!';
            $stmt->close();
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, mobile, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $mobile, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
                // Clear form data
                $name = $mobile = $email = '';
            } else {
                $error = 'Registration failed! Please try again.';
            }
            $stmt->close();
        }
        
        closeDBConnection($conn);
    }
}

$pageTitle = 'User Sign Up';
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary fw-bold"># ONSS</h2>
                        <h4 class="text-dark fw-bold mb-0">Sign Up</h4>
                    </div>
                    
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
                    
                    <form method="POST" action="" id="signupForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" 
                                   value="<?php echo isset($mobile) ? htmlspecialchars($mobile) : ''; ?>" 
                                   pattern="[0-9]{10,15}" required>
                            <small class="text-muted">Enter 10-15 digits</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="6" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <a href="login.php" class="text-primary text-decoration-none">
                                Already Registered !!
                            </a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-user-plus"></i> Sign Up
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

