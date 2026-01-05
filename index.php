<?php
// index.php
require_once 'db.php';
$theme = getTheme();
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grade Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="theme-toggle">
        <button class="theme-btn" onclick="toggleTheme()">
            <i class="fas fa-moon"></i> Theme
        </button>
    </div>
    
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-graduation-cap"></i> Student Grade Portal</h1>
            <p>Manage your grades, track progress, and customize your experience</p>
        </header>
        
        <div class="card">
            <h2>Welcome to Student Grade Portal</h2>
            <p>This portal allows students to:</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Register and login with secure password hashing</li>
                <li>View grades and academic performance</li>
                <li>Customize theme preferences using cookies</li>
                <li>Experience session-based authentication</li>
            </ul>
            
            <div style="margin-top: 30px; text-align: center;">
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                    <a href="grades.php" class="btn btn-secondary">
                        <i class="fas fa-chart-bar"></i> View Grades
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <h2>Features</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="color: #2575fc;"><i class="fas fa-lock"></i> Secure Authentication</h3>
                    <p>Password hashing with bcrypt and session management</p>
                </div>
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="color: #2575fc;"><i class="fas fa-palette"></i> Theme Customization</h3>
                    <p>Light/Dark mode preferences stored in cookies</p>
                </div>
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="color: #2575fc;"><i class="fas fa-chart-line"></i> Grade Tracking</h3>
                    <p>View and manage your academic performance</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleTheme() {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Set cookie for 30 days
            document.cookie = `theme=${newTheme}; path=/; max-age=${86400 * 30}`;
            
            // Apply new theme
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(newTheme);
            
            // Update button icon
            const icon = document.querySelector('.theme-btn i');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        
        // Set initial button icon based on current theme
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const icon = document.querySelector('.theme-btn i');
            icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</body>
</html>