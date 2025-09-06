<?php
session_start();

// Prevent back-button after logout (no caching)
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

// Fetch dashboard statistics
$speciesCount = $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
$geneCount = $pdo->query("SELECT COUNT(*) FROM genes")->fetchColumn();
$subfamilyCount = $pdo->query("SELECT COUNT(*) FROM subfamilies")->fetchColumn();
$imageCount = $pdo->query("SELECT COUNT(*) FROM images")->fetchColumn();
$backgroundCount = $pdo->query("SELECT COUNT(*) FROM backgrounds")->fetchColumn();
$newsCount = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$literatureCount = $pdo->query("SELECT COUNT(*) FROM literature")->fetchColumn();

// Recent activities
$recentSpecies = $pdo->query("SELECT * FROM species ORDER BY id DESC LIMIT 5")->fetchAll();
$recentImages = $pdo->query("SELECT * FROM images ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="bi bi-arrow-left me-2"></i>Back to Site
            </a>
            <div class="navbar-nav ms-auto">
                <a href="logout.php" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0">
                            <i class="bi bi-speedometer2 me-3"></i>
                            Welcome to Admin Dashboard
                        </h2>
                        <p class="mb-0 text-light">Manage your InsectaBase platform efficiently</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-bug text-primary"></i>
                    <h4><?= number_format($speciesCount) ?></h4>
                    <p>Total Species</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-diagram-3 text-success"></i>
                    <h4><?= number_format($geneCount) ?></h4>
                    <p>Genes</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-collection text-info"></i>
                    <h4><?= number_format($subfamilyCount) ?></h4>
                    <p>Subfamilies</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-images text-warning"></i>
                    <h4><?= number_format($imageCount) ?></h4>
                    <p>Images</p>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-palette text-danger"></i>
                    <h4><?= number_format($backgroundCount) ?></h4>
                    <p>Backgrounds</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-newspaper text-primary"></i>
                    <h4><?= number_format($newsCount) ?></h4>
                    <p>News Articles</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="dashboard-stat">
                    <i class="bi bi-journal-text text-success"></i>
                    <h4><?= number_format($literatureCount) ?></h4>
                    <p>Literature</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions-grid">
                            <a href="manage_species.php" class="quick-action-btn species">
                                <i class="bi bi-bug"></i>
                                <span>Manage Species</span>
                            </a>
                            <a href="manage_genes.php" class="quick-action-btn genes">
                                <i class="bi bi-diagram-3"></i>
                                <span>Manage Genes</span>
                            </a>
                            <a href="manage_subfamilies.php" class="quick-action-btn subfamilies">
                                <i class="bi bi-collection"></i>
                                <span>Manage Subfamilies</span>
                            </a>
                            <a href="upload_images.php" class="quick-action-btn upload">
                                <i class="bi bi-upload"></i>
                                <span>Upload Images</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Recent Species Added
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recentSpecies) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recentSpecies as $species): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($species['name']) ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($species['location'] ?? 'Location not specified') ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= htmlspecialchars($species['status'] ?? 'N/A') ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">No species added yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-image me-2"></i>
                            Recent Images Uploaded
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recentImages) > 0): ?>
                            <div class="row g-2">
                                <?php foreach ($recentImages as $image): ?>
                                    <div class="col-6">
                                        <div class="image-preview">
                                            <img src="../<?= htmlspecialchars($image['url']) ?>" 
                                                 alt="<?= htmlspecialchars($image['caption'] ?? 'Image') ?>"
                                                 class="img-fluid rounded">
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($image['caption'] ?? 'No caption') ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center mb-0">No images uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Links -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>
                            All Management Tools
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <div class="d-grid">
                                    <a href="manage_background.php" class="btn btn-outline-primary">
                                        <i class="bi bi-palette me-2"></i>Manage Backgrounds
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-grid">
                                    <a href="manage_news.php" class="btn btn-outline-success">
                                        <i class="bi bi-newspaper me-2"></i>Manage News
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-grid">
                                    <a href="manage_literature.php" class="btn btn-outline-info">
                                        <i class="bi bi-journal-text me-2"></i>Manage Literature
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-grid">
                                    <a href="manage_picture.php" class="btn btn-outline-warning">
                                        <i class="bi bi-image me-2"></i>Manage Pictures
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-grid">
                                    <a href="admin_view_contacts.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-envelope me-2"></i>View Contacts
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="d-grid">
                                    <a href="../index.php" class="btn btn-outline-dark">
                                        <i class="bi bi-house me-2"></i>View Site
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
