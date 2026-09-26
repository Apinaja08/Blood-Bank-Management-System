<?php
require "auth.php";
include "connection.php";
    $hid = require_role('hid');
    $reqid = require_id('reqid', 'bloodrequest.php');
	$status = "Rejected";
	// Only the hospital the request was sent to may change its status.
	$stmt = $conn->prepare("UPDATE bloodrequest SET status = ? WHERE reqid = ? AND hid = ?");
	$stmt->bind_param("sii", $status, $reqid, $hid);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
	$msg="You have Rejected the request.";
	header("location:../bloodrequest.php?msg=".urlencode($msg));
    } else {
    $error= "Request not found or you are not allowed to change it.";
    header("location:../bloodrequest.php?error=".urlencode($error));
    }
    mysqli_close($conn);
?>
