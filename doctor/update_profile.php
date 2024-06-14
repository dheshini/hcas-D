<?php
session_start();
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $home_address = $_POST['home_address'];
    $identity_card = $_POST['identity_card'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $max_appointments_per_day = $_POST['max_appointments_per_day'];
    $email = $_SESSION['email'];

    // Prepare and execute the SQL query to update the doctor's profile
    $sql = "UPDATE doctors SET phone = ?, gender = ?, age = ?, home_address = ?, identity_card = ?, dateOfBirth = ?, first_name = ?, last_name = ?, max_appointments_per_day = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssssis", $phone, $gender, $age, $home_address, $identity_card, $dateOfBirth, $first_name, $last_name, $max_appointments_per_day, $email);

    // Execute the query and handle errors
    if ($stmt->execute()) {
        echo '<script>alert("Profile updated successfully."); window.location.href = "profile.php";</script>';
    } else {
        echo '<script>alert("Error updating profile: ' . mysqli_error($conn) . '"); window.location.href = "profile.php";</script>';
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
