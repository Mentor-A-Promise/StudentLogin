<?php
include("../db/connect.php");
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
/*$host = 'localhost'; // Database host
$db   = 'MySql'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

// Create a database connection
$con = new mysqli($host, $user, $pass, $db);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);*/
}
$message_sent = '';


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the message from the form
    $message = htmlspecialchars($_POST['message']);
    $tutor_name = "Annie"; // Replace with the actual tutor's name if available
    $sender_name = "Katie"; // Replace with the logged-in user's name
    $email = "Annie.kk@example.com"; // Replace with the tutor's email if available

    // Prepare and bind
    $stmt = $con->prepare("INSERT INTO messages (tutor_name, sender_name, email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $tutor_name, $sender_name, $email, $message);

    // Execute the statement
    if ($stmt->execute()) {
        $message_sent = "Message Sent: <strong>" . $message . "</strong>";
    } else {
        echo "Error: " . $stmt->error;
    } 

    // Close statement
    $stmt->close();
}

// Close the database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message Interface</title>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#mytextarea', // The ID of the textarea to turn into a rich text editor
            height: 300, // Height of the editor in pixels
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating', // Makes the toolbar floating
            menubar: false, // Removes the menubar for a cleaner look
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
        });
    </script>

    <style>
        /* Basic styling for the form */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        #details {
            position: absolute; /* Ensures the div is positioned relative to the browser window */
            top: 90px; /* Adjust distance from the top */
            left: 550px; /* Adjust distance from the left */
           /* background: white; Background color for better readability */
            padding: 10px; /* Padding inside the box */
            border-radius: 5px; /* Rounded corners for better appearance */
            /*box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);  Adds a subtle shadow */
        }

        .container {
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 5, 0, 0.1);
            border-radius: 8px;
            border-color:#28a745 ;
            width: 800px;
            text-align: center;
        }
        .info-box {
            text-align: left;
            margin-bottom: 15px;
            font-size: 14px;
            line-height: 1.5;
        }

        
            .message-box {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            resize: none;
            color: #000; /* Text color inside the message box */
            align-self:auto ;
        }
        
        .send-button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .send-button:hover {
            background-color: #218838;
        }
        h2 {
            text-align: center;
            margin-top: 0;
        }

    </style>
</head>
<body>
<div id="details">
<p><strong>Logged in as :</strong> Katie</p>
<p><strong>Subject Mathematics  :</strong> Grade 5</p>
<p><strong>Mathematics Tutor Details  :</strong> </p>
</div>

<div class="container">
   <div class="info-box">
            <p><strong>Tutor Name:</strong> Annie</p>
            <p><strong>Tutor Email:</strong> Annie.kk@example.com</p>
            <p><strong>Tutor PHno: </strong> +1 7488976788</p>

        </div>

        <h2>Leave message for the Tutor</h2>
        <form action="send_message_interface.php" method="POST">
            <textarea name="message" class="message-box" placeholder="Type your message..." required></textarea>
            <button type="submit" class="send-button">Send Message</button>
        </form>
        <?php if (!empty($message_sent)): ?>
            <div style="margin-top: 15px; color: black; text-align: center;">
                <?php echo $message_sent; ?>
            </div>
        <?php endif; ?>


    </div>
</body>
</html>
