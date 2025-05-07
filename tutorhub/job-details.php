<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/functions.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['title']); ?> - TutorHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
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
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="login-prompt">
                            <h3>Interested in this tutoring opportunity?</h3>
                            <p>You need to be logged in as a tutor to apply for this job.</p>
                            <div class="prompt-actions">
                                <a href="login.php" class="btn btn-primary">Login</a>
                                <a href="register.php" class="btn btn-secondary">Register as Tutor</a>
                            </div>
                        </div>
                    <?php elseif ($_SESSION['role'] === 'tutor'): ?>
                        <a href="tutor/job-view.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">Apply for this Job</a>
                    <?php endif; ?>
                </div>
                
                <div class="back-link">
                    <a href="jobs.php" class="btn btn-secondary">Back to Jobs</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
