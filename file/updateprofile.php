<?php 
session_start();
require 'connection.php';
require 'csrf.php';

if (isset($_SESSION['rid']) && isset($_POST['update'])) {
    csrf_verify('rprofile.php');
    $id        = (int)$_SESSION['rid'];
    $rname     = trim($_POST['rname'] ?? '');
    $remail    = trim($_POST['remail'] ?? '');
    $rphone    = trim($_POST['rphone'] ?? '');
    $bg        = trim($_POST['bg'] ?? '');
    $rcity     = trim($_POST['rcity'] ?? '');
    $rpassword = $_POST['rpassword'] ?? '';

    $stmt = $conn->prepare("UPDATE receivers SET rname = ?, remail = ?, rpassword = ?, rphone = ?, rbg = ?, rcity = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $rname, $remail, $rpassword, $rphone, $bg, $rcity, $id);

    if ($stmt->execute()) {
        $msg = "Your profile is updated successfully.";
        header("Location: ../rprofile.php?msg=" . urlencode($msg));
    } else {
        $error = "Profile update failed.";
        header("Location: ../rprofile.php?error=" . urlencode($error));
    }
    $stmt->close();
    $conn->close();
    exit();

} elseif (isset($_SESSION['hid']) && isset($_POST['update'])) {
    csrf_verify('hprofile.php');
    $id        = (int)$_SESSION['hid'];
    $hname     = trim($_POST['hname'] ?? '');
    $hemail    = trim($_POST['hemail'] ?? '');
    $hphone    = trim($_POST['hphone'] ?? '');
    $hcity     = trim($_POST['hcity'] ?? '');
    $hpassword = $_POST['hpassword'] ?? '';

    $stmt = $conn->prepare("UPDATE hospitals SET hname = ?, hemail = ?, hpassword = ?, hphone = ?, hcity = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $hname, $hemail, $hpassword, $hphone, $hcity, $id);

    if ($stmt->execute()) {
        $msg = "Your profile is updated successfully.";
        header("Location: ../hprofile.php?msg=" . urlencode($msg));
    } else {
        $error = "Profile update failed.";
        header("Location: ../hprofile.php?error=" . urlencode($error));
    }
    $stmt->close();
    $conn->close();
    exit();

} else {
    header("Location: ../login.php");
    exit();
}
?>