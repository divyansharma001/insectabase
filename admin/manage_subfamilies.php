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

$msg = '';

// ADD new subfamily
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $imagePath = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/uploads/subfamilies/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $imagePath = "assets/uploads/subfamilies/" . $filename;
        }
    }

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO subfamilies (name, description, image_url) VALUES (?, ?, ?)");
        $stmt->execute([$name, $desc, $imagePath]);
        $msg = "Subfamily added successfully!";
    }
}

// DELETE subfamily
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete image file
    $stmt = $pdo->prepare("SELECT image_url FROM subfamilies WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    if ($image && file_exists("../" . $image)) {
        unlink("../" . $image);
    }

    $stmt = $pdo->prepare("DELETE FROM subfamilies WHERE id = ?");
    $stmt->execute([$id]);
    $msg = "Subfamily deleted.";
}

// Fetch all subfamilies
$subfamilies = $pdo->query("SELECT * FROM subfamilies ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subfamilies - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3 class="mb-3">Manage Subfamilies</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅️ Back to Dashboard</a>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label>Subfamily Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label>Upload Image (optional):</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        <button type="submit" name="add" class="btn btn-success">➕ Add Subfamily</button>
    </form>

    <h5>Existing Subfamilies:</h5>
    <table class="table table-bordered table-striped bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subfamily Name</th>
                <th>Description</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subfamilies as $sf): ?>
                <tr>
                    <td><?= $sf['id'] ?></td>
                    <td><?= htmlspecialchars($sf['name']) ?></td>
                    <td><?= htmlspecialchars($sf['description']) ?></td>
                    <td>
                        <?php if (!empty($sf['image_url'])): ?>
                            <img src="../<?= $sf['image_url'] ?>" alt="Image" style="height: 60px;">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?= $sf['id'] ?>" onclick="return confirm('Delete this subfamily?')" class="btn btn-sm btn-danger">🗑 Delete</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
</body>
</html>
