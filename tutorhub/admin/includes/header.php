<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="../index.php" class="navbar-brand">
                <i class="fas fa-chalkboard-teacher"></i>
                Tutor<span>Hub</span>
            </a>
            
            <button class="navbar-toggler" id="menuToggle" aria-expanded="false" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            
            <ul class="navbar-nav" id="navMenu" aria-hidden="true">
                <li><a href="../index.php" class="nav-link">Home</a></li>
                <li><a href="../jobs.php" class="nav-link">Browse Jobs</a></li>
                <li><a href="../about.php" class="nav-link">About Us</a></li>
                <li><a href="../contact.php" class="nav-link">Contact</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-menu">
                        <button class="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <img src="../assets/img/default-avatar.png" alt="User Avatar">
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        
                        <div class="user-menu-dropdown" aria-hidden="true">
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                                </a>
                                <a href="jobs.php">
                                    <i class="fas fa-briefcase"></i> Manage Jobs
                                </a>
                                <a href="applications.php">
                                    <i class="fas fa-file-alt"></i> Applications
                                </a>
                                <a href="tutors.php">
                                    <i class="fas fa-users"></i> Manage Tutors
                                </a>
                            <?php else: ?>
                                <a href="../tutor/dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                                <a href="../tutor/profile.php">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a href="../tutor/jobs.php">
                                    <i class="fas fa-briefcase"></i> Browse Jobs
                                </a>
                                <a href="../tutor/applications.php">
                                    <i class="fas fa-file-alt"></i> My Applications
                                </a>
                            <?php endif; ?>
                            
                            <div class="divider"></div>
                            <a href="../logout.php" class="logout">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="../login.php" class="nav-link">Login</a></li>
                    <li><a href="../register.php" class="btn btn-primary btn-sm">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
