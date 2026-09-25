<?php
session_start(); 
require 'connection.php';

if (!isset($_SESSION['rid'])) {
    header('location:../login.php');
    exit();
} else {
    if (isset($_POST['request'])) {
        // CHANGED (Lines 10-12): Added integer validation for $hid and strict allowlist validation for $bg
        $hid = filter_var($_POST['hid'] ?? null, FILTER_VALIDATE_INT);
        $rid = (int)$_SESSION['rid'];
        $bg  = trim($_POST['bg'] ?? '');

        // V3 FIX: Enforce allowlist on blood group so arbitrary strings/file paths cannot be processed
        $allowed_bg = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if (!$hid || !in_array($bg, $allowed_bg, true)) {
            $error = "Invalid blood group or hospital selection.";
            header("location:../abs.php?error=" . urlencode($error));
            exit();
        }

        // CHANGED (Line 13): Replaced raw string concatenation in SELECT with prepared statement
        $stmt_check = $conn->prepare("SELECT reqid FROM bloodrequest WHERE hid = ? AND rid = ?");
        $stmt_check->bind_param("ii", $hid, $rid);
        $stmt_check->execute();
        $check_data = $stmt_check->get_result();

        // CHANGED (Lines 15-32): Unified duplicate insertion logic and replaced with a single prepared statement
        $stmt_insert = $conn->prepare("INSERT INTO bloodrequest (bg, hid, rid) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sii", $bg, $hid, $rid);

        if ($stmt_insert->execute()) {
            $msg = 'You have requested for blood group ' . htmlspecialchars($bg) . '. For the updation of your request you can check your Status now.';
            header("location:../abs.php?msg=" . urlencode($msg));
        } else {
            $error = "Failed to submit request. Please try again.";
            header("location:../abs.php?error=" . urlencode($error));
        }

        $stmt_check->close();
        $stmt_insert->close();
        $conn->close();
        exit();
    }
}
?>