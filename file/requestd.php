<?php
session_start(); 
require 'connection.php';

if (!isset($_SESSION['hid'])) {
    header('location:../login.php');
    exit();
} else {
    if (isset($_POST['request'])) {
        $rid = filter_var($_POST['rid'] ?? null, FILTER_VALIDATE_INT);
        $hid = (int)$_SESSION['hid'];
        $bg  = trim($_POST['bg'] ?? '');

        $allowed_bg = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if (!$rid || !in_array($bg, $allowed_bg, true)) {
            $error = "Invalid donor ID or blood group selected.";
            header("location:../deleteit.php?error=" . urlencode($error));
            exit();
        }

        $stmt_check = $conn->prepare("SELECT donoid FROM blooddonate WHERE rid = ? AND hid = ?");
        $stmt_check->bind_param("ii", $rid, $hid);
        $stmt_check->execute();
        $check_data = $stmt_check->get_result();

        $stmt_insert = $conn->prepare("INSERT INTO blooddonate (bg, rid, hid) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sii", $bg, $rid, $hid);

        if ($stmt_insert->execute()) {
            $msg = 'You have requested for blood group ' . htmlspecialchars($bg) . '. For the updation of your request you can check your Status now.';
            header("location:../deleteit.php?msg=" . urlencode($msg));
        } else {
            $error = "Error: Failed to submit donation request.";
            header("location:../deleteit.php?error=" . urlencode($error));
        }

        $stmt_check->close();
        $stmt_insert->close();
        $conn->close();
        exit();
    }
}
?>