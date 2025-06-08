<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        // Handle image upload (optional)
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } else {
                $target_dir = "uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $filename = uniqid() . "." . $ext;
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image = $filename;
                } else {
                    $error = "Failed to upload image.";
                }
            }
        }

        if (!$error) {
            // Insert into posts table
            $stmt = $conn->prepare("INSERT INTO posts (title, content, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $content, $image);
            if ($stmt->execute()) {
                $success = "Post created successfully.";
                // Clear form fields
                $title = $content = '';
                $image = null;
            } else {
                $error = "Error creating post: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="max-width:700px;">
    <h2>Create New Post</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" rows="8" class="form-control" required><?= htmlspecialchars($content ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label>Image (optional)</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif" class="form-control" />
        </div>
        <button type="submit" class="btn btn-success">Create Post</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>
</body>
</html>
