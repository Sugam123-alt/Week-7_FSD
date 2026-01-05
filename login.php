<?php
// login.php
require_once 'db.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $password = $_POST['password'];
    
    if (empty($student_id) || empty($password)) {
        $message = "Please enter both Student ID and Password!";
        $messageType = "error";
    } else {
        try {
            // Prepare SQL statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT student_id, name, password FROM students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch();
            
            if ($student && password_verify($password, $student['password'])) {
                // Password is correct, start session
                $_SESSION['logged_in'] = true;
                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['name'] = $student['name'];
                
                // Redirect to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $message = "Invalid Student ID or Password!";
                $messageType = "error";
            }
        } catch(PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

$theme = getTheme();
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Grade Portal</title>
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
            <h1><i class="fas fa-sign-in-alt"></i> Student Login</h1>
            <p>Access your grade portal account</p>
        </header>
        
        <div class="card">
            <h2>Login to Your Account</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="student_id"><i class="fas fa-id-card"></i> Student ID:</label>
                    <input type="text" id="student_id" name="student_id" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" 
                           required placeholder="Enter your student ID">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password:</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                
                <a href="register.php" class="btn btn-secondary">
                    <i class="fas fa-user-plus"></i> Don't have an account? Register
                </a>
                <a href="index.php" class="btn">
                    <i class="fas fa-home"></i> Home
                </a>
            </form>
            
            <div class="message info" style="margin-top: 20px;">
                <p><i class="fas fa-info-circle"></i> <strong>Demo Accounts:</strong></p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Student ID: <strong>S001</strong>, Password: <strong>password</strong></li>
                    <li>Student ID: <strong>S002</strong>, Password: <strong>password</strong></li>
                </ul>
            </div>
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