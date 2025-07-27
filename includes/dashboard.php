<?php
session_start();
$_SESSION['loggedIn'] = true;

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}
// Prevent page caching hh
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>