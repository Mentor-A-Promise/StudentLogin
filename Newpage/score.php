
<?php
session_start(); // Start session

// Check if the necessary session variables are set
if (!isset($_SESSION['subject']) || !isset($_SESSION['score']) || !isset($_SESSION['answers'])) {
    echo "No test data found. Please start the test again.";
    exit;
}

// Get the subject, score, and total number of questions
$subject = $_SESSION['subject'];
$score = $_SESSION['score'];
$total_questions = count($_SESSION['answers']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Score</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS */
        .custom-alert {
            background-color: #e2e3e5; /* A custom light gray */
            color: #000; /* Dark text for contrast */
            border: 1px solid #d6d8db; /* Optional: gray border for the alert */
        }
    </style>

</head>
<body>
    <div class="container">
        <h1 class="text-center">Test Results</h1>
        <div class="alert custom-alert">
            <strong>Subject:</strong> <?php echo htmlspecialchars($subject); ?><br>
            <strong>Your Score:</strong> <?php echo $score; ?> out of <?php echo $total_questions; ?>
        </div>
        
        <!-- Display messages based on score -->
        <?php if ($score == $total_questions) { ?>
            <div class="alert custom-alert">Congratulations! You got a perfect score!</div>
        <?php } elseif ($score >= ($total_questions * 0.7)) { ?>
            <div class="alert alert-success">Great job! You passed the test.</div>
        <?php } else { ?>
            <div class="alert alert-warning">You did not pass. Better luck next time!</div>
        <?php } ?>

        <a href="../students/home.php" class="btn btn-primary">Back to Home</a>
    </div>
</body>
</html>

<?php
// Optionally clear the session after displaying the score
//session_unset();
//session_destroy();
?>
