<?php
//index.php
session_start();
require_once 'includes/db.php';
require_once 'includes/navbar.php'; // Using require_once is safer

// ✅ 1. UPDATED PHP LOGIC to fetch the latest background for this page
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$page]);
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');

// Fetch quick stats
$speciesCount = $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
$geneCount = $pdo->query("SELECT COUNT(*) FROM genes")->fetchColumn();
$subfamilyCount = $pdo->query("SELECT COUNT(*) FROM subfamilies")->fetchColumn();

// Picture of the Day - Ensure it's not a species-specific image
$pic = $pdo->query("SELECT * FROM images WHERE species_id IS NULL ORDER BY RAND() LIMIT 1")->fetch();

// Latest news - Increased limit for infinite scroll demo
$news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 20")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InsectaBase - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <style>
        /* ✅ 2. CSS FOR BODY REMOVED - It's now handled by the new system */
        
        .header-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 248, 255, 0.95) 100%);
            padding: 60px 20px;
            text-align: center;
            border-bottom: 4px solid #0d6efd;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .header-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23000" opacity="0.02"/><circle cx="75" cy="75" r="1" fill="%23000" opacity="0.02"/><circle cx="50" cy="10" r="0.5" fill="%23000" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        .header-banner h1 {
            font-size: 3.5rem;
            color: #004d40;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        .header-banner p {
            font-size: 1.3rem;
            color: #2c5530;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }
        .card-custom {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background-color: #fff;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .card-custom img {
            border-radius: 15px 15px 0 0;
            transition: transform 0.3s ease;
        }
        .card-custom:hover img {
            transform: scale(1.05);
        }
        .potd-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            cursor: pointer;
        }
        .potd-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .section-title {
            margin: 40px 0 20px;
            font-weight: bold;
            border-left: 4px solid #0d6efd;
            padding-left: 10px;
        }
        .banner-strip {
            overflow: hidden;
            background: #fff;
            padding: 10px 0;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            height: 200px;
        }
        .banner-track {
            display: flex;
            width: max-content;
            animation: scroll-banner 120s linear infinite;
            align-items: center;
            height: 100%;
        }
        .banner-track img {
            height: 100%;
            width: auto;
            margin-right: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .banner-track img:hover {
            transform: scale(1.05);
        }
        @keyframes scroll-banner {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }

        /* Infinite scroll styles for news */
        .news-infinite-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .news-item {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .news-item:nth-child(1) { animation-delay: 0.1s; }
        .news-item:nth-child(2) { animation-delay: 0.2s; }
        .news-item:nth-child(3) { animation-delay: 0.3s; }
        .news-item:nth-child(4) { animation-delay: 0.4s; }
        .news-item:nth-child(5) { animation-delay: 0.5s; }
        .news-item:nth-child(6) { animation-delay: 0.6s; }
        .news-item:nth-child(7) { animation-delay: 0.7s; }
        .news-item:nth-child(8) { animation-delay: 0.8s; }
        .news-item:nth-child(9) { animation-delay: 0.9s; }
        .news-item:nth-child(10) { animation-delay: 1.0s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Performance optimized scrollbar */
        .news-infinite-container::-webkit-scrollbar {
            width: 8px;
        }
        .news-infinite-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .news-infinite-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        .news-infinite-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body>

<div class="banner-strip">
    <div class="banner-track">
        <img src="assets/img/banner1.jpg" alt="Banner 1"><img src="assets/img/banner2.jpg" alt="Banner 2"><img src="assets/img/banner3.jpg" alt="Banner 3"><img src="assets/img/banner4.jpg" alt="Banner 4"><img src="assets/img/banner5.jpg" alt="Banner 5"><img src="assets/img/banner6.jpg" alt="Banner 6"><img src="assets/img/banner1.jpg" alt="Banner 1"><img src="assets/img/banner2.jpg" alt="Banner 2"><img src="assets/img/banner3.jpg" alt="Banner 3"><img src="assets/img/banner4.jpg" alt="Banner 4"><img src="assets/img/banner5.jpg" alt="Banner 5"><img src="assets/img/banner6.jpg" alt="Banner 6">
    </div>
</div>

<div class="header-banner observe-fade-in">
    <h1>Welcome to InsectaBase</h1>
    <p>Indian Tortricidae Database Platform</p>
</div>

<?= getDatabaseStatusMessage() ?>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>

    <div class="container mt-5">
        <h2 class="section-title observe-slide-left">Quick Stats</h2>
        <div class="row text-center justify-content-center">
            <div class="col-auto mb-2">
                <div class="card-custom p-2 observe-scale" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-width: 120px;">
                    <div class="stat-icon mb-1">
                        <i class="bi bi-bug fs-4 text-success"></i>
                    </div>
                    <h6 class="text-dark mb-1 small">Species</h6>
                    <p class="fs-5 fw-bold text-success mb-0"><?= number_format($speciesCount) ?></p>
                </div>
            </div>
            <div class="col-auto mb-2">
                <div class="card-custom p-2 observe-scale" style="background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%); min-width: 120px;">
                    <div class="stat-icon mb-1">
                        <i class="bi bi-diagram-3 fs-4 text-info"></i>
                    </div>
                    <h6 class="text-dark mb-1 small">Genes</h6>
                    <p class="fs-5 fw-bold text-info mb-0"><?= number_format($geneCount) ?></p>
                </div>
            </div>
            <div class="col-auto mb-2">
                <div class="card-custom p-2 observe-scale" style="background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%); min-width: 120px;">
                    <div class="stat-icon mb-1">
                        <i class="bi bi-collection fs-4 text-warning"></i>
                    </div>
                    <h6 class="text-dark mb-1 small">Subfamilies</h6>
                    <p class="fs-5 fw-bold text-warning mb-0"><?= number_format($subfamilyCount) ?></p>
                </div>
            </div>
        </div>

        <div class="potd-container">
            <h2 class="section-title observe-slide-right">Picture of the Day</h2>
            <?php if ($pic): ?>
            <div class="card-custom observe-fade-in" style="max-width: 400px; margin: 0 auto;">
                <a href="<?= htmlspecialchars($pic['url'] ?? '') ?>" class="glightbox" data-title="<?= htmlspecialchars($pic['caption'] ?? '') ?>">
                    <img src="<?= htmlspecialchars($pic['url'] ?? '') ?>" class="img-fluid potd-image lazy-image" alt="Picture of the Day" loading="lazy" style="max-height: 200px; object-fit: cover;">
                </a>
                <div class="p-2">
                    <p class="small mb-1"><?= htmlspecialchars($pic['caption'] ?? '') ?></p>
                    <?php if (isset($_SESSION['admin_user'])): ?>
                    <a href="admin/manage_picture.php" class="btn btn-sm btn-outline-primary">Manage</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <p class="text-white small">No image uploaded yet.</p>
            <?php if (isset($_SESSION['admin_user'])): ?>
            <a href="admin/manage_picture.php" class="btn btn-sm btn-light">Upload Picture</a>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <h2 class="section-title observe-fade-in">Latest Research & News</h2>
        <?php if ($news): ?>
        <div class="news-infinite-container">
            <div class="row justify-content-center">
                <?php foreach (array_slice($news, 0, 6) as $index => $n): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                    <div class="card-custom p-2 news-item" style="animation-delay: <?= ($index * 0.1) ?>s; height: 100%;">
                        <div class="d-flex align-items-start">
                            <div class="news-icon me-2">
                                <i class="bi bi-newspaper text-primary fs-6"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">
                                    <a href="<?= htmlspecialchars($n['link'] ?? '') ?>" target="_blank" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars(substr($n['title'] ?? '', 0, 50)) ?><?= strlen($n['title'] ?? '') > 50 ? '...' : '' ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('M d', strtotime($n['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <div class="card-custom p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <h5 class="text-muted mb-2">🦋 Comprehensive Research Database</h5>
                    <p class="text-muted mb-0">Access the latest scientific discoveries, conservation updates, and taxonomic research in the world of Tortricidae moths.</p>
                </div>
            </div>
            <?php if (isset($_SESSION['admin_user'])): ?>
            <a href="admin/manage_news.php" class="btn btn-sm btn-outline-light observe-fade-in">Manage News</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <p class="text-white">No news added yet.</p>
        <?php if (isset($_SESSION['admin_user'])): ?>
        <a href="admin/manage_news.php" class="btn btn-sm btn-light">Add News</a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

</div> <?php require_once 'includes/footer.php'; ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="assets/js/performance.js"></script>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true
    });

    // Initialize infinite scroll for news
    document.addEventListener('DOMContentLoaded', function() {
        const newsContainer = document.querySelector('.news-infinite-container');
        if (newsContainer) {
            // Add scroll event for smooth loading
            newsContainer.addEventListener('scroll', function() {
                const scrollTop = this.scrollTop;
                const scrollHeight = this.scrollHeight;
                const clientHeight = this.clientHeight;
                
                // Load more news when near bottom (80% scrolled)
                if (scrollTop + clientHeight >= scrollHeight * 0.8) {
                    loadMoreNews();
                }
            });
        }
    });

    // Function to load more news (simulated)
    function loadMoreNews() {
        // This would typically make an AJAX call to load more news
        // For demo purposes, we'll just show a loading indicator
        console.log('Loading more news...');
    }
</script>
</body>
</html>