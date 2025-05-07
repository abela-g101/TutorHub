<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$form_data = [
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'phone' => ''
];

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_data = [
        'username' => sanitize_input($_POST['username']),
        'email' => sanitize_input($_POST['email']),
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name']),
        'phone' => sanitize_input($_POST['phone'] ?? '')
    ];
    
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($form_data['username']) || empty($form_data['email']) || empty($password) || empty($confirm_password) || empty($form_data['first_name']) || empty($form_data['last_name'])) {
        $error = "All required fields must be filled out";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $form_data['username']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username already exists";
        } else {
            // Check if email already exists
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $form_data['email']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "Email already exists";
            } 
                
                if (empty($error)) {
                    // Hash password
                    $hashed_password = generate_password_hash($password);
                    
                    // Begin transaction
                    mysqli_begin_transaction($conn);
                    
                    try {
                        // Insert user
                        $sql = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'tutor', NOW())";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "sss", $form_data['username'], $form_data['email'], $hashed_password);
                        mysqli_stmt_execute($stmt);
                        
                        $user_id = mysqli_insert_id($conn);
                        
                        // Insert tutor profile
                        $sql = "INSERT INTO tutors (user_id, first_name, last_name, phone, created_at) VALUES (?, ?, ?, ?, NOW())";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "isss", $user_id, $form_data['first_name'], $form_data['last_name'], $form_data['phone']);
                        mysqli_stmt_execute($stmt);
                        
                        // Commit transaction
                        mysqli_commit($conn);
                        
                        $success = "Registration successful! You can now login.";
                        
                        // Clear form data after successful registration
                        $form_data = [
                            'username' => '',
                            'email' => '',
                            'first_name' => '',
                            'last_name' => '',
                            'phone' => ''
                        ];
                    } catch (Exception $e) {
                        // Rollback transaction on error
                        mysqli_rollback($conn);
                        $error = "Registration failed: " . $e->getMessage();
                    }
                }
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TutorHub</title>
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
                        <h2>Register as a Tutor</h2>
                        <p>Create your account and start your tutoring journey</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <p class="mt-2"><a href="login.php" class="btn btn-primary btn-sm">Click here to login</a></p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="register-form needs-validation" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                                <div class="form-text">Username must be unique and will be used for login.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name" class="form-label">
                                        <i class="fas fa-user-circle"></i> First Name
                                    </label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="last_name" class="form-label">
                                        <i class="fas fa-user-circle"></i> Last Name
                                    </label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number (Optional)
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($form_data['phone']); ?>">
                            </div>
                            
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock"></i> Password
                                    </label>
                                    <div class="password-input-container">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password')" aria-label="Toggle password visibility">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Password must be at least 8 characters long.</div>
                                    <div id="password-strength"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock"></i> Confirm Password
                                    </label>
                                    <div class="password-input-container">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm_password')" aria-label="Toggle password visibility">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match"></div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label for="terms" class="form-check-label">I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="privacy.php" target="_blank">Privacy Policy</a></label>
                                </div>
                            </div>
                            
                            <div class="form-submit">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="auth-footer">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
                
                <div class="auth-info">
                    <div class="auth-info-content">
                        <h3>Join Our Tutoring Community</h3>
                        <p>Become a tutor and share your knowledge with students who need your expertise.</p>
                        
                        <ul class="auth-benefits">
                            <li><i class="fas fa-check-circle"></i> Create your professional profile</li>
                            <li><i class="fas fa-check-circle"></i> Browse and apply for tutoring jobs</li>
                            <li><i class="fas fa-check-circle"></i> Set your own schedule and rates</li>
                            <li><i class="fas fa-check-circle"></i> Get paid for sharing your knowledge</li>
                            <li><i class="fas fa-check-circle"></i> Build your teaching experience</li>
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
