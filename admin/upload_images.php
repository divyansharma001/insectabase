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

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_images'])) {
    $species_id = $_POST['species_id'];
    $captions = $_POST['caption']; // array of captions

    if (!empty($_FILES['images']['name'][0]) && is_numeric($species_id)) {
        $target_dir = "../assets/uploads/";

        foreach ($_FILES['images']['name'] as $index => $originalName) {
            $filename = time() . '_' . basename($originalName);
            $target_file = $target_dir . $filename;
            $caption = trim($captions[$index]);

            if (move_uploaded_file($_FILES['images']['tmp_name'][$index], $target_file)) {
                $relative_path = "assets/uploads/" . $filename;
                $stmt = $pdo->prepare("INSERT INTO images (species_id, url, caption) VALUES (?, ?, ?)");
                $stmt->execute([$species_id, $relative_path, $caption]);
            }
        }

        $success = "✅ Images uploaded successfully.";
    } else {
        $error = "❌ Please select at least one image and a valid species.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT url FROM images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if ($image) {
        @unlink('../' . $image['url']); // delete physical file
        $pdo->prepare("DELETE FROM images WHERE id = ?")->execute([$id]);
        $success = "🗑️ Image deleted.";
    }
}

// Fetch species list and their images
$speciesList = $pdo->query("SELECT id, name FROM species ORDER BY name ASC")->fetchAll();

$imagesBySpecies = [];
foreach ($speciesList as $sp) {
    $stmt = $pdo->prepare("SELECT * FROM images WHERE species_id = ?");
    $stmt->execute([$sp['id']]);
    $imagesBySpecies[$sp['id']] = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Photos - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        .img-thumb {
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">🖼️ Upload Insect Photos</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label>Species:</label>
            <select name="species_id" class="form-select" required>
                <option value="">-- Select Species --</option>
                <?php foreach ($speciesList as $sp): ?>
                    <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Select Images (multiple):</label>
            <input type="file" name="images[]" multiple accept="image/*" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Captions (optional, one per image):</label>
            <input type="text" name="caption[]" class="form-control mb-1" placeholder="Caption for Image 1">
            <input type="text" name="caption[]" class="form-control mb-1" placeholder="Caption for Image 2">
            <input type="text" name="caption[]" class="form-control mb-1" placeholder="Caption for Image 3">
        </div>
        <button type="submit" name="upload_images" class="btn btn-primary">Upload</button>
    </form>

    <!-- Display Images -->
    <?php foreach ($speciesList as $sp): ?>
        <?php if (isset($imagesBySpecies[$sp['id']]) && count($imagesBySpecies[$sp['id']]) > 0): ?>
            <h5 class="mt-4">📸 <?= htmlspecialchars($sp['name']) ?> - Uploaded Images</h5>
            <div class="row">
                <?php foreach ($imagesBySpecies[$sp['id']] as $img): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="../<?= $img['url'] ?>" class="card-img-top img-thumb" alt="Image">
                            <div class="card-body p-2">
                                <small><?= htmlspecialchars($img['caption']) ?></small>
                                <div class="text-end">
                                    <a href="?delete=<?= $img['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Delete this image?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-4">⬅ Back to Dashboard</a>
</div>
</body>
</html>
