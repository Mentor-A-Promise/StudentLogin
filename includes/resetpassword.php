<?php
$headerType = 'reset'; 
require_once 'header.php';
include '../db/connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Query to find the user with the matching token
    $stmt = $con->prepare("SELECT * FROM login WHERE token_pw = ?");
    $stmt->bind_param('s', $token); // 's' for string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, retrieve user data
        $currentTime = date('Y-m-d H:i:s');
        $user = $result->fetch_assoc();

        if ($user['token_pw_expiry'] > $currentTime) {
            // Set variables for the form's hidden fields
            $usernamedisp = $user['username'];
            $loginid = $user['loginid'];
        } else {
            echo 'Token has expired. Please request a new login link.';
            exit;
        }
    } else {
        echo "Invalid token!";
        exit;
    }
} else {
    echo "No token provided!";
    exit;
}
?>

    <div class="container">
        <div class="row align-items-center vh-100">
            <div class="col-6 mx-auto">
                <div class="card shadow border">
                    <div class="card-body align-items-center">
                        <form id="password-reset-form" class="form" role="form" method="POST" action="resetpwdb.php" autocomplete="off">
                            <span class="anchor" id="formChangePassword"></span>

                            <!-- Form Card for Changing Password -->
                            <div class="card-header">
                                <h3 class="mb-0">Change Password</h3>
                            </div>
                            <div class="card-body">
                                <!-- New Password Input -->
                                <div class="form-group">
                                    <label for="inputPasswordNew">New Password</label>
                                    <input type="password" class="form-control" name="new_password" id="inputPasswordNew" required pattern=".{8,20}" title="Password must be between 8 and 20 characters and not contain spaces.">
                                    <span class="form-text small text-muted">
                                        The password must be 8-20 characters and must <em>not</em> contain spaces.
                                    </span>
                                </div>

                                <!-- Hidden Fields for Username and Login ID -->
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($usernamedisp); ?>">
                                <input type="hidden" name="loginid" value="<?php echo htmlspecialchars($loginid); ?>">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">


                                <!-- Confirm Password Input -->
                                <div class="form-group">
                                    <label for="inputPasswordNewVerify">Verify</label>
                                    <input type="password" class="form-control" name="confirm_password" id="inputPasswordNewVerify" required>
                                    <span class="form-text small text-muted">
                                        To confirm, type the new password again.
                                    </span>
                                </div>
                                <p id="pwdnomatch" class="text-danger p-4" style="display:none;">Passwords do not match</p> 
                                    <p id="pwdvalid" class="text-danger p-4" style="display:none;">Password must be 8-20 characters without spaces.</p> 
                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button class="btn btn-outline-dark rounded-pill bg-dark text-white pt-2 justify-content-end" type="submit"onclick="return validatePasswordForm()">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>