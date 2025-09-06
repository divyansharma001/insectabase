<?php
// Simple test file to debug deployment issues
echo "<h1>InsectaBase Test Page</h1>";
echo "<p>PHP is working!</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
    require_once 'includes/db.php';
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "<p>Test query result: " . $result['test'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Show environment variables
echo "<h2>Environment Variables</h2>";
echo "<p>DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "</p>";
echo "<p>DB_PORT: " . ($_ENV['DB_PORT'] ?? 'Not set') . "</p>";
echo "<p>DB_NAME: " . ($_ENV['DB_NAME'] ?? 'Not set') . "</p>";
echo "<p>DB_USER: " . ($_ENV['DB_USER'] ?? 'Not set') . "</p>";

// Show PHP info
echo "<h2>PHP Information</h2>";
echo "<p><a href='info.php'>View PHP Info</a></p>";
?>
