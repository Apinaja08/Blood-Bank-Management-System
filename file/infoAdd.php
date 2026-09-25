<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['hid'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['add'])) {
    $hid = (int)$_SESSION['hid'];
    $bg  = trim($_POST['bg'] ?? '');

    $allowed_bg = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    if (!in_array($bg, $allowed_bg, true)) {
        header("Location: ../bloodinfo.php?error=" . urlencode("Invalid blood group selected."));
        exit();
    }

    // Check duplicate
    $stmt_check = $conn->prepare("SELECT hid FROM bloodinfo WHERE hid = ? AND bg = ?");
    $stmt_check->bind_param("is", $hid, $bg);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        $error = "You have already added this blood sample.";
        header("Location: ../bloodinfo.php?error=" . urlencode($error));
        exit();
    }
    $stmt_check->close();

    // Insert
    $stmt_insert = $conn->prepare("INSERT INTO bloodinfo (bg, hid) VALUES (?, ?)");
    $stmt_insert->bind_param("si", $bg, $hid);

    if ($stmt_insert->execute()) {
        $msg = "Blood sample added successfully.";
        header("Location: ../bloodinfo.php?msg=" . urlencode($msg));
    } else {
        $error = "Failed to add sample.";
        header("Location: ../bloodinfo.php?error=" . urlencode($error));
    }

    $stmt_insert->close();
    $conn->close();
    exit();
}
?>