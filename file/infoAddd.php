<?php
require 'connection.php';
session_start();
require 'csrf.php';

if (!isset($_SESSION['rid'])) {
    header('location:../login.php');
    exit();
} else {
    if (isset($_POST['add'])) {
        csrf_verify('blooddinfo.php');
        $rid = (int)$_SESSION['rid'];
        $bg  = trim($_POST['bg'] ?? '');

        $allowed_bg = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if (!in_array($bg, $allowed_bg, true)) {
            $error = 'Invalid blood group selected.';
            header("location:../blooddinfo.php?error=" . urlencode($error));
            exit();
        }

        $stmt_check = $conn->prepare("SELECT rid FROM blooddinfo WHERE rid = ? AND bg = ?");
        $stmt_check->bind_param("is", $rid, $bg);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            $error = 'You have already added this blood sample.';
            header("location:../blooddinfo.php?error=" . urlencode($error));
            exit();
        }
        $stmt_check->close();

        $stmt_insert = $conn->prepare("INSERT INTO blooddinfo (bg, rid) VALUES (?, ?)");
        $stmt_insert->bind_param("si", $bg, $rid);

        if ($stmt_insert->execute()) {
            $msg = "You have added record successfully.";
            header("location:../blooddinfo.php?msg=" . urlencode($msg));
        } else {
            $error = "Error adding record. Please try again.";
            header("location:../blooddinfo.php?error=" . urlencode($error));
        }

        $stmt_insert->close();
        $conn->close();
        exit();
    }
}
?>