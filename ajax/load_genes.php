<?php 
 require_once '../includes/db.php'; 

 if (isset($_POST['subfamily_id'])) { 
  $subfamilyId = intval($_POST['subfamily_id']); 

  $stmt = $pdo->prepare("SELECT id, name, image_url, description FROM genes WHERE subfamily_id = ?"); 
  $stmt->execute([$subfamilyId]); 
  $genes = $stmt->fetchAll(); 

  // The container #gene-display is now a CSS Grid, so we don't need col-* wrappers.
  foreach ($genes as $gene): 
 ?> 
  <div class="gene-box" 
       data-gene-id="<?= htmlspecialchars($gene['id']) ?>" 
       data-description="<?= htmlspecialchars($gene['description']) ?>">
      <a href="<?= htmlspecialchars($gene['image_url']) ?>" class="glightbox" data-gallery="genes"> 
        <img src="<?= htmlspecialchars($gene['image_url']) ?>" class="gene-thumbnail"> 
      </a> 
      <div class="card-body"> 
        <h5 class="card-title mb-0"><?= htmlspecialchars($gene['name']) ?></h5> 
      </div> 
  </div> 
 <?php 
  endforeach; 
 } 
 ?>