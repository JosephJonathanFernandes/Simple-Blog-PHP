<?php
include 'db.php';

$searchTerm = '';
$sql = "SELECT id, title, content, created_at, image FROM posts";
$params = [];

if (!empty($_GET['q'])) {
    $searchTerm = "%{$_GET['q']}%";
    $sql .= " WHERE title LIKE ? OR content LIKE ?";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($searchTerm)) {
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">📝 Welcome to My Blog</h1>
    <p><a href="login.php" class="btn btn-primary">Admin Login</a></p>

    <form method="GET" class="mb-4">
        <input type="text" name="q" class="form-control" placeholder="Search posts..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
    </form>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3 shadow-sm">
            <?php if (!empty($row['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Post Image" style="max-height:300px; object-fit: cover;">
            <?php endif; ?>
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                <h6 class="card-subtitle mb-2 text-muted">🕒 <?php echo date("F j, Y", strtotime($row['created_at'])); ?></h6>
                <p class="card-text">
                    <?php
                    $excerpt = strip_tags($row['content']);
                    echo strlen($excerpt) > 200 ? substr($excerpt, 0, 200) . "..." : $excerpt;
                    ?>
                </p>
                <a href="view_post.php?id=<?php echo $row['id']; ?>" class="card-link">Read More →</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
