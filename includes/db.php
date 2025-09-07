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
            return new class($sql) {
                private $sql;
                public function __construct($sql) {
                    $this->sql = $sql;
                }
                public function fetchColumn() { 
                    // Return beautiful sample data for counts
                    if (strpos($this->sql, 'COUNT(*)') !== false) {
                        if (strpos($this->sql, 'species') !== false) return 247;
                        if (strpos($this->sql, 'genes') !== false) return 89;
                        if (strpos($this->sql, 'subfamilies') !== false) return 34;
                    }
                    return 0; 
                }
                public function fetch() { 
                    // Return beautiful sample picture of the day
                    if (strpos($this->sql, 'images') !== false && strpos($this->sql, 'species_id IS NULL') !== false) {
                        $sampleImages = [
                            [
                                'url' => 'assets/img/banner1.jpg',
                                'caption' => 'Beautiful Tortricidae Moth - Sample from InsectaBase Collection'
                            ],
                            [
                                'url' => 'assets/img/banner2.jpg', 
                                'caption' => 'Exquisite Wing Patterns - Indian Tortricidae Species'
                            ],
                            [
                                'url' => 'assets/img/banner3.jpg',
                                'caption' => 'Detailed Morphology Study - Tortricidae Family'
                            ]
                        ];
                        return $sampleImages[array_rand($sampleImages)];
                    }
                    return false; 
                }
                public function fetchAll() { 
                    // Return beautiful sample news data
                    if (strpos($this->sql, 'news') !== false) {
                        return [
                            [
                                'title' => 'New Tortricidae Species Discovered in Western Ghats',
                                'link' => 'https://example.com/news/new-species-discovery',
                                'created_at' => date('Y-m-d H:i:s')
                            ],
                            [
                                'title' => 'Research Paper: Molecular Phylogeny of Indian Tortricidae',
                                'link' => 'https://example.com/news/molecular-phylogeny-study',
                                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                            ],
                            [
                                'title' => 'InsectaBase Database Update - 50 New Species Added',
                                'link' => 'https://example.com/news/database-update',
                                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                            ],
                            [
                                'title' => 'Conservation Status of Endangered Tortricidae in India',
                                'link' => 'https://example.com/news/conservation-status',
                                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                            ],
                            [
                                'title' => 'Field Guide: Identifying Tortricidae Moths in India',
                                'link' => 'https://example.com/news/field-guide',
                                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
                            ],
                            [
                                'title' => 'Database Connection Required - Please configure your database',
                                'link' => '#',
                                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                            ]
                        ];
                    }
                    return []; 
                }
            };
        }
        public function prepare($sql) {
            return new class($sql) {
                private $sql;
                public function __construct($sql) {
                    $this->sql = $sql;
                }
                public function execute($params = []) { return true; }
                public function fetchColumn() { 
                    // Return beautiful default background for background images
                    if (strpos($this->sql, 'backgrounds') !== false) {
                        $backgrounds = [
                            'assets/img/banner1.jpg',
                            'assets/img/banner2.jpg', 
                            'assets/img/banner3.jpg',
                            'assets/img/banner4.jpg',
                            'assets/img/banner5.jpg',
                            'assets/img/banner6.jpg'
                        ];
                        return $backgrounds[array_rand($backgrounds)];
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
    return '<div class="alert alert-info alert-dismissible fade show" role="alert" style="margin: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3" style="font-size: 1.5rem; color: #0dcaf0;"></i>
            <div>
                <h6 class="alert-heading mb-1">🦋 InsectaBase Demo Mode</h6>
                <p class="mb-2">You are viewing sample data. The application is running in offline mode with beautiful demo content.</p>
                <small class="text-muted">Configure your database connection to access the full InsectaBase functionality.</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
