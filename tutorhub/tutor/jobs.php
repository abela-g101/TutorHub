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
}

// Initialize search parameters
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$subject = isset($_GET['subject']) ? sanitize_input($_GET['subject']) : '';
$grade_level = isset($_GET['grade_level']) ? sanitize_input($_GET['grade_level']) : '';
$location = isset($_GET['location']) ? sanitize_input($_GET['location']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 1000;

// Build query
$sql = "SELECT * FROM jobs WHERE status = 'active'";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR subject LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($subject)) {
    $sql .= " AND subject = ?";
    $params[] = $subject;
    $types .= "s";
}

if (!empty($grade_level)) {
    $sql .= " AND grade_level = ?";
    $params[] = $grade_level;
    $types .= "s";
}

if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $location_param = "%$location%";
    $params[] = $location_param;
    $types .= "s";
}

$sql .= " AND price BETWEEN ? AND ?";
$params[] = $min_price;
$params[] = $max_price;
$types .= "dd";

$sql .= " ORDER BY created_at DESC";

// Prepare and execute query
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$jobs = mysqli_stmt_get_result($stmt);

// Get distinct subjects and grade levels for filters
$subjects_query = "SELECT DISTINCT subject FROM jobs WHERE status = 'active' ORDER BY subject";
$subjects_result = mysqli_query($conn, $subjects_query);

$grade_levels_query = "SELECT DISTINCT grade_level FROM jobs WHERE status = 'active' ORDER BY grade_level";
$grade_levels_result = mysqli_query($conn, $grade_levels_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Jobs - TutorHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-briefcase"></i> Browse Tutoring Jobs</h1>
                <p>Find the perfect tutoring opportunity that matches your skills and preferences</p>
            </div>
            
            <div class="search-filters">
                <form id="jobSearchForm" method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="search-row">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search by keyword, subject, or description..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="search-button">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                    
                    
                    <div class="filter-row" id="advancedFilters" style="display: none;">
                        <div class="filter-group">
                            <label for="subject"><i class="fas fa-book"></i> Subject</label>
                            <select name="subject" id="subject">
                                <option value="">All Subjects</option>
                                <?php while ($subject_row = mysqli_fetch_assoc($subjects_result)): ?>
                                    <option value="<?php echo htmlspecialchars($subject_row['subject']); ?>" <?php echo ($subject == $subject_row['subject']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subject_row['subject']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="grade_level"><i class="fas fa-graduation-cap"></i> Grade Level</label>
                            <select name="grade_level" id="grade_level">
                                <option value="">All Grade Levels</option>
                                <?php while ($grade_row = mysqli_fetch_assoc($grade_levels_result)): ?>
                                    <option value="<?php echo htmlspecialchars($grade_row['grade_level']); ?>" <?php echo ($grade_level == $grade_row['grade_level']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($grade_row['grade_level']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="location"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" name="location" id="location" placeholder="Enter location" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label><i class="fas fa-dollar-sign"></i> Hourly Rate</label>
                            <div class="price-range">
                                <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" min="0">
                                <span>to</span>
                                <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price < 1000 ? $max_price : ''; ?>" min="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="jobs-container" id="jobsContainer">
                <?php if (mysqli_num_rows($jobs) > 0): ?>
                    <div class="jobs-count">
                        <p>Found <?php echo mysqli_num_rows($jobs); ?> tutoring opportunities</p>
                    </div>
                    
                    <div class="jobs-grid">
                        <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                            <div class="job-card animate-on-scroll">
                                <div class="job-card-header">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                </div>
                                <div class="job-card-padding">
                                <p><i class="fas fa-book"></i> <strong>Subject:</strong> <?php echo htmlspecialchars($job['subject']); ?></p>
                                <p><i class="fas fa-graduation-cap"></i> <strong>Grade Level:</strong> <?php echo htmlspecialchars($job['grade_level']); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                                <p><i class="fas fa-clock"></i> <strong>Schedule:</strong> <?php echo htmlspecialchars($job['time']); ?></p>
                                <p><i class="fas fa-dollar-sign"></i> <strong>Rate:</strong> $<?php echo htmlspecialchars($job['price']); ?>/hr</p>
                                <p class="job-date"><i class="fas fa-calendar-alt"></i> Posted <?php echo time_elapsed_string($job['created_at']); ?></p>
                                <a href="job-view.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                           
                                </div>
                                 </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-jobs">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>No tutoring jobs found</h3>
                        <p>Try adjusting your search criteria or check back later for new opportunities.</p>
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-primary">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
    <script>
        // Toggle advanced filters
        document.getElementById('filterToggle').addEventListener('click', function() {
            const advancedFilters = document.getElementById('advancedFilters');
            const icon = this.querySelector('.fa-chevron-down, .fa-chevron-up');
            
            if (advancedFilters.style.display === 'none') {
                advancedFilters.style.display = 'grid';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                advancedFilters.style.display = 'none';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
    </script>
</body>
</html>
