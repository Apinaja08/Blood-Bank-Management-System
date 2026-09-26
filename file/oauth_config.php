<?php
// OAuth 2.0 Configuration for Google Sign-In

define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '789241445860-vdfu98sif4f30t23s7usvb5fja4lj5r3.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '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');
define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8090/file/oauth_callback.php');
?>
