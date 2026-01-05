<?php
// dashboard.php
require_once 'db.php';
requireLogin(); // Redirect to login if not logged in

$theme = getTheme();

// Get student stats
$student_id = $_SESSION['student_id'];
try {
    // Get total subjects
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_subjects FROM grades WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $total_subjects = $stmt->fetch()['total_subjects'];
    
    // Get average marks
    $stmt = $pdo->prepare("SELECT AVG(marks) as avg_marks FROM grades WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $avg_marks = $stmt->fetch()['avg_marks'];
    $avg_marks = round($avg_marks, 2);
    
    // Get highest grade
    $stmt = $pdo->prepare("SELECT grade FROM grades WHERE student_id = ? ORDER BY marks DESC LIMIT 1");
    $stmt->execute([$student_id]);
    $highest_grade = $stmt->fetch()['grade'];
    
} catch(PDOException $e) {
    $total_subjects = 0;
    $avg_marks = 0;
    $highest_grade = 'N/A';
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Grade Portal</title>
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
            <h1><i class="fas fa-tachometer-alt"></i> Student Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
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
            <h2>Academic Overview</h2>
            
            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-book fa-2x"></i>
                    <h3>Total Subjects</h3>
                    <div class="stat-value"><?php echo $total_subjects; ?></div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-chart-line fa-2x"></i>
                    <h3>Average Marks</h3>
                    <div class="stat-value"><?php echo $avg_marks; ?>%</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-star fa-2x"></i>
                    <h3>Highest Grade</h3>
                    <div class="stat-value"><?php echo $highest_grade; ?></div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-user fa-2x"></i>
                    <h3>Student ID</h3>
                    <div class="stat-value"><?php echo htmlspecialchars($student_id); ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                <a href="grades.php" class="btn btn-primary" style="text-align: center;">
                    <i class="fas fa-chart-bar fa-2x"></i><br>
                    View Grades
                </a>
                
                <a href="preference.php" class="btn btn-secondary" style="text-align: center;">
                    <i class="fas fa-palette fa-2x"></i><br>
                    Change Theme
                </a>
                
                <a href="#" onclick="toggleTheme()" class="btn" style="text-align: center; background-color: #6a11cb;">
                    <i class="fas fa-toggle-on fa-2x"></i><br>
                    Toggle Theme
                </a>
                
                <a href="logout.php" class="btn btn-danger" style="text-align: center;">
                    <i class="fas fa-sign-out-alt fa-2x"></i><br>
                    Logout
                </a>
            </div>
        </div>
        
        <div class="card">
            <h2>Recent Activities</h2>
            <ul style="margin-left: 20px;">
                <li>Logged in successfully</li>
                <li>Session started at: <?php echo date('Y-m-d H:i:s'); ?></li>
                <li>Current theme: <?php echo ucfirst($theme); ?> Mode</li>
                <li>Session ID: <?php echo session_id(); ?></li>
            </ul>
        </div>
    </div>
    
    <script>
        function toggleTheme() {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.cookie = `theme=${newTheme}; path=/; max-age=${86400 * 30}`;
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(newTheme);
            
            const icon = document.querySelector('.theme-btn i');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const icon = document.querySelector('.theme-btn i');
            icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</body>
</html>