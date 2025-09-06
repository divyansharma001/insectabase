<?php 
require_once '../includes/db.php'; 

if (isset($_POST['gene_id'])) { 
    $geneId = intval($_POST['gene_id']); 

    // The SELECT * already gets the description and diagnosis, so no change is needed here.
    $stmt = $pdo->prepare("SELECT * FROM species WHERE gene_id = ?"); 
    $stmt->execute([$geneId]); 
    $speciesList = $stmt->fetchAll(); 

    foreach ($speciesList as $sp): 
?> 
    <div class="card h-100 species-box" 
         data-species-id="<?= $sp['id'] ?>"
         data-name="<?= htmlspecialchars($sp['name']) ?>"
         data-status="<?= htmlspecialchars($sp['status']) ?>"
         data-location="<?= htmlspecialchars($sp['location']) ?>"
         data-description="<?= htmlspecialchars($sp['description']) ?>"
         data-diagnosis="<?= htmlspecialchars($sp['diagnosis']) ?>"
    > 
        <?php if (!empty($sp['image_url'])): ?> 
            <a href="<?= htmlspecialchars($sp['image_url']) ?>" class="glightbox" data-gallery="species-<?= $geneId ?>"> 
                <img src="<?= htmlspecialchars($sp['image_url']) ?>" class="card-img-top species-image"> 
            </a> 
        <?php endif; ?> 
        <div class="card-body"> 
            <h5 class="card-title"><?= htmlspecialchars($sp['name']) ?></h5> 
            <p><strong>Status:</strong> <?= htmlspecialchars($sp['status']) ?></p> 
            <p><strong>Location:</strong> <?= htmlspecialchars($sp['location']) ?></p> 
        </div> 
    </div> 
<?php 
    endforeach; 
} 
?>