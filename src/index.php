<?php
  include 'inc/header.php';
  Session::CheckSession();
  if(Session::get('msg'))
  {
    echo Session::get('msg');
  }
?>
<div class="card ">
  <div class="card-header">
    <h3><i class="fas fa-users mr-2"></i> <span class="float-right">Welcome! <?php echo Session::get('firstname'); ?><strong>
        </strong></span></h3>
  </div>
</div>
<?php
  include 'inc/footer.php';
?>