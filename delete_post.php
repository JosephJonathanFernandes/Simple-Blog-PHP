<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid CSRF token");
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = intval($_POST['id']);

// Get image filename
$stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($post = $result->fetch_assoc()) {
    $stmt_del = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt_del->bind_param("i", $post_id);
    if ($stmt_del->execute()) {
        if ($post['image'] && file_exists("uploads/" . $post['image'])) {
            unlink("uploads/" . $post['image']);
        }
    }
}

header("Location: dashboard.php");
exit;
