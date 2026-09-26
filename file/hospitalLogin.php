<?php
session_start();
require 'connection.php';

if (isset($_POST['hlogin'])) {
    $hemail = trim($_POST['hemail'] ?? '');
    $hpassword = $_POST['hpassword'] ?? '';

    // CHANGED (Lines 7-8): Replaced dynamic SQL string interpolation with a prepared statement
    $stmt = $conn->prepare("SELECT id, hname, hemail, hpassword FROM hospitals WHERE hemail = ?");
    $stmt->bind_param("s", $hemail);
    $stmt->execute();
    $result = $stmt->get_result();

    // CHANGED (Lines 9-20): Safe credential verification and session ID regeneration
    if ($row = $result->fetch_assoc()) {
        if ($hpassword === $row['hpassword'] || password_verify($hpassword, $row['hpassword'])) {
            session_regenerate_id(true); // Defends against session fixation
            $_SESSION['hemail'] = $row['hemail'];
            $_SESSION['hname']  = $row['hname'];
            $_SESSION['hid']    = (int)$row['id'];
            $msg = $row['hname'] . ' have logged in.';
            header("location:../hospitalpage.html?msg=" . urlencode($msg));
            exit();
        }
    }

    $stmt->close();
    $conn->close();
    $error = "Wrong email or password. Please try again.";
    header("location:../login.php?error=" . urlencode($error));
    exit();
}
?>