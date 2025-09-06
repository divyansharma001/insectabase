<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'InsectaBase - Insect Database' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Performance Optimization Script -->
    <script src="assets/js/performance.js" defer></script>
    
    <!-- Meta tags for SEO and performance -->
    <meta name="description" content="<?= $page_description ?? 'Comprehensive insect database with taxonomy, images, and research literature' ?>">
    <meta name="keywords" content="insects, taxonomy, database, entomology, species, research">
    <meta name="author" content="Harsh Ramrakhiani">
    
    <!-- Performance optimizations -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="assets/css/bootstrap.min.css" as="style">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/logo.jpg">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">

    <style>
        /* Global styles for the dynamic background system */
        .content-background {
            position: relative;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 3rem 0;
            color: #ffffff;
        }
        .background-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 1;
        }
        .content-background > .container {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>

<?php 
// This includes your navigation bar on every page
require_once("navbar.php"); 
?>