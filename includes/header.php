<?php 

include 'dashboard.php';?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <!--css -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

   <style><?php include 'css/style.css'; ?></style>
  <!-- javascript -->
   
   <script><?php include 'css/script.js';?></script>

    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Mentor a Promise</title>
  </head>
  <body>
<header>
<?php if ($headerType == 'home'): ?>
  <nav class="navbar navbar-light justify-content-end bg-warning">
    <ul class="nav justify-content-end">
      <li class="nav-item">
        <a class="nav-link test1" href="#">About</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Programs</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Get Involved</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact</a>
      </li>
      <li class="nav-item">
        <button class="btn btn-outline-dark rounded-pill bg-dark text-white" type="button" data-toggle="modal" data-target="#loginModal">Login</button>
      </li>
    </ul>
  </nav>

<?php elseif ($headerType == 'logout'): ?>
  <nav class="navbar navbar-light justify-content-end bg-warning">
    <ul class="nav justify-content-end">
      <li class="nav-item">
        <a class="nav-link" href="../Mentor/loggedin.php">Study Material</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../Newpage/index.php">Exams</a>
      </li>
      <li class="nav-item">
        <a href="../includes/logout.php" class="btn btn-outline-dark rounded-pill bg-danger text-white">Logout</a>
      </li>
    </ul>
  </nav>

<?php elseif ($headerType == 'reset'): ?>
  <nav class="navbar navbar-light justify-content-end bg-warning">
    <ul class="nav justify-content-end">
      <li class="nav-item">
        <a class="nav-link" href="#">Sign Up</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../index.php">Sign In</a>
      </li>
    </ul>
  </nav>

<?php endif; ?>
</ul>
</nav>
</header>