<?php
require "auth.php";
require "csrf.php";
include "connection.php";
    $rid = require_role('rid');
    csrf_verify('blooddinfo.php');
    $bdid = require_id('bdid', 'blooddinfo.php');
	// A receiver may only delete their own donor blood samples.
	$stmt = $conn->prepare("DELETE FROM blooddinfo WHERE bdid = ? AND rid = ?");
	$stmt->bind_param("ii", $bdid, $rid);
	if ($stmt->execute() && $stmt->affected_rows > 0) {
	$msg="You have deleted one blood sample.";
	header("location:../blooddinfo.php?msg=".urlencode($msg));
    } else {
    $error="Sample not found or you are not allowed to delete it.";
    header("location:../blooddinfo.php?error=".urlencode($error));
    }
    mysqli_close($conn);
?>
