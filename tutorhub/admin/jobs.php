<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get all jobs
$sql = "SELECT * FROM jobs ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Manage Jobs</h1>
                <a href="jobs-add.php" class="btn btn-primary">Add New Job</a>
            </div>
            
            <div class="dashboard-content">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Grade Level</th>
                                <th>Location</th>
                                <th>Rate</th>
                                <th>Status</th>
                                <th>Date Posted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($job = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($job['grade_level']); ?></td>
                                    <td><?php echo htmlspecialchars($job['location']); ?></td>
                                    <td>$<?php echo htmlspecialchars($job['price']); ?>/hr</td>
                                    <td>
                                        <span class="status-badge <?php echo get_status_badge_class($job['status']); ?>">
                                            <?php echo ucfirst($job['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo format_date($job['created_at']); ?></td>
                                    <td class="action-buttons">
                                       
                                        <a href="jobs-edit.php?id=<?php echo $job['id']; ?>" class="btn-edit">Edit</a>
                                        <a href="jobs-delete.php?id=<?php echo $job['id']; ?>" class="btn-delete delete-confirm">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No jobs found. <a href="jobs-add.php">Add a new job</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
