<?php
require "auth.php";
require "csrf.php";
include "connection.php";
    $rid = require_role('rid');
    csrf_verify('blooddonate.php');
    $donoid = require_id('donoid', 'blooddonate.php');
	$status = 'Accepted';
	// Only the receiver the donation request was sent to may change its status.
	$stmt = $conn->prepare("UPDATE blooddonate SET status = ? WHERE donoid = ? AND rid = ?");
	$stmt->bind_param("sii", $status, $donoid, $rid);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
	$msg="You have accepted the request.";
	header("location:../blooddonate.php?msg=".urlencode($msg));
    } else {
    $error= "Request not found or you are not allowed to change it.";
    header("location:../blooddonate.php?error=".urlencode($error));
    }
    mysqli_close($conn);
?>
