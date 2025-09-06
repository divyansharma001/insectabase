<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}

$success = $error = '';

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_background'])) {
    $name = trim($_POST['name']);
    $page = trim($_POST['page']);

    if (!empty($_FILES['background']['name']) && $name !== '' && $page !== '') {
        $target_dir = "../assets/uploads/backgrounds/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $filename = time() . '_' . basename($_FILES["background"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["background"]["tmp_name"], $target_file)) {
            $relative_path = "assets/uploads/backgrounds/" . $filename;
            // Updated INSERT query
            $stmt = $pdo->prepare("INSERT INTO backgrounds (name, image_url, page) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $relative_path, $page])) {
                $success = "✅ Background uploaded successfully.";
            } else {
                $error = "❌ Failed to save background to DB.";
            }
        } else {
            $error = "❌ Upload failed.";
        }
    } else {
        $error = "❌ Please provide a name, page, and image.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE id = ?");
    $stmt->execute([$id]);
    $background = $stmt->fetch();
    if ($background) {
        @unlink('../' . $background['image_url']);
        $pdo->prepare("DELETE FROM backgrounds WHERE id = ?")->execute([$id]);
        $success = "🗑️ Background deleted.";
    }
}

$backgrounds = $pdo->query("SELECT * FROM backgrounds ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Backgrounds - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">🌄 Manage Page Backgrounds</h3>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4 bg-white p-3 rounded shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label>Background Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Page to Display On:</label>
                <select name="page" class="form-select" required>
                    <option value="">-- Select Page --</option>
                    <option value="index.php">Home</option>
                    <option value="morphology.php">Morphology</option>
                    <option value="factsheet.php">Fact Sheet</option>
                    <option value="checklist.php">Checklist</option>
                    <option value="literature.php">Literature</option>
                    <option value="credits.php">Credits</option>
                    <option value="contact.php">Contact</option>
                </select>
            </div>
            <div class="col-md-4">
                 <label>Select Image:</label>
                <input type="file" name="background" accept="image/*" class="form-control" required>
            </div>
            <div class="col-md-1">
                <button type="submit" name="upload_background" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php foreach ($backgrounds as $bg): ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="../<?= $bg['image_url'] ?>" style="height:120px; object-fit:cover;" alt="<?= htmlspecialchars($bg['name']) ?>">
                    <div class="card-body p-2">
                        <strong><?= htmlspecialchars($bg['name']) ?></strong>
                        <p class="small text-muted mb-1">Page: <?= htmlspecialchars($bg['page']) ?></p>
                        <a href="?delete=<?= $bg['id'] ?>" class="btn btn-sm btn-danger float-end" onclick="return confirm('Delete this background?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>