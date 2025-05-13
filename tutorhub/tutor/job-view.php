<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is tutor
require_tutor();

// Get job ID
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    header("Location: jobs.php");
    exit();
}

// Get job details
$sql = "SELECT * FROM jobs WHERE id = ? AND status = 'active'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: jobs.php");
    exit();
}

$job = mysqli_fetch_assoc($result);

// Get tutor info
$sql = "SELECT * FROM tutors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $tutor = mysqli_fetch_assoc($result);
    $tutor_id = $tutor['id'];
} else {
    header("Location: profile.php");
    exit();
}

// Check if tutor has already applied
$sql = "SELECT * FROM applications WHERE job_id = ? AND tutor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $job_id, $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$has_applied = mysqli_num_rows($result) > 0;

if ($has_applied) {
    $application = mysqli_fetch_assoc($result);
}

// Process application form
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$has_applied) {
    $message = sanitize_input($_POST['message']);
    
    if (empty($message)) {
        $error = "Please provide a message with your application";
    } else {
        // Handle CV upload
        $cv_path = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $max_size = 10 * 1024 * 1024; // 10MB
            
            if (!in_array($_FILES['cv']['type'], $allowed_types)) {
                $error = "Only PDF and Word documents are allowed";
            } elseif ($_FILES['cv']['size'] > $max_size) {
                $error = "File size should be less than 10MB";
            } else {
                // Create uploads directory if it doesn't exist
                if (!file_exists('../uploads/cvs')) {
                    mkdir('../uploads/cvs', 0777, true);
                }
                
                $filename = time() . '_' . $_FILES['cv']['name'];
                $destination = '../uploads/cvs/' . $filename;
                
                if (move_uploaded_file($_FILES['cv']['tmp_name'], $destination)) {
                    $cv_path = $destination;
                } else {
                    $error = "Failed to upload CV";
                }
            }
        }
        
        if (empty($error)) {
            // Insert application
            $sql = "INSERT INTO applications (job_id, tutor_id, message, cv_path, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $job_id, $tutor_id, $message, $cv_path);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Your application has been submitted successfully!";
                $has_applied = true;
                
                // Get the application details
                $application_id = mysqli_insert_id($conn);
                $sql = "SELECT * FROM applications WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $application_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $application = mysqli_fetch_assoc($result);
            } else {
                $error = "Error submitting application: " . mysqli_error($conn);
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
    <title><?php echo htmlspecialchars($job['title']); ?> - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="job-details">
                <div class="job-header">
                    <h1><?php echo htmlspecialchars($job['title']); ?></h1>
                    <div class="job-meta">
                        <span class="job-date">Posted on <?php echo format_date($job['created_at']); ?></span>
                    </div>
                </div>
                
                <div class="job-info">
                    <div class="job-info-item">
                        <h3>Subject</h3>
                        <p><?php echo htmlspecialchars($job['subject']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Grade Level</h3>
                        <p><?php echo htmlspecialchars($job['grade_level']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Location</h3>
                        <p><?php echo htmlspecialchars($job['location']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Time/Schedule</h3>
                        <p><?php echo htmlspecialchars($job['time']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Hourly Rate</h3>
                        <p>$<?php echo htmlspecialchars($job['price']); ?>/hr</p>
                    </div>
                </div>
                
                <div class="job-description">
                    <h2>Job Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                </div>
                
                <div class="job-actions">
                    <?php if ($has_applied): ?>
                        <div class="application-status">
                            <h3>Your Application Status</h3>
                            <div class="status-badge status-<?php echo $application['status']; ?>">
                                <?php echo ucfirst($application['status']); ?>
                            </div>
                            <p class="application-date">Applied on <?php echo format_date($application['created_at']); ?></p>
                            <div class="application-message">
                                <h4>Your Message</h4>
                                <p><?php echo nl2br(htmlspecialchars($application['message'])); ?></p>
                            </div>
                            <?php if (!empty($application['cv_path'])): ?>
                            <div class="application-cv">
                                <h4>Your CV</h4>
                                <p><a href="<?php echo htmlspecialchars($application['cv_path']); ?>" target="_blank">View CV</a></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="application-form">
                            <h3>Apply for this Job</h3>
                            
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success">
                                    <?php echo $success; ?>
                                </div>
                            <?php else: ?>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $job_id); ?>" class="needs-validation" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="message">Message to Admin</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Introduce yourself and explain why you're a good fit for this tutoring position..."></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="cv">Upload CV (Optional)</label>
                                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf,.doc,.docx">
                                        <div class="form-text">Upload your CV or resume. Accepted formats: PDF, DOC, DOCX. Max size: 10MB.</div>
                                    </div>
                                    
                                    <div class="form-submit">
                                        <button type="submit" class="btn btn-primary">Submit Application</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="back-link">
                    <a href="jobs.php" class="btn btn-secondary">Back to Jobs</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
