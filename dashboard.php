<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$limit = 5; // posts per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get total posts count
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM posts");
$totalRow = $totalRes->fetch_assoc();
$totalPosts = $totalRow['total'];
$totalPages = ceil($totalPosts / $limit);

// Fetch posts with limit and offset
$stmt = $conn->prepare("SELECT id, title, content, image FROM posts ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Dashboard</h2>
    <a href="create_post.php" class="btn btn-success mb-3">Create New Post</a>
    <table class="table table-bordered">
        <thead><tr><th>Title</th><th>Content</th><th>Image</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while ($post = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= htmlspecialchars(substr($post['content'], 0, 100)) ?>...</td>
                <td>
                    <?php if ($post['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Image" style="max-width:100px;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-primary btn-sm">Edit</a>

                    <form method="POST" action="delete_post.php" style="display:inline;" onsubmit="return confirm('Delete this post?');">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>

                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
</body>
</html>
