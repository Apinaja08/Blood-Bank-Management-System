<?php
require 'connection.php';

if (isset($_POST['rregister'])) {
    $rname     = trim($_POST['rname'] ?? '');
    $remail    = trim($_POST['remail'] ?? '');
    $rpassword = $_POST['rpassword'] ?? '';
    $rphone    = trim($_POST['rphone'] ?? '');
    $rcity     = trim($_POST['rcity'] ?? '');
    $rbg       = trim($_POST['rbg'] ?? '');

    // Validate blood group
    $allowed_bg = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    if (!in_array($rbg, $allowed_bg, true)) {
        header("Location: ../register.php?error=" . urlencode("Invalid blood group selected."));
        exit();
    }

    // Check duplicate email
    $stmt_check = $conn->prepare("SELECT id FROM receivers WHERE remail = ?");
    $stmt_check->bind_param("s", $remail);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        $error = "Email already exists. Please try another email.";
        header("Location: ../register.php?error=" . urlencode($error));
        exit();
    }
    $stmt_check->close();

    // Insert new user
    $stmt_insert = $conn->prepare("INSERT INTO receivers (rname, remail, rpassword, rphone, rcity, rbg) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssss", $rname, $remail, $rpassword, $rphone, $rcity, $rbg);

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