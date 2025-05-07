<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is tutor
require_tutor();

// Get tutor info
$sql = "SELECT t.*, u.email FROM tutors t JOIN users u ON t.user_id = u.id WHERE u.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $tutor = mysqli_fetch_assoc($result);
} else {
    // Create empty tutor profile if not exists
    $sql = "INSERT INTO tutors (user_id, first_name, last_name, created_at) VALUES (?, '', '', NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    
    // Get user email
    $sql = "SELECT email FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    $tutor = [
        'id' => mysqli_insert_id($conn),
        'user_id' => $_SESSION['user_id'],
        'first_name' => '',
        'last_name' => '',
        'bio' => '',
        'qualifications' => '',
        'phone' => '',
        'email' => $user['email']
    ];
}

$error = '';
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $bio = sanitize_input($_POST['bio']);
    $qualifications = sanitize_input($_POST['qualifications']);
    $phone = sanitize_input($_POST['phone']);
    
    // Validate input
    if (empty($first_name) || empty($last_name)) {
        $error = "First name and last name are required";
    } else {
        // Update tutor profile
        $sql = "UPDATE tutors SET first_name = ?, last_name = ?, bio = ?, qualifications = ?, phone = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $bio, $qualifications, $phone, $tutor['id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Profile updated successfully!";
            
            // Update tutor info
            $tutor['first_name'] = $first_name;
            $tutor['last_name'] = $last_name;
            $tutor['bio'] = $bio;
            $tutor['qualifications'] = $qualifications;
            $tutor['phone'] = $phone;
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="form-container" style="max-width: 800px;">
                <h2>My Tutor Profile</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($tutor['first_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($tutor['last_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($tutor['email']); ?>" disabled>
                        <div class="form-text">Email cannot be changed. Contact admin for assistance.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($tutor['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($tutor['bio']); ?></textarea>
                        <div class="form-text">Tell us about yourself, your teaching style, and experience.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="qualifications">Qualifications</label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="4"><?php echo htmlspecialchars($tutor['qualifications']); ?></textarea>
                        <div class="form-text">List your education, certifications, and relevant experience.</div>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
