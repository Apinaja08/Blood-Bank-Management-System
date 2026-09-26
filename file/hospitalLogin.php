<?php
session_start();
    require 'connection.php';
    if(isset($_POST['hlogin'])){
    $hemail=$_POST['hemail'];
    $hpassword=$_POST['hpassword'];
    $sql="select * from hospitals where hemail='$hemail' and hpassword='$hpassword'";
    $result=mysqli_query($conn,$sql);
    if (!$result) {
        error_log("Database Error: " . mysqli_error($conn));
        die(header("location:../login.php?error=An internal server error occurred. Please try again."));
    }
    $rows_fetched=mysqli_num_rows($result);
    if($rows_fetched==0){
        $error= "Wrong email or password. Please try again.";
        header( "location:../login.php?error=".$error);
    }else{
        $row=mysqli_fetch_array($result);
        session_regenerate_id(true); // V15 Fix: Prevent session fixation
        $_SESSION['hemail']=$row['hemail'];
        $_SESSION['hname']=$row['hname'];
        $_SESSION['hid']=$row['id'];
        $msg= $_SESSION['hname'].' have logged in.';
        header( "location:../hospitalpage.html?msg=".$msg);
    } 
  }
?>