<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change in production
$password = ""; // Change in production
$dbname = "tutorhub";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to ensure proper handling of special characters
mysqli_set_charset($conn, "utf8mb4");
?>