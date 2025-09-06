<?php
//credits.php
session_start();
require_once 'includes/db.php';
include_once("includes/navbar.php");

// ✅ 1. UPDATED PHP LOGIC to fetch the latest background for this page
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$page]);
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Credits - InsectaBase</title>
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

        .credit-card {
            background: #ffffffdd;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 20px;
        }

        .credit-card h5 {
            margin-bottom: 15px;
        }

        ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>
    
    <div class="container py-4">

        <div class="hero-section">
            <h1>🌟 Credits & Acknowledgments</h1>
            <p class="lead">Acknowledging every individual and tool that made InsectaBase possible.</p>
        </div>

        <div class="py-4">
            <div class="credit-card">
                <h5><i class="bi bi-person-fill"></i> Developed By</h5>
                <p><strong>Harsh Ramrakhiani</strong><br>
                Department of Information Technology<br>
                Maharaja Surajmal Institute of Technology, IPU, New Delhi</p>
            </div>

            <div class="credit-card">
                <h5><i class="bi bi-journal-code"></i> Project Guidance</h5>
                <p>Special thanks to faculty and academic mentors for providing insight and direction on structuring biological data systems.</p>
            </div>

            <div class="credit-card">
                <h5><i class="bi bi-database-check"></i> Data Sources</h5>
                <ul>
                    <li>Wikipedia - Insect species and anatomical information</li>
                    <li>NCBI Taxonomy Browser</li>
                    <li>Indian Agricultural Research Institute (IARI)</li>
                </ul>
            </div>

            <div class="credit-card">
                <h5><i class="bi bi-image"></i> Image Credits</h5>
                <p>Images used are sourced from public domain resources, Wikimedia Commons, or manually contributed by users via InsectaBase upload panel.</p>
            </div>

            <div class="credit-card">
                <h5><i class="bi bi-tools"></i> Tools & Frameworks</h5>
                <ul>
                    <li>PHP & MySQL (XAMPP)</li>
                    <li>Bootstrap 5 & Bootstrap Icons</li>
                    <li>Leaflet.js (for maps)</li>
                    <li>Chart.js / PDF.js (planned features)</li>
                </ul>
            </div>

            <div class="credit-card">
                <h5><i class="bi bi-people-fill"></i> Contributors</h5>
                <p>If you'd like to contribute to InsectaBase, contact: <strong>harsh.ramrakhiani@gmail.com</strong></p>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-success"><i class="bi bi-house-door-fill"></i> Back to Home</a>
            </div>
        </div>

    </div> </div> <?php include("includes/footer.php"); ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>