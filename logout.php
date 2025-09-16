<?php
session_start();

/* wipe everything */
session_unset();
session_destroy();

/* optional: delete the session cookie too */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

/* back to login */
header("Location: index.php");
exit();
?>