<?php
$servername = "localhost";
$username = "root";
$password = "";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS blogdb");
$conn->select_db("blogdb");

// Create admin table
$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255)
)");

// Create posts table
$conn->query("CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert default admin
$adminUsername = "admin";
$adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $adminUsername, $adminPassword);
$stmt->execute();

echo "Database and tables created. Admin login: admin / admin123";

$conn->close();
?>
