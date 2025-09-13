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
    
    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Meta tags -->
    <meta name="description" content="InsectaBase Admin Dashboard - Manage your insect database platform">
    <meta name="robots" content="noindex, nofollow">
    
    <style>
        /* Custom dashboard enhancements */
        .dashboard-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #212529;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            color: white !important;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .dashboard-header h1,
        .dashboard-header p {
            color: white !important;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            color: #212529;
        }
        
        .welcome-card h2,
        .welcome-card p,
        .welcome-card small,
        .welcome-card div {
            color: #212529 !important;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            color: #212529;
        }
        
        .stat-card .stat-number,
        .stat-card .stat-label {
            color: #212529 !important;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-color, var(--primary-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: var(--stat-bg, rgba(46, 125, 50, 0.1));
            color: var(--stat-color, var(--primary-color));
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }
        
        .quick-actions {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            color: #212529;
        }
        
        .quick-actions h4,
        .quick-actions p {
            color: #212529 !important;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .quick-action {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 2rem 1.5rem;
            text-decoration: none;
            color: #212529 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .quick-action .quick-action-title,
        .quick-action .quick-action-desc {
            color: #212529 !important;
        }
        
        .quick-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--action-color, var(--primary-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .quick-action:hover::before {
            transform: scaleX(1);
        }
        
        .quick-action:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: var(--action-color, var(--primary-color));
            text-decoration: none;
            color: var(--text-primary);
        }
        
        .quick-action-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            background: var(--action-bg, rgba(46, 125, 50, 0.1));
            color: var(--action-color, var(--primary-color));
        }
        
        .quick-action-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .quick-action-desc {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }
        
        .recent-activity {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            color: #212529;
        }
        
        .recent-activity h5,
        .recent-activity p {
            color: #212529 !important;
        }
        
        .activity-title {
            color: #212529 !important;
        }
        
        .activity-meta {
            color: #6c757d !important;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .activity-meta {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .management-tools {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            color: #212529;
        }
        
        .management-tools h4,
        .management-tools p {
            color: #212529 !important;
        }
        
        .tool-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .tool-btn {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            text-decoration: none;
            color: #212529 !important;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }
        
        .tool-btn:hover {
            background: #2e7d32;
            color: white !important;
            border-color: #2e7d32;
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        .tool-icon {
            width: 35px;
            height: 35px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
            font-size: 1rem;
        }
        
        .tool-btn:hover .tool-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem 0;
            }
            
            .welcome-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
            
            .quick-actions-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .quick-action {
                padding: 1.5rem 1rem;
            }
            
            .tool-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body class="dashboard-container">
    <!-- Modern Admin Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-shield-check" style="font-size: 2rem; color: rgba(255,255,255,0.9);"></i>
                        </div>
                        <div>
                            <h1 class="mb-1" style="font-size: 2rem; font-weight: 700;">Admin Dashboard</h1>
                            <p class="mb-0" style="color: rgba(255,255,255,0.8);">Manage your InsectaBase platform</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-3">
                        <a href="../index.php" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-house me-2"></i>View Site
                        </a>
                        <a href="logout.php" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-speedometer2" style="font-size: 1.5rem; color: white;"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-1" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary);">Welcome to Admin Dashboard</h2>
                            <p class="mb-0" style="color: var(--text-secondary); font-size: 1rem;">Manage your InsectaBase platform efficiently with modern tools</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                        <div class="text-center">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?= number_format($speciesCount + $geneCount + $subfamilyCount + $imageCount) ?></div>
                            <small style="color: var(--text-secondary);">Total Records</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card" style="--stat-color: #2e7d32; --stat-bg: rgba(46, 125, 50, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-bug"></i>
                </div>
                <div class="stat-number"><?= number_format($speciesCount) ?></div>
                <div class="stat-label">Total Species</div>
            </div>
            
            <div class="stat-card" style="--stat-color: #2196f3; --stat-bg: rgba(33, 150, 243, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="stat-number"><?= number_format($geneCount) ?></div>
                <div class="stat-label">Genes</div>
            </div>
            
            <div class="stat-card" style="--stat-color: #ff9800; --stat-bg: rgba(255, 152, 0, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-collection"></i>
                </div>
                <div class="stat-number"><?= number_format($subfamilyCount) ?></div>
                <div class="stat-label">Subfamilies</div>
            </div>
            
            <div class="stat-card" style="--stat-color: #9c27b0; --stat-bg: rgba(156, 39, 176, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-images"></i>
                </div>
                <div class="stat-number"><?= number_format($imageCount) ?></div>
                <div class="stat-label">Images</div>
        </div>

            <div class="stat-card" style="--stat-color: #f44336; --stat-bg: rgba(244, 67, 54, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-palette"></i>
                </div>
                <div class="stat-number"><?= number_format($backgroundCount) ?></div>
                <div class="stat-label">Backgrounds</div>
            </div>
            
            <div class="stat-card" style="--stat-color: #4caf50; --stat-bg: rgba(76, 175, 80, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-newspaper"></i>
                </div>
                <div class="stat-number"><?= number_format($newsCount) ?></div>
                <div class="stat-label">News Articles</div>
            </div>
            
            <div class="stat-card" style="--stat-color: #607d8b; --stat-bg: rgba(96, 125, 139, 0.1);">
                <div class="stat-icon">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div class="stat-number"><?= number_format($literatureCount) ?></div>
                <div class="stat-label">Literature</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-lightning" style="font-size: 1.25rem; color: white;"></i>
                    </div>
                </div>
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Quick Actions</h4>
                    <p class="mb-0" style="color: var(--text-secondary);">Access frequently used management tools</p>
                </div>
            </div>
            
                        <div class="quick-actions-grid">
                <a href="manage_species.php" class="quick-action" style="--action-color: #2e7d32; --action-bg: rgba(46, 125, 50, 0.1);">
                    <div class="quick-action-icon">
                                <i class="bi bi-bug"></i>
                    </div>
                    <div class="quick-action-title">Manage Species</div>
                    <div class="quick-action-desc">Add, edit, or delete insect species</div>
                            </a>
                
                <a href="manage_genes.php" class="quick-action" style="--action-color: #2196f3; --action-bg: rgba(33, 150, 243, 0.1);">
                    <div class="quick-action-icon">
                                <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="quick-action-title">Manage Genes</div>
                    <div class="quick-action-desc">Organize gene data and relationships</div>
                            </a>
                
                <a href="manage_subfamilies.php" class="quick-action" style="--action-color: #ff9800; --action-bg: rgba(255, 152, 0, 0.1);">
                    <div class="quick-action-icon">
                                <i class="bi bi-collection"></i>
                    </div>
                    <div class="quick-action-title">Manage Subfamilies</div>
                    <div class="quick-action-desc">Organize taxonomic classifications</div>
                            </a>
                
                <a href="upload_images.php" class="quick-action" style="--action-color: #9c27b0; --action-bg: rgba(156, 39, 176, 0.1);">
                    <div class="quick-action-icon">
                                <i class="bi bi-upload"></i>
                    </div>
                    <div class="quick-action-title">Upload Images</div>
                    <div class="quick-action-desc">Add new images to the database</div>
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="recent-activity">
        <div class="row">
            <div class="col-lg-6 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #2e7d32, #4caf50); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history" style="font-size: 1.1rem; color: white;"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Recent Species Added</h5>
                            <p class="mb-0" style="color: var(--text-secondary); font-size: 0.875rem;">Latest species entries in the database</p>
                        </div>
                    </div>
                    
                        <?php if (count($recentSpecies) > 0): ?>
                        <div>
                                <?php foreach ($recentSpecies as $species): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="bi bi-bug"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title"><?= htmlspecialchars($species['name']) ?></div>
                                        <div class="activity-meta">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($species['location'] ?? 'Location not specified') ?>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge rounded-pill" style="background: rgba(46, 125, 50, 0.1); color: #2e7d32; font-size: 0.75rem;">
                                            <?= htmlspecialchars($species['status'] ?? 'N/A') ?>
                                        </span>
                                    </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <div style="width: 60px; height: 60px; background: rgba(46, 125, 50, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                <i class="bi bi-bug" style="font-size: 1.5rem; color: #2e7d32;"></i>
                            </div>
                            <p style="color: var(--text-secondary); margin: 0;">No species added yet.</p>
                        </div>
                        <?php endif; ?>
            </div>

            <div class="col-lg-6 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #9c27b0, #e91e63); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 1.1rem; color: white;"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Recent Images Uploaded</h5>
                            <p class="mb-0" style="color: var(--text-secondary); font-size: 0.875rem;">Latest image uploads to the gallery</p>
                        </div>
                    </div>
                    
                        <?php if (count($recentImages) > 0): ?>
                            <div class="row g-2">
                                <?php foreach ($recentImages as $image): ?>
                                    <div class="col-6">
                                    <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                                            <img src="../<?= htmlspecialchars($image['url']) ?>" 
                                                 alt="<?= htmlspecialchars($image['caption'] ?? 'Image') ?>"
                                             style="width: 100%; height: 80px; object-fit: cover;">
                                        <div style="padding: 0.5rem;">
                                            <small style="color: var(--text-secondary); font-size: 0.75rem; line-height: 1.3;">
                                                <?= htmlspecialchars(substr($image['caption'] ?? 'No caption', 0, 30)) ?>
                                                <?= strlen($image['caption'] ?? 'No caption') > 30 ? '...' : '' ?>
                                                </small>
                                        </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <div style="width: 60px; height: 60px; background: rgba(156, 39, 176, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                <i class="bi bi-image" style="font-size: 1.5rem; color: #9c27b0;"></i>
                            </div>
                            <p style="color: var(--text-secondary); margin: 0;">No images uploaded yet.</p>
                        </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Management Tools -->
        <div class="management-tools">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-gear" style="font-size: 1.25rem; color: white;"></i>
                    </div>
                </div>
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: var(--text-primary);">All Management Tools</h4>
                    <p class="mb-0" style="color: var(--text-secondary);">Complete set of administrative tools</p>
                                </div>
                            </div>
            
            <div class="tool-grid">
                <a href="manage_background.php" class="tool-btn">
                    <div class="tool-icon">
                        <i class="bi bi-palette"></i>
                    </div>
                    <span>Manage Backgrounds</span>
                </a>
                
                <a href="manage_news.php" class="tool-btn">
                    <div class="tool-icon">
                        <i class="bi bi-newspaper"></i>
                                </div>
                    <span>Manage News</span>
                </a>
                
                <a href="manage_literature.php" class="tool-btn">
                    <div class="tool-icon">
                        <i class="bi bi-journal-text"></i>
                                </div>
                    <span>Manage Literature</span>
                </a>
                
                <a href="manage_picture.php" class="tool-btn">
                    <div class="tool-icon">
                        <i class="bi bi-image"></i>
                                </div>
                    <span>Manage Pictures</span>
                </a>
                
                <a href="admin_view_contacts.php" class="tool-btn">
                    <div class="tool-icon">
                        <i class="bi bi-envelope"></i>
                                </div>
                    <span>View Contacts</span>
                </a>
                
                <a href="../index.php" class="tool-btn">
                    <div class="tool-icon">
                        <i class="bi bi-house"></i>
                    </div>
                    <span>View Site</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Dashboard Scripts -->
    <script>
        // Add smooth animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Animate quick actions
            const quickActions = document.querySelectorAll('.quick-action');
            quickActions.forEach((action, index) => {
                action.style.opacity = '0';
                action.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    action.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    action.style.opacity = '1';
                    action.style.transform = 'translateY(0)';
                }, (statCards.length * 100) + (index * 100));
            });
            
            // Add hover effects to cards
            const cards = document.querySelectorAll('.stat-card, .quick-action, .tool-btn');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
        
        // Add loading states for better UX
        function showLoading(element) {
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
        }
        
        function hideLoading(element) {
            element.style.opacity = '1';
            element.style.pointerEvents = 'auto';
        }
        
        // Add click handlers for better feedback
        document.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', function() {
                showLoading(this);
                setTimeout(() => hideLoading(this), 1000);
            });
        });
    </script>
</body>
</html>
