<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Set a success message in the session to be displayed on the index page
$_SESSION['logout_message'] = "You have been successfully logged out.";

// Redirect to the index page
header("Location: ../index.php");
exit();
?>