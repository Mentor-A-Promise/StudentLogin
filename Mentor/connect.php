<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$con = new mysqli('localhost', 'appsmat1_sandboxs', 'Granite1@2', 'appsmat1_sandboxs'); // Add the port number here // Use proper quotes and specify the database name
//$con = mysqli_connect("localhost","appsmat1_sandboxs","Granite1@2","appsmat1_sandboxs");
if (!$con) {
    
    die(mysqli_error($con));
}
?>







