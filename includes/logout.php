<?php
// Start the session
session_start();
// Unset all session variables
$_SESSION = array();
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
$headerType == 'home';

// Destroy the session cookie (optional, but good practice)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page or any other page
header("Location: ../index.php");
exit;
?>
