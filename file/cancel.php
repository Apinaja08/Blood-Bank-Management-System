<?php
session_start();
include "connection.php";

if (!isset($_SESSION['rid'])) {
    header("location:../login.php");
    exit();
}

$reqid = filter_var($_GET['reqid'] ?? null, FILTER_VALIDATE_INT);
$rid   = (int)$_SESSION['rid'];

if ($reqid) {
    $stmt = $conn->prepare("DELETE FROM bloodrequest WHERE reqid = ? AND rid = ?");
    $stmt->bind_param("ii", $reqid, $rid);

    if ($stmt->execute()) {
        $msg = "You have cancelled request for the blood.";
        header("location:../sentrequest.php?msg=" . urlencode($msg));
    } else {
        $error = "Error cancelling request: Failed to execute deletion.";
        header("location:../sentrequest.php?error=" . urlencode($error));
    }
    $stmt->close();
} else {
    header("location:../sentrequest.php?error=" . urlencode("Invalid request ID."));
}

mysqli_close($conn);
exit();
?>