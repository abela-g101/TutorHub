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

// Check if job exists
$sql = "SELECT id FROM jobs WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 0) {
    header("Location: jobs.php");
    exit();
}

// Process deletion
$error = '';
$success = '';

// Check if confirmation is required
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    // Show confirmation page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Job - TutorHub</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <?php include_once '../includes/header.php'; ?>
        
        <main>
            <div class="container">
                <div class="form-container" style="max-width: 600px;">
                    <h2>Delete Job</h2>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Are you sure you want to delete this job? This action cannot be undone.
                    </div>
                    
                    <div class="form-submit">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $job_id . "&confirm=yes"); ?>" class="btn btn-danger">Yes, Delete Job</a>
                        <a href="jobs.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </main>
        
        <?php include_once '../includes/footer.php'; ?>
        <script src="../assets/js/main.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Delete job
$sql = "DELETE FROM jobs WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $job_id);

if (mysqli_stmt_execute($stmt)) {
    // Delete related applications
    $sql = "DELETE FROM applications WHERE job_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    mysqli_stmt_execute($stmt);
    
    // Set success message
    $_SESSION['success_message'] = "Job deleted successfully!";
} else {
    // Set error message
    $_SESSION['error_message'] = "Error deleting job: " . mysqli_error($conn);
}

// Redirect back to jobs page
header("Location: jobs.php");
exit();
?>
