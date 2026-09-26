<?php
require "auth.php";
include "connection.php";
    $hid = require_role('hid');
    $bid = require_id('bid', 'bloodinfo.php');
	// A hospital may only delete its own blood samples.
	$stmt = $conn->prepare("DELETE FROM bloodinfo WHERE bid = ? AND hid = ?");
	$stmt->bind_param("ii", $bid, $hid);
	if ($stmt->execute() && $stmt->affected_rows > 0) {
	$msg="You have deleted one blood sample.";
	header("location:../bloodinfo.php?msg=".urlencode($msg));
    } else {
    $error="Sample not found or you are not allowed to delete it.";
    header("location:../bloodinfo.php?error=".urlencode($error));
    }
    mysqli_close($conn);
?>
