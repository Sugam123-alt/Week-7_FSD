<?php
// db.php - Database connection with auto-setup
session_start();

$host = 'localhost';
$dbname = 'np03cs4a240381';
$username = 'np03cs4a240381';
$password = 'Na72ncXPy5';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // If database doesn't exist, create it
    if ($e->getCode() == 1049) {
        try {
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            $pdo->exec("USE $dbname");
            
            // Create tables
            $sql = "
            CREATE TABLE IF NOT EXISTS students (
                student_id VARCHAR(20) PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE TABLE IF NOT EXISTS grades (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id VARCHAR(20),
                subject VARCHAR(100) NOT NULL,
                grade VARCHAR(5) NOT NULL,
                marks INT NOT NULL,
                semester VARCHAR(20),
                FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
            );
            ";
            
            $pdo->exec($sql);
            
            // Insert sample data
            $hashedPassword = password_hash('password', PASSWORD_BCRYPT);
            
            // Insert sample students
            $stmt = $pdo->prepare("INSERT IGNORE INTO students (student_id, name, password) VALUES (?, ?, ?)");
            $stmt->execute(['S001', 'John Doe', $hashedPassword]);
            $stmt->execute(['S002', 'Jane Smith', $hashedPassword]);
            
            // Insert sample grades
            $grades = [
                ['S001', 'Mathematics', 'A', 95, 'Semester 1'],
                ['S001', 'Physics', 'B+', 88, 'Semester 1'],
                ['S001', 'Chemistry', 'A-', 92, 'Semester 1'],
                ['S002', 'Mathematics', 'B', 85, 'Semester 1'],
                ['S002', 'Physics', 'A', 96, 'Semester 1']
            ];
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO grades (student_id, subject, grade, marks, semester) VALUES (?, ?, ?, ?, ?)");
            foreach ($grades as $grade) {
                $stmt->execute($grade);
            }
            
            // Refresh page
            header("Refresh: 3; url=index.php");
            echo "Database and tables created automatically. Redirecting in 3 seconds...";
            exit();
            
        } catch(PDOException $e2) {
            die("Auto-setup failed: " . $e2->getMessage());
        }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Function to get current theme
function getTheme() {
    if (isset($_COOKIE['theme'])) {
        return $_COOKIE['theme'];
    }
    return 'light';
}
?>