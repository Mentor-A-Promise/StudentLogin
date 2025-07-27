/*$(document).ready(function() {
    $("#buttonLoginLink").click(function() {
        document.getElementById('loginModal').style.display = 'none';    });
});*/
// Check if the page is the login page by checking the URL


// code for sending email for login via email 
// Document ready function to ensure the DOM is fully loaded before running scripts
$(document).ready(function() {
    // Event handler for sending login link
    $("#buttonLoginLink").click(function(event) {
        event.preventDefault();  // Prevent default form submission

        var email = $('#emailid').val();  // Get the email input value

        $.ajax({
            url: 'includes/send_login_link.php',  // PHP script to send the email
            type: 'POST',
            dataType: 'json',
            data: { email: email },
            success: function(response) {
                if (response.success === true) {
                    alert(response.message);
                    document.getElementById('loginModal').style.display = 'none';
                    $('#loginEmailModal').modal('show');  // Show modal on success
                } else if (response.success === false) {
                    alert(response.message);  // Display error message
                    document.getElementById('loginModal').style.display = 'none';
                    $('#loginEmailInvalid').modal('show');  // Show invalid email modal
                }
            },
            error: function(xhr, status, error) {
                alert('Error sending email: ' + error);  // Display AJAX error
            }
        });
    });
    
    // Event handler for sending email for password reset
    $("#buttonsendemailpw").click(function(event) {
        event.preventDefault();  // Prevent default form submission

        var email = $('#emailid').val();  // Get the email input value
        

   

        $.ajax({
            url: 'send_pw_link.php',  // PHP script to send the password reset email
            type: 'POST',
            dataType: 'json',
            data: { email: email },
            success: function(response) {
                if (response.success === true) {
                    alert(response.message);  // Show success message
                    document.getElementById('pwdmsg').style.display = 'block';
                } else if (response.success === false) {
                    alert(response.message);  // Display error message
                    document.getElementById('pwdmsginvalid').style.display = 'block';
                }
            },
            error: function(xhr, status, error) {
                alert('Error sending email: ' + error);  // Display AJAX error
            }
        });
    });
});


$(document).ready(function() {
    // Attach validatePasswordForm function to form submit event
    $("#password-reset-form").submit(function(event) {
        // Call the validation function
        if (!validatePasswordForm()) {
            event.preventDefault();  // Prevent form submission if validation fails
        }
    });

    
});
function validatePasswordForm() {
    
    var new_password = $('#inputPasswordNew').val();
    var confirm_password = $('#inputPasswordNewVerify').val();

    if (new_password.length < 8 || new_password.length > 20 || /\s/.test(new_password)) {
        document.getElementById("pwdvalid").style.display = "block";
        return false;
    } else {
        document.getElementById("pwdvalid").style.display = "none";
    }

    if (new_password !== confirm_password) {
        document.getElementById("pwdnomatch").style.display = "block";
        return false;
    } else {
        document.getElementById("pwdnomatch").style.display = "none";
    }

    return true;
}