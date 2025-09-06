<?php
session_start();

// 🔒 Prevent back-button after logout (no caching)
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
require_once '../includes/db.php';
$success = $error = '';

// Handle Add News
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $title = trim($_POST['title']);
    $link = trim($_POST['link']);

    if ($title && $link) {
        $stmt = $pdo->prepare("INSERT INTO news (title, link) VALUES (?, ?)");
        if ($stmt->execute([$title, $link])) {
            $success = "✅ News added successfully.";
        } else {
            $error = "❌ Failed to add news.";
        }
    } else {
        $error = "❌ Please fill in both fields.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
    $success = "🗑️ News deleted.";
}

// Fetch all news
$newsList = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage News - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">📰 Manage Insect News</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label>News Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>News Link (URL):</label>
            <input type="url" name="link" class="form-control" required>
        </div>
        <button type="submit" name="add_news" class="btn btn-primary">Add News</button>
    </form>

    <?php if (count($newsList)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Link</th>
                    <th>Posted On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsList as $n): ?>
                    <tr>
                        <td><?= htmlspecialchars($n['title']) ?></td>
                        <td><a href="<?= $n['link'] ?>" target="_blank">🔗 Visit</a></td>
                        <td><?= date("d M Y", strtotime($n['created_at'])) ?></td>
                        <td>
                            <a href="?delete=<?= $n['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this news?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No news added yet.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>
