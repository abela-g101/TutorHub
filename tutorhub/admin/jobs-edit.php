<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get job ID
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    header("Location: jobs.php");
    exit();
}

// Get job details
$sql = "SELECT * FROM jobs WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: jobs.php");
    exit();
}

$job = mysqli_fetch_assoc($result);

$error = '';
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_input($_POST['title']);
    $subject = sanitize_input($_POST['subject']);
    $grade_level = sanitize_input($_POST['grade_level']);
    $location = sanitize_input($_POST['location']);
    $time = sanitize_input($_POST['time']);
    $price = sanitize_input($_POST['price']);
    $description = sanitize_input($_POST['description']);
    $status = sanitize_input($_POST['status']);
    
    // Validate input
    if (empty($title) || empty($subject) || empty($grade_level) || empty($location) || empty($time) || empty($price) || empty($description)) {
        $error = "All fields are required";
    } else {
        // Update job
        $sql = "UPDATE jobs SET title = ?, subject = ?, grade_level = ?, location = ?, time = ?, price = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssdssi", $title, $subject, $grade_level, $location, $time, $price, $description, $status, $job_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Job updated successfully!";
            
            // Update job data
            $job['title'] = $title;
            $job['subject'] = $subject;
            $job['grade_level'] = $grade_level;
            $job['location'] = $location;
            $job['time'] = $time;
            $job['price'] = $price;
            $job['description'] = $description;
            $job['status'] = $status;
        } else {
            $error = "Error updating job: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="form-container" style="max-width: 800px;">
                <h2>Edit Tutoring Job</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $job_id); ?>" class="needs-validation">
                    <div class="form-group">
                        <label for="title">Job Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($job['subject']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="grade_level">Grade/Year Level</label>
                        <input type="text" class="form-control" id="grade_level" name="grade_level" value="<?php echo htmlspecialchars($job['grade_level']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="time">Time/Schedule</label>
                        <input type="text" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($job['time']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Hourly Rate ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($job['price']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Job Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" <?php echo ($job['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($job['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            <option value="filled" <?php echo ($job['status'] === 'filled') ? 'selected' : ''; ?>>Filled</option>
                        </select>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary">Update Job</button>
                        <a href="jobs.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <?php include_once '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
