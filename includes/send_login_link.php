<?php
 header('Content-Type: application/json');
 include("../db/connect.php");

// Include PHPMailer dependencies
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Autoload PHPMailer dependencies

if (isset($_POST['email'])) {
    // Sanitize the incoming email input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare the database query
        $emailCheck = $con->prepare("SELECT * FROM login WHERE emailid = ?");
        $emailCheck->bind_param('s', $email); // 's' for string
        $emailCheck->execute();
        $result = $emailCheck->get_result();
        if ($result->num_rows === 0) {
            // Email ID not found
            echo json_encode(['success' => false, 'message' => 'Email ID not found']);
            exit; // Stop further execution
        }
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(16)); // Generate a token
            $expirationTime = date('Y-m-d H:i:s', strtotime('+20 minutes')); // Set token expiration (current time + 20 minutes)

            // Update the token and expiration time in the database
            $sql = "UPDATE login SET token = ?, token_expiry = ? WHERE emailid = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('sss', $token, $expirationTime, $email); // Bind token, expiry, and email
            if ($stmt->execute()) {
                // Send the email using PHPMailer
                $mail = new PHPMailer(true); // Create PHPMailer instance
                try {
                    // Server settings for Mailhog
                    $mail->SMTPDebug = 0; // Disable debug output
                    $mail->isSMTP(); // Use SMTP
                    $mail->Host = 'mail.sandboxs.online';  // Mailhog or other SMTP server
                    $mail->Port = 587; // Mailhog default port
                    $mail->SMTPAuth = true; // No SMTP authentication
                    $mail->SMTPSecure = 'tls'; // Disable encryption
                     $mail->Username = 'admin@sandboxs.online'; // Your SMTP username (full email address)
                        $mail->Password = 'Granite1@#1$1'; // Your SMTP password (use app password if applicable)
                    // Email content
                    $mail->setFrom('admin@sandboxs.online', 'Admin');
                    $mail->addAddress($email); // Recipient's email
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Login Link';
                    $mail->Body = '<a href="http://sandboxs.online/includes/loginviaEmail.php?token=' . $token . '">Click here to login</a>';
                    $mail->AltBody = 'Copy and paste this URL to login: http://sandboxs.online/includes/loginviaEmail.php?token=' . $token;

                    // Send the email
                    $mail->send();
                    // Respond back to AJAX on success
                    echo json_encode(['success' => true, 'message' => 'Login link sent to ' . $email]);
                } catch (Exception $e) {
                    // Catch and display PHPMailer errors
                    echo json_encode(['success' => false, 'message' => "Error: {$mail->ErrorInfo}"]);
                }
            } else {
                // Error updating token
                echo json_encode(['success' => false, 'message' => 'Failed to update token in database']);
            }
        } else {
            // Email not found in the database
            echo json_encode(['success' => false, 'message' => 'Email ID not found']);
        }
    } else {
        // Invalid email address
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    }
} else {
    // No email provided
    echo json_encode(['success' => false, 'message' => 'No email provided']);
}
