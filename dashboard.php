<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is tutor
require_tutor();

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

// Get stats
$stats = [
    'total_applications' => 0,
    'pending_applications' => 0,
    'approved_applications' => 0
];

// Total applications
$sql = "SELECT COUNT(*) as count FROM applications WHERE tutor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_applications'] = $row['count'];
}

// Pending applications
$sql = "SELECT COUNT(*) as count FROM applications WHERE tutor_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['pending_applications'] = $row['count'];
}

// Approved applications
$sql = "SELECT COUNT(*) as count FROM applications WHERE tutor_id = ? AND status = 'approved'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['approved_applications'] = $row['count'];
}

// Get recent applications
$sql = "SELECT a.*, j.title as job_title, j.subject, j.grade_level 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.tutor_id = ? 
        ORDER BY a.created_at DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$recent_applications = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1><i class="fas fa-tachometer-alt"></i> Welcome, <?php echo htmlspecialchars($tutor['first_name']); ?>!</h1>
                <div class="dashboard-actions">
                    <a href="profile.php" class="btn btn-secondary"><i class="fas fa-user-edit"></i> Edit Profile</a>
                    <a href="jobs.php" class="btn btn-primary"><i class="fas fa-search"></i> Find Jobs</a>
                </div>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-file-alt fa-3x" style="color: var(--primary-color); margin-bottom: 10px;"></i>
                    <h3><?php echo $stats['total_applications']; ?></h3>
                    <p>Total Applications</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock fa-3x" style="color: var(--warning-color); margin-bottom: 10px;"></i>
                    <h3><?php echo $stats['pending_applications']; ?></h3>
                    <p>Pending Applications</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle fa-3x" style="color: var(--success-color); margin-bottom: 10px;"></i>
                    <h3><?php echo $stats['approved_applications']; ?></h3>
                    <p>Approved Applications</p>
                </div>
            </div>
            
            <div class="dashboard-content">
                <h2><i class="fas fa-file-alt"></i> Your Recent Applications</h2>
                <?php if (mysqli_num_rows($recent_applications) > 0): ?>
                    <div class="table-responsive">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Subject</th>
                                    <th>Grade Level</th>
                                    <th>Status</th>
                                    <th>Date Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($application = mysqli_fetch_assoc($recent_applications)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                        <td><?php echo htmlspecialchars($application['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($application['grade_level']); ?></td>
                                        <td>
                                            <span class="badge <?php echo get_status_badge_class($application['status']); ?>">
                                                <?php echo ucfirst($application['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($application['created_at']); ?></td>
                                        <td class="action-buttons">
                                            <a href="application-view.php?id=<?php echo $application['id']; ?>" class="btn-view"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="view-all" style="margin-top: 20px;">
                        <a href="applications.php" class="btn btn-secondary">View All Applications <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-info-circle fa-3x"></i>
                        <p>You haven't applied to any jobs yet. <a href="jobs.php">Browse available jobs</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-content">
                <h2><i class="fas fa-star"></i> Recommended Jobs</h2>
                <?php
                // Get recommended jobs (active jobs that tutor hasn't applied to)
                $sql = "SELECT j.* FROM jobs j 
                        WHERE j.status = 'active' 
                        AND j.id NOT IN (
                            SELECT job_id FROM applications WHERE tutor_id = ?
                        )
                        ORDER BY j.created_at DESC LIMIT 3";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $tutor_id);
                mysqli_stmt_execute($stmt);
                $recommended_jobs = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($recommended_jobs) > 0):
                ?>
                    <div class="jobs-grid">
                        <?php while ($job = mysqli_fetch_assoc($recommended_jobs)): ?>
                            <div class="job-card">
                                <div class="job-card-header">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <span class="job-price">$<?php echo htmlspecialchars($job['price']); ?>/hr</span>
                                </div>
                                <div class="job-card-body">
    
                                    <p class="job-detail"><i class="fas fa-book"></i> <strong>Subject:</strong> <?php echo htmlspecialchars($job['subject']); ?></p>
                                    <p class="job-detail"><i class="fas fa-graduation-cap"></i> <strong>Grade Level:</strong> <?php echo htmlspecialchars($job['grade_level']); ?></p>
                                    <p class="job-detail"><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                                    <p class="job-detail"><i class="fas fa-clock"></i> <strong>Schedule:</strong> <?php echo htmlspecialchars($job['time']); ?></p>
                                  
                                    
                                </div>
                                <div class="job-card-footer">
                                    <a href="job-view.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="view-all" style="margin-top: 20px;">
                        <a href="jobs.php" class="btn btn-secondary">Browse All Jobs <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-info-circle fa-3x"></i>
                        <p>No recommended jobs available at the moment. <a href="jobs.php">Browse all available jobs</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>