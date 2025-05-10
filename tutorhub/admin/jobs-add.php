<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

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
        // Insert job
        $sql = "INSERT INTO jobs (title, subject, grade_level, location, time, price, description, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssdss", $title, $subject, $grade_level, $location, $time, $price, $description, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Job added successfully!";
        } else {
            $error = "Error adding job: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Job - TutorHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    
    <main>
        <div class="container">
            <div class="form-container" style="max-width: 800px;">
                <h2>Add New Tutoring Job</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <p><a href="jobs.php">View all jobs</a></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation">
                    <div class="form-group">
                        <label for="title">Job Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="grade_level">Grade/Year Level</label>
                        <input type="text" class="form-control" id="grade_level" name="grade_level" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="time">Time/Schedule</label>
                        <input type="text" class="form-control" id="time" name="time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Hourly Rate ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Job Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="filled">Filled</option>
                        </select>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary">Add Job</button>
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
