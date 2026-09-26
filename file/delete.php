<?php
session_start();
include "connection.php";

$bid = filter_var($_GET['bid'] ?? null, FILTER_VALIDATE_INT);

if ($bid) {
    $stmt = $conn->prepare("DELETE FROM bloodinfo WHERE bid = ?");
    $stmt->bind_param("i", $bid);

    if ($stmt->execute()) {
        $msg = "You have deleted one blood sample.";
        header("Location: ../bloodinfo.php?msg=" . urlencode($msg));
    } else {
        $error = "Error deleting sample record.";
        header("Location: ../bloodinfo.php?error=" . urlencode($error));
    }
    $stmt->close();
} else {
    header("Location: ../bloodinfo.php?error=" . urlencode("Invalid sample ID."));
}

$conn->close();
exit();
?>