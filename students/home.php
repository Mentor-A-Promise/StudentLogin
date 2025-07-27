<?php
$headerType = 'logout';  
require_once '../includes/header.php'; ?>
<?php

include("../db/connect.php");
 

// In students/home.php


if (isset($_SESSION['username']) && isset($_SESSION['loginid'])) {
    // Fetch session variables
    $usernamedisp = htmlspecialchars($_SESSION['username']);
    $loginid = htmlspecialchars($_SESSION['loginid']);
    
    echo '<div class="d-flex justify-content-between align-items-center m-4">';
    // Display username
    echo '<h3>Logged in as student: ' . $usernamedisp . '</h3>';
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




   $error='';
   
   
  

// Query to select data from student_subjects table 
// Below code populate data into first table
$sql = "SELECT ss1.*
        FROM student_subject ss1
        JOIN (
            SELECT subject_id,subject, MAX(last_opened) AS max_date
            FROM student_subject
            WHERE login_id = ?
            GROUP BY subject
        ) ss2 ON ss1.subject = ss2.subject AND ss1.last_opened = ss2.max_date
        WHERE ss1.login_id = ?
        ORDER BY ss1.subject";

// Prepare the statement
$stmt = $con->prepare($sql);

// Check if the statement was prepared correctly
if ($stmt === false) {
    // Output the error for debugging
    die("SQL prepare error: " . $con->error);
}

// Bind the dynamic values (loginid in this case)
$stmt->bind_param("ss", $loginid, $loginid);  // Ensure you pass two strings

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

$subjects = [];
while ($row = mysqli_fetch_assoc($result)) {
    $subjects[$row['subject']][] = $row; // Group data by subject
}
?>

<div class="container">
<?php if (empty($subjects)): // Check if subjects array is empty ?>
        <div class="alert alert-warning mt-3">
            <strong>No subjects found!</strong> You have not opened any subjects yet. Please check back later or contact your tutor for assistance.
        </div>
    <?php else: ?>
<table class="table table-bordered mt-3 table_student_subjects">
    <thead>
        <tr>
            <?php
            // Display subject names dynamically in the header
            foreach ($subjects as $subject => $data) {
                // here need to add the code for routing it to subject details page 
                echo '<th><a href="#"class="text-white">' . $subject . '</a></th>';
              //  echo '<th><a href="yourpage.php?subject_id=' . $subject_id . '" class="text-white">' . htmlspecialchars($subject) . '</a></th>'; this line page should be replaced with chapter is 

            }
            ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            // Display study unit, last opened, and tutor name under each subject
           // foreach ($subjects as $data) {
                foreach ($subjects as $subject => $data) {
                echo "<td>";
                foreach ($data as $row) {
                    echo "Study Unit: " . $row['study_unit'] . "<br>";
                    echo "Last Opened: " . $row['last_opened'] . "<br>";
                    echo 'Tutor Name: <a href="tutor_profile.php?tutor=' . urlencode($row['tutor']) . '">' . $row['tutor'] . '</a><br><br>';
                    // pass chapter id here to route it to second page 
                   //echo '<a href="../Mentor/loggedin.php">Study Material</a>';
                   echo '<a href="../Mentor/loggedin.php?subject_id=' . urlencode($row['subject_id']) . '">Study Material</a>';

                    //  echo '<th><a href="yourpage.php?subject_id=' . $subject_id . '" class="text-white">' . htmlspecialchars($subject) . '</a></th>'; this line page should be replaced with chapter is
                    
                }
                echo "</td>";
            }
            ?>
        </tr>
    </tbody>
</table>
<?php endif; ?>
        </div>


        
        <div class="container">
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb bg-transparent">
    <li class="breadcrumb-item"><a href="#">Quizzes</a></li>
    <li class="breadcrumb-item"><a href="#">Test</a></li>
    <li class="breadcrumb-item ">Exam</li>
  </ol>
</nav>  
            
<?php
// Query to get subjects, chapters, and due_date from subject_testing table second table started 
$sql = "SELECT subjects,subject_id chapters, due_date 
        FROM subject_testing 
        WHERE (subjects, due_date) IN (
            SELECT subjects, MIN(due_date) 
            FROM subject_testing 
            WHERE login_id = '$loginid'  -- Add the loginid condition here
            GROUP BY subjects
        )
        AND login_id = '$loginid'  -- Also add the loginid condition to the outer query
        ";

$stmt = $con->prepare($sql);

// Execute the prepared statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Organize data by subjects
$subjectData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $subjectData[$row['subjects']][] = $row; // Group by subject
}

// Start the table with dynamic headers
if (empty($subjectData)): // Check if subjectData is empty
    echo '<div class="alert alert-warning mt-3">
            <strong>No subjects found!</strong> You have no due subjects at the moment.
          </div>';
else:
echo '<table class="table table-bordered mt-3 table_student_subjects">
        <thead>
          <tr>';
foreach (array_keys($subjectData) as $subject) {
   //echo '<th>' . $subject . '</th>';
    echo '<th><a href="#"class="text-white">' . $subject . '</a></th>'; // Here is the <a> link without a destination
    //echo '<th><a href="yourpage.php?subject_id=' . $subject_id . '" class="text-white">' . htmlspecialchars($subject) . '</a></th>'; this line page should be replaced with chapter is 


}
echo '</tr></thead><tbody><tr>';

// Loop through subjects to display details
foreach ($subjectData as $data) {
    echo '<td>';
    foreach ($data as $details) {
        echo 'Detail: ' . $details['chapters'] . '<br>';
        echo 'Due Date: ' . $details['due_date'] . '<br><br>';
        //echo '<a href="../Newpage/index.php">Start</a>';
       echo'<a href="../Newpage/test1.php?subject=' . urlencode($details['subjects']) . '">Start</a>';

    }
    echo '</td>';
}
echo '</tr></tbody></table>';
$con->close();
$stmt->close();
endif


 
?>
</div>

