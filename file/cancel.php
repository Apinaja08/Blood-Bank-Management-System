<?php
require "auth.php";
include "connection.php";
    $rid = require_role('rid');
    $reqid = require_id('reqid', 'sentrequest.php');
	// A receiver may only cancel requests they sent themselves.
	$stmt = $conn->prepare("DELETE FROM bloodrequest WHERE reqid = ? AND rid = ?");
	$stmt->bind_param("ii", $reqid, $rid);
	if ($stmt->execute() && $stmt->affected_rows > 0) {
	$msg="You have cancelled request for the blood.";
	header("location:../sentrequest.php?msg=".$msg );
    } else {
    $error="Request not found or you are not allowed to cancel it.";
    header("location:../sentrequest.php?error=".$error );
    }
    mysqli_close($conn);
?>
