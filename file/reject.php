<?php
session_start();
include "connection.php";

$reqid = filter_var($_GET['reqid'] ?? null, FILTER_VALIDATE_INT);
$status = "Rejected";

if ($reqid) {
    $stmt = $conn->prepare("UPDATE bloodrequest SET status = ? WHERE reqid = ?");
    $stmt->bind_param("si", $status, $reqid);

    if ($stmt->execute()) {
        $msg = "You have rejected the request.";
        header("Location: ../bloodrequest.php?msg=" . urlencode($msg));
    } else {
        $error = "Error updating request status.";
        header("Location: ../bloodrequest.php?error=" . urlencode($error));
    }
    $stmt->close();
} else {
    header("Location: ../bloodrequest.php?error=" . urlencode("Invalid request ID."));
}

$conn->close();
exit();
?>