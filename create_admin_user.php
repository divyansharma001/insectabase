<?php
// create_admin_user.php - Create admin user for login
require_once 'includes/db.php';

echo "<h2>Creating Admin User</h2>";

try {
    // Check if database is connected
    if (isDatabaseConnected()) {
        echo "✅ Database connected successfully<br>";
        
        // Check if admin users already exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "Admin users already exist. Count: $count<br>";
            $stmt = $pdo->query("SELECT username, role FROM admin_users");
            $users = $stmt->fetchAll();
            foreach ($users as $user) {
                echo "- " . $user['username'] . " (" . $user['role'] . ")<br>";
            }
        } else {
            echo "No admin users found. Creating default admin user...<br>";
            
            // Create default admin user
            $username = 'admin';
            $password = 'admin123';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, role) VALUES (?, ?, ?)");
            $result = $stmt->execute([$username, $hashedPassword, 'admin']);
            
            if ($result) {
                echo "✅ Admin user created successfully!<br>";
                echo "Username: <strong>$username</strong><br>";
                echo "Password: <strong>$password</strong><br>";
                echo "⚠️ Please change the password after first login!<br>";
            } else {
                echo "❌ Failed to create admin user<br>";
            }
        }
    } else {
        echo "❌ Database not connected. Creating tables and admin user...<br>";
        
        // Create tables if they don't exist
        $createTables = "
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer'
        );
        ";
        
        try {
            $pdo->exec($createTables);
            echo "✅ Tables created successfully<br>";
            
            // Now create admin user
            $username = 'admin';
            $password = 'admin123';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, role) VALUES (?, ?, ?)");
            $result = $stmt->execute([$username, $hashedPassword, 'admin']);
            
            if ($result) {
                echo "✅ Admin user created successfully!<br>";
                echo "Username: <strong>$username</strong><br>";
                echo "Password: <strong>$password</strong><br>";
                echo "⚠️ Please change the password after first login!<br>";
            } else {
                echo "❌ Failed to create admin user<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error creating tables: " . $e->getMessage() . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<h3>Test Login</h3>";
echo "<a href='admin/login.php' class='btn btn-primary'>Go to Admin Login</a>";
echo "<br><br>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Homepage</a>";
?>
