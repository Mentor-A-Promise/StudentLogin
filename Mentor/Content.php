<?php
   include("db/connect.php");
   session_start();
   $error='';
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {

      
      $sql = "SELECT subject , Last , username FROM subject_testing WHERE emailid = '$myusername' and password = '$mypassword'";

      $result = mysqli_query($con,$sql);      
      $row = mysqli_num_rows($result);      
      $count = mysqli_num_rows($result);
      
   
      if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    print_r($row);
        //$_SESSION['login_user'] = $row['username']; // Store the username in session
        $db_email = $row['emailid'];
    $db_password = $row['password']; // This is the hashed password
    $db_username = $row['username'];
        $usernamedisp = $row['username']; // Save username to variable
       header("Location: students/home.php?user=" . urlencode($usernamedisp));
        exit(); 

        
      } else {
         $error = "Your Login Name or Password is invalid";
      }
   }
   //echo "Logged in as student: " . htmlspecialchars($usernamedisp);

   //header("Location: students/home.php");
?> 