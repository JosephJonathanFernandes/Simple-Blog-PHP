<?php
include 'db.php';  // Your database connection file

// Check if 'image' column exists in 'posts' table
$result = $conn->query("SHOW COLUMNS FROM posts LIKE 'image'");
if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE posts ADD COLUMN image VARCHAR(255) NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'image' added successfully.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column 'image' already exists.";
}

$conn->close();
?>
