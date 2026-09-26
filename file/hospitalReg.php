<?php
require 'connection.php';

if (isset($_POST['hregister'])) {
    $hname     = trim($_POST['hname'] ?? '');
    $hemail    = trim($_POST['hemail'] ?? '');
    $hpassword = $_POST['hpassword'] ?? '';
    $hphone    = trim($_POST['hphone'] ?? '');
    $hcity     = trim($_POST['hcity'] ?? '');

    // Check if email already exists using prepared statement
    $stmt_check = $conn->prepare("SELECT id FROM hospitals WHERE hemail = ?");
    $stmt_check->bind_param("s", $hemail);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        $error = "Email already exists. Please try another email.";
        header("Location: ../register.php?error=" . urlencode($error));
        exit();
    }
    $stmt_check->close();

    // Insert new hospital record securely
    $stmt_insert = $conn->prepare("INSERT INTO hospitals (hname, hemail, hpassword, hphone, hcity) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssss", $hname, $hemail, $hpassword, $hphone, $hcity);

    if ($stmt_insert->execute()) {
        $msg = "You have successfully registered. Please login to continue.";
        header("Location: ../login.php?msg=" . urlencode($msg));
    } else {
        $error = "Registration failed. Please try again.";
        header("Location: ../register.php?error=" . urlencode($error));
    }

    $stmt_insert->close();
    $conn->close();
    exit();
}
?>