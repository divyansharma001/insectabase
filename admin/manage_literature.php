<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}

$success = $error = '';

// Handle add with PDF upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $title = trim($_POST['title'] ?? '');
    // ✅ FIX: Use null coalescing operator (??) to prevent warnings
    $authors = trim($_POST['authors'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $pdf_path = ''; // Initialize PDF path

    // FILE UPLOAD LOGIC
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $target_dir = "../assets/literature/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $filename = time() . '_' . basename($_FILES["pdf_file"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
            $pdf_path = "assets/literature/" . $filename;
        } else {
            $error = "❌ Failed to upload the PDF file.";
        }
    }

    if (empty($error) && $title && $authors && $year) {
        $stmt = $pdo->prepare("INSERT INTO literature (title, authors, year, link, pdf) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $authors, $year, $link, $pdf_path])) {
            $success = "✅ Literature entry added successfully.";
        } else {
            $error = "❌ Failed to insert entry into the database.";
        }
    } else if (empty($error)) {
        $error = "❌ Title, Authors, and Year are required fields.";
    }
}


// Handle delete, including file deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $pdo->prepare("SELECT pdf FROM literature WHERE id = ?");
    $stmt->execute([$id]);
    $pdf_to_delete = $stmt->fetchColumn();

    $deleteStmt = $pdo->prepare("DELETE FROM literature WHERE id = ?");
    if ($deleteStmt->execute([$id])) {
        if ($pdf_to_delete && file_exists('../' . $pdf_to_delete)) {
            unlink('../' . $pdf_to_delete);
        }
        $success = "🗑️ Entry and associated file deleted successfully.";
    } else {
        $error = "❌ Failed to delete the entry.";
    }
}

$literature = $pdo->query("SELECT * FROM literature ORDER BY year DESC, id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Literature - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">📚 Manage Literature</h3>

    <?php if ($success): ?><div class="alert alert-success"> <?= $success ?> </div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"> <?= $error ?> </div><?php endif; ?>

    <form method="POST" class="mb-4 bg-white p-3 rounded shadow-sm" enctype="multipart/form-data">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Title*</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Authors*</label>
                <input type="text" name="authors" class="form-control" required>
            </div>
            <div class="col-md-1">
                <label class="form-label">Year*</label>
                <input type="text" name="year" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">External Link</label>
                <input type="url" name="link" placeholder="(optional)" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Upload PDF</label>
                <input type="file" name="pdf_file" class="form-control" accept=".pdf,application/pdf">
            </div>
            <div class="col-md-1">
                <button type="submit" name="add" class="btn btn-success w-100">➕</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Authors</th>
                <th>Year</th>
                <th>Links</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($literature as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['authors']) ?></td>
                    <td><?= htmlspecialchars($row['year']) ?></td>
                    <td>
                        <?php if(!empty($row['link'])): ?>
                            <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="btn btn-sm btn-info">Link</a>
                        <?php endif; ?>
                        <?php if(!empty($row['pdf'])): ?>
                            <a href="../<?= htmlspecialchars($row['pdf']) ?>" target="_blank" class="btn btn-sm btn-danger">PDF</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure? This will also delete the PDF file.')" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
</div>
</body>
</html>