<?php
/**
 * User Login Page
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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required!';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format!';
    } else {
        $conn = getDBConnection();
        
        // Get user by email
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session
                setUserSession($user['id'], $user['name'], $user['email']);
                
                // Redirect to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password!';
            }
        } else {
            $error = 'Invalid email or password!';
        }
        
        $stmt->close();
        closeDBConnection($conn);
    }
}

$pageTitle = 'User Login';
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="text-primary fw-bold mb-3"># ONSS</h2>
                        <h4 class="text-dark fw-bold">Log In</h4>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <a href="signup.php" class="text-primary text-decoration-none">
                                Don't have an account? Sign Up
                            </a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt"></i> Log In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

