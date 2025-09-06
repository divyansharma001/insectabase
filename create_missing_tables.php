<?php
// Script to create missing database tables and fix column issues
require_once 'includes/db.php';

echo "<h3>Creating Missing Database Tables & Fixing Schema Issues</h3>";

$errors = [];
$successes = [];

try {
    // Create news table
    $pdo->exec('CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        link TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )');
    $successes[] = "✅ News table created/verified successfully.";
    
    // Create backgrounds table
    $pdo->exec('CREATE TABLE IF NOT EXISTS backgrounds (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        image_url TEXT NOT NULL,
        page VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )');
    $successes[] = "✅ Backgrounds table created/verified successfully.";
    
    // Create contacts table
    $pdo->exec('CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )');
    $successes[] = "✅ Contacts table created/verified successfully.";
    
    // Add missing columns to existing tables (if they don't exist)
    
    // Add image_url to subfamilies table
    try {
        $pdo->exec('ALTER TABLE subfamilies ADD COLUMN image_url TEXT');
        $successes[] = "✅ Added image_url column to subfamilies table.";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $successes[] = "✅ image_url column already exists in subfamilies table.";
        } else {
            $errors[] = "❌ Error adding image_url to subfamilies: " . $e->getMessage();
        }
    }
    
    // Add image_url to genes table
    try {
        $pdo->exec('ALTER TABLE genes ADD COLUMN image_url TEXT');
        $successes[] = "✅ Added image_url column to genes table.";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $successes[] = "✅ image_url column already exists in genes table.";
        } else {
            $errors[] = "❌ Error adding image_url to genes: " . $e->getMessage();
        }
    }
    
    // Add year column to literature table
    try {
        $pdo->exec('ALTER TABLE literature ADD COLUMN year INT');
        $successes[] = "✅ Added year column to literature table.";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $successes[] = "✅ year column already exists in literature table.";
        } else {
            $errors[] = "❌ Error adding year to literature: " . $e->getMessage();
        }
    }
    
    // Add latitude and longitude columns to species table
    try {
        $pdo->exec('ALTER TABLE species ADD COLUMN latitude DECIMAL(10, 8)');
        $successes[] = "✅ Added latitude column to species table.";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $successes[] = "✅ latitude column already exists in species table.";
        } else {
            $errors[] = "❌ Error adding latitude to species: " . $e->getMessage();
        }
    }
    
    try {
        $pdo->exec('ALTER TABLE species ADD COLUMN longitude DECIMAL(11, 8)');
        $successes[] = "✅ Added longitude column to species table.";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $successes[] = "✅ longitude column already exists in species table.";
        } else {
            $errors[] = "❌ Error adding longitude to species: " . $e->getMessage();
        }
    }
    
    // Display results
    echo "<h4>Success Messages:</h4>";
    echo "<ul>";
    foreach ($successes as $success) {
        echo "<li>$success</li>";
    }
    echo "</ul>";
    
    if (!empty($errors)) {
        echo "<h4>Error Messages:</h4>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
    
    // Verify tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h4>Current tables in database:</h4>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";
    
    echo "<h4>Testing Database Schema:</h4>";
    $testQueries = [
        "SELECT COUNT(*) FROM species" => "Species table",
        "SELECT COUNT(*) FROM genes" => "Genes table", 
        "SELECT COUNT(*) FROM subfamilies" => "Subfamilies table",
        "SELECT COUNT(*) FROM images" => "Images table",
        "SELECT COUNT(*) FROM literature" => "Literature table",
        "SELECT COUNT(*) FROM news" => "News table",
        "SELECT COUNT(*) FROM backgrounds" => "Backgrounds table",
        "SELECT COUNT(*) FROM contacts" => "Contacts table",
        "SELECT COUNT(*) FROM admin_users" => "Admin users table"
    ];
    
    echo "<ul>";
    foreach ($testQueries as $query => $description) {
        try {
            $count = $pdo->query($query)->fetchColumn();
            echo "<li>✅ $description: $count records</li>";
        } catch (Exception $e) {
            echo "<li>❌ $description: Error - " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
    
    echo "<p><strong>Database schema update completed!</strong></p>";
    echo "<p><a href='index.php' class='btn btn-success'>🏠 Test Homepage</a></p>";
    echo "<p><a href='admin/dashboard.php' class='btn btn-primary'>🛠️ Admin Dashboard</a></p>";
    echo "<p><a href='contact.php' class='btn btn-info'>📧 Test Contact Form</a></p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Critical error: " . $e->getMessage() . "</p>";
}
?>
