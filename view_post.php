<?php
include 'db.php';

$id = $_GET['id'] ?? 0;
if (!is_numeric($id)) {
    die("Invalid post ID.");
}

$stmt = $conn->prepare("SELECT title, content, created_at, image FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p class="text-muted">🕒 <?php echo date("F j, Y", strtotime($post['created_at'])); ?></p>

    <?php if (!empty($post['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="img-fluid mb-3" style="max-height:400px; object-fit: cover;">
    <?php endif; ?>

    <div>
        <?php $post['content']; ?>
    </div>

    <a href="index.php" class="btn btn-secondary mt-4">← Back to blog</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
