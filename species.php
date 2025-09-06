<?php
// species.php
session_start();
require_once 'includes/db.php';
include_once("includes/navbar.php");

// ✅ 1. ADD PHP LOGIC to fetch the dynamic background
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$page]);
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');


// --- 1. GET AND VALIDATE THE SPECIES ID ---
$species_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $species_id = intval($_GET['id']);
}

$species = null;
$gallery_images = [];

if ($species_id) {
    // --- 2. FETCH ALL SPECIES DATA FROM DATABASE ---
    $stmt = $pdo->prepare("
        SELECT s.*, g.name as gene_name, sf.name as subfamily_name
        FROM species s
        LEFT JOIN genes g ON s.gene_id = g.id
        LEFT JOIN subfamilies sf ON s.subfamily_id = sf.id
        WHERE s.id = ?
    ");
    $stmt->execute([$species_id]);
    $species = $stmt->fetch(PDO::FETCH_ASSOC);

    // If species was found, fetch its gallery images
    if ($species) {
        $imgStmt = $pdo->prepare("SELECT * FROM images WHERE species_id = ?");
        $imgStmt->execute([$species_id]);
        $gallery_images = $imgStmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $species ? htmlspecialchars($species['name']) : 'Not Found' ?> - InsectaBase</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <style>
        .navbar {
            background-color: #000000 !important;
        }
        .species-header {
            background-color: #343a40;
            color: white;
            padding: 4rem 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .details-card {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }
        .species-image, .gallery-thumb {
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .species-image:hover, .gallery-thumb:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        .gallery-thumb {
            height: 150px;
        }
        /* Make gallery title text readable on dark background */
        .content-background .text-center {
            color: #ffffff;
        }

        /* Performance optimizations */
        .species-header, .details-card, .gallery-item {
            will-change: transform;
            backface-visibility: hidden;
        }

        /* Lazy loading placeholder */
        .lazy-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading-shimmer 1.5s infinite;
        }

        @keyframes loading-shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>

    <div class="container my-5">

        <?php if ($species): // CHECK if species exists BEFORE trying to display it ?>

            <div class="species-header text-center observe-fade-in">
                <h1><i><?= htmlspecialchars($species['name']) ?></i></h1>
                <p class="lead">Detailed Profile</p>
            </div>

            <a href="checklist.php" class="btn btn-light mb-4 observe-slide-left"><i class="bi bi-arrow-left"></i> Back to Checklist</a>

            <div class="row g-4">
                <div class="col-md-5 text-center">
                    <a href="<?= htmlspecialchars($species['image_url'] ?? 'assets/img/placeholder.png') ?>" class="glightbox" data-gallery="species-gallery">
                        <img src="<?= htmlspecialchars($species['image_url'] ?? 'assets/img/placeholder.png') ?>" 
                             alt="<?= htmlspecialchars($species['name']) ?>" 
                             class="species-image lazy-image" 
                             loading="lazy"
                             decoding="async">
                    </a>
                </div>

                <div class="col-md-7">
                    <div class="details-card observe-slide-right">
                        <h3>Details</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Subfamily:</strong> <?= htmlspecialchars($species['subfamily_name'] ?? 'N/A') ?></li>
                            <li class="list-group-item"><strong>Gene:</strong> <?= htmlspecialchars($species['gene_name'] ?? 'N/A') ?></li>
                            <li class="list-group-item"><strong>Status:</strong> <?= htmlspecialchars($species['status'] ?? 'N/A') ?></li>
                            <li class="list-group-item"><strong>Location:</strong> <?= htmlspecialchars($species['location'] ?? 'N/A') ?></li>
                        </ul>

                        <?php if (!empty($species['description'])): ?>
                            <h4 class="mt-4">Description</h4>
                            <p><?= nl2br(htmlspecialchars($species['description'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($species['diagnosis'])): ?>
                            <h4 class="mt-4">Diagnosis</h4>
                            <p><?= nl2br(htmlspecialchars($species['diagnosis'])) ?></p>
                        <?php endif; ?>

                        <div class="mt-4">
                            <?php if (!empty($species['pdf_url'])): ?>
                                <a href="<?= htmlspecialchars($species['pdf_url']) ?>" class="btn btn-danger" target="_blank"><i class="bi bi-file-pdf"></i> View PDF</a>
                            <?php endif; ?>
                            <?php if (!empty($species['map_link'])): ?>
                                 <a href="<?= htmlspecialchars($species['map_link']) ?>" class="btn btn-info" target="_blank"><i class="bi bi-geo-alt-fill"></i> View Map</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (count($gallery_images) > 0): ?>
            <div class="mt-5 observe-fade-in">
                <hr style="border-color: #ffffff;">
                <h3 class="text-center mb-4">Photo Gallery</h3>
                <div class="row g-3">
                    <?php foreach($gallery_images as $index => $img): ?>
                        <div class="col-6 col-md-4 col-lg-3 gallery-item" style="animation-delay: <?= ($index * 0.1) ?>s">
                            <a href="<?= htmlspecialchars($img['url']) ?>" class="glightbox" data-gallery="species-gallery" data-title="<?= htmlspecialchars($img['caption']) ?>">
                                <img src="<?= htmlspecialchars($img['url']) ?>" 
                                     class="gallery-thumb lazy-image" 
                                     alt="<?= htmlspecialchars($img['caption']) ?>"
                                     loading="lazy"
                                     decoding="async">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php else: // This part runs if the species was NOT found ?>

            <div class="text-center text-white observe-fade-in">
                <h1 class="display-4">404</h1>
                <h2>Species Not Found</h2>
                <p class="lead">The species you are looking for does not exist or may have been moved.</p>
                <a href="checklist.php" class="btn btn-primary mt-3"><i class="bi bi-arrow-left"></i> Back to Checklist</a>
            </div>

        <?php endif; ?>

    </div>
</div> <?php include("includes/footer.php"); ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="assets/js/performance.js"></script>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        width: '90vw',
        height: '80vh'
    });

    // Performance optimization: Preload gallery images
    document.addEventListener('DOMContentLoaded', function() {
        const galleryImages = document.querySelectorAll('.gallery-thumb');
        galleryImages.forEach(img => {
            // Add intersection observer for lazy loading
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.classList.remove('lazy-image');
                                observer.unobserve(img);
                            }
                        }
                    });
                }, { threshold: 0.1 });
                
                observer.observe(img);
            }
        });
    });
</script>
</body>
</html>