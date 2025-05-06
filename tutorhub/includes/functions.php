<?php
// Security function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to check if user is tutor
function is_tutor() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'tutor';
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit();
    }
}

// Redirect if not admin
function require_admin() {
    require_login();
    if (!is_admin()) {
        header("Location: ../index.php");
        exit();
    }
}

// Redirect if not tutor
function require_tutor() {
    require_login();
    if (!is_tutor()) {
        header("Location: ../index.php");
        exit();
    }
}

// Generate a secure password hash
function generate_password_hash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password against hash
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Get user data by ID
function get_user_by_id($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

// Get tutor data by user ID
function get_tutor_by_user_id($conn, $user_id) {
    $sql = "SELECT * FROM tutors WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

// Format date for display
function format_date($date_string) {
    $date = new DateTime($date_string);
    return $date->format('F j, Y');
}

// Time elapsed string (e.g., "2 days ago")

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate weeks separately
    $weeks = floor($diff->d / 7);
    $diff->d -= $weeks * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    $result = [];
    foreach ($string as $k => $v) {
        if ($k === 'w' && $weeks) {
            $result[] = $weeks . ' ' . $v . ($weeks > 1 ? 's' : '');
        } elseif ($k !== 'w' && $diff->$k) {
            $result[] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        }
    }

    if (!$full) {
        $result = array_slice($result, 0, 1);
    }

    return $result ? implode(', ', $result) . ' ago' : 'just now';
}

// Generate random string
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Get status badge class
function get_status_badge_class($status) {
    $classes = [
        'pending' => 'status-pending',
        'approved' => 'status-approved',
        'rejected' => 'status-rejected',
        'active' => 'status-active',
        'inactive' => 'status-inactive',
        'filled' => 'status-filled'
    ];
    
    return isset($classes[$status]) ? $classes[$status] : '';
}

// Get application status badge HTML
function get_status_badge($status) {
    $class = get_status_badge_class($status);
    return '<span class="status-badge ' . $class . '">' . ucfirst($status) . '</span>';
}

// Truncate text to a certain length
function truncate_text($text, $length = 100, $append = '...') {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= $append;
    }
    return $text;
}

// Get count of unread notifications for a user
function get_unread_notifications_count($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Create a notification
function create_notification($conn, $user_id, $message, $link = '') {
    $sql = "INSERT INTO notifications (user_id, message, link, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $message, $link);
    return mysqli_stmt_execute($stmt);
}

// Check if a tutor has already applied to a job
function has_applied_to_job($conn, $tutor_id, $job_id) {
    $sql = "SELECT id FROM applications WHERE tutor_id = ? AND job_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $tutor_id, $job_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

// Get application status for a tutor and job
function get_application_status($conn, $tutor_id, $job_id) {
    $sql = "SELECT status FROM applications WHERE tutor_id = ? AND job_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $tutor_id, $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['status'];
    }
    
    return false;
}
?>
