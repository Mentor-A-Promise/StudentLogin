<?php

include("../db/connect.php");

//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
//$conn = new mysqli("localhost", "root", "", "appsmat1_sandboxs");

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //$sender_id = $_SESSION['user_id'];
    $sender_id = intval($_POST['sender_id']);
    $receiver_id = intval($_POST['receiver_id']);
    $message = $con->real_escape_string($_POST['message']);
    $parentId = isset($_POST['parent_message_id']) ? intval($_POST['parent_message_id']) : NULL;
    
    if (!empty($receiver_id) && !empty($message)) {
        echo "Inside if block - Receiver and message content present";
        echo 'Parent ID before insert:'.$parentId;
        // Insert the message into the database
        $sql = "INSERT INTO messages (sender_id, receiver_id, message_content, parent_message_id) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("iisi", $sender_id, $receiver_id, $message,$parentId);

        if ($stmt->execute()) {
            echo "Message sent!";
        } else {
            error_log("Database error: " . $stmt->error);
            echo "An error occurred. Please try again.";
        }

        $stmt->close();
    }
    else {
        // Display validation error if receiver or message is missing
        echo "Receiver and message content are required.";
    }    
}
$con->close();

?>

