<?php
 session_start();
$headerType = 'home'; 
 require_once 'includes/header.php'; ?>
<?php include './db/connect.php';?>
   <!-- <h1 class ="text-center">Hello, world!</h1>-->
   
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-body">

      <form role="form"action="login.php"method="post">
      <div class="form-group row">
    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
      <input type="email" class="form-control border border-success" id="emailid"name="emailid" placeholder="Email">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control border border-success" name ="password" placeholder="Password">
    </div>
  </div>
  <div class="row">
    <div class="col-6">
  <button type="button" id="buttonLoginLink"class="btn btn-link pt-2">Login with a Link</button>
</div>
<div class="col-6">
  <a href="./includes/resetpwemail.php"><button type="button" class="btn btn-link pt-2">Forgot Password</button></a>
</div>
</div>

  <div class="modal-footer border-0">
  <button class="btn btn-outline-dark rounded-pill bg-dark text-white pt-2 justify-content-end" type="submit" >Login</button>

        
      </div>
  
            </form>
      </div>
      
    </div>
  </div>
</div>

<!-- modal for login with email -->
<div class="modal fade" id="loginEmailModal" tabindex="-1" role="dialog" aria-labelledby="Logins" inert style="display: none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-body">

     <p> A login link has been sent to your email, if you did not receive a link, click ‘Resend Link’ below or contact admin@map.com</p>

      </div>
      <div class="modal-footer border-0 d-flex justify-content-between">
      <button type="button" id="resendLink"class="btn btn-link pt-2">Resend Link</button>
  <button class="btn btn-outline-dark rounded-pill bg-dark text-white pt-2 " type="submit" >Login with a password</button>

        
      </div>
      
    </div>
  </div>
</div>

<!--modal for displaying email not found -->
<div class="modal fade" id="loginEmailInvalid" tabindex="-1" role="dialog" aria-labelledby="pw" inert style="display: none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-body">

     <p> email id not found</p>

      </div>
      
      
    </div>
  </div>
</div>

<!-- modal for displaying link sent for passwod reset -->
<div class="modal fade" id="pwdreset" tabindex="-1" role="dialog" aria-labelledby="Linvalid" inert style="display: none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-body">

     <p> A email has been sent to your email for resetting your password, if you did not receive a link, click ‘Resend Link’ below or contact admin@map.com</p>

      </div>
      
      
    </div>
  </div>
</div>
<?php require_once 'login.php'; ?>
<?php require_once 'includes/footer.php'; ?>
