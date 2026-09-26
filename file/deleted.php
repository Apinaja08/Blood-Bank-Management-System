<?php
session_start();
include "connection.php";

if (!isset($_SESSION['rid'])) {
    header("location:../login.php");
    exit();
}

$bdid = filter_var($_GET['bdid'] ?? null, FILTER_VALIDATE_INT);
$rid  = (int)$_SESSION['rid'];

if ($bdid) {
    $stmt = $conn->prepare("DELETE FROM blooddinfo WHERE bdid = ? AND rid = ?");
    $stmt->bind_param("ii", $bdid, $rid);

    if ($stmt->execute()) {
        $msg = "You have deleted one blood sample.";
        header("location:../blooddinfo.php?msg=" . urlencode($msg));
    } else {
        $error = "Error deleting record: Failed to execute deletion.";
        header("location:../blooddinfo.php?error=" . urlencode($error));
    }
    $stmt->close();
} else {
    header("location:../blooddinfo.php?error=" . urlencode("Invalid blood sample ID."));
}

mysqli_close($conn);
exit();
?>