<?php
session_start(); 
require 'connection.php';

// 1. ACCESS CONTROL: Ensure only logged-in receivers can submit requests
if (!isset($_SESSION['rid'])) {
    header('location:../login.php');
    exit();
} else {
    if (isset($_POST['request'])) {

        // 2. INTEGER TYPE VALIDATION: Validate $hid as a strict positive integer
        $hid = filter_var($_POST['hid'] ?? null, FILTER_VALIDATE_INT);
        $rid = (int)$_SESSION['rid'];
        $bg  = trim($_POST['bg'] ?? '');

        // 3. STRICT ALLOWLIST (V3 Core Fix): Restrict $bg exclusively to valid blood groups
        $allowed_bg = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if (!$hid || !in_array($bg, $allowed_bg, true)) {
            // Rejects any traversal sequences (../), filenames, or unexpected strings
            $error = "Invalid blood group or hospital selection.";
            header("location:../abs.php?error=" . urlencode($error));
            exit();
        }

        // 4. PREPARED STATEMENTS: Parameterize the duplicate check query
        $stmt_check = $conn->prepare("SELECT reqid FROM bloodrequest WHERE hid = ? AND rid = ?");
        $stmt_check->bind_param("ii", $hid, $rid);
        $stmt_check->execute();
        $check_data = $stmt_check->get_result();

        // 5. PREPARED STATEMENTS: Parameterize the INSERT query
        $stmt_insert = $conn->prepare("INSERT INTO bloodrequest (bg, hid, rid) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sii", $bg, $hid, $rid);

        if ($stmt_insert->execute()) {
            // Output encoding prevents XSS on reflection
            $msg = 'You have requested for blood group ' . htmlspecialchars($bg, ENT_QUOTES, 'UTF-8') . '. For the updation of your request you can check your Status now.';
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