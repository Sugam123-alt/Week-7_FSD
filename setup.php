<?php
// setup.php - Database setup script
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_grade_portal';

try {
    // Connect without selecting database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    $pdo->exec("USE $database");
    
    echo "Database '$database' created successfully!<br>";
    
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
    echo "Tables created successfully!<br>";
    
    // Insert sample data
    $hashedPassword = password_hash('password', PASSWORD_BCRYPT);
    
    // Insert students
    $stmt = $pdo->prepare("INSERT IGNORE INTO students (student_id, name, password) VALUES (?, ?, ?)");
    $stmt->execute(['S001', 'John Doe', $hashedPassword]);
    $stmt->execute(['S002', 'Jane Smith', $hashedPassword]);
    echo "Sample students inserted!<br>";
    
    // Insert grades
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
    echo "Sample grades inserted!<br>";
    
    echo "<h3>Setup completed successfully!</h3>";
    echo '<a href="index.php">Go to Student Portal</a>';
    
} catch(PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>