<?php
/**
 * Deployment script for InsectaBase
 * This script sets up the database tables and initial data
 */

require_once 'includes/db.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('create_tables.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Database tables created successfully!<br>";
    
    // Insert sample data if tables are empty
    $speciesCount = $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
    if ($speciesCount == 0) {
        // Insert sample subfamily
        $pdo->exec("INSERT INTO subfamilies (name, description) VALUES ('Tortricinae', 'Main subfamily of Tortricidae moths')");
        
        // Insert sample gene
        $pdo->exec("INSERT INTO genes (name, subfamily_id, region, description) VALUES ('COI', 1, 'Mitochondrial', 'Cytochrome oxidase I gene')");
        
        // Insert sample species
        $pdo->exec("INSERT INTO species (name, status, location, diagnosis, subfamily_id, gene_id) VALUES ('Sample Species', 'Valid', 'India', 'Sample diagnosis', 1, 1)");
        
        echo "✅ Sample data inserted successfully!<br>";
    }
    
    // Create admin user if not exists
    $adminCount = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    if ($adminCount == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admin_users (username, password, role) VALUES ('admin', '$hashedPassword', 'admin')");
        echo "✅ Admin user created (username: admin, password: admin123)<br>";
    }
    
    echo "🎉 Deployment completed successfully!";
    
} catch (Exception $e) {
    echo "❌ Deployment failed: " . $e->getMessage();
}
?>
