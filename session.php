<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout duration in seconds (2 minutes = 120 seconds)
$timeout_duration = 300;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: logout.php?timeout=1");
        exit();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();
?>
