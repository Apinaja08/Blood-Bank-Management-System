<?php 
session_start();
require 'connection.php';
if (isset($_SESSION['rid'])) {
if(isset($_POST['update'])){
    $id=$_SESSION['rid'];
    $rname = $_POST['rname'];
    $remail = $_POST['remail'];
    $rphone = $_POST['rphone'];
    $bg = $_POST['bg'];
    $rcity = $_POST['rcity'];
    $rpassword = $_POST['rpassword'];
    if (!empty($rpassword) && !password_get_info($rpassword)['algo']) {
        $rpassword = password_hash($rpassword, PASSWORD_BCRYPT);
    }
    $update = "UPDATE receivers SET rname='$rname', remail='$remail', rpassword='$rpassword', rphone='$rphone', rbg='$bg',rcity='$rcity' WHERE id='$id'";
    if ($conn->query($update) === TRUE) {
        $msg = "Your profile is updated successfully.";
        header("Location: ../rprofile.php?msg=" . urlencode($msg));
    } else {
        $error = "Error: " . $conn->error;
        header( "location:../rprofile.php?error=".$error );
    }
    $stmt->close();
    $conn->close();
    exit();

} elseif (isset($_SESSION['hid']) && isset($_POST['update'])) {
    csrf_verify('hprofile.php');
    $id        = (int)$_SESSION['hid'];
    $hname     = trim($_POST['hname'] ?? '');
    $hemail    = trim($_POST['hemail'] ?? '');
    $hphone    = trim($_POST['hphone'] ?? '');
    $hcity     = trim($_POST['hcity'] ?? '');
    $hpassword = $_POST['hpassword'] ?? '';

}elseif (isset($_SESSION['hid'])) {
    if(isset($_POST['update'])){
        $id=$_SESSION['hid'];
    $hname = $_POST['hname'];
    $hemail = $_POST['hemail'];
    $hphone = $_POST['hphone'];
    $hcity = $_POST['hcity'];
    $hpassword = $_POST['hpassword'];
    if (!empty($hpassword) && !password_get_info($hpassword)['algo']) {
        $hpassword = password_hash($hpassword, PASSWORD_BCRYPT);
    }
    $update = "UPDATE hospitals SET hname='$hname', hemail='$hemail', hpassword='$hpassword', hphone='$hphone', hcity='$hcity' WHERE id='$id'";
    if ($conn->query($update) === TRUE) {
        $msg= "Your profile is updated successfully.";
        header( "location:../hprofile.php?msg=".$msg);
    } else {
        $error= "Error: " . $conn->error;
        header( "location:../hprofile.php?error=".$error);
    }
    $stmt->close();
    $conn->close();
    exit();

} else {
    header("Location: ../login.php");
    exit();
}
?>