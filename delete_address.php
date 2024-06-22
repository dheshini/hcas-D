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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['address_id'])) {
    $address_id = $_POST['address_id'];
    $patient_id = $_SESSION['patient_id'];

    try {
        $sql = "DELETE FROM addresses WHERE address_id = ? AND patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $address_id, $patient_id);
        $stmt->execute();
        echo "Address deleted successfully!";
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
