<?php
session_start();
require_once '../includes/db.php';

// Protect page
if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit();
}

$success = $error = '';

// Handle picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim($_POST['caption']);

    if (!empty($_FILES['image']['name']) && $caption !== '') {
        $target_dir = "../assets/uploads/potd/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $relative_path = "assets/uploads/potd/" . $filename;
            $stmt = $pdo->prepare("INSERT INTO images (url, caption) VALUES (?, ?)");
            if ($stmt->execute([$relative_path, $caption])) {
                $success = "✅ Picture uploaded successfully.";
            } else {
                $error = "❌ Failed to save picture info to DB.";
            }
        } else {
            $error = "❌ Upload failed.";
        }
    } else {
        $error = "❌ Please select an image and enter a caption.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT url FROM images WHERE id = ?");
    $stmt->execute([$id]);
    $pic = $stmt->fetch();

    if ($pic) {
        @unlink('../' . $pic['url']);
        $pdo->prepare("DELETE FROM images WHERE id = ?")->execute([$id]);
        $success = "🗑️ Picture deleted.";
    }
}

// Fetch all pictures
$pictures = $pdo->query("SELECT * FROM images ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Picture of the Day - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">🖼️ Manage Picture of the Day</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"> <?= $success ?> </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"> <?= $error ?> </div>
    <?php endif; ?>

    <!-- Upload form -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Caption:</label>
            <input type="text" name="caption" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Image:</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload Picture</button>
    </form>

    <!-- Existing pictures -->
    <div class="row">
        <?php foreach ($pictures as $pic): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../<?= $pic['url'] ?>" class="card-img-top" alt="Picture">
                    <div class="card-body">
                        <p><?= htmlspecialchars($pic['caption']) ?></p>
                        <a href="?delete=<?= $pic['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this picture?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>
