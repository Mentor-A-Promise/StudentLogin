<?php
session_start();
include("../db/connect.php");
/*if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $loginid = $_SESSION['loginid'];
} else {
    // Redirect to login page if the session does not exist
    header("Location: ../students/home.php");
    exit();
}*/
// Clear previous session variables related to the test
if (!isset($_SESSION['subject'])&&(!isset($_SESSION['testno']))) {
    // Only clear if we are starting fresh
    unset($_SESSION['question_index']);
    unset($_SESSION['score']);
    unset($_SESSION['answers']);
    unset($_SESSION['subject']);
    unset($_SESSION['testno']);
}

// Initialize session variables if this is the first request
if (!isset($_SESSION['subject'])&&(!isset($_SESSION['testno'])))
 {
    if (isset($_POST['subject'])&& isset($_POST['testno'])) 
 {
    $_SESSION['subject'] = $_POST['subject'];
    $_SESSION['testno'] = $_POST['testno'];
 }// Store selected subject
    
    if (!isset($_SESSION['question_index'])) {
        $_SESSION['question_index'] = 0; // Start at first question
    }
        if (!isset($_SESSION['score'])) 
        { $_SESSION['score'] = 0; }// Initialize score
        if (!isset($_SESSION['answers'])) {
            $_SESSION['answers'] = []; // Initialize answers array
    } else {
        // If no subject is set, redirect to the start page
        echo "Subject or Test number not set. Redirecting to the start page.";
        header('Location: index.php');
        exit;
    
    }
}

// Get the subject from the session
$subject = $_SESSION['subject'];
$testno=$_SESSION['testno'];

// Database connection
//$servername = "localhost";
//$username = "root";
//$password = "";
//$dbname = "MySql";

//$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
/*if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}*/

// Retrieve questions from the database based on the subject
$sql = "SELECT * FROM allquestions WHERE subject = ?  AND testno = ? ";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $subject, $testno);
$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC); // Fetch all questions
$total_questions = count($questions); // Total questions retrieved


// Check if there are questions available
if ($total_questions == 0) {
    echo "No questions found for this subject.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store the answer if provided
    if (isset($_POST['answer'])) {
        $current_question_index = $_SESSION['question_index'];
        $current_question = $questions[$current_question_index];

        // Check if the submitted answer is correct
        if ($_POST['answer'] === $current_question['correct_answer']) {
            $_SESSION['score']++; // Increment score for a correct answer
        }

        // Store the user's answer for this question
        $_SESSION['answers'][$current_question_index] = $_POST['answer'];
    }

    // Handle navigation: Previous, Next, or Submit
    if (isset($_POST['prev'])) {
        // Move to the previous question
        if ($_SESSION['question_index'] > 0) {
            $_SESSION['question_index']--;
        }
    } elseif (isset($_POST['next'])) {
        // Move to the next question
        if ($_SESSION['question_index'] < $total_questions - 1) {
            $_SESSION['question_index']++;
        }
    } elseif (isset($_POST['submit'])) {
        // Redirect to score page
        header('Location: score.php');
        exit;
    }
}

// Get the current question to display
$current_question_index = $_SESSION['question_index'];
$current_question = $questions[$current_question_index];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subject); ?> Test</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the custom CSS file -->
</head>
<body>
<div id="quiz-container">
    <div id="info-box">
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($subject); ?></p>
        <p><strong>Test:</strong> <?php echo htmlspecialchars($subject); ?> Class Test</p>
        <p><strong>Logged in as :</strong> <?php 
            // Check if the session variable for username is set
            if (isset($_SESSION['username'])) {
                echo '$username';
            } else {
                echo "Guest"; // Default fallback
            }
            ?>
</p>
    </div>

    <div class="container" >
        <h1><?php echo htmlspecialchars($subject); ?> Test</h1>
        
        <div class="question-content">
            <h3 class="question-title"><strong><?php echo htmlspecialchars($current_question['question']); ?></strong></h3>
            <form method="POST" action="test.php">
                <div class="answer-option">
                    <input type="radio" name="answer" value="A" required> 
                    <label><?php echo htmlspecialchars($current_question['option_a']); ?></label>
                </div>
                <div class="answer-option">
                    <input type="radio" name="answer" value="B"> 
                    <label><?php echo htmlspecialchars($current_question['option_b']); ?></label>
                </div>
                <div class="answer-option">
                    <input type="radio" name="answer" value="C"> 
                    <label><?php echo htmlspecialchars($current_question['option_c']); ?></label>
                </div>
                <div class="answer-option">
                    <input type="radio" name="answer" value="D"> 
                    <label><?php echo htmlspecialchars($current_question['option_d']); ?></label>
                </div>

                <!-- Navigation Buttons -->
                <div class="buttons">
                    <?php if ($current_question_index > 0) { ?>
                        <button type="submit" name="prev" style="font-size: 16px; padding: 10px 20px;">Previous</button>
                    <?php } ?>
                    <?php if ($current_question_index < $total_questions - 1) { ?>
                        <button type="submit" name="next" style="font-size: 16px; padding: 10px 20px;" >Next</button>
                    <?php } else { ?>
                        <button type="submit" name="submit" style="font-size: 16px; padding: 10px 20px;" >Submit</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php
// Close the database connection
$con->close();
?>

