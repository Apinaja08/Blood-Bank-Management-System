<?php
session_start();
require 'connection.php';
require 'oauth_config.php';

// Check if authorization code and state parameter were received
if (!isset($_GET['code']) || !isset($_GET['state'])) {
    $error = "OAuth authorization failed: Missing code or state parameter.";
    header("location:../login.php?error=" . urlencode($error));
    exit();
}

// 1. Validate Anti-CSRF OAuth State Parameter
if (empty($_SESSION['oauth_state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    unset($_SESSION['oauth_state']);
    $error = "Invalid OAuth state parameter. Potential CSRF attempt blocked.";
    header("location:../login.php?error=" . urlencode($error));
    exit();
}
unset($_SESSION['oauth_state']); // Invalidate state after verification

$code = $_GET['code'];

// 2. Exchange Authorization Code for Access Token
$token_url = 'https://oauth2.googleapis.com/token';
$post_params = [
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error || !$response) {
    $error = "Failed to connect to Google token endpoint.";
    header("location:../login.php?error=" . urlencode($error));
    exit();
}

$token_data = json_decode($response, true);
if (empty($token_data['access_token'])) {
    $error = "Unable to obtain Google access token: " . ($token_data['error_description'] ?? 'Authentication failed.');
    header("location:../login.php?error=" . urlencode($error));
    exit();
}

$access_token = $token_data['access_token'];

// 3. Retrieve Verified User Profile from Google UserInfo API
$userinfo_url = 'https://www.googleapis.com/oauth2/v3/userinfo';
$ch = curl_init($userinfo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$user_info_response = curl_exec($ch);
curl_close($ch);

$user_profile = json_decode($user_info_response, true);
if (empty($user_profile['email'])) {
    $error = "Unable to retrieve verified email from Google profile.";
    header("location:../login.php?error=" . urlencode($error));
    exit();
}

$email = trim($user_profile['email']);
$name  = trim($user_profile['name'] ?? explode('@', $email)[0]);

// 4. Look Up Existing User or Auto-Provision Account
$stmt_check = $conn->prepare("SELECT id, rname, remail FROM receivers WHERE remail = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id   = (int)$row['id'];
    $user_name = $row['rname'];
} else {
    // Auto-provision a new receiver record for first-time Google sign-in
    $random_password = bin2hex(random_bytes(16));
    $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
    $default_phone   = "0000000000";
    $default_city    = "Not Specified";
    $default_bg      = "O+";

    $stmt_insert = $conn->prepare("INSERT INTO receivers (rname, remail, rpassword, rphone, rcity, rbg) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssss", $name, $email, $hashed_password, $default_phone, $default_city, $default_bg);
    $stmt_insert->execute();
    $user_id   = (int)$conn->insert_id;
    $user_name = $name;
    $stmt_insert->close();
}
$stmt_check->close();
$conn->close();

// 5. Establish Secure Authenticated Session
session_regenerate_id(true); // Defends against session fixation
$_SESSION['rid']    = $user_id;
$_SESSION['rname']  = $user_name;
$_SESSION['remail'] = $email;

$msg = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') . " logged in successfully with Google.";
header("location:../Userpage.html?msg=" . urlencode($msg));
exit();
?>
