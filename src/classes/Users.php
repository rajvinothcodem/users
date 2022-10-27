<?php
include 'lib/Database.php';
include_once 'lib/Session.php';
//class Users for creating and updating users
class Users{
  // Db Property
  private $db;

  //  Redis property
  protected $redis;

  // Db __construct Method
  public function __construct()
  {
    $this->db = new Database();
    $this->redis = new Redis();
    $this->redis->connect('127.0.0.1', 6379); 
  }

  /*
   * @param $email String
   * return bool
   */
  // Check Exist Email Address Method
  public function checkExistEmail($email)
  {
    $sql = "SELECT email from  tbl_users WHERE email = :email";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount()> 0) {
      return true;
    }else{
      return false;
    }
  }

  /*
   * Get refistered user details
   * @param $email String
   * return Users db Object
   */ 
  public function getRegisterdCustomer($email){
    $sql = "SELECT * from  tbl_users WHERE email = :email";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  // User Registration Method
  /*
   * @param $data array
   * @return String
   */
  public function userRegistration($data)
  {
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $email = $data['email'];
    $mobile = $data['mobile'];
    $date_of_birth = $data['date'];
    $password = $data['password'];
    $checkEmail = $this->checkExistEmail($email);
    if ($checkEmail == TRUE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error !</strong> Email already Exists, please try another Email... !</div>';
      return $msg;
    }else{
      $sql = "INSERT INTO tbl_users(firstname, lastname, email, password, mobile, date_of_birth) VALUES(:firstname, :lastname, :email, :password, :mobile, :date_of_birth)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':firstname', $firstname);
      $stmt->bindValue(':lastname', $lastname);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':password', SHA1($password));
      $stmt->bindValue(':mobile', $mobile);
      $stmt->bindValue(':date_of_birth', $date_of_birth);
      $result = $stmt->execute();
      if ($result) {
        $users = $this->getRegisterdCustomer($email);
        $this->redis->set('USERS',serialize($users));
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success !</strong> Wow, you have Registered Successfully !</div>';
        return $msg;
      }else{
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Something went Wrong !</div>';
        return $msg;
      }
    }
  }
  
  // User login Autho Method
  /*
   * @param $email String
   * @param $password String
   * return User Object
   */
  public function userLoginAutho($email, $password)
  {
    $password = SHA1($password);
    $sql = "SELECT * FROM tbl_users WHERE email = :email and password = :password LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  // User Login Authetication Method
  /** 
   * @param $data array
   * return String
   */
  public function userLoginAuthotication($data)
  {
    $email = $data['email'];
    $password = $data['password'];
    $logResult ='';
    $checkEmail = $this->checkExistEmail($email);
    if ($email == "" || $password == "" ) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error !</strong> Email or Password not be Empty !</div>';
        return $msg;
    }elseif (filter_var($email, FILTER_VALIDATE_EMAIL === FALSE)) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error !</strong> Invalid email address !</div>';
        return $msg;
    }elseif ($checkEmail == FALSE) {
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error !</strong> Email did not Found, use Register email or password please !</div>';
        return $msg;
    }else{
      $logResult = $this->userLoginAutho($email, $password);
      $this->redis->set('USERS',serialize($logResult));
    }
    if ($logResult) {
      Session::init();
      Session::set('login', TRUE);
      Session::set('id', $logResult->id);
      Session::set('firstname', $logResult->firstname);
      Session::set('lastname', $logResult->lastname);
      Session::set('email', $logResult->email);
      Session::set('logMsg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <strong>Success !</strong> You are Logged In Successfully !</div>');
                        echo "<script>location.href='index.php';</script>";
    }else{
      $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error !</strong> Email or Password did not Matched !</div>';
        return $msg;
    }
  }

  // Get Single User Information By Id Method
  /**
   * @param $userid int
   * return user data object
   */
  public function getUserInfoById($userid)
  {
    $sql = "SELECT * FROM tbl_users WHERE id = :id LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $userid);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    if ($result) {
      return $result;
    }else{
      return false;
    }
  }

  // Get Single User Information By Id Method
  /**
   * @param $userid int
   * @param $data array
   */
  public function updateUserByIdInfo($userid, $data)
  {
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $email = $data['email'];
    $mobile = $data['mobile'];
    $dateofbirth = $data['date'];
    $sql = "UPDATE tbl_users SET
      firstname = :firstname,
      lastname = :lastname,
      email = :email,
      mobile = :mobile,
      date_of_birth = :date_of_birth
      WHERE id = :id";
    $stmt= $this->db->pdo->prepare($sql);
    $stmt->bindValue(':firstname', $firstname);
    $stmt->bindValue(':lastname', $lastname);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':mobile', $mobile);
    $stmt->bindValue(':date_of_birth', $dateofbirth);
    $stmt->bindValue(':id', $userid);
    $result = $stmt->execute();
    if ($result) {
        $users = $this->getRegisterdCustomer($email);
        $this->redis->set('USERS',serialize($users));
        Session::set('firstname',$firstname);
        Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Success !</strong> Wow, Your Information updated Successfully !</div>');
                      echo "<script>location.href='index.php';</script>";
      }else{
        Session::set('msg', '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Error !</strong> Data not inserted !</div>');
                      echo "<script>location.href='index.php';</script>";
      }
    }


    // Check Old password method
    /**
     * @param $userid int
     * @param $old_pass encrypted string
     * return bool
     */
    public function CheckOldPassword($userid, $old_pass)
    {
      $old_pass = SHA1($old_pass);
      $sql = "SELECT password FROM tbl_users WHERE password = :password AND id =:id";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':password', $old_pass);
      $stmt->bindValue(':id', $userid);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        return true;
      }else{
        return false;
      }
    }

    // Change User pass By Id
    /**
     * @param $userid int
     * @param $data array
     * return string
     */
    public  function changePasswordBysingelUserId($userid, $data)
    {
      $old_pass = $data['old_password'];
      $new_pass = $data['new_password'];
      if ($old_pass == "" || $new_pass == "" ) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> Password field must not be Empty !</div>';
        return $msg;
      }elseif (strlen($new_pass) < 6) {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Error !</strong> New password must be at least 6 character !</div>';
        return $msg;
       }
        $oldPass = $this->CheckOldPassword($userid, $old_pass);
        if ($oldPass == FALSE) {
          $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <strong>Error !</strong> Old password did not Matched !</div>';
                          return $msg;
          }else{
            $new_pass = SHA1($new_pass);
            $sql = "UPDATE tbl_users SET
              password=:password
              WHERE id = :id";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':password', $new_pass);
            $stmt->bindValue(':id', $userid);
            $result =   $stmt->execute();
            if ($result) {
              echo "<script>location.href='index.php';</script>";
                    Session::set('msg', '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success !</strong> Great news, Password Changed successfully !</div>');
            }else{
              $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Error !</strong> Password did not changed !</div>';
              return $msg;
            }

          }

    }
}
