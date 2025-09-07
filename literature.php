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

// If no literature found, provide sample data with Cloudinary PDFs
if (empty($literature)) {
    $cloudinaryUrls = getSampleCloudinaryUrls();
    $literature = [
        [
            'title' => 'Molecular Phylogeny and Systematics of Indian Tortricidae (Lepidoptera)',
            'authors' => 'Dr. Rajesh Kumar, Dr. Priya Sharma, Dr. Amit Patel',
            'year' => 2024,
            'link' => 'https://www.nature.com/articles/s41598-024-12345-6',
            'pdf' => $cloudinaryUrls['pdfs']['taxonomic_study']
        ],
        [
            'title' => 'Field Guide to Tortricidae Moths of Western Ghats',
            'authors' => 'Dr. Suresh Nair, Dr. Meera Iyer',
            'year' => 2023,
            'link' => 'https://www.biodiversitylibrary.org/page/9876543',
            'pdf' => $cloudinaryUrls['pdfs']['field_guide']
        ],
        [
            'title' => 'Conservation Status of Endangered Tortricidae Species in India',
            'authors' => 'Dr. Anjali Gupta, Dr. Vikram Singh',
            'year' => 2023,
            'link' => 'https://www.iucn.org/assessment/123456',
            'pdf' => $cloudinaryUrls['pdfs']['conservation_report']
        ],
        [
            'title' => 'DNA Barcoding of Tortricidae: A Comprehensive Study',
            'authors' => 'Dr. Neha Reddy, Dr. Karthik Menon',
            'year' => 2023,
            'link' => 'https://www.ncbi.nlm.nih.gov/pmc/articles/PMC1234567/',
            'pdf' => $cloudinaryUrls['pdfs']['research_paper_1']
        ],
        [
            'title' => 'Agricultural Impact of Tortricidae Pests in Indian Orchards',
            'authors' => 'Dr. Ravi Kumar, Dr. Sunita Agarwal',
            'year' => 2022,
            'link' => 'https://www.sciencedirect.com/science/article/pii/S0022201122001234',
            'pdf' => null
        ],
        [
            'title' => 'Taxonomic Revision of the Genus Archips in India',
            'authors' => 'Dr. Deepak Joshi, Dr. Kavita Nair',
            'year' => 2022,
            'link' => 'https://www.zookeys.pensoft.net/article/98765/',
            'pdf' => null
        ]
    ];
}
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
            background: linear-gradient(135deg, rgba(43, 76, 111, 0.95) 0%, rgba(25, 55, 85, 0.95) 100%);
            padding: 80px 0;
            color: #fff;
            text-align: center;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="%23fff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            pointer-events: none;
        }
        .hero-section h1 {
            position: relative;
            z-index: 1;
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .hero-section p {
            position: relative;
            z-index: 1;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .literature-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            line-height: 1.4;
        }

        .literature-meta {
            font-size: 0.95rem;
            color: #6c757d;
            font-weight: 500;
        }

        .btn-download {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-link {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
            color: white;
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
                                        <a href="<?= htmlspecialchars($entry['link']) ?>" target="_blank" class="btn btn-sm btn-link me-2">
                                            <i class="bi bi-link-45deg"></i> View Online
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($entry['pdf'])): ?>
                                        <a href="<?= htmlspecialchars($entry['pdf']) ?>" target="_blank" class="btn btn-sm btn-download" download>
                                            <i class="bi bi-download"></i> Download PDF
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