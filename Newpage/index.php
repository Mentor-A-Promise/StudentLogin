
<?php 
//session_start();
 /*if (isset($_SESSION['username'])) {
    $usernamedisp = $_SESSION['username'];
    $loginid = $_SESSION['loginid'];
} else {
    // Redirect to login page if the session does not exist
    header("Location: ../students/home.php");
    exit();
}*/

$headerType = 'logout';  
require_once '../includes/header.php';

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- CSS Styles -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<div class="navbar">
        <a href="#">Study Material</a>
        <a href="#">Exams</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>-->

    <title>Select a Test</title>
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        /* Test Button Wrapper */
        .test-button {
            position: relative;
            display: inline-block;
            margin: 10px;
        }

        .test-button button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .test-button button:hover {
            background-color: #5a6268;
        }

        /* The list box is hidden by default */
        .test-list {
            display: none;
            position: absolute;
            top: 100%; /* Show below the button */
            left: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 100;
            min-width: 120px;
        }

        /* Ensure hover applies to the entire test-button container */
        .test-button:hover .test-list {
            display: block;
        }

        .test-list form {
            margin: 0;
            padding: 0;
        }

        .test-list button {
            display: block;
            width: 100%;
            padding: 5px;
            font-size: 14px;
            background-color: white;
            color: #333;
            text-align: left;
            border: none;
            cursor: pointer;
        }

        .test-list button:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Select a Test</h1>
        <!-- English Test Button with Test List -->
        <div class="test-button">
            <button>Start English Test</button>
            <!-- List of test options in form -->
            <div class="test-list">
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="English">
                    <input type="hidden" name="testno" value="1">
                    <button type="submit">Test 1</button>
                </form>
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="English">
                    <input type="hidden" name="testno" value="2">
                    <button type="submit">Test 2</button>
                </form>
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="English">
                    <input type="hidden" name="testno" value="3">
                    <button type="submit">Test 3</button>
                </form>
            </div>
        </div>

        <!-- Math Test Button with Test List -->
        <div class="test-button">
            <button>Start Math Test</button>
            <!-- List of test options in form -->
            <div class="test-list">
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="Math">
                    <input type="hidden" name="testno" value="1">
                    <button type="submit">Test 1</button>
                </form>
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="Math">
                    <input type="hidden" name="testno" value="2">
                    <button type="submit">Test 2</button>
                </form>
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="Math">
                    <input type="hidden" name="testno" value="3">
                    <button type="submit">Test 3</button>
                </form>
            </div>
        </div>

        <!-- Science Test Button with Test List -->
        <div class="test-button">
            <button>Start Science Test</button>
            <!-- List of test options in form -->
            <div class="test-list">
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="Science">
                    <input type="hidden" name="testno" value="1">
                    <button type="submit">Test 1</button>
                </form>
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="Science">
                    <input type="hidden" name="testno" value="2">
                    <button type="submit">Test 2</button>
                </form>
                <form method="POST" action="test.php">
                    <input type="hidden" name="subject" value="Science">
                    <input type="hidden" name="testno" value="3">
                    <button type="submit">Test 3</button>
                </form>
            </div>
        </div>

    </div>
</body>
</html>
<?php //session_destroy(); ?>







































<!--

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select a Test</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
    </style>

</head>
<body>
    <div class="container text-center">
        <h1>Select a Test</h1>
        <form method="POST" action="test.php">
            <input type="hidden" name="subject" value="English">
            <button type="submit" class="btn btn-secondary">Start English Test</button>
        </form>
        
        <form method="POST" action="test.php">
            <input type="hidden" name="subject" value="Math">
            <button type="submit" class="btn btn-secondary">Start Math Test</button>
        </form>
        
        <form method="POST" action="test.php">
            <input type="hidden" name="subject" value="Science">
            <button type="submit" class="btn btn-secondary">Start Science Test</button>
        </form>
    </div>
</body>
</html>
*/
 