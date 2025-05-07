<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get application ID
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($application_id <= 0) {
    header("Location: applications.php");
    exit();
}

// Get application details
$sql = "SELECT a.*, j.title as job_title, j.subject, j.grade_level, j.location, j.time, j.price, j.description,
        t.first_name, t.last_name, t.bio, t.qualifications, t.phone, u.email, u.username
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN tutors t ON a.tutor_id = t.id
        JOIN users u ON t.user_id = u.id
        WHERE a.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $application_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: applications.php");
    exit();
}

$application = mysqli_fetch_assoc($result);

// Process status update
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
    $status = sanitize_input($_POST['status']);
    
    if ($status === 'approved' || $status === 'rejected') {
        $sql = "UPDATE applications SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $application_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $application['status'] = $status;
            $success = "Application status updated to " . ucfirst($status);
        } else {
            $error = "Error updating application status: " . mysqli_error($conn);
        }
    }
}
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
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
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
                
                <div class="application-details">
                    <h2>Tutor Information</h2>
                    <div class="tutor-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></p>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($application['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($application['phone']); ?></p>
                        
                        <?php if (!empty($application['bio'])): ?>
                            <h3>Bio</h3>
                            <p><?php echo nl2br(htmlspecialchars($application['bio'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($application['qualifications'])): ?>
                            <h3>Qualifications</h3>
                            <p><?php echo nl2br(htmlspecialchars($application['qualifications'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="job-info">
                    <h2>Job Details</h2>
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
                
                <div class="application-message">
                    <h2>Application Message</h2>
                    <p><?php echo nl2br(htmlspecialchars($application['message'])); ?></p>
                    
                    <?php if (!empty($application['cv_path'])): ?>
                    <div class="application-cv">
                        <h3>CV</h3>
                        <p><a href="<?php echo htmlspecialchars($application['cv_path']); ?>" target="_blank">View CV</a></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="application-actions">
                    <h2>Update Application Status</h2>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $application_id); ?>" class="status-form">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending" <?php echo ($application['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo ($application['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo ($application['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="form-submit">
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
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
