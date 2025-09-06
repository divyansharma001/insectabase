<?php
//literature.php
session_start();
require_once 'includes/db.php';
include_once("includes/navbar.php");

// ✅ 1. UPDATED PHP LOGIC to fetch the latest background for this page
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$page]);
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');

// Fetch all literature entries
$literature = $pdo->query("SELECT * FROM literature ORDER BY year DESC, id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Literature - InsectaBase</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* ✅ 2. CSS FOR BODY REMOVED - It's now handled by the new system */

        .hero-section {
            background-color: rgba(43, 76, 111, 0.9);
            padding: 80px 0;
            color: #fff;
            text-align: center;
            border-radius: 10px;
        }

        .card {
            background-color: #ffffffdd;
            border: none;
        }

        .literature-title {
            font-size: 1.15rem;
            font-weight: 600;
        }

        .literature-meta {
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>
    
    <div class="container py-5">

        <div class="hero-section mb-4">
            <h1>📚 Insect Literature</h1>
            <p class="lead">Browse research papers and publications related to Tortricidae and insect taxonomy.</p>
        </div>

        <div class="row g-4">
            <?php if (count($literature) > 0): ?>
                <?php foreach ($literature as $entry): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm p-3">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <div class="literature-title"><?= htmlspecialchars($entry['title']) ?></div>
                                    <div class="literature-meta mb-2">
                                        <?= htmlspecialchars($entry['authors']) ?> (<?= htmlspecialchars($entry['year']) ?>)
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <?php if (!empty($entry['link'])): ?>
                                        <a href="<?= htmlspecialchars($entry['link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="bi bi-link-45deg"></i> Link
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($entry['pdf'])): ?>
                                        <a href="<?= htmlspecialchars($entry['pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            No literature entries found.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div> </div> <?php include_once 'includes/footer.php'; ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>