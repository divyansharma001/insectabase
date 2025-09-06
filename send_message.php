<?php
// send_message.php
session_start();
require_once 'includes/db.php'; // Make sure this path is correct

$response = [
    'success' => false,
    'message' => 'Something went wrong. Please try again later.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $message]);

                $response['success'] = true;
                $response['message'] = '✅ Your message has been sent successfully!';
            } catch (PDOException $e) {
                error_log("DB Insert Error: " . $e->getMessage());
                $response['message'] = '❌ Database error. Please try again later.';
            }
        }
    } else {
        $response['message'] = '❗ All fields are required.';
    }
}

// For AJAX requests
if (
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For standard form redirect
$_SESSION['contact_status'] = $response;
header('Location: contact.php');
exit;
