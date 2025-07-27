

<?php
   if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

   include("db/connect.php");

   $error = '';

   if ($_SERVER["REQUEST_METHOD"] == "POST") {
       // Sanitize inputs
       $myusername = mysqli_real_escape_string($con, $_POST['emailid']);
       $mypassword = mysqli_real_escape_string($con, $_POST['password']); 

       // Prepare the SQL statement
       $sql = "SELECT emailid, loginid, username FROM login WHERE emailid = ? AND password = ?";
       $stmt = $con->prepare($sql);

       // Bind parameters (email and password)
       $stmt->bind_param("ss", $myusername, $mypassword);

       // Execute the statement
       $stmt->execute();

       // Get the result
       $result = $stmt->get_result();

       // Check if the user exists
       if ($result->num_rows > 0) {
           // Fetch the row
           $row = $result->fetch_assoc();

           // Set session variables
           $_SESSION['loginid'] = $row['loginid'];
           $_SESSION['username'] = $row['username'];

           // Redirect to home page
           header("Location: students/home.php");
           exit();
       } else {
           // Invalid login
           
           $error = "Invalid email or password";
           
           echo "<script>
           alert('Invalid email or password');
           window.location.href = 'index.php';
       </script>";
       }
   }
?>