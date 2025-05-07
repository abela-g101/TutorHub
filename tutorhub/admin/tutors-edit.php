<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get tutor ID
$tutor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tutor_id <= 0) {
    header("Location: tutors.php");
    exit();
}

// Get tutor details
$sql = "SELECT t.*, u.username, u.email
        FROM tutors t
        JOIN users u ON t.user_id = u.id
        WHERE t.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: tutors.php");
    exit();
}

$tutor = mysqli_fetch_assoc($result);

$error = '';
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $phone = sanitize_input($_POST['phone']);
    $bio = sanitize_input($_POST['bio']);
    $qualifications = sanitize_input($_POST['qualifications']);
    
    // Validate input
    if (empty($first_name) || empty($last_name)) {
        $error = "First name and last name are required";
    } else {
        // Handle profile photo upload
        $profile_photo_path = $tutor['profile_photo'];
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['profile_photo']['type'], $allowed_types)) {
                $error = "Only JPG, PNG, and GIF images are allowed";
            } elseif ($_FILES['profile_photo']['size'] > $max_size) {
                $error = "Image size should be less than 5MB";
            } else {
                // Create uploads directory if it doesn't exist
                if (!file_exists('../uploads/profile_photos')) {
                    mkdir('../uploads/profile_photos', 0777, true);
                }
                
                $filename = time() . '_' . $_FILES['profile_photo']['name'];
                $destination = '../uploads/profile_photos/' . $filename;
                
                if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                    // Delete old profile photo if exists
                    if (!empty($tutor['profile_photo']) && file_exists($tutor['profile_photo'])) {
                        unlink($tutor['profile_photo']);
                    }
                    
                    $profile_photo_path = $destination;
                } else {
                    $error = "Failed to upload profile photo";
                }
            }
        }
        
        if (empty($error)) {
            // Update tutor profile
            $sql = "UPDATE tutors SET first_name = ?, last_name = ?, phone = ?, bio = ?, qualifications = ?, profile_photo = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssi", $first_name, $last_name, $phone, $bio, $qualifications, $profile_photo_path, $tutor_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Tutor profile updated successfully!";
                
                // Update tutor data
                $tutor['first_name'] = $first_name;
                $tutor['last_name'] = $last_name;
                $tutor['phone'] = $phone;
                $tutor['bio'] = $bio;
                $tutor['qualifications'] = $qualifications;
                $tutor['profile_photo'] = $profile_photo_path;
            } else {
                $error = "Error updating tutor profile: " . mysqli_error($conn);
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
    <title>Edit Tutor - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="form-container" style="max-width: 800px;">
                <h2>Edit Tutor Profile</h2>
                
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
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $tutor_id); ?>" class="needs-validation" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($tutor['username']); ?>" disabled>
                        <div class="form-text">Username cannot be changed.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($tutor['email']); ?>" disabled>
                        <div class="form-text">Email cannot be changed.</div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($tutor['first_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($tutor['last_name']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($tutor['phone']); ?>">
                    </div>
                    
                    <?php if (!empty($tutor['profile_photo'])): ?>
                    <div class="form-group">
                        <label>Current Profile Photo</label>
                        <div class="profile-photo-preview">
                            <img src="<?php echo htmlspecialchars($tutor['profile_photo']); ?>" alt="Profile Photo" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="profile_photo">Update Profile Photo</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/jpeg, image/png, image/gif">
                        <div class="form-text">Upload a professional photo. Max size: 5MB. Formats: JPG, PNG, GIF.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($tutor['bio']); ?></textarea>
                        <div class="form-text">Information about the tutor's teaching style and experience.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="qualifications">Qualifications</label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="4"><?php echo htmlspecialchars($tutor['qualifications']); ?></textarea>
                        <div class="form-text">Education, certifications, and relevant experience.</div>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="tutors.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
