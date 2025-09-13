<?php
// debug_login.php - Debug admin login issues
session_start();
require_once 'includes/db.php';

echo "<h2>Admin Login Debug</h2>";

// Check database connection
if (isDatabaseConnected()) {
    echo "✅ Database connected<br>";
} else {
    echo "❌ Database not connected - using fallback<br>";
}

// Check if admin_users table exists and has data
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    $count = $stmt->fetchColumn();
    echo "Admin users in database: $count<br>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT username, role FROM admin_users");
        $users = $stmt->fetchAll();
        echo "Users found:<br>";
        foreach ($users as $user) {
            echo "- Username: " . $user['username'] . " (Role: " . $user['role'] . ")<br>";
        }
    } else {
        echo "No admin users found!<br>";
    }
} catch (Exception $e) {
    echo "❌ Error querying admin_users table: " . $e->getMessage() . "<br>";
}

// Test login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<br><h3>Login Attempt Debug:</h3>";
    echo "Username: '$username'<br>";
    echo "Password length: " . strlen($password) . "<br>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "User found in database<br>";
            echo "Stored password hash: " . substr($user['password'], 0, 20) . "...<br>";
            
            $passwordCheck = password_verify($password, $user['password']);
            echo "Password verification: " . ($passwordCheck ? "✅ SUCCESS" : "❌ FAILED") . "<br>";
            
            if ($passwordCheck) {
                echo "✅ Login would be successful!<br>";
                $_SESSION['admin_user'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                echo "Session set. Redirecting to dashboard...<br>";
                header("Location: admin/dashboard.php");
                exit();
            }
        } else {
            echo "❌ User not found in database<br>";
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Test Login Form</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" value="admin">
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" value="admin123">
            </div>
            <button type="submit" class="btn btn-primary">Test Login</button>
        </form>
        
        <br>
        <a href="create_admin_user.php" class="btn btn-success">Create Admin User</a>
        <a href="admin/login.php" class="btn btn-secondary">Go to Real Login</a>
    </div>
</body>
</html>
