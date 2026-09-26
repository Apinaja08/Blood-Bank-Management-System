<?php
// CSRF protection helpers, shared by every state-changing endpoint.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Return the current session's CSRF token, creating one on first use.
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// A hidden field carrying the token. Place this inside every POST form.
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

// Verify the submitted token against the session. On mismatch, stop the
// request and send the user back with an error. $redirect is relative to
// the site root, e.g. "bloodinfo.php".
function csrf_verify($redirect) {
    $sent = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        header("location:../" . $redirect . "?error=Invalid or missing CSRF token.");
        exit;
    }
}
?>
