<?php
require "csrf.php";
include "connection.php";
    csrf_verify('blooddonate.php');
    $donoid=$_POST['donoid'];
	$status = 'Accepted';
	$sql = "UPDATE blooddonate SET status = '$status' WHERE donoid = '$donoid'";
    if (mysqli_query($conn, $sql)) {
	$msg="You have accepted the request.";
	header("location:../blooddonate.php?msg=".$msg );
    } else {
    $error= "Error changing status: " . mysqli_error($conn);
    header("location:../blooddonate.php?error=".$error );
    }
    mysqli_close($conn);
?>
