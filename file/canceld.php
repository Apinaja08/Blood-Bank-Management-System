<?php
require "auth.php";
require "csrf.php";
include "connection.php";
    $hid = require_role('hid');
    csrf_verify('sentrequestd.php');
    $donoid = require_id('donoid', 'sentrequestd.php');
	// A hospital may only cancel donation requests it sent itself.
	$stmt = $conn->prepare("DELETE FROM blooddonate WHERE donoid = ? AND hid = ?");
	$stmt->bind_param("ii", $donoid, $hid);
	if ($stmt->execute() && $stmt->affected_rows > 0) {
	$msg="You have cancelled request for the blood.";
	header("location:../sentrequestd.php?msg=".urlencode($msg));
    } else {
    $error="Request not found or you are not allowed to cancel it.";
    header("location:../sentrequestd.php?error=".urlencode($error));
    }
    mysqli_close($conn);
?>
