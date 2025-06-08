<?php
$conn = new mysqli("localhost", "root", "", "blogdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
