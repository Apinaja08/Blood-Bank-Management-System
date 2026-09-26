<?php 
session_start();
require_once 'file/csrf.php';
if (isset($_SESSION['hid'])) {
  header("location:bloodrequest.php");
}elseif (isset($_SESSION['rid'])) {
  header("location:sentrequest.php");
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <style>
body{
    background: url(jastimage/bb1.jpg) no-repeat center;
    background-size: cover;
    min-height: 0;
    height: 530px;
  }
.login-form{
    width: calc(100% - 20px);
    max-height: 650px;
    max-width: 450px;
    background-color: white;
}
</style>
</head>
<?php $title="Bloodbank | Register"; ?>
<?php require 'head.php'; ?>
<body>
  <?php include 'header.php'; ?>

    <div class="container cont">

    <?php require 'message.php'; ?>

      <div class="row justify-content-center">
        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-7 mb-5">
          <div class="card rounded">
            <ul class="nav nav-tabs justify-content-center bg-light" style="padding: 20px">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#hospitals">Hospitals</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#receivers">User</a>
              </li>
            </ul>

    <div class="tab-content">

       <div class="tab-pane container active" id="hospitals">

        <form action="file/hospitalReg.php" method="post" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <input type="text" name="hname" placeholder="Hospital Name" class="form-control mb-3" required>
          <input type="text" name="hcity" placeholder="Hospital City" class="form-control mb-3" required>
          <input type="tel" name="hphone" placeholder="Hospital Phone Number" class="form-control mb-3" required pattern="[0,6-9]{1}[0-9]{9,11}" title="Password must have start from 0,6,7,8 or 9 and must have 10 to 12 digit">
          <input type="email" name="hemail" placeholder="Hospital Email" class="form-control mb-3" required>
          <input type="password" name="hpassword" placeholder="Hospital Password" class="form-control mb-3" required minlength="6">
          <input type="submit" name="hregister" value="Register" class="btn btn-primary btn-block mb-4">
        </form>

       </div>


       <div class="tab-pane container fade" id="receivers">

         <form action="file/receiverReg.php" method="post" enctype="multipart/form-data">
           <?php echo csrf_field(); ?>
          <input type="text" name="rname" placeholder="User Name" class="form-control mb-3" required>
          <select name="rbg" class="form-control mb-3" required>
                <option disabled="" selected="">Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
          </select>
          <input type="text" name="rcity" placeholder="User City" class="form-control mb-3" required>
          <input type="tel" name="rphone" placeholder="User Phone Number" class="form-control mb-3" required pattern="[0,6-9]{1}[0-9]{9,11}" title="Password must have start from 0,6,7,8 or 9 and must have 10 to 12 digit">
          <input type="email" name="remail" placeholder="User Email" class="form-control mb-3" required>
          <input type="password" name="rpassword" placeholder="User Password" class="form-control mb-3" required minlength="6">
          <input type="submit" name="rregister" value="Register" class="btn btn-primary btn-block mb-3">
        </form>

        <?php
        require_once 'file/oauth_config.php';
        $_SESSION['oauth_state'] = bin2hex(random_bytes(16));
        $google_auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $_SESSION['oauth_state'],
            'prompt'        => 'select_account'
        ]);
        ?>
        <div class="px-3 pb-3 text-center">
          <div class="text-muted my-2 font-weight-bold" style="display: flex; align-items: center; text-align: center;">
            <hr style="flex: 1;"> <span style="padding: 0 10px; color: #888;">OR</span> <hr style="flex: 1;">
          </div>
          <a href="<?php echo htmlspecialchars($google_auth_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-danger btn-block font-weight-bold py-2 shadow" style="background-color: #ea4335; border-color: #ea4335;">
            <svg style="width:18px;height:18px;margin-right:8px;vertical-align:middle;" viewBox="0 0 24 24">
              <path fill="#fff" d="M21.35 11.1h-9.17v2.98h5.27c-.23 1.25-1.11 2.5-2.73 3.32-1.62.82-3.69.82-5.46-.22-2.31-1.35-3.48-4.04-2.83-6.64.65-2.6 2.94-4.54 5.62-4.54 1.54 0 2.97.58 4.06 1.62l2.24-2.24C16.94 3.9 14.6 3 12.08 3 7.64 3 3.9 6.2 3.12 10.5c-.78 4.3 1.76 8.44 5.92 9.61 4.16 1.17 8.52-.96 10.15-4.99.6-1.48.81-2.71.81-3.65 0-.29-.03-.58-.06-.87z"/>
            </svg>
            Sign up with Google
          </a>
        </div>
       </div>
    </div>
    <a href="login.php" class="text-center mb-4" title="Click here">Already have account?</a>
</div>
</div>
</div>
</div>
<?php require 'footer.php' ?>
</body>
</html>
<?php } ?>