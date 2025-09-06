<?php
//contact.php
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
    <title>Contact Us - InsectaBase</title>
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
            border-radius: 10px; /* Rounded corners look nice inside the container */
        }

        .form-container {
            background-color: #ffffffdd;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-control:focus {
            border-color: #2b4c6f;
            box-shadow: 0 0 0 0.2rem rgba(43, 76, 111, 0.25);
        }
    </style>
</head>
<body>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>

    <div class="container py-5">
        
        <div class="hero-section mb-4">
            <h1>Contact Us</h1>
            <p class="lead">We’d love to hear from you! Share your thoughts, questions, or collaborations.</p>
        </div>

        <?php if (!empty($_SESSION['contact_status'])): ?>
            <div class="alert alert-<?= $_SESSION['contact_status']['success'] ? 'success' : 'danger' ?>">
                <?= htmlspecialchars($_SESSION['contact_status']['message']) ?>
            </div>
            <?php unset($_SESSION['contact_status']); ?>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <form action="send_message.php" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label"><strong>Name</strong></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Your full name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><strong>Email</strong></label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="you@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label"><strong>Message</strong></label>
                            <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Your message here..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div> </div> <?php include_once("includes/footer.php"); ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>