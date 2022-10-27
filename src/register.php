<?php
  include 'inc/header.php';
  Session::CheckLogin();
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    $register = $users->userRegistration($_POST);
  }
  if (isset($register)) {
    echo $register;
  }
?>
 <div class="card ">
   <div class="card-header">
          <h3 class='text-center'><i class="fas fa-sign-in-alt mr-2"></i>Register</h3>
        </div>
        <div class="cad-body">
            <div style="width:600px; margin:0px auto">
            <form class="" method="post" action="">
                <div class="form-group pt-3">
                  <label for="name">First Name</label>
                  <input type="text" name="firstname"  class="form-control" id="validationDefault01" required>
                </div>
                <div class="form-group">
                  <label for="username">Last Name</label>
                  <input type="text" name="lastname" id="validationDefault01"  class="form-control" required>
                </div>
                <div class="form-group"> <!-- Date input -->
                <label class="control-label" for="date">Date</label>
                <input class="form-control" type="date" id="validationDefault01" name="date" placeholder="MM/DD/YYY" required>
              </div>
                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" name="email" id="validationDefault01"  class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="mobile">Mobile Number</label>
                  <input type="text" name="mobile" id="validationDefault01"  class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" name="password" class="form-control" id="validationDefault01" required>
                </div>
                <div class="form-group">
                  <button type="submit" name="register" class="btn btn-success">Register</button>
                </div>
            </form>
          </div>
        </div>
      </div>
<?php
  include 'inc/footer.php';
?>
