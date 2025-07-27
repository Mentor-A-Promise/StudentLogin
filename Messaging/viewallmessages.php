<?php
$headerType = 'logout';  
require_once '../includes/header.php';

include("../db/connect.php");

//session_start();
//$conn = new mysqli("localhost", "root", "", "appsmat1_sandboxs");

//if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
//}

//$loggedInUserId = $_SESSION['user_id'];
$loggedInUserId = '';
$loggedInUserName = '';

if (isset($_SESSION['username']) && isset($_SESSION['loginid'])) {
    // Fetch session variables
    $loggedInUserName = htmlspecialchars($_SESSION['username']);
    $loggedInUserId = htmlspecialchars($_SESSION['loginid']);
    
    echo '<div class="d-flex justify-content-between align-items-center m-4">';
    // Display username
    echo '<h3>Logged in as student: ' . $loggedInUserName . '</h3>';
    // Add an icon for messages
    echo '<a href="../Messaging/messagetutors.php" class="message-icon">
            <i class="fas fa-comment fa-2x"></i><span class="badge badge-light"></span>
          </a>';
    echo '</div>';
} else {
    // User not logged in
    echo '<div class="alert alert-warning m-4">';
    echo '<h3>Please log in to access this page.</h3>';
    echo '<a href="index.php" class="btn btn-primary">Login</a>'; // Redirect to login page
    echo '</div>';
}



$username = "SELECT username FROM login WHERE loginid = ?";
$usernamestmt = $con->prepare($username);
$usernamestmt->bind_param("i", $loggedInUserId);
$usernamestmt->execute();
$usernameresult = $usernamestmt->get_result();
if ($usernameresult->num_rows > 0) {
    $row = $usernameresult->fetch_assoc();
    $loggedInUserName = $row['username'];
}
$usernamestmt->close();

// Query to get all messages where the user is the sender or receiver
$sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message_content, m.createdOn, u1.username AS sender, u2.username AS receiver
        FROM messages m
        JOIN login u1 ON m.sender_id = u1.loginid
        JOIN login u2 ON m.receiver_id = u2.loginid
        WHERE m.receiver_id = ?
        ORDER BY m.createdOn DESC";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();

$unreadMessageIds = [];
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;  
    $unreadMessageIds[] = $row['id'];
}

$stmt->close();

//Mark the fetched messages as read using the IDs
/*if (!empty($unreadMessageIds)) {
    // Create a comma-separated list of IDs for the SQL query
    $idsToUpdate = implode(',', $unreadMessageIds);
    
    // Update query to mark the messages as read
    $updateSql = "UPDATE messages SET is_read = 1 WHERE id IN ($idsToUpdate)";
    $conn->query($updateSql);
}*/

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="viewallmessagesstyles.css">
</head>
<body>

<!-- <h4>Logged in as : <?php echo $loggedInUserName ?> </h4> -->

<div class='outer-container'>
<?php foreach ($messages as $message): ?>
    <div class="message-container">
        <div class="message-content ">
            <strong><?php echo $message['sender']; ?></strong>
            <?php echo $message['message_content']; ?>
        </div>

        <div class="bottom-right">
            <?php echo $message['createdOn']; ?>
            <a href="messageThread.php?receiver_id=<?php echo $message['sender_id']; ?>" class="reply-link">Reply</a>
       </div>
    </div>
    <div class="separator"></div>
<?php endforeach; ?>
</div>

</body>
</html>
