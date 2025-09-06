<?php
session_start();

// Prevent back-button after logout
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

$error = '';
$success = '';

// Add Gene
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_gene'])) {
    $gene_name = trim($_POST['gene_name']);
    $description = trim($_POST['description']);
    $subfamily_id = $_POST['subfamily_id'];
    $image_url = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/uploads/";
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = "assets/uploads/" . $filename;
        }
    }

    if (!empty($gene_name) && is_numeric($subfamily_id)) {
        $stmt = $pdo->prepare("INSERT INTO genes (name, description, image_url, subfamily_id) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$gene_name, $description, $image_url, $subfamily_id])) {
            $success = "✅ Gene added successfully.";
        } else {
            $error = "❌ Failed to add gene.";
        }
    } else {
        $error = "❌ Gene name and Subfamily must be provided.";
    }
}

// Delete Gene
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM genes WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "🗑️ Gene deleted successfully.";
    } else {
        $error = "❌ Failed to delete gene.";
    }
}

$genes = $pdo->query("SELECT genes.*, subfamilies.name AS subfamily_name FROM genes 
                      LEFT JOIN subfamilies ON genes.subfamily_id = subfamilies.id 
                      ORDER BY genes.id DESC")->fetchAll();

$subfamilies = $pdo->query("SELECT * FROM subfamilies ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Genes</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">🧬 Manage Genes</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4 bg-white p-4 shadow-sm rounded">
        <h5>Add New Gene</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label>Gene Name</label>
                <input type="text" name="gene_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Subfamily</label>
                <select name="subfamily_id" class="form-select" required>
                    <option value="">-- Select Subfamily --</option>
                    <?php foreach ($subfamilies as $sf): ?>
                        <option value="<?= $sf['id'] ?>"><?= htmlspecialchars($sf['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Gene Image</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>
            <div class="col-md-12">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12 text-end">
                <button type="submit" name="add_gene" class="btn btn-success">Add Gene</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered bg-white">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Subfamily</th>
            <th>Description</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($genes as $gene): ?>
            <tr>
                <td><?= $gene['id'] ?></td>
                <td><?= htmlspecialchars($gene['name']) ?></td>
                <td><?= htmlspecialchars($gene['subfamily_name'] ?? '—') ?></td>
                <td><?= nl2br(htmlspecialchars($gene['description'])) ?></td>
                <td>
                    <?php if ($gene['image_url']): ?>
                        <img src="../<?= $gene['image_url'] ?>" alt="Gene Image" style="height: 60px;">
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?delete=<?= $gene['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this gene?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-4">⬅ Back to Dashboard</a>
</div>
</body>
</html>
