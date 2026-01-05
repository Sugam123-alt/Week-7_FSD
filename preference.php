<?php
// preference.php
require_once 'db.php';
requireLogin();

$theme = getTheme();
$message = '';
$messageType = '';

// Handle theme preference form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_theme = $_POST['theme'] ?? 'light';
    
    // Set cookie for 30 days
    setcookie('theme', $selected_theme, time() + (86400 * 30), "/");
    
    $message = "Theme preference saved successfully!";
    $messageType = "success";
    
    // Update current theme variable
    $theme = $selected_theme;
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferences - Student Grade Portal</title>
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
            <h1><i class="fas fa-palette"></i> Theme Preferences</h1>
            <p>Customize your portal experience</p>
        </header>
        
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-btn"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="grades.php" class="nav-btn"><i class="fas fa-chart-bar"></i> View Grades</a>
            <a href="preference.php" class="nav-btn"><i class="fas fa-palette"></i> Theme Preferences</a>
            <a href="logout.php" class="nav-btn" style="background-color: #dc3545; color: white;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
        
        <div class="card">
            <h2>Theme Customization</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Theme:</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="theme" value="light" <?php echo ($theme == 'light') ? 'checked' : ''; ?>>
                            <div style="margin-left: 10px; padding: 15px; border: 2px solid #e1e5eb; border-radius: 8px; background: white; color: black;">
                                <i class="fas fa-sun fa-2x"></i><br>
                                Light Mode
                            </div>
                        </label>
                        
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="radio" name="theme" value="dark" <?php echo ($theme == 'dark') ? 'checked' : ''; ?>>
                            <div style="margin-left: 10px; padding: 15px; border: 2px solid #0f3460; border-radius: 8px; background: #16213e; color: white;">
                                <i class="fas fa-moon fa-2x"></i><br>
                                Dark Mode
                            </div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Preferences
                </button>
                
                <button type="button" onclick="toggleTheme()" class="btn btn-secondary">
                    <i class="fas fa-toggle-on"></i> Toggle Current Theme
                </button>
            </form>
            
            <div class="message info" style="margin-top: 20px;">
                <p><i class="fas fa-info-circle"></i> <strong>Note:</strong> Theme preferences are stored in cookies and will persist for 30 days.</p>
                <p>Current cookie value: <code><?php echo $_COOKIE['theme'] ?? 'Not set'; ?></code></p>
            </div>
        </div>
        
        <div class="card">
            <h2>Current Session Information</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><strong>Student ID:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><?php echo htmlspecialchars($_SESSION['student_id']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><strong>Name:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><?php echo htmlspecialchars($_SESSION['name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><strong>Session ID:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><?php echo session_id(); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><strong>Current Theme:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #e1e5eb;"><?php echo ucfirst($theme); ?> Mode</td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><strong>Cookie Theme:</strong></td>
                    <td style="padding: 10px;"><?php echo isset($_COOKIE['theme']) ? ucfirst($_COOKIE['theme']) . ' Mode' : 'Not set'; ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <script>
        function toggleTheme() {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Update the form radio button
            document.querySelector(`input[name="theme"][value="${newTheme}"]`).checked = true;
            
            // Set cookie
            document.cookie = `theme=${newTheme}; path=/; max-age=${86400 * 30}`;
            
            // Apply theme
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(newTheme);
            
            // Update button icon
            const icon = document.querySelector('.theme-btn i');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const icon = document.querySelector('.theme-btn i');
            icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            
            // Update form radio button to match current theme
            document.querySelector(`input[name="theme"][value="${currentTheme}"]`).checked = true;
        });
    </script>
</body>
</html>