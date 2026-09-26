<?php
require 'auth.php';
require 'csrf.php';
require 'connection.php';
$hid = require_role('hid');
if(isset($_POST['request'])){
	csrf_verify('deleteit.php');
	$bdid = filter_input(INPUT_POST, 'bdid', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
	// Take the donor and blood group from the stored sample, not from the form.
	$stmt = $conn->prepare("SELECT rid, bg FROM blooddinfo WHERE bdid = ?");
	$stmt->bind_param("i", $bdid);
	$stmt->execute();
	$stmt->bind_result($rid, $bg);
	$found = $bdid && $stmt->fetch();
	$stmt->close();
	if(!$found){
		$error = "The selected blood sample does not exist.";
		header( "location:../deleteit.php?error=".urlencode($error));
		exit;
	}
	$stmt = $conn->prepare("INSERT INTO blooddonate (bg, rid, hid) VALUES (?, ?, ?)");
	$stmt->bind_param("sii", $bg, $rid, $hid);
	if ($stmt->execute()) {
		$msg = 'You have requested for blood group '.$bg.'.For the updation of your request you can check your Status now.';
		header( "location:../deleteit.php?msg=".urlencode($msg));
	} else {
		$error = "Could not send your request. Please try again.";
		header( "location:../deleteit.php?error=".urlencode($error));
	}
	$conn->close();
}
?>
