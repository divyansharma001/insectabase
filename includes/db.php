<?php
$host = 'mysql-f82fb10-harsh-95b2.d.aivencloud.com';
$port = 13853;
$dbname = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_xjqBzadfCGiLfDQ_Dcb';

// Full path to CA certificate
$ca_cert = 'C:/xampp/htdocs/insectabase/includes/ca.pem';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => $ca_cert,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ]
    );
    echo "✅ Database connected successfully!<br>";

    // Test query
    $stmt = $pdo->query("SELECT NOW()");
    echo "Server time: " . $stmt->fetchColumn();
} catch (PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}
