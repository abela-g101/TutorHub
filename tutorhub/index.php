<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TutorHub - Find Local Tutoring Jobs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <main>
        <section class="hero">
            <div class="container">
                <h1>Find the Perfect Tutoring Opportunity</h1>
                <p>Connect with students in your area and share your knowledge while earning competitive rates</p>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="cta-buttons">
                        <a href="register.php" class="btn btn-primary btn-large">Register as Tutor</a>
                        <a href="login.php" class="btn btn-secondary btn-large">Login</a>
                    </div>
                <?php else: ?>
                    <div class="cta-buttons">
                        <a href="<?php echo ($_SESSION['role'] === 'admin') ? 'admin/dashboard.php' : 'tutor/jobs.php'; ?>" class="btn btn-primary btn-large">
                            <?php echo ($_SESSION['role'] === 'admin') ? 'Go to Admin Dashboard' : 'Browse Jobs'; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="featured-jobs">
            <div class="container">
                <div class="section-header">
                    <h2>Featured Tutoring Opportunities</h2>
                    <p>Discover the latest tutoring positions available in your area</p>
                </div>
                
                <div class="jobs-grid">
                    <?php
                    $sql = "SELECT * FROM jobs WHERE status = 'active' ORDER BY created_at DESC LIMIT 6";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="job-card">';
                            echo '<div class="job-card-header">';
                            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                            echo '</div>';
                            echo '<p><i class="fas fa-book"></i> <strong>Subject:</strong> ' . htmlspecialchars($row['subject']) . '</p>';
                            echo '<p><i class="fas fa-graduation-cap"></i> <strong>Grade Level:</strong> ' . htmlspecialchars($row['grade_level']) . '</p>';
                            echo '<p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> ' . htmlspecialchars($row['location']) . '</p>';
                            echo '<p><i class="fas fa-clock"></i> <strong>Schedule:</strong> ' . htmlspecialchars($row['time']) . '</p>';
                            echo '<p><i class="fas fa-dollar-sign"></i> <strong>Rate:</strong> $' . htmlspecialchars($row['price']) . '/hr</p>';

                            if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'tutor') {
                                echo '<a href="tutor/job-view.php?id=' . $row['id'] . '" class="btn btn-primary">View Details</a>';
                            } else {
                        
                                echo '<a href="job-details.php?id=' . $row['id'] . '" class="btn btn-primary">View Details</a>';
                            }
                            
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-jobs">';
                        echo '<p>No tutoring jobs available at the moment. Please check back later.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <div class="view-all">
                    <a href="jobs.php" class="btn btn-secondary btn-large">View All Jobs</a>
                </div>
            </div>
        </section>
        
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
