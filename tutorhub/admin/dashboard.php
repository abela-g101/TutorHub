<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get stats
$stats = [
    'total_jobs' => 0,
    'active_jobs' => 0,
    'total_tutors' => 0,
    'total_applications' => 0
];

// Total jobs
$sql = "SELECT COUNT(*) as count FROM jobs";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_jobs'] = $row['count'];
}

// Active jobs
$sql = "SELECT COUNT(*) as count FROM jobs WHERE status = 'active'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['active_jobs'] = $row['count'];
}

// Total tutors
$sql = "SELECT COUNT(*) as count FROM users WHERE role = 'tutor'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_tutors'] = $row['count'];
}

// Total applications
$sql = "SELECT COUNT(*) as count FROM applications";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $stats['total_applications'] = $row['count'];
}

// Get recent jobs
$sql = "SELECT * FROM jobs ORDER BY created_at DESC LIMIT 5";
$recent_jobs = mysqli_query($conn, $sql);

// Get recent applications
$sql = "SELECT a.*, j.title as job_title, u.username as tutor_name 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN tutors t ON a.tutor_id = t.id 
        JOIN users u ON t.user_id = u.id
        ORDER BY a.created_at DESC LIMIT 5";
$recent_applications = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <a href="jobs-add.php" class="btn btn-primary">Add New Job</a>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3><?php echo $stats['total_jobs']; ?></h3>
                    <p>Total Jobs</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['active_jobs']; ?></h3>
                    <p>Active Jobs</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_tutors']; ?></h3>
                    <p>Registered Tutors</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_applications']; ?></h3>
                    <p>Total Applications</p>
                </div>
            </div>
            
            <div class="dashboard-content">
                <h2>Recent Jobs</h2>
                <?php if (mysqli_num_rows($recent_jobs) > 0): ?>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Grade Level</th>
                                <th>Status</th>
                                <th>Date Posted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($job = mysqli_fetch_assoc($recent_jobs)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($job['grade_level']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo get_status_badge_class($job['status']); ?>">
                                            <?php echo ucfirst($job['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_date($job['created_at']); ?></td>
                                    <td class="action-buttons">
                                        <a href="jobs-view.php?id=<?php echo $job['id']; ?>" class="btn-view">View</a>
                                        <a href="jobs-edit.php?id=<?php echo $job['id']; ?>" class="btn-edit">Edit</a>
                                        <a href="jobs-delete.php?id=<?php echo $job['id']; ?>" class="btn-delete delete-confirm">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div class="view-all" style="margin-top: 20px;">
                        <a href="jobs.php" class="btn btn-secondary">View All Jobs</a>
                    </div>
                <?php else: ?>
                    <p>No jobs found. <a href="jobs-add.php">Add a new job</a>.</p>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-content" style="margin-top: 30px;">
                <h2>Recent Applications</h2>
                <?php if (mysqli_num_rows($recent_applications) > 0): ?>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Tutor</th>
                                <th>Status</th>
                                <th>Date Applied</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($application = mysqli_fetch_assoc($recent_applications)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($application['tutor_name']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo get_status_badge_class($application['status']); ?>">
                                            <?php echo ucfirst($application['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_date($application['created_at']); ?></td>
                                    <td class="action-buttons">
                                        <a href="applications-view.php?id=<?php echo $application['id']; ?>" class="btn-view">View</a>
                                        <a href="applications-update.php?id=<?php echo $application['id']; ?>&status=approved" class="btn-edit">Approve</a>
                                        <a href="applications-update.php?id=<?php echo $application['id']; ?>&status=rejected" class="btn-delete">Reject</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div class="view-all" style="margin-top: 20px;">
                        <a href="applications.php" class="btn btn-secondary">View All Applications</a>
                    </div>
                <?php else: ?>
                    <p>No applications found.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
