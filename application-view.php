<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is tutor
require_tutor();

// Get application ID
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($application_id <= 0) {
    header("Location: applications.php");
    exit();
}

// Get tutor info
$tutor_id = 0;
$sql = "SELECT * FROM tutors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $tutor = mysqli_fetch_assoc($result);
    $tutor_id = $tutor['id'];
} else {
    // Redirect if tutor profile not found
    header("Location: profile.php");
    exit();
}

// Get application details
$sql = "SELECT a.*, j.title as job_title, j.subject, j.grade_level, j.location, j.time, j.price, j.description 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.id = ? AND a.tutor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $application_id, $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: applications.php");
    exit();
}

$application = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="job-details">
                <div class="job-header">
                    <h1>Application for: <?php echo htmlspecialchars($application['job_title']); ?></h1>
                    <div class="job-meta">
                        <span class="job-date">Applied on <?php echo format_date($application['created_at']); ?></span>
                        <span class="status-badge status-<?php echo $application['status']; ?>">
                            <?php echo ucfirst($application['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="job-info">
                    <div class="job-info-item">
                        <h3>Subject</h3>
                        <p><?php echo htmlspecialchars($application['subject']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Grade Level</h3>
                        <p><?php echo htmlspecialchars($application['grade_level']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Location</h3>
                        <p><?php echo htmlspecialchars($application['location']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Time/Schedule</h3>
                        <p><?php echo htmlspecialchars($application['time']); ?></p>
                    </div>
                    <div class="job-info-item">
                        <h3>Hourly Rate</h3>
                        <p>$<?php echo htmlspecialchars($application['price']); ?>/hr</p>
                    </div>
                </div>
                
                <div class="job-description">
                    <h2>Job Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
                </div>
                
                <div class="application-details">
                    <h2>Your Application</h2>
                    <div class="application-message">
                        <h3>Your Message</h3>
                        <p><?php echo nl2br(htmlspecialchars($application['message'])); ?></p>
                    </div>
                    
                    <?php if (!empty($application['cv_path'])): ?>
                    <div class="application-cv">
                        <h3>Your CV</h3>
                        <p><a href="<?php echo htmlspecialchars($application['cv_path']); ?>" target="_blank">View CV</a></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="back-link">
                    <a href="applications.php" class="btn btn-secondary">Back to Applications</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
