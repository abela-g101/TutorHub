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

// Check if tutor exists and get user_id
$sql = "SELECT t.id, t.user_id, u.username 
        FROM tutors t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $tutor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: tutors.php");
    exit();
}

$tutor = mysqli_fetch_assoc($result);
$user_id = $tutor['user_id'];
$username = $tutor['username'];

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
        <title>Delete Tutor - TutorHub</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <?php include_once '../includes/header.php'; ?>
        
        <main>
            <div class="container">
                <div class="form-container" style="max-width: 600px;">
                    <h2>Delete Tutor: <?php echo htmlspecialchars($username); ?></h2>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Are you sure you want to delete this tutor? This will also delete their user account, applications, and all associated data. This action cannot be undone.
                    </div>
                    
                    <div class="form-submit">
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $tutor_id . "&confirm=yes"); ?>" class="btn btn-danger">Yes, Delete Tutor</a>
                        <a href="tutors.php" class="btn btn-secondary">Cancel</a>
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

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Delete applications
    $sql = "DELETE FROM applications WHERE tutor_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $tutor_id);
    mysqli_stmt_execute($stmt);
    
    // Delete tutor
    $sql = "DELETE FROM tutors WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $tutor_id);
    mysqli_stmt_execute($stmt);
    
    // Delete user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Set success message
    $_SESSION['success_message'] = "Tutor deleted successfully!";
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    // Set error message
    $_SESSION['error_message'] = "Error deleting tutor: " . $e->getMessage();
}

// Redirect back to tutors page
header("Location: tutors.php");
exit();
?>
