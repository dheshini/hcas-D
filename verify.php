<?php
include '../config.php';
include('../session.php');

$verification_code = $_GET['code'];

// Check if the verification code belongs to a patient
$sql = "SELECT patient_id, verification_code_expires FROM patients WHERE verification_code = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $verification_code);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $patient_id, $verification_code_expires);
            mysqli_stmt_fetch($stmt);

            // Check if verification code has expired
            if (strtotime($verification_code_expires) < time()) {
                echo "<script>alert('Could not verify your email. Verification code has expired. Please register again.');</script>";
                echo "<script>window.location.href='patient_register.php';</script>"; // Redirect to patient_register.php
                exit(); // Stop further execution
            } else {
                // If verification code is valid and not expired, update the verified column
                $sql_update = "UPDATE patients SET verified = 1 WHERE verification_code = ?";
                if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
                    mysqli_stmt_bind_param($stmt_update, "s", $verification_code);
                    if (mysqli_stmt_execute($stmt_update)) {
                        echo "<script>alert('Email successfully verified.'); window.location.href='patient_login.php';</script>";
                    } else {
                        echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
                    }
                    mysqli_stmt_close($stmt_update);
                }
            }
        } else {
            echo "<script>alert('Invalid verification code.');</script>";
            echo "<script>window.location.href='patient_register.php';</script>"; // Redirect to patient_register.php
            exit(); // Stop further execution
        }
    } else {
        echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        echo "<script>window.location.href='patient_register.php';</script>"; // Redirect to patient_register.php
        exit(); // Stop further execution
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
