<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/functions.php';


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$username = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
       
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
               
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: tutor/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TutorHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <main class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-form-container">
                    <div class="auth-header">
                        <h2>Login to TutorHub</h2>
                        <p>Enter your credentials to access your account</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="auth-form needs-validation">
                        <div class="form-group">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                            </div>
                            <div class="password-input-container">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password')" aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label for="remember" class="form-check-label">Remember me</label>
                            </div>
                        </div>
                        
                        <div class="form-submit">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
                
                <div class="auth-info">
                    <div class="auth-info-content">
                        <h3>Welcome to TutorHub</h3>
                        <p>Join our community of tutors and find the perfect opportunities to share your knowledge.</p>
                        
                        <ul class="auth-benefits">
                            <li><i class="fas fa-check-circle"></i> Find tutoring jobs that match your skills</li>
                            <li><i class="fas fa-check-circle"></i> Set your own schedule and rates</li>
                            <li><i class="fas fa-check-circle"></i> Connect with students in your area</li>
                            <li><i class="fas fa-check-circle"></i> Build your teaching portfolio</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
