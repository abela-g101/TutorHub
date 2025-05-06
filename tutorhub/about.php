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
    <title>About Us - TutorHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <main>
        <section class="hero">
            <div class="container">
                <h1>About TutorHub</h1>
                <p>Learn about our mission to connect qualified tutors with students</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2>Our Story</h2>
                        <p>TutorHub was founded in 2023 with a simple mission: to make quality education accessible to all students by connecting them with qualified tutors.</p>
                        <p>What started as a small community of passionate educators has grown into a platform that serves thousands of students and tutors across the country.</p>
                        <p>We believe that personalized learning is the key to academic success, and our platform makes it easy for students to find the right tutor for their specific needs.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section bg-light">
            <div class="container">
                <div class="section-header">
                    <h2>Our Mission</h2>
                    <p>We're dedicated to improving education through personalized tutoring</p>
                </div>
            </div>
        </section>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
