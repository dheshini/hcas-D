<?php
include '../config.php';
include('../session.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: patient_login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the activation_tokens table
    $sql_check_token = "SELECT * FROM activation_tokens WHERE token = ?";
    $stmt_check_token = $conn->prepare($sql_check_token);

    if ($stmt_check_token !== false) {
        $stmt_check_token->bind_param("s", $token);
        $stmt_check_token->execute();
        $result_check_token = $stmt_check_token->get_result();

        if ($result_check_token->num_rows > 0) {
            // Token exists, activate the user account
            $row = $result_check_token->fetch_assoc();
            $patient_id = $row['patient_id'];

            // Update the patient's account status or perform any other activation tasks
            // For example, you could set a flag in the patients table indicating the account is activated

            // After activation, delete the token from the activation_tokens table
            $sql_delete_token = "DELETE FROM activation_tokens WHERE token = ?";
            $stmt_delete_token = $conn->prepare($sql_delete_token);

            if ($stmt_delete_token !== false) {
                $stmt_delete_token->bind_param("s", $token);
                $stmt_delete_token->execute();

                // Redirect the user to the patient_login.php page
                header("Location: patient_login.php");
                exit;
            } else {
                echo "Failed to delete activation token.";
            }
        } else {
            echo "Invalid or expired activation token.";
        }
    } else {
        echo "Failed to check activation token.";
    }
} else {
    echo "Invalid request.";
}
?>
