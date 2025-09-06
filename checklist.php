<?php
//checklist.php
require_once 'includes/db.php';
include_once("includes/navbar.php");

// ✅ 1. ADD PHP LOGIC to fetch the dynamic background
$page = basename($_SERVER['PHP_SELF']);
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$page]);
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Species Checklist - InsectaBase</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .species-item a {
            text-decoration: none;
            color: #0d6efd;
            transition: all 0.3s ease;
        }
        .species-item a:hover {
            text-decoration: underline;
            color: #0a58ca;
            transform: translateX(5px);
        }
        .gene-section {
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .gene-section:nth-child(1) { animation-delay: 0.1s; }
        .gene-section:nth-child(2) { animation-delay: 0.2s; }
        .gene-section:nth-child(3) { animation-delay: 0.3s; }
        .gene-section:nth-child(4) { animation-delay: 0.4s; }
        .gene-section:nth-child(5) { animation-delay: 0.5s; }

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

        /* Make sure the text is readable on a dark background */
        .content-background .text-success, .content-background .form-control::placeholder {
             color: #ffffff !important;
        }
        .content-background .form-control {
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* Performance optimizations */
        .accordion-item {
            will-change: transform;
            backface-visibility: hidden;
        }

        /* Search highlight */
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Smooth transitions */
        .accordion-collapse {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Performance enhanced scrollbar */
        .accordion-body::-webkit-scrollbar {
            width: 6px;
        }
        .accordion-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .accordion-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        .accordion-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body>

<div class="content-background" style="background-image: url('<?= $bg_url ?>');">
    <div class="background-overlay"></div>

    <div class="container my-5">
        <div class="checklist-container observe-fade-in">
            <div class="checklist-header">
                <h2>🗂️ Species Checklist</h2>
            </div>

            <div class="checklist-search observe-slide-left">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search species or genus..." autocomplete="off">
                <div class="search-help">
                    <i class="bi bi-info-circle"></i> 
                    Type to search through subfamilies, genes, and species
                </div>
            </div>

        <div class="accordion" id="checklistAccordion">
            <?php
            $subfamilies = $pdo->query("SELECT * FROM subfamilies ORDER BY name")->fetchAll();
            $sfIndex = 0;

            foreach ($subfamilies as $sub) {
                $sfId = 'sf' . $sfIndex;
                ?>
                <div class="accordion-item observe-fade-in" style="animation-delay: <?= ($sfIndex * 0.1) ?>s">
                    <h2 class="accordion-header" id="heading<?= $sfId ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $sfId ?>">
                            <i class="bi bi-collection"></i>
                            <a href="factsheet.php?subfamily=<?= urlencode($sub['id']) ?>" class="subfamily-link" onclick="event.stopPropagation();">
                                <?= htmlspecialchars($sub['name']) ?>
                            </a>
                        </button>
                    </h2>
                    <div id="collapse<?= $sfId ?>" class="accordion-collapse collapse" data-bs-parent="#checklistAccordion">
                        <div class="accordion-body">
                            <?php
                            $genes = $pdo->prepare("SELECT * FROM genes WHERE subfamily_id = ? ORDER BY name");
                            $genes->execute([$sub['id']]);
                            $geneIndex = 0;
                            foreach ($genes as $gene) {
                                ?>
                                <div class="gene-section" style="animation-delay: <?= ($geneIndex * 0.1) ?>s">
                                    <strong>
                                        <i class="bi bi-diagram-3"></i>
                                        <a href="morphology.php?gene=<?= urlencode($gene['id']) ?>" class="gene-link">
                                            <?= htmlspecialchars($gene['name']) ?>
                                        </a>
                                    </strong>
                                    <ul class="list-unstyled ms-3 mt-2 species-list">
                                        <?php
                                        $species = $pdo->prepare("SELECT * FROM species WHERE gene_id = ? ORDER BY name");
                                        $species->execute([$gene['id']]);
                                        $speciesIndex = 0;
                                        foreach ($species as $sp) {
                                            echo "<li class='species-item' style='animation-delay: " . ($speciesIndex * 0.05) . "s'>
                                                <a href='species.php?id={$sp['id']}' title='Click to view profile'>
                                                    <i class='bi bi-arrow-right-circle'></i> " . htmlspecialchars($sp['name']) . "
                                                </a>
                                            </li>";
                                            $speciesIndex++;
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <hr>
                            <?php 
                                $geneIndex++;
                            } 
                            ?>
                        </div>
                    </div>
                </div>
                <?php $sfIndex++;
            }
            ?>
        </div>

            <!-- Performance metrics -->
            <div class="checklist-stats observe-fade-in">
                <small>
                    <i class="bi bi-speedometer2"></i> 
                    Total: <?= count($subfamilies) ?> subfamilies, 
                    <?= $pdo->query("SELECT COUNT(*) FROM genes")->fetchColumn() ?> genes, 
                    <?= $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn() ?> species
                </small>
            </div>
        </div>
    </div>
</div> <?php include("includes/footer.php"); ?>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/performance.js"></script>
<script>
// Enhanced search with performance optimizations
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const accordionItems = document.querySelectorAll('.accordion-item');

// Debounced search for better performance
searchInput.addEventListener('input', function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(this.value.toLowerCase());
    }, 300);
});

function performSearch(filter) {
    if (!filter) {
        // Show all items if no search term
        accordionItems.forEach(item => {
            item.style.display = '';
            item.querySelectorAll('.species-item').forEach(species => {
                species.style.display = '';
            });
        });
        return;
    }

    accordionItems.forEach(item => {
        let isMatch = false;
        const subfamilyName = item.querySelector('.accordion-button').textContent.toLowerCase();
        
        // Check subfamily name
        if (subfamilyName.includes(filter)) {
            isMatch = true;
        }

        // Check gene names and species
        item.querySelectorAll('.gene-section').forEach(geneSection => {
            const geneName = geneSection.querySelector('strong').textContent.toLowerCase();
            let geneHasMatch = false;
            
            if (geneName.includes(filter)) {
                geneHasMatch = true;
                isMatch = true;
            }
            
            // Check species names
            geneSection.querySelectorAll('.species-item').forEach(species => {
                const speciesName = species.textContent.toLowerCase();
                if (speciesName.includes(filter)) {
                    species.style.display = '';
                    geneHasMatch = true;
                    isMatch = true;
                    
                    // Highlight search term
                    const speciesText = species.querySelector('a');
                    if (speciesText && !speciesText.innerHTML.includes('search-highlight')) {
                        const regex = new RegExp(`(${filter})`, 'gi');
                        speciesText.innerHTML = speciesText.innerHTML.replace(regex, '<span class="search-highlight">$1</span>');
                    }
                } else {
                    species.style.display = 'none';
                }
            });
            
            // Show/hide gene section based on matches
            geneSection.style.display = geneHasMatch ? '' : 'none';
        });

        // Show/hide the entire accordion item
        item.style.display = isMatch ? '' : 'none';
        
        // Auto-expand matching sections
        if (isMatch) {
            const collapseElement = item.querySelector('.accordion-collapse');
            if (collapseElement && !collapseElement.classList.contains('show')) {
                const button = item.querySelector('.accordion-button');
                button.click();
            }
        }
    });
}

// Performance optimization: Lazy load accordion content
document.addEventListener('DOMContentLoaded', function() {
    const accordionButtons = document.querySelectorAll('.accordion-button');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement && !targetElement.classList.contains('show')) {
                // Add loading state
                this.classList.add('loading');
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                
                // Simulate loading delay for better UX
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.innerHTML = this.getAttribute('data-original-text') || this.innerHTML;
                }, 300);
            }
        });
    });
});

// Clear search highlights when search is cleared
searchInput.addEventListener('keyup', function() {
    if (!this.value) {
        document.querySelectorAll('.search-highlight').forEach(highlight => {
            highlight.outerHTML = highlight.innerHTML;
        });
    }
});
</script>
</body>
</html>