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
//$loggedInUserId = 1;
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

// Query to get all messages where the user is the sender or receiver
$sql = "SELECT student_login.username AS student_name, student_login.loginid AS studentid, tutor_login.username AS tutor_name, tutor_login.loginid AS tutorid, student_subject.subject
        FROM student_tutor
        JOIN login AS student_login ON student_tutor.student_id = student_login.loginid
        JOIN login AS tutor_login ON student_tutor.tutor_id = tutor_login.loginid
        JOIN student_subject ON student_tutor.student_id = student_subject.login_id
        WHERE student_tutor.subject_id = student_subject.subject_id
        AND student_tutor.student_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Tutors</title>
    <link rel="stylesheet" href="viewallmessagesstyles.css">
</head>
<body>

<div class='outer-container'>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($stdtutor = $result->fetch_assoc()): ?>
            <div class="message-container">
                <div class="message-content">
                    <a href="viewallmessages.php" class="reply-link"><strong><?= htmlspecialchars($stdtutor['tutor_name']) ?></strong></a><br>
                    <?= htmlspecialchars($stdtutor['subject']) ?>
                </div>
                <div class="bottom-right">
                    <a href="messageThread.php?receiver_id=<?= $stdtutor['tutorid'] ?>" >
                        <button class="green-button" type="button">Send Message</button>
                    </a>
                </div>
            </div>
            <div class="separator"></div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No messages found.</p>
    <?php endif; ?>
</div>

</body>
</html>

