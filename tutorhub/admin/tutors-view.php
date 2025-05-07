<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get tutor ID
$tutor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tutor_id <= 0) {
    header("Location: tutors.php");
    exit();
}

// Get tutor details
$sql = "SELECT t.*, u.username, u.email, u.created_at as registration_date
        FROM tutors t
        JOIN users u ON t.user_id = u.id
        WHERE t.id = ? AND u.role = 'tutor'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: tutors.php");
    exit();
}

$tutor = mysqli_fetch_assoc($result);

// Get tutor's applications
$sql = "SELECT a.*, j.title as job_title
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
    <title>Tutor Profile - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="job-details">
                <div class="job-header">
                    <h1>Tutor Profile: <?php echo htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']); ?></h1>
                    <div class="job-meta">
                        <span class="job-date">Registered on <?php echo format_date($tutor['registration_date']); ?></span>
                    </div>
                </div>
                
                <div class="tutor-info">
                    <div class="tutor-info-item">
                        <h3>Username</h3>
                        <p><?php echo htmlspecialchars($tutor['username']); ?></p>
                    </div>
                    <div class="tutor-info-item">
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($tutor['email']); ?></p>
                    </div>
                    <div class="tutor-info-item">
                        <h3>Phone</h3>
                        <p><?php echo htmlspecialchars($tutor['phone'] ?: 'Not provided'); ?></p>
                    </div>
                    
                    <?php if (!empty($tutor['profile_photo'])): ?>
                    <div class="tutor-info-item">
                        <h3>Profile Photo</h3>
                        <div class="profile-photo">
                            <img src="<?php echo htmlspecialchars($tutor['profile_photo']); ?>" alt="Profile Photo">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($tutor['bio'])): ?>
                <div class="tutor-bio">
                    <h2>Bio</h2>
                    <p><?php echo nl2br(htmlspecialchars($tutor['bio'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($tutor['qualifications'])): ?>
                <div class="tutor-qualifications">
                    <h2>Qualifications</h2>
                    <p><?php echo nl2br(htmlspecialchars($tutor['qualifications'])); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="tutor-applications">
                    <h2>Applications</h2>
                    <?php if (mysqli_num_rows($applications) > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Status</th>
                                    <th>Date Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($application = mysqli_fetch_assoc($applications)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $application['status']; ?>">
                                                <?php echo ucfirst($application['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($application['created_at']); ?></td>
                                        <td class="action-buttons">
                                            <a href="applications-view.php?id=<?php echo $application['id']; ?>" class="btn-view">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>This tutor has not applied to any jobs yet.</p>
                    <?php endif; ?>
                </div>
                
                <div class="back-link">
                    <a href="tutors.php" class="btn btn-secondary">Back to Tutors</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
