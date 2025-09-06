<?php
session_start();

// Destroy session
$_SESSION = [];
session_unset();
session_destroy();

// Set cache headers to prevent back navigation
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login with a message
header("Location: login.php?msg=loggedout");
exit();
?>
