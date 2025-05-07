<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/functions.php';

$success = '';
$error = '';

// Process contact form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // In a real application, you would send an email here
        // For now, we'll just show a success message
        $success = "Thank you for your message! We will get back to you soon.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TutorHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <main>
        <section class="hero" style="background: linear-gradient(rgba(37, 99, 235, 0.9), rgba(29, 78, 216, 0.9)), url('assets/img/contact-hero.jpg');">
            <div class="container">
                <div class="hero-content">
                    <h1>Contact Us</h1>
                    <p>Get in touch with our team for any questions or support</p>
                </div>
            </div>
        </section>
        
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <h2>Get In Touch</h2>
                        <p class="mb-4">Have questions about TutorHub? Want to learn more about our platform? Fill out the form and we'll get back to you as soon as possible.</p>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Your Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Your Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-tag"></i> Subject
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message" class="form-label">
                                    <i class="fas fa-comment"></i> Message
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <div class="form-submit">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="contact-info mt-5 mt-lg-0">
                            <h2>Contact Information</h2>
                            <p>Feel free to reach out to us using any of the contact methods below.</p>
                            
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h4>Address</h4>
                                    <p>123 Education Street, Learning City, ED 12345</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <h4>Phone</h4>
                                    <p>(123) 456-7890</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h4>Email</h4>
                                    <p>info@tutorhub.com</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h4>Business Hours</h4>
                                    <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                                    <p>Saturday: 10:00 AM - 2:00 PM</p>
                                    <p>Sunday: Closed</p>
                                </div>
                            </div>
                            
                            <div class="social-links mt-4">
                                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section bg-light">
            <div class="container">
                <div class="section-header">
                    <h2>Frequently Asked Questions</h2>
                    <p>Find answers to common questions about TutorHub</p>
                </div>
                
                <div class="faq-container mt-5">
                    <div class="faq-item">
                        <h3>How do I register as a tutor?</h3>
                        <p>To register as a tutor, click on the "Register" button in the navigation menu and fill out the registration form. Once your account is created, you can complete your profile and start applying for tutoring jobs.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>How do I find tutoring jobs?</h3>
                        <p>After logging in, navigate to the "Browse Jobs" section where you can search and filter available tutoring opportunities based on subject, grade level, location, and hourly rate.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>How do I apply for a tutoring job?</h3>
                        <p>When you find a job that interests you, click on "View Details" to see more information. If you're interested, click the "Apply" button and submit your application with a personalized message.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>How do I get paid for tutoring?</h3>
                        <p>Payment arrangements are typically made directly between you and the student or their parents after your application has been approved. TutorHub serves as a platform to connect tutors with students.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
