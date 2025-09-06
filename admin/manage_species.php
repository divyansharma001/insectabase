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

$success = $error = '';

// Handle Add Species
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_species'])) {
    $name = trim($_POST['name'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $gene_id = $_POST['gene_id'] ?? null;
    $subfamily_id = $_POST['subfamily_id'] ?? null;
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $map_link = trim($_POST['map_link'] ?? '');
    $pdf_url = trim($_POST['pdf_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // ✅ 1. ADDED: Get Latitude and Longitude from the form
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    // Ensure empty strings are saved as NULL in the database
    $latitude = !empty($latitude) ? $latitude : null;
    $longitude = !empty($longitude) ? $longitude : null;

    $image_url = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/uploads/";
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = "assets/uploads/" . $filename;
        }
    }

    if (!empty($name) && is_numeric($gene_id)) {
        // ✅ 1. ADDED: Include latitude and longitude in the INSERT query
        $stmt = $pdo->prepare("INSERT INTO species 
            (name, status, location, diagnosis, map_link, gene_id, subfamily_id, image_url, pdf_url, description, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $status, $location, $diagnosis, $map_link, $gene_id, $subfamily_id, $image_url, $pdf_url, $description, $latitude, $longitude])) {
            $success = "✅ Species added successfully.";
        } else {
            $error = "❌ Failed to add species.";
        }
    } else {
        $error = "❌ Name and gene must be selected.";
    }
}

// Handle Delete Species
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // ✅ 2. IMPROVEMENT: Also delete the image file from the server
    $stmt = $pdo->prepare("SELECT image_url FROM species WHERE id = ?");
    $stmt->execute([$id]);
    $image_to_delete = $stmt->fetchColumn();

    $deleteStmt = $pdo->prepare("DELETE FROM species WHERE id = ?");
    if ($deleteStmt->execute([$id])) {
        if ($image_to_delete && file_exists('../' . $image_to_delete)) {
            unlink('../' . $image_to_delete);
        }
        $success = "🗑️ Species and associated image deleted.";
    } else {
        $error = "❌ Deletion failed.";
    }
}

$genes = $pdo->query("SELECT genes.id, genes.name AS gene_name, subfamilies.name AS subfamily_name FROM genes LEFT JOIN subfamilies ON genes.subfamily_id = subfamilies.id ORDER BY subfamilies.name, genes.name")->fetchAll();
$subfamilies = $pdo->query("SELECT id, name FROM subfamilies ORDER BY name ASC")->fetchAll();
$speciesList = $pdo->query("SELECT species.*, genes.name AS gene_name, subfamilies.name AS subfamily_name FROM species LEFT JOIN genes ON species.gene_id = genes.id LEFT JOIN subfamilies ON species.subfamily_id = subfamilies.id ORDER BY species.id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Species</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">🦋 Manage Species</h3>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm mb-4">
        <h5>Add New Species</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label>Species Name*</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Status</label>
                <input type="text" name="status" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Location Name</label>
                <input type="text" name="location" class="form-control" placeholder="e.g., New Delhi">
            </div>

            <div class="col-md-4">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" placeholder="e.g., 28.6139">
            </div>
            <div class="col-md-4">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" placeholder="e.g., 77.2090">
            </div>
             <div class="col-md-4">
                <label>Map Link (Optional)</label>
                <input type="url" name="map_link" class="form-control" placeholder="Google Maps URL">
            </div>


            <div class="col-md-6">
                <label>Gene*</label>
                <select name="gene_id" class="form-select" required>
                    <option value="">-- Select Gene --</option>
                    <?php foreach ($genes as $g): ?>
                        <option value="<?= $g['id'] ?>">
                            <?= htmlspecialchars($g['gene_name']) ?> (<?= htmlspecialchars($g['subfamily_name'] ?? '—') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Subfamily*</label>
                <select name="subfamily_id" class="form-select" required>
                    <option value="">-- Select Subfamily --</option>
                    <?php foreach ($subfamilies as $sf): ?>
                        <option value="<?= $sf['id'] ?>"><?= htmlspecialchars($sf['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Main Image</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>
            <div class="col-md-6">
                <label>PDF Link (Optional)</label>
                <input type="url" name="pdf_url" class="form-control">
            </div>
            <div class="col-md-12">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-12">
                <label>Diagnosis</label>
                <textarea name="diagnosis" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12 text-end">
                <button type="submit" name="add_species" class="btn btn-success mt-2">Add Species</button>
            </div>
        </div>
    </form>

    <h5 class="mt-5">Existing Species</h5>
    <table class="table table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Gene</th>
                <th>Location</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($speciesList as $sp): ?>
                <tr>
                    <td><?= htmlspecialchars($sp['name']) ?></td>
                    <td><?= htmlspecialchars($sp['gene_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($sp['location']) ?></td>
                    <td>
                        <?php if (!empty($sp['image_url'])): ?>
                            <img src="../<?= $sp['image_url'] ?>" alt="Species Image" style="height: 50px;">
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_species.php?id=<?= $sp['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="?delete=<?= $sp['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary mt-4">⬅ Back to Dashboard</a>
</div>
</body>
</html>