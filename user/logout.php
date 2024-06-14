<?php
session_start();
session_unset();
session_destroy();

if (isset($_GET['timeout'])) {
    echo "<script>alert('Your session has been timed out, please log in again.');</script>";
}

	header("location:patient_login.php");
exit();
?>