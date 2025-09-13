<?php 
//factsheet.php
session_start(); 
require_once 'includes/db.php'; 
include_once("includes/navbar.php"); 

$page = basename($_SERVER['PHP_SELF']); 
$stmt = $pdo->prepare("SELECT image_url FROM backgrounds WHERE page = ? ORDER BY id DESC LIMIT 1"); 
$stmt->execute([$page]); 
$bg_url = htmlspecialchars($stmt->fetchColumn() ?? '');

$subfamilies = $pdo->query("SELECT * FROM subfamilies ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// If no subfamilies found, create sample data for demonstration
if (empty($subfamilies)) {
    $subfamilies = [
        [
            'id' => 1,
            'name' => 'Tortricinae',
            'description' => 'The largest subfamily of Tortricidae moths, containing many economically important pest species.',
            'image_url' => 'assets/img/banner1.jpg'
        ],
        [
            'id' => 2,
            'name' => 'Olethreutinae',
            'description' => 'A diverse subfamily known for their distinctive wing patterns and feeding habits.',
            'image_url' => 'assets/img/banner2.jpg'
        ],
        [
            'id' => 3,
            'name' => 'Chlidanotinae',
            'description' => 'A smaller subfamily with unique morphological characteristics.',
            'image_url' => 'assets/img/banner3.jpg'
        ]
    ];
} 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Insect Fact Sheet - InsectaBase</title> 
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="assets/css/style.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"> 
    <style>
        .hero-section { 
            background-color: rgba(43, 76, 111, 0.85); 
            padding: 100px 0; 
            color: #fff; 
            text-align: center; 
        } 
        .terminal-bar { 
            display: flex; 
            flex-wrap: wrap; 
            justify-content: center; 
            gap: 15px; 
            background-color: #000; 
            padding: 15px; 
            border-top: 4px solid gold; 
        }
        /* ✅ POPUP POSITIONING FIX */
        .popup { 
            position: fixed; /* Changed to fixed */
            background: #fff; 
            padding: 15px; 
            border: 2px solid #2b4c6f; 
            border-radius: 10px; 
            box-shadow: 0 6px 20px rgba(0,0,0,0.3); 
            display: none; 
            z-index: 1000; 
            max-width: 320px; 
            color: #333;
            pointer-events: none;
        } 
        .popup h5 { 
            font-weight: bold; 
            color: #2b4c6f; 
        }
        .popup img {
            max-width: 150px;
            border-radius: 5px;
            margin: 8px 0;
            display: block;
        }
        #gene-display, #species-display {
            display: grid;
            gap: 1.5rem;
        }
        #gene-display {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        }
        #species-display {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        .gene-box {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            background: #fff;
        }
        .gene-box a {
            display: block;
        }
        .gene-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .gene-box .card-body {
            padding: 0.75rem;
        }
        /* Species styling now handled in main style.css */
    </style> 
</head> 
<body> 
    <div class="hero-section"> 
        <div class="container"> 
            <h1>🧬 Insect Fact Sheet</h1> 
            <p class="lead">Explore detailed information about subfamilies, genes, and species</p> 
        </div> 
    </div> 

    <div class="terminal-bar"> 
        <?php foreach ($subfamilies as $sub): ?> 
            <button class="terminal-button" 
                    data-subid="<?= $sub['id'] ?>" 
                    data-name="<?= htmlspecialchars($sub['name']) ?>" 
                    data-description="<?= htmlspecialchars($sub['description']) ?>" 
                    data-image="<?= htmlspecialchars($sub['image_url']) ?>"> 
                <?= htmlspecialchars($sub['name']) ?> 
            </button> 
        <?php endforeach; ?> 
    </div> 

    <div class="content-background" style="background-image: url('<?= $bg_url ?>');">
        <div class="background-overlay"></div>
        
        <div class="container my-5"> 
            <div id="gene-display"></div> 
            <div id="species-display" class="mt-4"></div> 
        </div> 

        <div id="popup" class="popup"></div> 

    </div>

    <?php include("includes/footer.php"); ?> 

    <script src="assets/js/bootstrap.bundle.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script> 
    
    <script> 
        const geneDisplay = document.getElementById('gene-display'); 
        const speciesDisplay = document.getElementById('species-display'); 
        const popup = document.getElementById('popup'); 
        const lightbox = GLightbox({ selector: '.glightbox', width: '900px' }); 

        // ✅ POPUP POSITIONING FIX
        function showPopup(e, content) {
            if (!content) return;
            popup.innerHTML = content;
            popup.style.top = `${e.clientY + 15}px`; // Using clientY
            popup.style.left = `${e.clientX + 15}px`; // Using clientX
            popup.style.display = 'block';
        }
        function hidePopup() {
            popup.style.display = 'none';
        }

        document.querySelectorAll(".terminal-button").forEach(btn => { 
            btn.addEventListener("click", async () => { 
                const res = await fetch("ajax/load_genes.php", { 
                    method: "POST", 
                    headers: { "Content-Type": "application/x-www-form-urlencoded" }, 
                    body: "subfamily_id=" + btn.dataset.subid 
                }); 
                geneDisplay.innerHTML = await res.text(); 
                speciesDisplay.innerHTML = "";
                lightbox.reload(); 
            }); 
            btn.addEventListener("mouseenter", e => { 
                const imgHtml = btn.dataset.image ? `<img src="${btn.dataset.image}" alt="${btn.dataset.name}">` : '';
                const html = `<h5>${btn.dataset.name}</h5>${imgHtml}<p>${btn.dataset.description}</p>`; 
                showPopup(e, html);
            }); 
            btn.addEventListener("mouseleave", hidePopup); 
        }); 

        geneDisplay.addEventListener('dblclick', e => {
            const box = e.target.closest('.gene-box');
            if (!box) return;
            fetch("ajax/load_species.php", {
                method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: "gene_id=" + box.dataset.geneId
            }).then(res => res.text()).then(html => {
                speciesDisplay.innerHTML = html;
                lightbox.reload();
            });
        });
        
        geneDisplay.addEventListener("mouseover", e => { 
            const box = e.target.closest(".gene-box"); 
            if (box) showPopup(e, `<p>${box.dataset.description || ""}</p>`); 
        }); 
        geneDisplay.addEventListener("mouseout", e => { 
            if (e.target.closest(".gene-box")) hidePopup();
        });

        // ✅ POPUP CONTENT FIX
        speciesDisplay.addEventListener("mouseover", e => { 
            const card = e.target.closest(".species-box"); 
            if (card && e.target.classList.contains("species-image")) { 
                const name = card.dataset.name;
                const status = card.dataset.status;
                const location = card.dataset.location;
                const description = card.dataset.description;
                const diagnosis = card.dataset.diagnosis;

                let popupHTML = `<h5>${name || ''}</h5>`;
                if (status) popupHTML += `<p><strong>Status:</strong> ${status}</p>`;
                if (location) popupHTML += `<p><strong>Location:</strong> ${location}</p>`;
                if (description) popupHTML += `<p><strong>Description:</strong> ${description}</p>`;
                if (diagnosis) popupHTML += `<p><strong>Diagnosis:</strong> ${diagnosis}</p>`;
                
                showPopup(e, popupHTML);
            } 
        });
        speciesDisplay.addEventListener("mouseout", e => {
            if (e.target.closest(".species-box")) hidePopup();
        });
    </script> 
</body> 
</html>