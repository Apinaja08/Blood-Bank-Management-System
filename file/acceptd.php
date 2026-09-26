<?php
session_start();
include "connection.php";

if (!isset($_SESSION['rid']) && !isset($_SESSION['hid'])) {
    header("location:../login.php");
    exit();
}

$donoid = filter_var($_GET['donoid'] ?? null, FILTER_VALIDATE_INT);
$status = 'Accepted';

if ($donoid) {
    $stmt = $conn->prepare("UPDATE blooddonate SET status = ? WHERE donoid = ?");
    $stmt->bind_param("si", $status, $donoid);

    if ($stmt->execute()) {
        $msg = "You have accepted the request.";
        header("location:../blooddonate.php?msg=" . urlencode($msg));
    } else {
        $error = "Error changing status: Failed to execute query.";
        header("location:../blooddonate.php?error=" . urlencode($error));
    }
    $stmt->close();
} else {
    header("location:../blooddonate.php?error=" . urlencode("Invalid donation request ID."));
}

mysqli_close($conn);
exit();
?>