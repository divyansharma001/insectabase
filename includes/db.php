<?php
// Database configuration - supports both local and Render deployment
$host = $_ENV['DB_HOST'] ?? 'mysql-f82fb10-harsh-95b2.d.aivencloud.com';
$port = (int)($_ENV['DB_PORT'] ?? '13853');
$dbname = $_ENV['DB_NAME'] ?? 'defaultdb';
$user = $_ENV['DB_USER'] ?? 'avnadmin';
$pass = $_ENV['DB_PASSWORD'] ?? 'AVNS_xjqBzadfCGiLfDQ_Dcb';

// SSL configuration for Aiven (only when using Aiven database)
$ssl_options = [];
if (strpos($host, 'aivencloud.com') !== false) {
    $ca_cert = __DIR__ . '/ca.pem';
    if (file_exists($ca_cert)) {
        $ssl_options = [
            PDO::MYSQL_ATTR_SSL_CA => $ca_cert,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ];
    }
}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        array_merge([
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ], $ssl_options)
    );
    
    // Only show connection success in development
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        echo "✅ Database connected successfully!<br>";
        $stmt = $pdo->query("SELECT NOW()");
        echo "Server time: " . $stmt->fetchColumn();
    }
} catch (PDOException $e) {
    // Log error instead of dying to prevent 500 errors
    error_log("Database Connection Failed: " . $e->getMessage());
    
    // For now, create a mock PDO object to prevent fatal errors
    // This allows the app to load even without database connection
    $pdo = new class {
        public function query($sql) {
            throw new Exception("Database not available");
        }
        public function prepare($sql) {
            throw new Exception("Database not available");
        }
        public function exec($sql) {
            throw new Exception("Database not available");
        }
    };
}
