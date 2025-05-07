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

// Get all applications for this tutor
$sql = "SELECT a.*, j.title as job_title, j.subject, j.grade_level 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.tutor_id = ? 
        ORDER BY a.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$applications = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>My Applications</h1>
                <a href="jobs.php" class="btn btn-primary">Browse Jobs</a>
            </div>
            
            <div class="dashboard-content">
                <?php if (mysqli_num_rows($applications) > 0): ?>
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
                            <?php while ($application = mysqli_fetch_assoc($applications)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($application['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($application['grade_level']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $application['status']; ?>">
                                            <?php echo ucfirst($application['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_date($application['created_at']); ?></td>
                                    <td class="action-buttons">
                                        <a href="application-view.php?id=<?php echo $application['id']; ?>" class="btn-view">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You haven't applied to any jobs yet. <a href="jobs.php">Browse available jobs</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
