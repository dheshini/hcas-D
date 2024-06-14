<?php
// Include config file
include '../config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get appointment ID and new status
    $appointment_id = $_POST['appointment_id'];
$new_status = $_POST['status'];


    // Update status in the database
    $sql = "UPDATE appointment SET status = ? WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $appointment_id);
    $stmt->execute();

    // Redirect back to view_appointment.php after updating
    header("location: view_appointment.php");
    exit();
}
?>
