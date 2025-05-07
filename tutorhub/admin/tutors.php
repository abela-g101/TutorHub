<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get all tutors
$sql = "SELECT t.*, u.username, u.email, u.created_at as registration_date
        FROM tutors t
        JOIN users u ON t.user_id = u.id
        WHERE u.role = 'tutor'
        ORDER BY t.last_name, t.first_name";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tutors - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Manage Tutors</h1>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
            
            <div class="dashboard-content">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($tutor = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($tutor['username']); ?></td>
                                    <td><?php echo htmlspecialchars($tutor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($tutor['phone'] ?: 'Not provided'); ?></td>
                                    <td><?php echo format_date($tutor['registration_date']); ?></td>
                                    <td class="action-buttons">
                                        <a href="tutors-view.php?id=<?php echo $tutor['id']; ?>" class="btn-view">View</a>
                                        <a href="tutors-edit.php?id=<?php echo $tutor['id']; ?>" class="btn-edit">Edit</a>
                                        <a href="tutors-delete.php?id=<?php echo $tutor['id']; ?>" class="btn-delete delete-confirm">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tutors found.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
