<?php
session_start();


// Include config file
include '../config.php';

// Retrieve doctor ID from session
$doctor_id = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT doctor_id FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $doctor_id = $row['doctor_id'];
    }
    $stmt->close();
}

// Initialize a variable to track if all emails were sent successfully
$allEmailsSent = true;

// Retrieve appointments for the next day for the current doctor
if (!is_null($doctor_id)) {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Calculate the next day
    $nextDay = date('Y-m-d', strtotime($currentDate . ' +1 day'));

    // Query appointments for the next day
    $sql = "SELECT a.appointment_id, p.email, p.first_name, p.last_name
            FROM appointment a 
            JOIN patients p ON a.patient_id = p.patient_id 
            WHERE a.doctor_id = ? AND DATE(a.appointment_time) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctor_id, $nextDay);
    $stmt->execute();
    $result = $stmt->get_result();

    // Send email to each patient
    while ($row = $result->fetch_assoc()) {
        $patient_email = $row['email'];
        $patient_name = $row['first_name'] . ' ' . $row['last_name'];
        $to = $patient_email;
        $subject = "Reminder: Be On Time for Your Appointment Tomorrow";
        $body = "Dear $patient_name,\n\nThis is a reminder from Dr. ".$_SESSION['email']." to be on time for your appointment tomorrow.\n\nThank you.";
        $headers = "From: Hannani Clinic Team <" . $_SESSION['email'] . ">";

        // Send email
        if (!mail($to, $subject, $body, $headers)) {
            // Set the flag to false if any email fails to send
            $allEmailsSent = false;
        }
    }

    // Check if all emails were sent successfully
    if ($allEmailsSent) {
        // Display success message using JavaScript alert
        echo "<script>alert('Reminder emails sent successfully to patients for tomorrow\'s appointments.');</script>";
    } else {
        // Display error message using JavaScript alert
        echo "<script>alert('Failed to send one or more reminder emails to patients.');</script>";
    }
}

// Redirect back to previous page
header("location: message.php");
exit;
?>
