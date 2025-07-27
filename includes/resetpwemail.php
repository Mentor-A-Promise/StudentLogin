<?php
$headerType = 'reset'; 
 require_once 'header.php'; ?>
<?php include '../db/connect.php';?>


<div class="container">
    <div class="row align-items-center vh-100">
        <div class="col-6 mx-auto">
            <div class="card shadow border">
                <div class="card-body align-items-center">
                <form id="password-reset-form" method="post"> 
                
                    <span class="anchor" id="formChangePassword"></span>
                  

                    <!-- form card change password -->
                    
                        <div class="card-header">
                            <h3 class="mb-0">Change Password</h3>
                        </div>
                        <div class="card-body">
                            <form class="form" role="form" autocomplete="off">
                            <div class="form-group row">
    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
      <input type="email" class="form-control border " id="emailid"name="emailid" placeholder="Email">
    </div>
                                
                                <div class="form-group mt-5">
                                <button id ="buttonsendemailpw"class="btn btn-outline-dark rounded-pill bg-dark text-white pt-2 justify-content-end" type="submit" >send email</button>
                                <p id="pwdmsg"class="text-primary p-4"style="display:none;"> A email has been sent  for resetting your password, if you did not receive a link contact admin@map.comk</p>
                                <p id="pwdmsginvalid"class="text-danger p-4"style="display:none;"> Invalid email</p> 
                                
                                  
                            </div>
                            </form>
                        </div>
                    
                    <!-- /form card change password -->

                
                </div>
            </div>
        </div>
    </div>
</div>
