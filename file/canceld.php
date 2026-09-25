<?php
session_start();
include "connection.php";

if (!isset($_SESSION['hid'])) {
    header("location:../login.php");
    exit();
}

$donoid = filter_var($_GET['donoid'] ?? null, FILTER_VALIDATE_INT);
$hid    = (int)$_SESSION['hid'];

if ($donoid) {
    $stmt = $conn->prepare("DELETE FROM blooddonate WHERE donoid = ? AND hid = ?");
    $stmt->bind_param("ii", $donoid, $hid);

    if ($stmt->execute()) {
        $msg = "You have cancelled request for the blood.";
        header("location:../sentrequestd.php?msg=" . urlencode($msg));
    } else {
        $error = "Error cancelling record: Failed to execute deletion.";
        header("location:../sentrequestd.php?error=" . urlencode($error));
    }
    $stmt->close();
} else {
    header("location:../sentrequestd.php?error=" . urlencode("Invalid donation request ID."));
}

mysqli_close($conn);
exit();
?>