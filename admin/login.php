<?php
session_start();

// Prevent back-button after logout (no caching)
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_user'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "❌ Database error. Please try again.";
        }
    } else {
        $error = "❌ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-background"></div>
        
        <div class="login-form-container">
            <div class="login-header text-center mb-4">
                <a href="../index.php" class="back-link">
                    <i class="bi bi-arrow-left me-2"></i>Back to Site
                </a>
                
                <div class="logo-section mt-4">
                    <img src="../assets/img/logo.jpg" alt="InsectaBase Logo" class="login-logo">
                    <h1 class="login-title">InsectaBase</h1>
                    <p class="login-subtitle">Admin Portal</p>
                </div>
            </div>

            <div class="login-card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Admin Login
                    </h3>
                    <p class="mb-0 text-light">Enter your credentials to access the admin panel</p>
                </div>
                
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="login-form">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-person me-2"></i>Username
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                   required 
                                   autocomplete="username"
                                   placeholder="Enter your username">
                        </div>

                        <div class="form-group mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-key me-2"></i>Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Enter your password">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </button>
                        </div>
                    </form>

                    <div class="login-footer text-center mt-4">
                        <p class="text-muted mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Contact administrator for access credentials
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 450px;
            z-index: 2;
        }

        .login-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            opacity: 0.1;
            z-index: -1;
        }

        .login-form-container {
            animation: slideInUp 0.8s ease-out;
        }

        .login-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .back-link {
            color: #6c757d !important;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .back-link:hover {
            color: #2e7d32 !important;
            transform: translateX(-5px);
        }

        .logo-section {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-color);
            box-shadow: var(--shadow-md);
            margin-bottom: 1rem;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .login-title {
            color: #1b5e20 !important;
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-align: center;
            line-height: 1.2;
        }

        .login-subtitle {
            color: #6c757d !important;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0;
            text-align: center;
        }

        .login-card {
            background: var(--bg-primary);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: none;
        }

        .login-card .card-header {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            color: white !important;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-card .card-header h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white !important;
        }

        .login-card .card-header p {
            text-align: center;
            margin: 0;
            opacity: 0.9;
            color: white !important;
        }

        .login-card .card-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            background: white;
            color: #212529;
        }

        .form-group {
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .form-label {
            font-weight: 600;
            color: #212529 !important;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            text-align: left;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #dee2e6;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: white;
            color: #212529;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
            outline: none;
            transform: translateY(-1px);
            background-color: white;
            color: #212529;
        }

        .password-input-group {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .password-input-group .form-control {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #2e7d32;
            background: rgba(46, 125, 50, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            border: none;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0 auto;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .login-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 1.5rem;
            text-align: center;
            margin-top: 1rem;
        }

        .login-footer p {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #6c757d !important;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24 !important;
            border-left: 4px solid #dc3545;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-page {
                padding: 1.5rem 1rem;
            }
            
            .login-container {
                max-width: 100%;
            }
            
            .login-card .card-header {
                padding: 1.5rem 1.5rem 1rem;
            }
            
            .login-card .card-body {
                padding: 1.5rem;
            }
            
            .login-title {
                font-size: 2.2rem;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .form-control {
                padding: 0.875rem;
            }
            
            .btn-primary {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .login-page {
                padding: 1rem 0.75rem;
            }
            
            .login-container {
                max-width: 100%;
            }
            
            .login-card .card-header {
                padding: 1.25rem 1.25rem 0.75rem;
            }
            
            .login-card .card-body {
                padding: 1.25rem;
            }
            
            .login-title {
                font-size: 2rem;
            }
            
            .login-subtitle {
                font-size: 1rem;
            }
            
            .login-logo {
                width: 60px;
                height: 60px;
            }
            
            .form-control {
                padding: 0.75rem;
                font-size: 0.95rem;
            }
            
            .btn-primary {
                padding: 0.75rem 1.25rem;
                font-size: 0.95rem;
            }
            
            .form-label {
                font-size: 0.9rem;
            }
            
            .password-toggle {
                right: 0.75rem;
                padding: 0.375rem;
            }
        }

        @media (max-width: 400px) {
            .login-page {
                padding: 0.75rem 0.5rem;
            }
            
            .login-card .card-header {
                padding: 1rem 1rem 0.5rem;
            }
            
            .login-card .card-body {
                padding: 1rem;
            }
            
            .login-title {
                font-size: 1.75rem;
            }
            
            .login-logo {
                width: 50px;
                height: 50px;
            }
        }
    </style>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            }
        }

        // Add focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Form validation
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
        });
    </script>
</body>
</html>
