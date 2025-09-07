<?php
/**
 * Cloudinary Setup Script for InsectaBase
 * Run this script to configure Cloudinary integration
 */

echo "🦋 InsectaBase Cloudinary Setup\n";
echo "================================\n\n";

// Check if composer is available
if (!file_exists('vendor/autoload.php')) {
    echo "❌ Composer dependencies not found. Please run: composer install\n";
    exit(1);
}

echo "✅ Composer dependencies found\n";

// Check if Cloudinary is installed
if (!class_exists('Cloudinary\Configuration\Configuration')) {
    echo "❌ Cloudinary PHP SDK not found. Installing...\n";
    echo "Please run: composer require cloudinary/cloudinary_php\n";
    exit(1);
}

echo "✅ Cloudinary PHP SDK found\n";

// Create environment file if it doesn't exist
$envFile = '.env';
if (!file_exists($envFile)) {
    $envContent = "# InsectaBase Environment Configuration\n";
    $envContent .= "# Database Configuration\n";
    $envContent .= "DB_HOST=mysql-f82fb10-harsh-95b2.d.aivencloud.com\n";
    $envContent .= "DB_PORT=13853\n";
    $envContent .= "DB_NAME=defaultdb\n";
    $envContent .= "DB_USER=avnadmin\n";
    $envContent .= "DB_PASSWORD=AVNS_xjqBzadfCGiLfDQ_Dcb\n\n";
    $envContent .= "# Cloudinary Configuration\n";
    $envContent .= "CLOUDINARY_CLOUD_NAME=your_cloud_name\n";
    $envContent .= "CLOUDINARY_API_KEY=your_api_key\n";
    $envContent .= "CLOUDINARY_API_SECRET=your_api_secret\n";
    
    file_put_contents($envFile, $envContent);
    echo "✅ Created .env file with default configuration\n";
} else {
    echo "✅ .env file already exists\n";
}

// Load environment variables
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

echo "\n📋 Configuration Summary:\n";
echo "Database Host: " . ($_ENV['DB_HOST'] ?? 'Not set') . "\n";
echo "Cloudinary Cloud Name: " . ($_ENV['CLOUDINARY_CLOUD_NAME'] ?? 'Not set') . "\n";
echo "Cloudinary API Key: " . (isset($_ENV['CLOUDINARY_API_KEY']) ? 'Set' : 'Not set') . "\n";
echo "Cloudinary API Secret: " . (isset($_ENV['CLOUDINARY_API_SECRET']) ? 'Set' : 'Not set') . "\n";

echo "\n🔧 Next Steps:\n";
echo "1. Sign up for a free Cloudinary account at https://cloudinary.com\n";
echo "2. Get your Cloud Name, API Key, and API Secret from the dashboard\n";
echo "3. Update the .env file with your actual Cloudinary credentials\n";
echo "4. Enable PDF delivery in Cloudinary Settings > Security\n";
echo "5. Upload your images and PDFs to Cloudinary\n";

echo "\n📚 Sample Cloudinary URLs are already configured for demo purposes.\n";
echo "The application will work with sample data until you configure real credentials.\n";

echo "\n✅ Setup complete! You can now use InsectaBase with Cloudinary integration.\n";
?>
