<?php
// register.php
require_once 'db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($student_id) || empty($name) || empty($password)) {
        $message = "All fields are required!";
        $messageType = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long!";
        $messageType = "error";
    } else {
        try {
            // Check if student ID already exists
            $stmt = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            
            if ($stmt->rowCount() > 0) {
                $message = "Student ID already exists!";
                $messageType = "error";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                // Insert student using prepared statement
                $stmt = $pdo->prepare("INSERT INTO students (student_id, name, password) VALUES (?, ?, ?)");
                $stmt->execute([$student_id, $name, $hashedPassword]);
                
                $message = "Registration successful! You can now login.";
                $messageType = "success";
                
                // Clear form
                $_POST = [];
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
    <title>Register - Student Grade Portal</title>
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
            <h1><i class="fas fa-user-plus"></i> Student Registration</h1>
            <p>Create your account to access the grade portal</p>
        </header>
        
        <div class="card">
            <h2>Register New Account</h2>
            
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
                    <label for="name"><i class="fas fa-user"></i> Full Name:</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                           required placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password:</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           required placeholder="Minimum 6 characters">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           required placeholder="Re-enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Register
                </button>
                
                <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> Already have an account? Login
                </a>
                <a href="index.php" class="btn">
                    <i class="fas fa-home"></i> Home
                </a>
            </form>
            
            <div class="message info" style="margin-top: 20px;">
                <p><i class="fas fa-info-circle"></i> <strong>Note:</strong> Passwords are securely hashed using bcrypt algorithm.</p>
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