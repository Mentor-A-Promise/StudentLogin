<?php
//session_start();
//include 'header.php'; 

$headerType = 'logout';  
require_once '../includes/header.php';

include("../db/connect.php");
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $loginid = $_SESSION['loginid'];
} else {
    // Redirect to login page if the session does not exist
    header("Location: ../students/home.php");
    exit();
}
?>

<body>
  <div class="container mt-4 ">
        <div class="msg-icon" style="position: relative; margin-bottom: 10px; text-align: right;">
          <!--<a href="../Messaging/send_message_interface.php"><i class="fa-regular fa-comments fa-lg"></i></a>-->

          <a href="../Messaging/viewallmessages.php" class="message-icon">
            <i class="fas fa-comment fa-2x"></i><span class="badge badge-light"></span>
          </a>

        </div>
    <div class="row align-items-center">
      <div class="col-md-8">
        <?php
            $id=$loginid; //Replace this with the loginid from swathy ID logic
            echo "<h1>Logged in as student:". htmlspecialchars($username)."</h1>";
        ?>
  </div>

  <?php
                //$id = 1; // Replace this with the actual student ID logic (e.g., from session)

               if (isset($_GET['subject_id'])) {
                $subject_id = intval($_GET['subject_id']); // This code for getting the subject id from swathy code and passing in thr where condition instead of hard coding
                }
                else {
    echo "<p>Error: No ID provided in the URL.</p>";
    exit;
}

                // Prepare the SQL statement to fetch subject and grade
                $sql = "SELECT subject, grade FROM student_subject WHERE login_id = ? AND subject_id=? ";
                $stmt = mysqli_prepare($con, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ii", $id,$subject_id); // Bind the student ID
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        // Store the subject and grade for later use
                        $subject_name = htmlspecialchars($row['subject']);
                        $grade = htmlspecialchars($row['grade']);


                        // Display the subject and grade
                    
                        echo "<p style='font-size: 1.25rem; margin: 0;'>Subject: " . htmlspecialchars($subject_name) . " (Grade: " . htmlspecialchars($grade) . ")</p>";
                        //echo "<p style='font-size: 1.25rem; margin: 0;'>Subject: " . $subject_name . " (Grade: " . $grade . ")</p>";
                      
                    } else {
                        echo "<p>No subjects found.</p>";
                    }

                    // Close the statement
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<p>Error preparing the statement.</p>";
                }
            ?>
        </div>
            <div class="col-md-4 text-end">
                <p style="font-size: 1.25rem; margin: 0;">Study Materials for <?php echo $subject_name ?? 'the subject'; ?></p>
            </div>
        </div>


    <div class =container>
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h1 style="font-size: 1.5rem;"><?php echo "Grade " . $grade . " in " . $subject_name; ?></h1>
            <ul class='list-unstyled'>
            <!--<div class="pdf-icon" style="position: absolute; top: 10px; right: 10px;">
                    <a href=<i class="fa-solid fa-file-arrow-down fa-2x"></i> <br><p>pdf</p>
                    </a>
            </div> -->


            <!-- PDF Download Icon -->
        <div class="pdf-icon" style="position: absolute; top: 10px; right: 10px;">
            <a href="path/to/your/pdf-file.pdf" download>
            <i class="fas fa-file-download fa-2x"></i> 
            <!--<br><p>Download PDF</p>-->
            </a>
        </div>
            </ul>
            <?php
            // SQL query to fetch chapters
            $sql = "SELECT login_id, study_unit,chapter_id,subject_id FROM student_subject WHERE subject = ?";// AND login_id =?
            $stmt = mysqli_prepare($con, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $subject_name); // Bind the subject name
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    echo "<ul class='list-unstyled'>";
                    while ($row = mysqli_fetch_assoc($result)) {
                       echo "<li><a href='chapter.php?chapter_id=" . $row['chapter_id'] . "&subject_id=" . $row['subject_id'] . "'>" . htmlspecialchars($row['study_unit']) . "</a></li>";


                    }
                    echo "</ul>"; // Close the list
                } else {
                    echo "<p>No chapters found for " . htmlspecialchars($subject_name) . ".</p>";
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                echo "<p>Error preparing the statement.</p>";
            }

            ?>

            <!-- New Section for Projects -->
            <?php
            // SQL query to fetch projects
            $sql = "SELECT login_id, projects FROM student_subject WHERE subject = ?";
            $stmt = mysqli_prepare($con, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $subject_name); // Bind the subject name
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    echo "<ul class='list-unstyled'>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<li>Project : <a href='project.php?id=" . $row['login_id'] . "'>" . htmlspecialchars($row['projects']) . "</a></li>";
                    }
                    echo "</ul>"; // Close the list
                } else {
                    echo "<p>No projects found for " . htmlspecialchars($subject_name) . ".</p>";
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                echo "<p>Error preparing the statement.</p>";
            }
            ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        
        <div class="card" style="height: 100%;">
          <div class="card-body">
            <h3>Next Due Work:</h3>
            <?php
            // SQL query to fetch next due work
            $sql = "SELECT study_unit, DATE_FORMAT(due_date, '%d %M %Y') AS formatted_due_date, tutor, login_id FROM student_subject WHERE subject = ? ORDER BY due_date DESC LIMIT 1"; // Added id here
            $stmt = mysqli_prepare($con, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $subject_name); // Bind the subject name
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    // Use the chapter ID for the link
                    $chapter_id = $row['login_id'];
                    echo "<p><a href='chapter.php?id=" . $chapter_id . "'>" . htmlspecialchars($row['study_unit']) . "</a><br>
                          Due: " . htmlspecialchars($row['formatted_due_date']) . "<br>
                          Tutor: <a href='chapter.php?id=" . $chapter_id . "'>" . htmlspecialchars($row['tutor']) . "</a></p>";
                } else {
                    echo "<p>No due work found.</p>";
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                echo "<p>Error preparing the statement.</p>";
            }
            ?>

            <h3>Tests:</h3>
            <?php
            // SQL query to fetch tests
            $sql = "SELECT DATE_FORMAT(test_date, '%d %M %Y') AS formatted_test_date,login_id FROM student_subject WHERE subject = ? ORDER BY formatted_test_date DESC";
            $stmt = mysqli_prepare($con, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $subject_name); // Bind the subject name
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    echo "<ul>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<li>Test: <a href='chapter.php?id=" . $row['login_id'] . "'>" . htmlspecialchars($row['formatted_test_date']) . "</a></li>";
                        
                    }
                    echo "</ul>"; // Close the list
                } else {
                    echo "<p>No tests found.</p>";
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                echo "<p>Error preparing the statement.</p>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
   </div>
  </div>
</body>