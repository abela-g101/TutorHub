<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get all applications
$sql = "SELECT a.*, j.title as job_title, t.first_name, t.last_name, u.username as tutor_username
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN tutors t ON a.tutor_id = t.id
        JOIN users u ON t.user_id = u.id
        ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Manage Applications</h1>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
            
            <div class="dashboard-content">
                <?php if (mysqli_num_rows($result) > 0): ?>
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
                            <?php while ($application = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                    <td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?> (<?php echo htmlspecialchars($application['tutor_username']); ?>)</td>
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
