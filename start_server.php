<?php
/**
 * Simple PHP Development Server Starter
 * Run this file to start a local development server for InsectaBase
 */

echo "🚀 Starting InsectaBase Development Server...\n\n";

// Check if PHP is available
if (!function_exists('phpversion')) {
    echo "❌ PHP is not available. Please install PHP first.\n";
    echo "   Download from: https://www.php.net/downloads.php\n";
    exit(1);
}

echo "✅ PHP Version: " . phpversion() . "\n";

// Check required PHP extensions
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "❌ Missing PHP extensions: " . implode(', ', $missing_extensions) . "\n";
    echo "   Please install these extensions to run InsectaBase.\n";
    exit(1);
}

echo "✅ All required PHP extensions are available\n";

// Test database connection
echo "\n🔍 Testing database connection...\n";

try {
    require_once 'includes/db.php';
    echo "✅ Database connection successful!\n";
    echo "   Host: localhost\n";
    echo "   Database: insectabase\n";
    echo "   Port: 3307 (or 3306)\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
    echo "📋 To fix this:\n";
    echo "   1. Make sure MySQL is running\n";
    echo "   2. Check the database credentials in includes/db.php\n";
    echo "   3. Create the database and tables using create_tables.sql\n";
    echo "   4. Update the port number if needed (3306 vs 3307)\n\n";
    
    echo "🔧 Quick setup commands:\n";
    echo "   mysql -u root -p < create_tables.sql\n";
    echo "   # or manually create the database and import the schema\n\n";
}

// Check if required directories exist
$required_dirs = [
    'assets/uploads',
    'assets/uploads/backgrounds',
    'assets/uploads/potd',
    'assets/uploads/subfamilies'
];

echo "📁 Checking required directories...\n";
foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created directory: $dir\n";
        } else {
            echo "❌ Failed to create directory: $dir\n";
        }
    } else {
        echo "✅ Directory exists: $dir\n";
    }
}

echo "\n🌐 Starting PHP development server...\n";
echo "   Server will be available at: http://localhost:8000\n";
echo "   Press Ctrl+C to stop the server\n\n";

echo "📱 Open your browser and navigate to:\n";
echo "   http://localhost:8000\n\n";

echo "🔐 Admin access:\n";
echo "   http://localhost:8000/admin/login.php\n\n";

echo "📚 Available pages:\n";
echo "   - Home: http://localhost:8000/index.php\n";
echo "   - Species: http://localhost:8000/species.php\n";
echo "   - Checklist: http://localhost:8000/checklist.php\n";
echo "   - Literature: http://localhost:8000/literature.php\n";
echo "   - Contact: http://localhost:8000/contact.php\n";
echo "   - Stats: http://localhost:8000/stats.php\n\n";

echo "🚀 Starting server...\n";
echo "=====================================\n";

// Start the PHP development server
$command = 'php -S localhost:8000';
echo "Running: $command\n";
echo "=====================================\n";

// Execute the command
passthru($command);
?>
