<?php
$headerType = 'reset'; 
require_once 'header.php';
include '../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['token'], $_POST['new_password'], $_POST['confirm_password'])) {
        echo "Invalid request. Please try again.";
        exit;
    }

    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password match
    

    // Hash the new password
   // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    //var_dump($hashed_password); // Debug hashed password

    // Prepare to find user by token
    $stmt = $con->prepare("SELECT emailid FROM login WHERE token_pw = ? AND token_pw_expiry > NOW()");
    if (!$stmt) {
        die("Error preparing select statement: " . $con->error);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['emailid'];

        // Update the password
        $updateStmt = $con->prepare("UPDATE login SET password = ?, token_pw = NULL, token_pw_expiry = NULL WHERE emailid = ?");
        if (!$updateStmt) {
            die("Error preparing update statement: " . $con->error);
        }

        $updateStmt->bind_param("ss", $new_password, $email);

        // Execute and check for errors
        if ($updateStmt->execute()) {



            echo '<div class="container">
            <div class="row align-items-center vh-100">
                <div class="col-6 mx-auto">
                    <div class="card shadow border">
                        <div class="card-body align-items-center">
                            <div class="card-header">
                                <h3 class="mb-0">Change Password</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-success">Password updated successfully.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';    


            
        } else {
            echo "Failed to update password: " . $updateStmt->error;
        }

        $updateStmt->close();
    } else {
        echo "Invalid or expired token.";
    }

    // Close statements and connection
    $stmt->close();
    $con->close();
} else {
    echo "Invalid request method.";
}
?>