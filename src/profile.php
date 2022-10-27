<?php
  include 'inc/header.php';
  Session::CheckSession();
  $redis = new Redis();
  $redis->connect('127.0.0.1', 6379); 
  if (Session::get('id') == TRUE) {
    $userid =(int)Session::get('id');
  }else{
    header('Location:login.php');
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $updateUser = $users->updateUserByIdInfo($userid, $_POST);
  }
  if (isset($updateUser)) {
    echo $updateUser;
  }
?>
 <div class="card ">
   <div class="card-header">
          <h3>User Profile <span class="float-right"> <a href="index.php" class="btn btn-primary">Back</a> </h3>
        </div>
        <div class="card-body">
    <?php
    if($redis->get('USERS'))
    {
      $getUinfo = unserialize($redis->get('USERS'));
    }else{
      
      $getUinfo = $users->getUserInfoById($userid);
    }
    if ($getUinfo) { ?>
          <div style="width:600px; margin:0px auto">
          <form class="" action="" method="POST">
              <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" value="<?php echo $getUinfo->firstname; ?>" class="form-control">
              </div>
              <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" value="<?php echo $getUinfo->lastname; ?>" class="form-control">
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $getUinfo->email; ?>" class="form-control">
              </div>
              <div class="form-group">
                <label for="date">Date of Birth</label>
                <input type="date" id="date" name="date" value="<?php echo $getUinfo->date_of_birth; ?>" class="form-control">
              </div>
              <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo $getUinfo->mobile; ?>" class="form-control">
              </div>
              <div class="form-group">
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <a class="btn btn-primary" href="changepass.php?id=<?php echo $getUinfo->id;?>">Password change</a>
              </div>
          </form>
        </div>
      <?php }else{
        header('Location:index.php');
      } ?>
      </div>
    </div>
<?php
  include 'inc/footer.php';
?>
