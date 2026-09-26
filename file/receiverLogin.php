<?php
session_start();
    require 'connection.php';
    if(isset($_POST['rlogin'])){
    $remail=$_POST['remail'];
    $rpassword=$_POST['rpassword'];
    $sql="select * from receivers where remail='$remail' and rpassword='$rpassword'";
    try { $result=mysqli_query($conn,$sql); } catch (mysqli_sql_exception $e) { error_log("Database Error: " . $e->getMessage()); die(header("location:../login.php?error=An internal server error occurred. Please try again.")); }
    $rows_fetched=mysqli_num_rows($result);
    if($rows_fetched==0){
        $error= "Wrong email or password. Please try again.";
        header( "location:../login.php?error=".$error);
    }else{
        $row=mysqli_fetch_array($result);
        session_regenerate_id(true); // V15 Fix: Prevent session fixation
        $_SESSION['remail']=$row['remail'];
        $_SESSION['rname']=$row['rname'];
        $_SESSION['rid']=$row['id'];
        $msg= $_SESSION['rname'].' have logged in.';
        header( "location:../Userpage.html?msg=".$msg);
    } 
  }
?>



