<?php
//morphology.php
session_start();
require_once 'includes/db.php';
include_once("includes/navbar.php");

// ✅ 1. UPDATED PHP LOGIC to fetch the latest background for this page
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$page]);
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');


// ✅ 2. IMPROVEMENT: Fetch the 'image_url' for each gene from the database
$genes = $pdo->query("SELECT id, name, description, image_url FROM genes ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insect Morphology - InsectaBase</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* ✅ 3. CSS for BODY REMOVED */

        .hero-section {
            background-color: rgba(43, 76, 111, 0.9);
            padding: 70px 0;
            color: white;
            text-align: center;
            border-radius: 10px;
        }

        .morphology-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            background-color: #ffffffdd;
        }

        .morphology-card:hover {
            transform: translateY(-5px);
        }

        .gene-img {
            height: 200px; /* Changed from max-height to height for consistency */
            object-fit: cover;
            border-radius: 12px 12px 0 0;
        }
    </style>
</head>
<body>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>

    <div class="container py-5">
        <div class="hero-section mb-4">
            <h1>🧠 Insect Brain Morphology</h1>
            <p class="lead">Explore gene-linked brain regions and their role in insect behavior and anatomy</p>
        </div>

        <div class="row g-4">
            <?php foreach ($genes as $gene): ?>
                <div class="col-md-4">
                    <div class="card morphology-card h-100">
                        
                        <?php if (!empty($gene['image_url'])): ?>
                            <img src="<?= htmlspecialchars($gene['image_url']) ?>" class="card-img-top gene-img" alt="<?= htmlspecialchars($gene['name']) ?>">
                        <?php else: ?>
                            <img src="assets/img/placeholder.png" class="card-img-top gene-img" alt="Placeholder Image">
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title">🧬 <?= htmlspecialchars($gene['name']) ?></h5>
                            <p class="card-text small"><?= htmlspecialchars(substr($gene['description'], 0, 120)) ?>...</p>
                            <a href="factsheet.php#gene<?= $gene['id'] ?>" class="btn btn-outline-success btn-sm">View Related Species</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div> </div> <?php include 'includes/footer.php'; ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>