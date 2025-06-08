<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch current post data
$stmt = $conn->prepare("SELECT title, content, image FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit;
}

$post = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $image = $post['image']; // keep old image if no new upload

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
                    // Delete old image file if exists
                    if ($post['image'] && file_exists($target_dir . $post['image'])) {
                        unlink($target_dir . $post['image']);
                    }
                    $image = $filename;
                } else {
                    $error = "Failed to upload image.";
                }
            }
        }

        if (!$error) {
            $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $content, $image, $post_id);
            if ($stmt->execute()) {
                $success = "Post updated successfully.";
                // Refresh post data
                $post['title'] = $title;
                $post['content'] = $content;
                $post['image'] = $image;
            } else {
                $error = "Error updating post: " . $conn->error;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/htcs43aqnr1kj24u2haa7qwzo0hkb6u8agwbyrxn4fu1adq0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
          selector: 'textarea[name="content"]'
        });
    </script>
</head>
<body>
<div class="container mt-5" style="max-width:700px;">
    <h2>Edit Post</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="postForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" rows="8" class="form-control" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Current Image</label><br>
            <?php if ($post['image']): ?>
                <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width:200px;">
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label>Change Image (optional)</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>

<script>
document.getElementById('postForm').addEventListener('submit', function(e) {
    let title = this.title.value.trim();
    let content = tinymce.get(this.content.id).getContent({format: 'text'}).trim();
    if (!title || !content) {
        alert('Title and Content cannot be empty.');
        e.preventDefault();
    }
});
</script>
</body>
</html>
