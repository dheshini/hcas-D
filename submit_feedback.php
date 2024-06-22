<?php
session_start();
include '../config.php';
include('../session.php');


// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: patient_login.php");
    exit();
}

var_dump($_SESSION);
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    if(isset($_SESSION['patient_id'])) {
        $patient_id = $_SESSION['patient_id']; // Assuming you have patient session data
    } else {
        // Handle the case when 'patient_id' is not set
        // You can redirect the user to the login page or display an error message
        exit("Patient ID not set in session.");
    }
    
    $type = $_POST['type'];
    $message = $_POST['message'];

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (patient_id, type, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $patient_id, $type, $message);
    $stmt->execute();

    // Check if insertion was successful
    if ($stmt->affected_rows > 0) {
        // Feedback submitted successfully
        $_SESSION['feedback_success'] = "Feedback submitted successfully.";
    } else {
        // Failed to submit feedback
        $_SESSION['feedback_error'] = "Failed to submit feedback. Please try again.";
    }

    // Redirect back to the feedback page
    header("Location: feedback.php");
    exit();
} else {
    // Redirect to the feedback page if form is not submitted
    header("Location: feedback.php");
    exit();
}

// Close connection
$conn->close();
?>
