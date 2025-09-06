<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}

$success = $error = '';

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "✅ Message deleted successfully.";
    } else {
        $error = "❌ Failed to delete message.";
    }
}

// Fetch messages
$messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Contact Messages - InsectaBase</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">📥 Contact Messages</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (count($messages) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $i => $msg): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($msg['name']) ?></td>
                        <td><?= htmlspecialchars($msg['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?></td>
                        <td>
                            <a href="?delete=<?= $msg['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?')" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No messages submitted yet.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>
