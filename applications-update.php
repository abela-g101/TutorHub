<?php
session_start();
include_once '../includes/db.php';
include_once '../includes/functions.php';

// Check if user is admin
require_admin();

// Get application ID and status
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';

if ($application_id <= 0 || !in_array($status, ['approved', 'rejected', 'pending'])) {
    header("Location: applications.php");
    exit();
}

// Update application status
$sql = "UPDATE applications SET status = ?, updated_at = NOW() WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $application_id);

if (mysqli_stmt_execute($stmt)) {
    // Success
    $_SESSION['success_message'] = "Application status updated to " . ucfirst($status);
} else {
    // Error
    $_SESSION['error_message'] = "Error updating application status: " . mysqli_error($conn);
}

// Redirect back to applications page
header("Location: applications.php");
exit();
?>
