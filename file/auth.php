<?php
// Access-control helpers shared by the action scripts in this folder.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Returns the logged-in user's id for the given role ('hid' = hospital,
// 'rid' = receiver). Anyone else is sent to the login page.
function require_role($role) {
    if (!isset($_SESSION[$role])) {
        header('location:../login.php');
        exit;
    }
    return (int) $_SESSION[$role];
}

// Reads a positive integer id from the POSTed form, or stops the request.
function require_id($name, $redirect) {
    $id = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if (!$id) {
        header("location:../".$redirect."?error=Invalid request.");
        exit;
    }
    return $id;
}
?>
