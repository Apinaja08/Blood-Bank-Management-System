<?php
require 'auth.php';
require 'connection.php';
$rid = require_role('rid');
if(isset($_POST['request'])){
	$bid = filter_input(INPUT_POST, 'bid', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
	// Take the hospital and blood group from the stored sample, not from the form.
	$stmt = $conn->prepare("SELECT hid, bg FROM bloodinfo WHERE bid = ?");
	$stmt->bind_param("i", $bid);
	$stmt->execute();
	$stmt->bind_result($hid, $bg);
	$found = $bid && $stmt->fetch();
	$stmt->close();
	if(!$found){
		$error = "The selected blood sample does not exist.";
		header( "location:../abs.php?error=".$error );
		exit;
	}
	$stmt = $conn->prepare("INSERT INTO bloodrequest (bg, hid, rid) VALUES (?, ?, ?)");
	$stmt->bind_param("sii", $bg, $hid, $rid);
	if ($stmt->execute()) {
		$msg = 'You have requested for blood group '.$bg.'.For the updation of your request you can check your Status now.';
		header( "location:../abs.php?msg=".$msg);
	} else {
		$error = "Could not send your request. Please try again.";
		header( "location:../abs.php?error=".$error );
	}
	$conn->close();
}
?>
