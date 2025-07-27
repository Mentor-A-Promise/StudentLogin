<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db/connect.php';
   
   $error='';

   if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Query to find the user with the matching token
    $stmt = $con->prepare("SELECT * FROM login WHERE token = ?");
    $stmt->bind_param('s', $token); // 's' for string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, you can log the user in
        $currentTime = date('Y-m-d H:i:s');
        $user = $result->fetch_assoc(); // Fetch the row into $user

        if ($user['token_expiry'] > $currentTime) {
            // Start session and set user session variables
            $usernamedisp = $user['username']; // Save username to variable
    // Set session variables
    $loginid = $user['loginid'];
$_SESSION['loginid'] = $user['loginid'];
           $_SESSION['username'] = $usernamedisp;

            // Redirect to homepage
             header("Location: ../students/home.php");
            //header("Location: http://sandboxs.online/map/students/home.php?user=" . urlencode($usernamedisp) . "&loginid=" . urlencode($loginid));
            exit();
        } else {
            echo 'Token has expired. Please request a new login link.';
        }
    } else {
        echo "Invalid token!";
    }
} else {
    echo "No token provided!";
}

// Close connection

?>