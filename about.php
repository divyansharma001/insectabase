
<?php
// about.php
session_start();
require_once 'includes/db.php';
include_once("includes/navbar.php");
include_once("includes/header.php");

// 🖼️ Dynamic background support
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE name = ?");
$stmt->execute([$page]);
$bg = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About - InsectaBase</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      <?php if ($bg): ?>
        background-image: url('<?= $bg ?>');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
      <?php else: ?>
        background-color: #f8f9fa;
      <?php endif; ?>
      font-family: 'Segoe UI', sans-serif;
    }

    .hero-section {
      background-color: rgba(43, 76, 111, 0.9);
      padding: 80px 0;
      color: #fff;
      text-align: center;
      margin-bottom: 40px;
    }

    .card, .shadow-sm {
      background-color: #ffffffdd;
    }

    .rounded {
      border-radius: 12px !important;
    }
  </style>
</head>
<body>

<!-- ✅ Hero Section -->
<div class="hero-section">
  <div class="container">
    <h1>About</h1>
    <p class="lead">An introduction to the Indian Tortricidae Database</p>
  </div>
</div>

<!-- ✅ Main Content -->
<div class="container my-5">
  <h2 class="mb-4 text-center">About InsectaBase</h2>

  <div class="row mb-5 align-items-center">
    <div class="col-md-6 mb-4 mb-md-0">
      <img src="assets/img/about_insects.jpg" class="img-fluid rounded shadow" alt="Insect Research Image">
    </div>
    <div class="col-md-6">
      <h4>🎯 Our Mission</h4>
      <p>InsectaBase was created with a vision to increase awareness about the diverse world of insects, focusing on their species, genetic structure, and brain morphology. Our mission is to provide students, researchers, and enthusiasts with reliable, visual, and structured data about insects.</p>

      <h4 class="mt-4">🔬 Our Vision</h4>
      <p>We envision InsectaBase becoming a centralized and authoritative source for insect classification, biology, and taxonomy. The platform bridges science and technology to promote deeper understanding and conservation of insect biodiversity.</p>
    </div>
  </div>

  <!-- ✅ Developer Info -->
  <div class="bg-white border rounded p-4 shadow-sm">
    <h4>👨‍💻 Developer</h4>
    <p>This project was developed by <strong>Harsh Ramrakhiani</strong> as part of a research initiative. It integrates biological data and digital infrastructure to serve the scientific community and the general public alike.</p>
    <p>For suggestions, contributions, or collaborations, feel free to reach out via the <a href="contact.php">Contact page</a>.</p>
  </div>
</div>

<?php include_once 'includes/footer.php'; ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
