<?php
// fix_admin_password.php - Fix admin password hash
require_once 'includes/db.php';

echo "<h2>Fixing Admin Password</h2>";

try {
    if (isDatabaseConnected()) {
        echo "✅ Database connected<br>";
        
        // Check current admin user
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute(['admin']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "Found admin user in database<br>";
            echo "Current password hash: " . substr($user['password'], 0, 30) . "...<br>";
            
            // Create a fresh password hash
            $newPassword = 'admin123';
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            echo "New password hash: " . substr($newHash, 0, 30) . "...<br>";
            
            // Update the password
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $result = $stmt->execute([$newHash, 'admin']);
            
            if ($result) {
                echo "✅ Password updated successfully!<br>";
                
                // Test the new password
                $testResult = password_verify($newPassword, $newHash);
                echo "Password verification test: " . ($testResult ? "✅ SUCCESS" : "❌ FAILED") . "<br>";
                
                echo "<br><strong>Login Credentials:</strong><br>";
                echo "Username: <strong>admin</strong><br>";
                echo "Password: <strong>admin123</strong><br>";
            } else {
                echo "❌ Failed to update password<br>";
            }
        } else {
            echo "❌ Admin user not found. Creating new one...<br>";
            
            // Create new admin user
            $username = 'admin';
            $password = 'admin123';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, role) VALUES (?, ?, ?)");
            $result = $stmt->execute([$username, $hashedPassword, 'admin']);
            
            if ($result) {
                echo "✅ New admin user created!<br>";
                echo "Username: <strong>admin</strong><br>";
                echo "Password: <strong>admin123</strong><br>";
            } else {
                echo "❌ Failed to create admin user<br>";
            }
        }
    } else {
        echo "❌ Database not connected<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<h3>Test Login</h3>";
echo "<a href='debug_login.php' class='btn btn-primary'>Test Login Again</a>";
echo "<br><br>";
echo "<a href='admin/login.php' class='btn btn-success'>Go to Admin Login</a>";
echo "<br><br>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Homepage</a>";
?>
