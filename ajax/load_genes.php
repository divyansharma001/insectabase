<?php 
 require_once '../includes/db.php'; 

 if (isset($_POST['subfamily_id'])) { 
  $subfamilyId = intval($_POST['subfamily_id']); 

  $stmt = $pdo->prepare("SELECT id, name, image_url, description FROM genes WHERE subfamily_id = ?"); 
  $stmt->execute([$subfamilyId]); 
  $genes = $stmt->fetchAll(); 

  // If no genes found, create sample data for demonstration
  if (empty($genes)) {
    $sampleGenes = [
      [
        'id' => 1,
        'name' => 'COI Gene',
        'description' => 'Cytochrome c oxidase subunit I - commonly used for DNA barcoding',
        'image_url' => 'assets/img/banner4.jpg'
      ],
      [
        'id' => 2,
        'name' => 'ITS2 Gene',
        'description' => 'Internal Transcribed Spacer 2 - nuclear ribosomal DNA marker',
        'image_url' => 'assets/img/banner5.jpg'
      ],
      [
        'id' => 3,
        'name' => 'EF-1α Gene',
        'description' => 'Elongation Factor 1-alpha - nuclear protein-coding gene',
        'image_url' => 'assets/img/banner6.jpg'
      ]
    ];
    $genes = $sampleGenes;
  }

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