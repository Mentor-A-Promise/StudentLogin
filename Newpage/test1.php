<?php
session_start();
include("../db/connect.php");

// Debugging: Output subject from URL
if (isset($_GET['subject'])) {
    // If the subject is passed via URL, set it in session
    $_SESSION['subject'] = $_GET['subject'];
    $_SESSION['question_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['answers'] = [];
} elseif (!isset($_SESSION['subject'])) {
    // If no subject is found, redirect to the index page
    echo "Redirecting because 'subject' is not set.";
    header('Location: index.php');
    exit;
}

// Fetch questions for the subject
$sql = "SELECT * FROM allquestions WHERE subject = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}
$stmt->bind_param("s", $_SESSION['subject']);
if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);
$total_questions = count($questions);

if ($total_questions == 0) {
    die("No questions found for this subject.");
}

// Handle form submission for answers and navigation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answer'])) {
        $current_question_index = $_SESSION['question_index'];
        $current_question = $questions[$current_question_index];

        // Check answer correctness and update score
        if ($_POST['answer'] === $current_question['correct_answer']) {
            $_SESSION['score']++;
        }

        // Save the answer for the current question
        $_SESSION['answers'][$current_question_index] = $_POST['answer'];
    }

    // Navigation logic for Previous, Next, and Submit
    if (isset($_POST['prev']) && $_SESSION['question_index'] > 0) {
        $_SESSION['question_index']--;
    } elseif (isset($_POST['next']) && $_SESSION['question_index'] < $total_questions - 1) {
        $_SESSION['question_index']++;
    } elseif (isset($_POST['submit'])) {
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
    <title><?php echo htmlspecialchars($_SESSION['subject']); ?> Test</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS file -->
</head>
<body>
<div id="quiz-container">
    <div id="info-box">
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($_SESSION['subject']); ?></p>
        <p><strong>Test:</strong> <?php echo htmlspecialchars($_SESSION['subject']); ?> Class Test</p>
        <p><strong>Logged in as:</strong> Student</p>
    </div>

    <div class="container">
        <h1><?php echo htmlspecialchars($_SESSION['subject']); ?> Test</h1>
        
        <div class="question-content">
            <h3 class="question-title"><strong><?php echo htmlspecialchars($current_question['question']); ?></strong></h3>
            <form method="POST" action="test1.php">
                <div class="answer-option">
                    <input type="radio" name="answer" value="A" 
                           <?php echo (isset($_SESSION['answers'][$current_question_index]) && $_SESSION['answers'][$current_question_index] === 'A') ? 'checked' : ''; ?> 
                           required>
                    <label><?php echo htmlspecialchars($current_question['option_a']); ?></label>
                </div>
                <div class="answer-option">
                    <input type="radio" name="answer" value="B" 
                           <?php echo (isset($_SESSION['answers'][$current_question_index]) && $_SESSION['answers'][$current_question_index] === 'B') ? 'checked' : ''; ?>>
                    <label><?php echo htmlspecialchars($current_question['option_b']); ?></label>
                </div>
                <div class="answer-option">
                    <input type="radio" name="answer" value="C" 
                           <?php echo (isset($_SESSION['answers'][$current_question_index]) && $_SESSION['answers'][$current_question_index] === 'C') ? 'checked' : ''; ?>>
                    <label><?php echo htmlspecialchars($current_question['option_c']); ?></label>
                </div>
                <div class="answer-option">
                    <input type="radio" name="answer" value="D" 
                           <?php echo (isset($_SESSION['answers'][$current_question_index]) && $_SESSION['answers'][$current_question_index] === 'D') ? 'checked' : ''; ?>>
                    <label><?php echo htmlspecialchars($current_question['option_d']); ?></label>
                </div>

                <!-- Navigation Buttons -->
                <div class="buttons">
                    <?php if ($current_question_index > 0) { ?>
                        <button type="submit" name="prev">Previous</button>
                    <?php } ?>
                    <?php if ($current_question_index < $total_questions - 1) { ?>
                        <button type="submit" name="next">Next</button>
                    <?php } else { ?>
                        <button type="submit" name="submit">Submit</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php
$con->close();
?>
