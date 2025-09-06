<?php
// Script to test all pages for database errors
require_once 'includes/db.php';

echo "<h3>Testing All Pages for Database Errors</h3>";

$pages = [
    'index.php' => 'Homepage',
    'species.php' => 'Species page',
    'morphology.php' => 'Morphology page',
    'factsheet.php' => 'Fact Sheet page',
    'checklist.php' => 'Checklist page',
    'literature.php' => 'Literature page',
    'credits.php' => 'Credits page',
    'contact.php' => 'Contact page',
    'about.php' => 'About page',
    'stats.php' => 'Stats page'
];

echo "<h4>Page Tests:</h4>";
echo "<ul>";

foreach ($pages as $file => $name) {
    try {
        // Start output buffering to catch any errors
        ob_start();
        
        // Set up basic variables that pages might need
        $_SERVER['PHP_SELF'] = $file;
        
        // Include the page file
        include $file;
        
        // Clear the output buffer
        ob_end_clean();
        
        echo "<li>✅ <strong>$name</strong> ($file) - Loads successfully</li>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<li>❌ <strong>$name</strong> ($file) - Error: " . $e->getMessage() . "</li>";
    } catch (Error $e) {
        ob_end_clean();
        echo "<li>❌ <strong>$name</strong> ($file) - Fatal Error: " . $e->getMessage() . "</li>";
    }
}

echo "</ul>";

echo "<h4>Database Connection Test:</h4>";
try {
    $result = $pdo->query("SELECT 1")->fetchColumn();
    echo "<p>✅ Database connection is working properly.</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection error: " . $e->getMessage() . "</p>";
}

echo "<h4>All Table Queries Test:</h4>";
$tableQueries = [
    "SELECT COUNT(*) FROM admin_users" => "Admin users",
    "SELECT COUNT(*) FROM species" => "Species",
    "SELECT COUNT(*) FROM genes" => "Genes", 
    "SELECT COUNT(*) FROM subfamilies" => "Subfamilies",
    "SELECT COUNT(*) FROM images" => "Images",
    "SELECT COUNT(*) FROM literature" => "Literature",
    "SELECT COUNT(*) FROM news" => "News",
    "SELECT COUNT(*) FROM backgrounds" => "Backgrounds",
    "SELECT COUNT(*) FROM contacts" => "Contacts"
];

echo "<ul>";
foreach ($tableQueries as $query => $description) {
    try {
        $count = $pdo->query($query)->fetchColumn();
        echo "<li>✅ $description table: $count records</li>";
    } catch (Exception $e) {
        echo "<li>❌ $description table: Error - " . $e->getMessage() . "</li>";
    }
}
echo "</ul>";

echo "<p><strong>Page testing completed!</strong></p>";
echo "<p><a href='create_missing_tables.php'>🔧 Run Database Fix Script</a></p>";
echo "<p><a href='index.php'>🏠 Go to Homepage</a></p>";
?>
