<?php
// Database configuration - supports both local and Render deployment
$host = $_ENV['DB_HOST'] ?? 'mysql-f82fb10-harsh-95b2.d.aivencloud.com';
$port = (int)($_ENV['DB_PORT'] ?? '13853');
$dbname = $_ENV['DB_NAME'] ?? 'defaultdb';
$user = $_ENV['DB_USER'] ?? 'avnadmin';
$pass = $_ENV['DB_PASSWORD'] ?? 'AVNS_xjqBzadfCGiLfDQ_Dcb';

// Track database connection status
$db_connected = false;

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    $db_connected = true;
    
    // Only show connection success in development
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        echo "✅ Database connected successfully!<br>";
        $stmt = $pdo->query("SELECT NOW()");
        echo "Server time: " . $stmt->fetchColumn();
    }
} catch (PDOException $e) {
    // Log error instead of dying to prevent 500 errors
    error_log("Database Connection Failed: " . $e->getMessage());
    
    // Create a mock PDO object that returns fallback data instead of throwing exceptions
    // This allows the app to load even without database connection
    $pdo = new class {
        public function query($sql) {
            return new class {
                public function fetchColumn() { 
                    // Return sample data for counts
                    if (strpos($sql, 'COUNT(*)') !== false) {
                        if (strpos($sql, 'species') !== false) return 150;
                        if (strpos($sql, 'genes') !== false) return 75;
                        if (strpos($sql, 'subfamilies') !== false) return 25;
                    }
                    return 0; 
                }
                public function fetch() { 
                    // Return sample picture of the day
                    if (strpos($sql, 'images') !== false && strpos($sql, 'species_id IS NULL') !== false) {
                        return [
                            'url' => 'assets/img/banner1.jpg',
                            'caption' => 'Sample insect image - Database connection required for real data'
                        ];
                    }
                    return false; 
                }
                public function fetchAll() { 
                    // Return sample news data
                    if (strpos($sql, 'news') !== false) {
                        return [
                            [
                                'title' => 'Database Connection Required',
                                'link' => '#',
                                'created_at' => date('Y-m-d H:i:s')
                            ],
                            [
                                'title' => 'Please check your database configuration',
                                'link' => '#',
                                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                            ]
                        ];
                    }
                    return []; 
                }
            };
        }
        public function prepare($sql) {
            return new class {
                public function execute($params = []) { return true; }
                public function fetchColumn() { 
                    // Return empty string for background images
                    if (strpos($sql, 'backgrounds') !== false) {
                        return 'assets/img/banner2.jpg'; // Default background
                    }
                    return ''; 
                }
                public function fetch() { return false; }
                public function fetchAll() { return []; }
            };
        }
        public function exec($sql) {
            return 0;
        }
    };
}

// Function to check if database is connected
function isDatabaseConnected() {
    global $db_connected;
    return $db_connected;
}

// Function to show database status message
function getDatabaseStatusMessage() {
    if (isDatabaseConnected()) {
        return '';
    }
    return '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Database Connection Issue:</strong> The application is running in offline mode with sample data. 
        Please check your database configuration to access full functionality.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}
