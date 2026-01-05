<?php
// grades.php
require_once 'db.php';
requireLogin();

$theme = getTheme();
$student_id = $_SESSION['student_id'];

// Get grades for the logged-in student
$grades = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM grades WHERE student_id = ? ORDER BY semester, subject");
    $stmt->execute([$student_id]);
    $grades = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error fetching grades: " . $e->getMessage();
}

// Calculate statistics
$total_marks = 0;
$total_subjects = count($grades);
foreach ($grades as $grade) {
    $total_marks += $grade['marks'];
}
$average_marks = $total_subjects > 0 ? round($total_marks / $total_subjects, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades - Student Grade Portal</title>
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
            <h1><i class="fas fa-chart-bar"></i> Academic Grades</h1>
            <p>Student: <?php echo htmlspecialchars($_SESSION['name']); ?> (ID: <?php echo htmlspecialchars($student_id); ?>)</p>
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
            <h2>Grade Summary</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                <div style="padding: 20px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; border-radius: 8px;">
                    <h3>Total Subjects</h3>
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $total_subjects; ?></div>
                </div>
                
                <div style="padding: 20px; background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; border-radius: 8px;">
                    <h3>Average Marks</h3>
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $average_marks; ?>%</div>
                </div>
                
                <div style="padding: 20px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 8px;">
                    <h3>Total Marks</h3>
                    <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $total_marks; ?></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Detailed Grade Report</h2>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (empty($grades)): ?>
                <div class="message warning">
                    <p>No grades found for your account.</p>
                    <p>Contact your administrator to add grades.</p>
                </div>
            <?php else: ?>
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Marks</th>
                            <th>Semester</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): 
                            // Determine status color based on grade
                            $status_color = '';
                            if ($grade['grade'] == 'A' || $grade['grade'] == 'A+') {
                                $status_color = '#28a745';
                            } elseif ($grade['grade'] == 'B' || $grade['grade'] == 'B+') {
                                $status_color = '#ffc107';
                            } else {
                                $status_color = '#dc3545';
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                            <td style="font-weight: bold; color: <?php echo $status_color; ?>">
                                <?php echo htmlspecialchars($grade['grade']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($grade['marks']); ?>%</td>
                            <td><?php echo htmlspecialchars($grade['semester']); ?></td>
                            <td>
                                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: <?php echo $status_color; ?>;"></span>
                                <?php 
                                if ($grade['marks'] >= 90) echo 'Excellent';
                                elseif ($grade['marks'] >= 80) echo 'Good';
                                elseif ($grade['marks'] >= 70) echo 'Average';
                                else echo 'Needs Improvement';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>GPA Calculation:</strong> Based on your grades, your estimated GPA is 
                        <strong>
                            <?php
                            // Simple GPA calculation
                            $gpa_points = 0;
                            foreach ($grades as $grade) {
                                if ($grade['grade'] == 'A' || $grade['grade'] == 'A+') $gpa_points += 4.0;
                                elseif ($grade['grade'] == 'A-') $gpa_points += 3.7;
                                elseif ($grade['grade'] == 'B+') $gpa_points += 3.3;
                                elseif ($grade['grade'] == 'B') $gpa_points += 3.0;
                                elseif ($grade['grade'] == 'B-') $gpa_points += 2.7;
                                elseif ($grade['grade'] == 'C+') $gpa_points += 2.3;
                                elseif ($grade['grade'] == 'C') $gpa_points += 2.0;
                                else $gpa_points += 1.0;
                            }
                            $gpa = $total_subjects > 0 ? round($gpa_points / $total_subjects, 2) : 0.0;
                            echo $gpa;
                            ?>
                        </strong>
                    </div>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            <?php endif; ?>
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
            const currentTheme = documentbody.classList.contains('dark') ? 'dark' : 'light';
            const icon = document.querySelector('.theme-btn i');
            icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</body>
</html>