<?php
include 'db.php';

// Step 1: Create users table if it doesn't exist
$createTableSQL = "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Checked users table.<br>";
} else {
    die("Error creating users table: " . $conn->error);
}

// Step 2: Insert admin user if it doesn't exist
$username = 'admin';
$password = 'admin123'; // Change this password after setup!

// Check if admin user already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin user already exists.<br>";
} else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hash);

    if ($stmt->execute()) {
        echo "Admin user created successfully.<br>";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
}

$conn->close();
?>
