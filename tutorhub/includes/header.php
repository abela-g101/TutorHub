<?php
// Define a function to determine the base path
function getBasePath() {
    $currentPath = $_SERVER['PHP_SELF'];
    if (strpos($currentPath, '/admin/') !== false) {
        return '../';
    } elseif (strpos($currentPath, '/tutor/') !== false) {
        return '../';
    } else {
        return '';
    }
}
?>

<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="<?php echo getBasePath(); ?>index.php" class="navbar-brand">
                <i class="fas fa-chalkboard-teacher"></i>
                Tutor<span>Hub</span>
            </a>
            
            <button class="navbar-toggler" id="menuToggle" aria-expanded="false" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            
            <ul class="navbar-nav" id="navMenu" aria-hidden="true">
                <li><a href="<?php echo getBasePath(); ?>index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo getBasePath(); ?>jobs.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'jobs.php') ? 'active' : ''; ?>">Browse Jobs</a></li>
                <li><a href="<?php echo getBasePath(); ?>about.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="<?php echo getBasePath(); ?>contact.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-menu">
                        <button class="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        
                        <div class="user-menu-dropdown" aria-hidden="true">
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="<?php echo getBasePath(); ?>admin/dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                                </a>
                                <a href="<?php echo getBasePath(); ?>admin/jobs.php">
                                    <i class="fas fa-briefcase"></i> Manage Jobs
                                </a>
                                <a href="<?php echo getBasePath(); ?>admin/applications.php">
                                    <i class="fas fa-file-alt"></i> Applications
                                </a>
                                <a href="<?php echo getBasePath(); ?>admin/tutors.php">
                                    <i class="fas fa-users"></i> Manage Tutors
                                </a>
                            <?php else: ?>
                                <a href="<?php echo getBasePath(); ?>tutor/dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                                <a href="<?php echo getBasePath(); ?>tutor/profile.php">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a href="<?php echo getBasePath(); ?>tutor/jobs.php">
                                    <i class="fas fa-briefcase"></i> Browse Jobs
                                </a>
                                <a href="<?php echo getBasePath(); ?>tutor/applications.php">
                                    <i class="fas fa-file-alt"></i> My Applications
                                </a>
                            <?php endif; ?>
                            
                            <div class="divider"></div>
                            <a href="<?php echo getBasePath(); ?>logout.php" class="logout">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?php echo getBasePath(); ?>login.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">Login</a></li>
                    <li><a href="<?php echo getBasePath(); ?>register.php" class="btn btn-primary btn-sm <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
